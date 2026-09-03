<?php

namespace App\Jobs;

use App\Models\AssessmentScore;
use App\Models\Student;
use App\Models\TelegramConnection;
use App\Models\TelegramTemplate;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGradeTelegramNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function __construct(
        public int $assessmentId,
        public int $studentId,
        public int $parentId,
        public ?int $templateId = null,
        public ?int $botId = null,
    ) {}

    public static function dispatchForScore(int $assessmentId, int $studentId): void
    {
        $student = Student::find($studentId);

        $student?->parents()
            ->whereNotNull('telegram_chat_id')
            ->pluck('users.id')
            ->each(fn ($parentId) => self::dispatch($assessmentId, $studentId, (int) $parentId)->afterCommit());
    }

    public static function dispatchForPublishedScore(int $assessmentId, int $studentId, int $templateId, ?int $botId = null): void
    {
        $template = TelegramTemplate::with('bot')->find($templateId);
        $student = Student::find($studentId);
        if (! $template || ! $template->is_active || ! $student) return;
        $botId = $botId ?: $template->telegram_bot_id;
        if (! $botId) return;

        TelegramConnection::where('telegram_bot_id', $botId)
            ->whereNotNull('chat_id')
            ->whereHas('user', fn ($q) => $q->whereHas('students', fn ($sq) => $sq->whereKey($studentId)))
            ->pluck('user_id')
            ->each(fn ($parentId) => self::dispatch($assessmentId, $studentId, (int) $parentId, $templateId, $botId)->afterCommit());
    }

    public function handle(TelegramService $telegram): void
    {
        $score = AssessmentScore::with([
            'student',
            'assessment.teachingAssignment.subject',
            'assessment.teachingAssignment.customSubject',
            'assessment.teachingAssignment.teacher',
        ])->where('assessment_id', $this->assessmentId)
            ->where('student_id', $this->studentId)
            ->first();

        if (! $score || $score->score === null) {
            return;
        }

        $parent = $score->student->parents()->whereKey($this->parentId)->first();
        if (! $parent) return;

        $connection = null;
        $template = $this->templateId ? TelegramTemplate::with('bot')->find($this->templateId) : null;
        if ($template) {
            $bot = $this->botId ? \App\Models\TelegramBot::find($this->botId) : $template->bot;
            $connection = TelegramConnection::where('user_id', $parent->id)->where('telegram_bot_id', $bot?->id)->whereNotNull('chat_id')->first();
            if (! $connection) return;
        } elseif (! $parent->telegram_chat_id) {
            return;
        }

        $assignment = $score->assessment->teachingAssignment;
        $subjectModel = $assignment->subject ?? $assignment->customSubject;
        $subject = $subjectModel?->name ?? $subjectModel?->nama ?? '-';
        $teacher = $assignment->teacher?->full_name ?? $assignment->teacher?->name ?? '-';
        $kkm = (float) ($subjectModel?->kkm ?? 75);
        $passed = (float) $score->score >= $kkm;
        $status = $passed ? '✅ Tuntas' : '⚠️ Perlu Perhatian';
        $advice = $passed
            ? 'Terima kasih atas pendampingan Bapak/Ibu. Tetap dukung semangat belajarnya.'
            : 'Nilai belum mencapai KKM. Mohon dampingi siswa untuk meningkatkan hasil belajarnya.';
        $feedback = $score->feedback
            ? "\n\n💬 Catatan Guru\n{$score->feedback}"
            : '';
        $scoreValue = $this->formatNumber($score->score);
        $maxScore = $this->formatNumber($score->assessment->max_score);
        $kkmValue = $this->formatNumber($kkm);
        $date = $score->graded_at?->format('d-m-Y') ?? now()->format('d-m-Y');
        $portalUrl = route('portal.laporan');
        $schoolName = config('school.name', config('app.name'));

        $message = "🔔 INFORMASI NILAI SISWA\n\n"
            ."Permisi.. Bapak/Ibu,\n\n"
            ."Nilai baru telah diberikan oleh guru untuk:\n\n"
            ."👤 Siswa\n{$score->student->full_name}\n\n"
            ."📚 Mata Pelajaran\n{$subject}\n\n"
            ."📝 Penilaian\n{$score->assessment->title}\n\n"
            ."🏆 Nilai\n{$scoreValue} / {$maxScore} (KKM {$kkmValue})\n\n"
            ."📌 Status\n{$status}\n\n"
            ."👨‍🏫 Guru\n{$teacher}\n\n"
            ."📅 Tanggal Penilaian\n{$date}"
            ."{$feedback}\n\n"
            ."{$advice}\n\n"
            ."Salam,\n{$schoolName}";

        if ($template) {
            $message = strtr($template->body, [
                '{{nama_siswa}}' => $score->student->full_name,
                '{{mata_pelajaran}}' => $subject,
                '{{penilaian}}' => $score->assessment->title,
                '{{nilai}}' => $scoreValue,
                '{{nilai_maksimal}}' => $maxScore,
                '{{kkm}}' => $kkmValue,
                '{{status}}' => strip_tags($status),
                '{{guru}}' => $teacher,
                '{{tanggal}}' => $date,
                '{{catatan}}' => $score->feedback ?: '-',
                '{{link_detail}}' => $portalUrl,
                '{{nama_sekolah}}' => $schoolName,
            ]);
        }

        $telegram->sendMessage($connection?->chat_id ?: $parent->telegram_chat_id, $message, $bot ?? $template?->bot);
    }

    private function formatNumber(float|string $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
