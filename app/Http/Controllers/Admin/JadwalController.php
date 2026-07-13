<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\GuruMapel;
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

        $guruMapels = GuruMapel::where('semester_id', $semesterId)
            ->with(['subject', 'teacher', 'jadwals'])
            ->get();

        $jadwals = Jadwal::whereIn('guru_mapel_id', $guruMapels->pluck('id'))
            ->get()
            ->keyBy(fn($j) => $j->guru_mapel_id . '_' . $j->day);

        $grid = [];
        foreach (self::DAYS as $day) {
            $grid[$day] = [];
            foreach (self::TIME_SLOTS as $slot => $time) {
                $grid[$day][$slot] = null;
            }
        }

        foreach ($guruMapels as $gm) {
            foreach ($gm->jadwals as $jadwal) {
                $grid[$jadwal->day][$jadwal->time_slot] = [
                    'subject' => $gm->subject->name,
                    'code' => $gm->subject->code,
                    'teacher' => $gm->teacher->full_name ?? $gm->teacher->name,
                    'jadwal_id' => $jadwal->id,
                ];
            }
        }

        $periods = AcademicPeriod::orderByDesc('academic_year')->get();
        $subjects = $guruMapels->map(fn($gm) => [
            'guru_mapel_id' => $gm->id,
            'label' => $gm->subject->code . ' — ' . $gm->subject->name . ' (' . ($gm->teacher->full_name ?? $gm->teacher->name) . ')',
        ])->values();

        return view('admin.jadwal', compact('grid', 'periods', 'subjects', 'semesterId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id',
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'time_slot' => 'required|integer|min:1|max:5',
        ]);

        $exists = Jadwal::where([
            'guru_mapel_id' => $validated['guru_mapel_id'],
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
