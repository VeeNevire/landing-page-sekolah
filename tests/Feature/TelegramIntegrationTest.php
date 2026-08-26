<?php

namespace Tests\Feature;

use App\Jobs\SendGradeTelegramNotification;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class TelegramIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_create_a_telegram_link_without_exposing_the_stored_hash(): void
    {
        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.bot_username' => 'school_test_bot',
        ]);

        $parent = User::factory()->create(['role' => 'parent']);

        $response = $this->actingAs($parent)->post(route('portal.telegram.connect'));

        $response->assertRedirectContains('https://t.me/school_test_bot?start=link_');
        $link = $response->headers->get('Location');
        $rawToken = Str::after($link, 'start=link_');

        $this->assertNotEmpty($rawToken);
        $this->assertSame(hash('sha256', $rawToken), $parent->fresh()->telegram_link_token_hash);
        $this->assertNotSame($rawToken, $parent->fresh()->telegram_link_token_hash);
    }

    public function test_long_polling_links_parent_and_sends_confirmation(): void
    {
        config([
            'services.telegram.bot_token' => 'test-token',
            'services.telegram.bot_username' => 'school_test_bot',
        ]);
        Cache::forget('telegram.poll.offset.'.substr(hash('sha256', 'test-token'), 0, 16));
        Http::fakeSequence()
            ->push(['ok' => true, 'result' => true])
            ->push([
                'ok' => true,
                'result' => [[
                    'update_id' => 100,
                    'message' => [
                        'chat' => ['id' => 123456],
                        'text' => '/start link_abc123',
                    ],
                ]],
            ])
            ->push(['ok' => true, 'result' => []]);

        $parent = User::factory()->create([
            'role' => 'parent',
            'telegram_link_token_hash' => hash('sha256', 'abc123'),
            'telegram_link_token_expires_at' => now()->addMinutes(10),
        ]);

        $this->artisan('telegram:poll', ['--once' => true])->assertSuccessful();

        $this->assertSame('123456', $parent->fresh()->telegram_chat_id);
        $this->assertNull($parent->fresh()->telegram_link_token_hash);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/sendMessage')
            && $request['chat_id'] === '123456');
    }

    public function test_grade_job_sends_to_each_connected_parent(): void
    {
        config(['services.telegram.bot_token' => 'test-token']);
        Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true])]);

        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Budi']);
        $parent = User::factory()->create(['role' => 'parent', 'telegram_chat_id' => '998877']);
        $period = AcademicPeriod::create([
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-19',
            'is_active' => true,
        ]);
        $subject = Subject::create(['code' => 'MAT', 'name' => 'Matematika', 'kkm' => 75]);
        $student = Student::create([
            'full_name' => 'Ahmad Fauzan',
            'nisn' => '1234567890',
            'class_name' => 'X RPL 1',
            'program_name' => 'RPL',
            'status' => 'active',
        ]);
        $student->parents()->attach($parent->id, ['relationship' => 'Ayah']);
        $assignment = TeachingAssignment::create([
            'period_id' => $period->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'class_name' => 'X RPL 1',
        ]);
        $assessment = Assessment::create([
            'teaching_assignment_id' => $assignment->id,
            'title' => 'Tugas 1',
            'component' => 'homework',
            'assessment_date' => now(),
            'max_score' => 100,
        ]);
        AssessmentScore::create([
            'assessment_id' => $assessment->id,
            'student_id' => $student->id,
            'score' => 85,
            'feedback' => 'Baik',
            'graded_at' => now(),
        ]);

        (new SendGradeTelegramNotification($assessment->id, $student->id, $parent->id))->handle(app(TelegramService::class));

        $sentRequest = collect(Http::recorded())
            ->first(fn ($record) => str_ends_with($record[0]->url(), '/sendMessage'))[0] ?? null;

        $this->assertNotNull($sentRequest);
        $this->assertStringContainsString('Ahmad Fauzan', $sentRequest['text']);
        $this->assertStringContainsString('Matematika', $sentRequest['text']);
        $this->assertStringContainsString('85 / 100 (KKM 75)', $sentRequest['text']);
        $this->assertStringContainsString('✅ Tuntas', $sentRequest['text']);
        $this->assertStringContainsString('Baik', $sentRequest['text']);
    }
}
