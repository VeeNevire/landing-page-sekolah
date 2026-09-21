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
        return view('admin.rfid-attendance', compact('tab', 'rows', 'classes', 'summary', 'lastScan'));
    }

    public function data(Request $request)
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
        return response()->json([
            'summary' => $summary,
            'html' => $html,
            'lastScan' => $lastScan,
            'server_time' => now()->format('H:i:s'),
        ])->header('Cache-Control', 'no-store');
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
        $state = DB::table('rfid_reader_state')->where('id', 1)->first();
        $active = $state && $rfid->active($state);
        $mine = $active && (int) $state->owner_id === $request->user()->id;
        return response()->json([
            'configured' => (string) config('rfid.token') !== '',
            'active' => (bool) $active, 'mine' => (bool) $mine,
            'session' => $mine ? $state->session_id : null,
            'uid' => $mine ? $state->uid : null,
            'student' => $mine ? Student::find($state->student_id)?->only(['full_name', 'rfid_uid']) : null,
            'expires_at' => $mine ? $state->expires_at : null,
            'last_message' => $state?->last_message, 'last_seen_at' => $state?->last_seen_at,
        ])->header('Cache-Control', 'no-store');
    }

    public function start(Request $request, RfidService $rfid)
    {
        $data = $request->validate(['student_id' => 'required|integer|exists:students,id']);
        return response()->json(['session' => $rfid->start($data['student_id'], $request->user()->id)]);
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
