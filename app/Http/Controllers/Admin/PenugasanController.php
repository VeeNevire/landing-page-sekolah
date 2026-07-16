<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\GuruMapel;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        $semesterId = $request->query('semester_id', $activePeriod?->id);

        $assignments = GuruMapel::where('semester_id', $semesterId)
            ->with(['subject', 'teacher', 'semester'])
            ->orderBy('class_name')
            ->orderBy('mapel_id')
            ->get();

        $periods = AcademicPeriod::orderByDesc('academic_year')->get();
        $subjects = Subject::orderBy('code')->get();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        $classes = TeachingAssignment::where('period_id', $semesterId)
            ->select('class_name')
            ->distinct()
            ->orderBy('class_name')
            ->pluck('class_name');

        return view('admin.penugasan', compact('assignments', 'periods', 'subjects', 'teachers', 'semesterId', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:academic_periods,id',
            'mapel_id' => 'required|exists:subjects,id',
            'guru_id' => 'required|exists:users,id',
            'class_name' => 'nullable|string|max:80',
        ]);

        $exists = GuruMapel::where([
            'semester_id' => $validated['semester_id'],
            'mapel_id' => $validated['mapel_id'],
            'guru_id' => $validated['guru_id'],
            'class_name' => $validated['class_name'] ?? null,
        ])->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Penugasan ini sudah ada.']);
            }
            return back()->with('error', 'Penugasan ini sudah ada.');
        }

        $guruMapel = GuruMapel::create($validated);

        AuditService::log('guru-mapel.create', 'GuruMapel', $guruMapel->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penugasan guru mapel berhasil ditambahkan.']);
        }

        return back()->with('success', 'Penugasan guru mapel berhasil ditambahkan.');
    }

    public function destroy(Request $request, GuruMapel $guruMapel)
    {
        AuditService::log('guru-mapel.delete', 'GuruMapel', $guruMapel->id);
        $guruMapel->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penugasan berhasil dihapus.']);
        }

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }

    public function copyFromPrevious(Request $request)
    {
        $currentPeriod = AcademicPeriod::where('is_active', true)->first();
        if (!$currentPeriod) {
            return back()->with('error', 'Tidak ada semester aktif.');
        }

        $previousPeriod = AcademicPeriod::where('id', '<', $currentPeriod->id)
            ->orderByDesc('id')
            ->first();

        if (!$previousPeriod) {
            return back()->with('error', 'Tidak ada semester sebelumnya untuk disalin.');
        }

        $prevAssignments = GuruMapel::where('semester_id', $previousPeriod->id)->get();

        $created = 0;
        $skipped = 0;

        foreach ($prevAssignments as $prev) {
            $exists = GuruMapel::where([
                'semester_id' => $currentPeriod->id,
                'mapel_id' => $prev->mapel_id,
                'guru_id' => $prev->guru_id,
            ])->exists();

            if (!$exists) {
                GuruMapel::create([
                    'semester_id' => $currentPeriod->id,
                    'mapel_id' => $prev->mapel_id,
                    'guru_id' => $prev->guru_id,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        AuditService::log('guru-mapel.copy', 'GuruMapel');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Disalin: {$created} penugasan. Dilompati: {$skipped} (sudah ada)."]);
        }

        return back()->with('success', "Disalin: {$created} penugasan dari {$previousPeriod->academic_year} {$previousPeriod->semester}. Dilompati: {$skipped} (sudah ada).");
    }
}
