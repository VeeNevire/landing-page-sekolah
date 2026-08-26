<?php

namespace App\Jobs;

use App\Models\AssessmentScore;
use App\Models\Student;
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
    ) {}

    public static function dispatchForScore(int $assessmentId, int $studentId): void
    {
        $student = Student::find($studentId);

        $student?->parents()
            ->whereNotNull('telegram_chat_id')
            ->pluck('users.id')
            ->each(fn ($parentId) => self::dispatch($assessmentId, $studentId, (int) $parentId)->afterCommit());
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

        $parent = $score->student->parents()
            ->whereKey($this->parentId)
            ->whereNotNull('telegram_chat_id')
            ->first();

        if (! $parent) {
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

        $telegram->sendMessage($parent->telegram_chat_id, $message);
    }

    private function formatNumber(float|string $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}
