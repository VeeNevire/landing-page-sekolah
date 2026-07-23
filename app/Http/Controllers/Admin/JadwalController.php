<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\TeachingAssignment;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
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
            ->with(['subject', 'customSubject', 'teacher', 'jadwals'])
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
            $subjectName = $ta->subject?->name ?? $ta->customSubject?->nama ?? '-';
            $subjectCode = $ta->subject?->code ?? $ta->customSubject?->kode ?? '-';
            foreach ($ta->jadwals as $jadwal) {
                $grid[$jadwal->day][$jadwal->time_slot] = [
                    'subject' => $subjectName,
                    'code' => $subjectCode,
                    'teacher' => $ta->teacher->full_name ?? $ta->teacher->name,
                    'jadwal_id' => $jadwal->id,
                    'time_slot' => $jadwal->time_slot,
                ];
            }
        }

        $periods = AcademicPeriod::orderByDesc('academic_year')->get();
        $subjects = $assignments->map(fn($ta) => [
            'teaching_assignment_id' => $ta->id,
            'label' => ($ta->subject?->code ?? $ta->customSubject?->kode ?? '-') . ' — ' . ($ta->subject?->name ?? $ta->customSubject?->nama ?? '-') . ' (' . ($ta->teacher->full_name ?? $ta->teacher->name) . ')',
        ])->values();

        $kelasList = Kelas::with(['jurusan', 'homeroomTeacher'])
            ->withCount('students')
            ->orderBy('tingkat')->orderBy('nama')
            ->get();

        $guruList = User::whereIn('role', ['teacher', 'homeroom'])
            ->with(['teachingAssignments' => function ($q) use ($semesterId) {
                $q->where('period_id', $semesterId)
                  ->with(['jadwals', 'subject', 'customSubject']);
            }])
            ->orderBy('name')
            ->get();

        $roleLabels = ['teacher' => 'Guru', 'homeroom' => 'Wali Kelas', 'principal' => 'Kepsek'];
        $guruData = [];
        foreach ($guruList as $guru) {
            $schedule = [];
            foreach (self::DAYS as $day) {
                $schedule[$day] = [];
                foreach (self::TIME_SLOTS as $slot => $time) {
                    $schedule[$day][$slot] = null;
                }
            }
            $mapel = collect();
            $jadwalList = [];
            foreach ($guru->teachingAssignments as $ta) {
                $subjectName = $ta->subject?->name ?? $ta->customSubject?->nama ?? '-';
                $subjectCode = $ta->subject?->code ?? $ta->customSubject?->kode ?? '-';
                $mapel->push($subjectName);
                foreach ($ta->jadwals as $j) {
                    $schedule[$j->day][$j->time_slot] = [
                        'code' => $subjectCode,
                        'subject' => $subjectName,
                        'class' => $ta->class_name,
                    ];
                    $jadwalList[] = [
                        'day' => ucfirst($j->day),
                        'timeLabel' => self::TIME_SLOTS[$j->time_slot],
                        'subject' => $subjectName,
                        'class' => $ta->class_name,
                    ];
                }
            }
            usort($jadwalList, function ($a, $b) {
                $d = array_search(strtolower($a['day']), self::DAYS) - array_search(strtolower($b['day']), self::DAYS);
                return $d !== 0 ? $d : strcmp($a['timeLabel'], $b['timeLabel']);
            });
            $guruData[$guru->id] = [
                'name' => $guru->full_name ?? $guru->name,
                'mapel' => $mapel->unique()->values()->toArray(),
                'jadwalList' => $jadwalList,
                'schedule' => $schedule,
            ];
        }

        $allAssignments = TeachingAssignment::where('period_id', $semesterId)
            ->with(['subject', 'customSubject', 'teacher'])
            ->get();

        $kelasData = [];
        foreach ($allAssignments->groupBy('class_name') as $className => $assignments) {
            $kelasData[$className] = [
                'guru' => $assignments->map(fn($ta) => [
                    'nama' => $ta->teacher->full_name ?? $ta->teacher->name,
                    'mapel' => $ta->subject?->name ?? $ta->customSubject?->nama ?? '-',
                ])->values(),
            ];
        }

        return view('admin.jadwal', compact('grid', 'periods', 'subjects', 'semesterId', 'classNames', 'selectedClass', 'kelasList', 'guruList', 'guruData', 'kelasData'));
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
        $jadwal->load('teachingAssignment.subject', 'teachingAssignment.customSubject');
        $jadwalLabel = ($jadwal->teachingAssignment->subject?->name ?? $jadwal->teachingAssignment->customSubject?->nama ?? 'Mapel') . ' - ' . $jadwal->teachingAssignment->class_name;
        AuditService::log('jadwal.create', 'Jadwal', $jadwal->id, $jadwalLabel);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil ditambahkan.']);
        }

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'time_slot' => 'required|integer|min:1|max:5',
        ]);

        $exists = Jadwal::where('teaching_assignment_id', $jadwal->teaching_assignment_id)
            ->where('day', $validated['day'])
            ->where('id', '!=', $jadwal->id)
            ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Mapel ini sudah ada di hari tersebut.']);
            }
            return back()->with('error', 'Mapel ini sudah ada di hari tersebut.');
        }

        $jadwal->update($validated);
        $jadwal->load('teachingAssignment.subject', 'teachingAssignment.customSubject');
        $jadwalLabel = ($jadwal->teachingAssignment->subject?->name ?? $jadwal->teachingAssignment->customSubject?->nama ?? 'Mapel') . ' - ' . $jadwal->teachingAssignment->class_name;
        AuditService::log('jadwal.update', 'Jadwal', $jadwal->id, $jadwalLabel);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil diperbarui.']);
        }

        return back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Request $request, Jadwal $jadwal)
    {
        $jadwal->load('teachingAssignment.subject', 'teachingAssignment.customSubject');
        $jadwalLabel = ($jadwal->teachingAssignment->subject?->name ?? $jadwal->teachingAssignment->customSubject?->nama ?? 'Mapel') . ' - ' . $jadwal->teachingAssignment->class_name;
        AuditService::log('jadwal.delete', 'Jadwal', $jadwal->id, $jadwalLabel);
        $jadwal->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus.']);
        }

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
