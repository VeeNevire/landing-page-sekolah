<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\TeachingAssignment;
use App\Models\Jadwal;
use App\Services\AuditService;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    const TIME_SLOTS = [
        1 => '07:00 – 08:30',
        2 => '08:30 – 10:00',
        3 => '10:15 – 11:45',
        4 => '12:30 – 14:00',
        5 => '14:00 – 15:30',
    ];

    const DAYS = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

    public function index(Request $request)
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        $semesterId = $request->query('semester_id', $activePeriod?->id);

        $classNames = TeachingAssignment::where('period_id', $semesterId)
            ->distinct()->orderBy('class_name')
            ->pluck('class_name');

        $selectedClass = $request->query('class', $classNames->first());

        $assignments = TeachingAssignment::where('period_id', $semesterId)
            ->when($selectedClass, fn($q) => $q->where('class_name', $selectedClass))
            ->with(['subject', 'teacher', 'jadwals'])
            ->get();

        $jadwals = Jadwal::whereIn('teaching_assignment_id', $assignments->pluck('id'))
            ->get()
            ->keyBy(fn($j) => $j->teaching_assignment_id . '_' . $j->day);

        $grid = [];
        foreach (self::DAYS as $day) {
            $grid[$day] = [];
            foreach (self::TIME_SLOTS as $slot => $time) {
                $grid[$day][$slot] = null;
            }
        }

        foreach ($assignments as $ta) {
            foreach ($ta->jadwals as $jadwal) {
                $grid[$jadwal->day][$jadwal->time_slot] = [
                    'subject' => $ta->subject->name,
                    'code' => $ta->subject->code,
                    'teacher' => $ta->teacher->full_name ?? $ta->teacher->name,
                    'jadwal_id' => $jadwal->id,
                ];
            }
        }

        $periods = AcademicPeriod::orderByDesc('academic_year')->get();
        $subjects = $assignments->map(fn($ta) => [
            'teaching_assignment_id' => $ta->id,
            'label' => $ta->subject->code . ' — ' . $ta->subject->name . ' (' . ($ta->teacher->full_name ?? $ta->teacher->name) . ')',
        ])->values();

        return view('admin.jadwal', compact('grid', 'periods', 'subjects', 'semesterId', 'classNames', 'selectedClass'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'time_slot' => 'required|integer|min:1|max:5',
        ]);

        $exists = Jadwal::where([
            'teaching_assignment_id' => $validated['teaching_assignment_id'],
            'day' => $validated['day'],
        ])->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Mapel ini sudah ada di hari tersebut.']);
            }
            return back()->with('error', 'Mapel ini sudah ada di hari tersebut.');
        }

        $jadwal = Jadwal::create($validated);

        AuditService::log('jadwal.create', 'Jadwal', $jadwal->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil ditambahkan.']);
        }

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function destroy(Request $request, Jadwal $jadwal)
    {
        AuditService::log('jadwal.delete', 'Jadwal', $jadwal->id);
        $jadwal->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus.']);
        }

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
