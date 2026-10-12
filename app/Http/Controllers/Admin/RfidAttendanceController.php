<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\AuditService;
use App\Services\RfidService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RfidAttendanceController extends Controller
{
    public function index(Request $request, RfidService $rfid)
    {
        $tab = $request->query('tab') === 'registration' ? 'registration' : 'today';
        $students = Student::where('status', 'active')
            ->when($request->query('class'), fn ($q, $v) => $q->where('class_name', $v))
            ->when($request->query('search'), fn ($q, $v) => $q->where(fn ($q) => $q
                ->where('full_name', 'like', '%'.$v.'%')->orWhere('nis', 'like', '%'.$v.'%')->orWhere('nisn', 'like', '%'.$v.'%')));
        $records = Attendance::where('attendance_date', today()->toDateString())->whereIn('student_id', (clone $students)->select('id'));
        $summary = ['active' => (clone $students)->count(), 'recorded' => (clone $records)->count(),
            'present' => (clone $records)->where('status', 'present')->count()];
        $summary['unrecorded'] = $summary['active'] - $summary['recorded'];
        $rows = $tab === 'registration' ? $students->orderBy('full_name')->paginate(20)->withQueryString()
            : $records->with('student')->orderByDesc('check_in_at')->orderByDesc('id')->paginate(30)->withQueryString();
        $classes = Student::where('status', 'active')->distinct()->orderBy('class_name')->pluck('class_name');
        $lastScan = $this->lastScan();

        $useStream = config('rfid.live_stream_enabled', true);

        return view('admin.rfid-attendance', compact('tab', 'rows', 'classes', 'summary', 'lastScan', 'useStream'));
    }

    public function data(Request $request)
    {
        return response()->json($this->todayPayload($request))->header('Cache-Control', 'no-store');
    }

    public function stream(Request $request)
    {
        return $this->liveResponse('attendance', fn () => $this->todayPayload($request), ['server_time']);
    }

    public function registrationStream(Request $request, RfidService $rfid)
    {
        return $this->liveResponse('registration', fn () => $this->state($request, $rfid)->getData(true), ['remaining_seconds', 'server_time']);
    }

    private function liveResponse(string $event, callable $payload, array $volatileFields)
    {
        abort_unless(config('rfid.live_stream_enabled', true), 503, 'Koneksi live RFID dinonaktifkan.');

        return response()->stream(function () use ($event, $payload, $volatileFields) {
            set_time_limit(0);
            @ini_set('zlib.output_compression', '0');
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
            if (! app()->runningUnitTests()) {
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
                ob_implicit_flush(true);
            }

            $lastSignature = null;
            $lastPing = time();
            // Bound each connection; EventSource reconnects and rechecks authentication.
            $deadline = microtime(true) + 45;
            echo "retry: 2000\n\n";
            while (! connection_aborted() && microtime(true) < $deadline) {
                $data = $payload();
                $stable = array_diff_key($data, array_flip($volatileFields));
                $signature = hash('sha256', json_encode($stable));
                if ($signature !== $lastSignature) {
                    echo "event: {$event}\ndata: ".json_encode($data)."\n\n";
                    $lastSignature = $signature;
                    $lastPing = time();
                } elseif (time() - $lastPing >= 15) {
                    echo ": keep-alive\n\n";
                    $lastPing = time();
                }
                if (app()->runningUnitTests()) {
                    break;
                }
                @ob_flush();
                flush();
                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function todayPayload(Request $request): array
    {
        $students = Student::where('status', 'active')
            ->when($request->query('class'), fn ($q, $v) => $q->where('class_name', $v))
            ->when($request->query('search'), fn ($q, $v) => $q->where(fn ($q) => $q
                ->where('full_name', 'like', '%'.$v.'%')->orWhere('nis', 'like', '%'.$v.'%')->orWhere('nisn', 'like', '%'.$v.'%')));
        $records = Attendance::where('attendance_date', today()->toDateString())->whereIn('student_id', (clone $students)->select('id'));
        $summary = ['active' => (clone $students)->count(), 'recorded' => (clone $records)->count(),
            'present' => (clone $records)->where('status', 'present')->count()];
        $summary['unrecorded'] = $summary['active'] - $summary['recorded'];
        $rows = $records->with('student')->orderByDesc('check_in_at')->orderByDesc('id')->paginate(30)->withQueryString();
        $lastScan = $this->lastScan();
        $html = view('admin.partials.rfid-today-table', compact('rows', 'summary', 'lastScan'))->render();

        return [
            'summary' => $summary,
            'html' => $html,
            'lastScan' => $lastScan,
            'server_time' => now()->format('H:i:s'),
        ];
    }

    private function lastScan(): array
    {
        $state = DB::table('rfid_reader_state')->where('id', 1)->first();

        return [
            'message' => $state?->last_message,
            'seen_at' => $state?->last_seen_at ? Carbon::parse($state->last_seen_at)->format('H:i:s') : null,
        ];
    }

    public function state(Request $request, RfidService $rfid)
    {
        $request->validate(['student_id' => 'nullable|integer|min:1']);
        $requestedStudent = $request->filled('student_id')
            ? Student::where('status', 'active')->find($request->integer('student_id')) : null;

        $state = DB::table('rfid_reader_state')->where('id', 1)->first();
        $active = $state && $rfid->active($state);
        $mine = $active && (int) $state->owner_id === $request->user()->id;

        return response()->json([
            'server_time' => now()->toISOString(),
            'configured' => (string) config('rfid.token') !== '',
            'requested_student' => $requestedStudent?->only(['id', 'full_name', 'rfid_uid']),
            'active' => (bool) $active, 'mine' => (bool) $mine,
            'session' => $mine ? $state->session_id : null,
            'uid' => $mine ? $state->uid : null,
            'student' => $mine ? Student::find($state->student_id)?->only(['id', 'full_name', 'rfid_uid']) : null,
            'expires_at' => $mine ? Carbon::parse($state->expires_at)->toISOString() : null,
            'remaining_seconds' => $mine ? max(0, (int) ceil(now()->diffInSeconds(Carbon::parse($state->expires_at), false))) : 0,
            'last_message' => $state?->last_message, 'last_seen_at' => $state?->last_seen_at,
        ])->header('Cache-Control', 'no-store');
    }

    public function start(Request $request, RfidService $rfid)
    {
        $data = $request->validate(['student_id' => 'required|integer|exists:students,id']);

        abort_if((string) config('rfid.token') === '', 503, 'Token perangkat RFID belum dikonfigurasi.');
        $rfid->start($data['student_id'], $request->user()->id);

        return $this->state($request, $rfid);
    }

    public function finish(Request $request, RfidService $rfid)
    {
        $data = $request->validate(['session' => 'required|uuid', 'confirm' => 'required|boolean',
            'uid' => 'nullable|string|max:29', 'approved' => 'exclude_unless:confirm,true|required|accepted']);
        $student = $rfid->finish($data['session'], $request->user()->id, $data['confirm'], $data['uid'] ?? null);

        return response()->json([
            'message' => $data['confirm'] ? 'Kartu berhasil disimpan.' : 'Registrasi dibatalkan.',
            'student' => $student ? ['id' => $student->id, 'full_name' => $student->full_name, 'rfid_uid' => $student->rfid_uid] : null,
        ]);
    }

    public function unlink(Request $request, Student $student, RfidService $rfid)
    {
        $data = $request->validate(['uid' => 'required|string|max:29', 'approved' => 'required|accepted']);
        $rfid->locked(function () use ($student, $request, $data) {
            $student->refresh();
            abort_unless($student->rfid_uid === $data['uid'], 409, 'Kartu sudah berubah. Muat ulang halaman.');
            $student->update(['rfid_uid' => null]);
            AuditService::log('rfid.card_unlink', 'Student', $student->id, $student->full_name, $request->user()->id);
        });

        return response()->json(['message' => 'Kartu berhasil dilepaskan.']);
    }
}
