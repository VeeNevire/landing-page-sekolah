<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use App\Models\JurusanCustomSubject;
use App\Models\Kelas;
use App\Models\Material;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TeacherNote;
use App\Models\CourseModule;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    const TIME_SLOTS = [
        1 => '07:00 – 08:30',
        2 => '08:30 – 10:00',
        3 => '10:15 – 11:45',
        4 => '12:30 – 14:00',
        5 => '14:00 – 15:30',
    ];

    const DAY_NAMES = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    const DAY_MAP = [
        'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu',
        'kamis' => 'Kamis', 'jumat' => 'Jumat',
    ];

    private function getAssignments($user)
    {
        return $user->teachingAssignments()
            ->with('subject', 'customSubject', 'period', 'jadwals')
            ->whereHas('period', fn($q) => $q->where('is_active', true))
            ->get();
    }

    private function getActivePeriod()
    {
        return AcademicPeriod::where('is_active', true)->first();
    }

    private function subjectName($ta): string
    {
        if ($ta->subject) return $ta->subject->name;
        if ($ta->customSubject) return $ta->customSubject->nama;
        return '-';
    }

    private function subjectCode($ta): string
    {
        if ($ta->subject) return $ta->subject->code;
        if ($ta->customSubject) return $ta->customSubject->kode;
        return '-';
    }

    private function subjectRouteId($ta): string
    {
        if ($ta->subject_id) return (string) $ta->subject_id;
        if ($ta->custom_subject_id) return 'cs_' . $ta->custom_subject_id;
        return '';
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $studentsPerClass = [];
        foreach ($classNames as $class) {
            $studentsPerClass[$class] = Student::where('class_name', $class)
                ->where('status', 'active')->get();
        }

        $homeroomStudents = $user->homeroomStudents()->where('status', 'active')->get();
        $isHomeroom = $homeroomStudents->isNotEmpty();
        $totalStudents = collect($studentsPerClass)->flatten()->unique('id')->count();
        $activePeriod = $this->getActivePeriod();

        $schedule = $this->buildScheduleFromAssignments($teachingAssignments);
        $today = date('N') <= 5 ? self::DAY_NAMES[date('N') - 1] : '';
        $todaySchedule = collect($schedule)->where('day', $today)->values();

        return view('guru.dashboard', [
            'teachingAssignments' => $teachingAssignments,
            'classNames' => $classNames,
            'studentsPerClass' => $studentsPerClass,
            'homeroomStudents' => $homeroomStudents,
            'isHomeroom' => $isHomeroom,
            'totalStudents' => $totalStudents,
            'totalClasses' => $classNames->count(),
            'totalSubjects' => $teachingAssignments->filter(fn($a) => $a->subject_id || $a->custom_subject_id)->unique(fn($a) => $a->subject_id ?? 'cs_' . $a->custom_subject_id)->count(),
            'schedule' => $schedule,
            'todaySchedule' => $todaySchedule,
            'today' => $today,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function kelas(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();
        $activePeriod = $this->getActivePeriod();

        $allStudents = Student::whereIn('class_name', $classNames->toArray())
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get()
            ->groupBy('class_name');

        $allAttendance = Attendance::whereIn('student_id', $allStudents->flatten()->pluck('id'))
            ->selectRaw('student_id, status, count(*) as total')
            ->groupBy('student_id', 'status')
            ->get()
            ->groupBy('student_id');

        $allTaIds = $teachingAssignments->pluck('id');

        $assessmentsByTa = Assessment::whereIn('teaching_assignment_id', $allTaIds)
            ->get(['id', 'teaching_assignment_id'])
            ->groupBy('teaching_assignment_id')
            ->map(fn($group) => $group->pluck('id'));

        $allAssessmentIds = $assessmentsByTa->flatten();
        $allScoresByAssessment = $allAssessmentIds->isNotEmpty()
            ? AssessmentScore::whereIn('assessment_id', $allAssessmentIds)
                ->get(['assessment_id', 'score'])
                ->groupBy('assessment_id')
                ->map(fn($group) => $group->pluck('score')->filter())
            : collect();

        $classList = [];
        foreach ($classNames as $class) {
            $students = $allStudents->get($class, collect());

            $subjects = $teachingAssignments->where('class_name', $class)
                ->map(fn($a) => $a->subject_id || $a->custom_subject_id ? [
                    'id' => $this->subjectRouteId($a),
                    'name' => $this->subjectName($a),
                    'code' => $this->subjectCode($a),
                ] : null)
                ->filter()
                ->unique('id')->values()->all();

            $subjectAverages = [];
            foreach ($subjects as $subject) {
                $subjectId = is_numeric($subject['id']) ? $subject['id'] : null;
                $csId = !is_numeric($subject['id']) ? str_replace('cs_', '', $subject['id']) : null;
                $taIds = $teachingAssignments->where('class_name', $class)
                    ->when($subjectId && is_numeric($subjectId), fn($q) => $q->where('subject_id', (int) $subjectId))
                    ->when($csId && is_numeric($csId), fn($q) => $q->where('custom_subject_id', (int) $csId))
                    ->pluck('id')
                    ->toArray();

                $assessmentIds = collect();
                foreach ($taIds as $tid) {
                    if ($assessmentsByTa->has($tid)) {
                        $assessmentIds = $assessmentIds->merge($assessmentsByTa->get($tid));
                    }
                }
                $scores = $assessmentIds->flatMap(fn($aid) => $allScoresByAssessment->get($aid, collect()))->toArray();
                $subjectAverages[$subject['id']] = $scores ? round(array_sum($scores) / count($scores), 1) : null;
            }

            $studentIds = $students->pluck('id');
            $attendanceCounts = [];
            foreach ($studentIds as $sid) {
                foreach ($allAttendance->get($sid, collect()) as $record) {
                    $attendanceCounts[$record->status] = ($attendanceCounts[$record->status] ?? 0) + $record->total;
                }
            }
            $totalDays = array_sum($attendanceCounts);
            $attendanceRate = $totalDays > 0 ? round(($attendanceCounts['present'] ?? 0) / $totalDays * 100, 1) : 0;

            $classList[] = [
                'name' => $class,
                'student_count' => $students->count(),
                'students' => $students,
                'subjects' => $subjects,
                'subject_averages' => $subjectAverages,
                'attendance_rate' => $attendanceRate,
                'attendance' => $attendanceCounts,
                'total_attendance_days' => $totalDays,
            ];
        }

        return view('guru.kelas', [
            'classList' => $classList,
        ]);
    }

    public function kelasData(Request $request, $className)
    {
        $user = $request->user();
        $activePeriod = $this->getActivePeriod();

        $students = Student::where('class_name', $className)->where('status', 'active')
            ->orderBy('full_name')->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'full_name' => $s->full_name,
                'nisn' => $s->nisn,
                'birth_date' => $s->birth_date?->format('d M Y'),
            ]);

        $assignments = TeachingAssignment::where('class_name', $className)
            ->when($activePeriod, fn($q) => $q->where('period_id', $activePeriod->id))
            ->with('subject', 'customSubject')
            ->get();

        $subjectGrades = [];
        foreach ($assignments as $a) {
            if (!$a->subject_id && !$a->custom_subject_id) continue;
            $scores = AssessmentScore::whereHas('assessment', fn($q) => $q->where('teaching_assignment_id', $a->id))
                ->pluck('score')->filter()->toArray();
            $avg = $scores ? round(array_sum($scores) / count($scores), 1) : null;
            $subjectGrades[] = [
                'subject' => $this->subjectName($a),
                'code' => $this->subjectCode($a),
                'average' => $avg,
                'grade' => $avg !== null ? $this->gradeLetter($avg) : '-',
            ];
        }

        $studentIds = $students->pluck('id');
        $attendance = Attendance::whereIn('student_id', $studentIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $overallAvg = collect($subjectGrades)->whereNotNull('average')->avg('average');

        return response()->json([
            'class_name' => $className,
            'students' => $students,
            'subject_grades' => $subjectGrades,
            'attendance' => $attendance,
            'total_attendance_days' => array_sum($attendance),
            'attendance_rate' => array_sum($attendance) > 0 ? round(($attendance['present'] ?? 0) / array_sum($attendance) * 100, 1) : 0,
            'overall_average' => $overallAvg ? round($overallAvg, 1) : null,
        ]);
    }

    private function gradeLetter(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 85 => 'A-',
            $score >= 80 => 'B+',
            $score >= 75 => 'B',
            $score >= 70 => 'C+',
            $score >= 65 => 'C',
            default => 'D',
        };
    }

    public function nilai(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $classSubjectPairs = $teachingAssignments->map(fn($a) => [
            'class_name' => $a->class_name,
            'subject_id' => $this->subjectRouteId($a),
            'subject_name' => $this->subjectName($a),
        ])->filter(fn($p) => $p['subject_id'] !== '')
            ->unique(fn($p) => $p['class_name'] . '-' . $p['subject_id'])->values();

        return view('guru.nilai', [
            'classNames' => $classNames,
            'pairs' => $classSubjectPairs,
        ]);
    }

    public function nilaiDetail(Request $request, $class, $subject)
    {
        $user = $request->user();
        $period = $this->getActivePeriod();
        $students = Student::where('class_name', $class)->where('status', 'active')->get();

        $isCustom = str_starts_with($subject, 'cs_');
        $subjectModel = null;
        $assignment = null;

        if ($isCustom) {
            $csId = (int) str_replace('cs_', '', $subject);
            $subjectModel = JurusanCustomSubject::findOrFail($csId);
            $assignment = $user->teachingAssignments()
                ->where('class_name', $class)
                ->where('custom_subject_id', $csId)
                ->whereHas('period', fn($q) => $q->where('is_active', true))
                ->first();
        } else {
            $subjectModel = Subject::findOrFail($subject);
            $assignment = $user->teachingAssignments()
                ->where('class_name', $class)
                ->where('subject_id', $subject)
                ->whereHas('period', fn($q) => $q->where('is_active', true))
                ->first();
        }

        $assessments = [];
        if ($assignment) {
            $assessments = Assessment::where('teaching_assignment_id', $assignment->id)->get();
        }

        $scores = [];
        foreach ($assessments as $assess) {
            $assessScores = AssessmentScore::where('assessment_id', $assess->id)
                ->pluck('score', 'student_id')->toArray();
            $scores[$assess->id] = $assessScores;
        }

        $savedDraft = session("nilai_draft.{$class}.{$subject}", []);

        return view('guru.nilai-detail', [
            'class' => $class,
            'subject' => $subjectModel,
            'students' => $students,
            'assessments' => $assessments,
            'scores' => $scores,
            'savedDraft' => $savedDraft,
            'isCustom' => $isCustom,
        ]);
    }

    public function nilaiStore(Request $request, $class, $subject)
    {
        $user = $request->user();
        $period = $this->getActivePeriod();

        $isCustom = str_starts_with($subject, 'cs_');
        $assignment = null;

        if ($isCustom) {
            $csId = (int) str_replace('cs_', '', $subject);
            $assignment = $user->teachingAssignments()
                ->where('class_name', $class)
                ->where('custom_subject_id', $csId)
                ->whereHas('period', fn($q) => $q->where('is_active', true))
                ->first();
        } else {
            $assignment = $user->teachingAssignments()
                ->where('class_name', $class)
                ->where('subject_id', $subject)
                ->whereHas('period', fn($q) => $q->where('is_active', true))
                ->first();
        }

        if (!$assignment) {
            return back()->with('error', 'Tidak ada penugasan ditemukan.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:160',
            'component' => 'required|in:quiz,homework,project,uts,uas',
            'scores' => 'required|array',
            'assessment_date' => 'nullable|date',
        ]);

        $assessment = Assessment::create([
            'teaching_assignment_id' => $assignment->id,
            'title' => $validated['title'],
            'component' => $validated['component'],
            'assessment_date' => $validated['assessment_date'] ?? now()->toDateString(),
            'max_score' => 100,
        ]);

        foreach ($validated['scores'] as $studentId => $score) {
            if ($score !== null && $score !== '') {
                AssessmentScore::create([
                    'assessment_id' => $assessment->id,
                    'student_id' => $studentId,
                    'score' => $score,
                    'graded_at' => now(),
                ]);
            }
        }

        AuditService::log('assessment.create', 'Assessment', $assessment->id, $assessment->title);

        return back()->with('success', "Nilai \"{$validated['title']}\" berhasil disimpan.");
    }

    public function nilaiUpdate(Request $request, $class, $subject, $assessmentId)
{
    $assessment = Assessment::findOrFail($assessmentId);

    $validated = $request->validate([
        'title' => 'required|string|max:160',
        'component' => 'required|in:quiz,homework,project,uts,uas',
        'scores' => 'nullable|array',
        'assessment_date' => 'nullable|date',
    ]);

    $assessment->update([
        'title' => $validated['title'],
        'component' => $validated['component'],
        'assessment_date' => $validated['assessment_date'] ?? $assessment->assessment_date,
    ]);

    foreach ($validated['scores'] ?? [] as $studentId => $score) {
        if ($score !== null && $score !== '') {
            DB::table('assessment_scores')->updateOrInsert(
                ['assessment_id' => $assessment->id, 'student_id' => $studentId],
                ['score' => $score, 'graded_at' => now()]
            );
        } else {
            DB::table('assessment_scores')->updateOrInsert(
                ['assessment_id' => $assessment->id, 'student_id' => $studentId],
                ['score' => null, 'graded_at' => now()]
            );
        }
    }

    AuditService::log('assessment.update', 'Assessment', $assessment->id, $assessment->title);

    return response()->json(['success' => true, 'message' => 'Nilai berhasil diperbarui.']);
}

    public function nilaiDestroy(Request $request, $class, $subject, $assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);

        AuditService::log('assessment.delete', 'Assessment', $assessment->id, $assessment->title);

        $assessment->scores()->delete();
        $assessment->delete();

        return response()->json(['success' => true, 'message' => 'Penilaian berhasil dihapus.']);
    }

    public function absensi(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $selectedClass = $request->query('class', $classNames->first());
        $date = $request->query('date', now()->format('Y-m-d'));

        $classAssignments = $teachingAssignments->where('class_name', $selectedClass)->values();

        $subjectList = [];
        foreach ($classAssignments as $ta) {
            $subjectList[] = [
                'id' => $this->subjectRouteId($ta),
                'name' => $this->subjectName($ta),
            ];
        }

        $selectedSubjectId = $request->query('subject', $subjectList[0]['id'] ?? '');
        $selectedSubjectName = '';
        foreach ($subjectList as $s) {
            if ($s['id'] === $selectedSubjectId) {
                $selectedSubjectName = $s['name'];
                break;
            }
        }

        $students = Student::where('class_name', $selectedClass)
            ->where('status', 'active')->get();

        $existing = Attendance::where('attendance_date', $date)
            ->whereIn('student_id', $students->pluck('id'))
            ->pluck('status', 'student_id')
            ->toArray();

        return view('guru.absensi', [
            'classNames' => $classNames,
            'selectedClass' => $selectedClass,
            'date' => $date,
            'subjectList' => $subjectList,
            'selectedSubjectId' => $selectedSubjectId,
            'selectedSubjectName' => $selectedSubjectName,
            'students' => $students,
            'existing' => $existing,
        ]);
    }

    public function absensiStore(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|array',
        ]);

        $user = $request->user();
        $students = Student::where('class_name', $validated['class_name'])
            ->where('status', 'active')->pluck('id');

        foreach ($validated['status'] as $studentId => $status) {
            if (in_array($status, ['present', 'sick', 'excused', 'unexcused', 'late'])) {
                Attendance::updateOrCreate(
                    ['student_id' => $studentId, 'attendance_date' => $validated['date']],
                    ['status' => $status, 'recorded_by' => $user->id]
                );
            }
        }

        AuditService::log('attendance.record', 'Attendance', null, null, $user->id);
        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function jadwal(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $schedule = $this->buildScheduleFromAssignments($teachingAssignments);

        return view('guru.jadwal', [
            'schedule' => $schedule,
            'timeSlots' => self::TIME_SLOTS,
            'dayNames' => self::DAY_NAMES,
        ]);
    }

    public function catatan(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();
        $period = $this->getActivePeriod();

        $selectedStudent = $request->query('student');

        $students = Student::whereIn('class_name', $classNames->toArray())
            ->where('status', 'active')->get();

        $existingNotes = collect();
        if ($selectedStudent) {
            $existingNotes = TeacherNote::where('student_id', $selectedStudent)
                ->where('period_id', $period?->id)
                ->with('author')
                ->latest()
                ->get();
        }

        return view('guru.catatan', [
            'classNames' => $classNames,
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'existingNotes' => $existingNotes,
        ]);
    }

    public function catatanStore(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'category' => 'required|in:academic,behavior,career,general',
            'note' => 'required|string|max:1000',
            'follow_up' => 'nullable|string|max:500',
        ]);

        $period = $this->getActivePeriod();

        $note = TeacherNote::create([
            'student_id' => $validated['student_id'],
            'period_id' => $period?->id,
            'author_id' => $request->user()->id,
            'category' => $validated['category'],
            'note' => $validated['note'],
            'follow_up' => $validated['follow_up'] ?? null,
        ]);

        AuditService::log('teacher-note.create', 'TeacherNote', $note->id, null);
        return redirect()->route('guru.catatan', ['student' => $validated['student_id']])->with('success', 'Catatan berhasil disimpan.');
    }

    public function publikasi(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $classList = [];
        foreach ($classNames as $class) {
            $students = Student::where('class_name', $class)->where('status', 'active')->get();
            $subjectNames = $teachingAssignments->where('class_name', $class)
                ->map(fn($a) => $this->subjectName($a))
                ->filter()->unique()->values()->implode(', ');
            $totalAssessments = Assessment::whereHas('teachingAssignment', fn($q) => $q->where('class_name', $class))->count();
            $publishedCount = Assessment::whereHas('teachingAssignment', fn($q) => $q->where('class_name', $class))
                ->whereNotNull('published_at')->count();

            $classList[] = [
                'name' => $class,
                'student_count' => $students->count(),
                'subjects' => $subjectNames,
                'total_assessments' => $totalAssessments,
                'published_count' => $publishedCount,
                'all_published' => $totalAssessments > 0 && $publishedCount === $totalAssessments,
            ];
        }

        return view('guru.publikasi', [
            'classList' => $classList,
        ]);
    }

    public function publikasiStore(Request $request, $class)
    {
        Assessment::whereHas('teachingAssignment', fn($q) => $q->where('class_name', $class))
            ->whereNull('published_at')
            ->update(['published_at' => now()]);

        AuditService::log('grade.publish', 'Assessment', null, null);
        return back()->with('success', "Nilai untuk kelas {$class} berhasil dipublikasikan.");
    }

    public function materi(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);

        $selectedTaId = $request->query('ta_id');

        $materials = Material::with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'module')
            ->whereIn('teaching_assignment_id', $teachingAssignments->pluck('id'))
            ->when($selectedTaId, fn($q) => $q->where('teaching_assignment_id', $selectedTaId))
            ->orderBy('order')
            ->latest('id')
            ->get();

        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();
        $pairs = $teachingAssignments->map(fn($a) => [
            'assignment_id' => $a->id,
            'class_name' => $a->class_name,
            'subject_name' => $this->subjectName($a),
        ])->unique('assignment_id')->values();

        $selectedTa = $selectedTaId ? $teachingAssignments->firstWhere('id', $selectedTaId) : $teachingAssignments->first();
        $modules = collect();
        if ($selectedTa) {
            $modules = $selectedTa->modules()->with('materials')->get();
        }

        return view('guru.materi', [
            'materials' => $materials,
            'classNames' => $classNames,
            'pairs' => $pairs,
            'modules' => $modules,
            'selectedTa' => $selectedTa,
        ]);
    }

    public function materiStore(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'module_id' => 'nullable|exists:course_modules,id',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:500',
            'url' => 'nullable|url|max:500',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,gif,webp,svg,mp4,webm,mp3,zip,rar|max:51200',
            'type' => 'nullable|in:file,link,embed',
        ]);

        $user = $request->user();

        if (!$user->teachingAssignments()->where('id', $validated['teaching_assignment_id'])->exists()) {
            abort(403);
        }

        $data = [
            'teaching_assignment_id' => $validated['teaching_assignment_id'],
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'url' => $validated['url'] ?? null,
            'type' => $validated['type'] ?? ($request->hasFile('file') ? 'file' : 'link'),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $taId = $validated['teaching_assignment_id'];
            $path = $file->store("lms/materials/{$taId}", 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getMimeType();
        }

        $maxOrder = Material::where('teaching_assignment_id', $validated['teaching_assignment_id'])
            ->max('order') ?? -1;
        $data['order'] = $maxOrder + 1;

        $material = Material::create($data);

        AuditService::log('material.create', 'Material', $material->id, $material->title);

        $redirect = redirect()->route('guru.materi', ['ta_id' => $validated['teaching_assignment_id']]);
        return $redirect->with('success', 'Materi berhasil ditambahkan.');
    }

    public function materiDestroy(Request $request, Material $material)
    {
        $user = $request->user();

        if (!$user->teachingAssignments()->where('id', $material->teaching_assignment_id)->exists()) {
            abort(403);
        }

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        AuditService::log('material.delete', 'Material', $material->id, $material->title);
        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    private function buildScheduleFromAssignments($assignments): array
    {
        $schedule = [];
        foreach ($assignments as $a) {
            foreach ($a->jadwals as $jadwal) {
                $dayName = self::DAY_MAP[$jadwal->day] ?? ucfirst($jadwal->day);
                $schedule[] = [
                    'day' => $dayName,
                    'time' => self::TIME_SLOTS[$jadwal->time_slot] ?? '-',
                    'subject' => $this->subjectName($a),
                    'class_name' => $a->class_name,
                    'subject_id' => $a->subject_id,
                ];
            }
        }

        $dayOrder = array_flip(self::DAY_NAMES);
        usort($schedule, function ($a, $b) use ($dayOrder) {
            $d = ($dayOrder[$a['day']] ?? 99) - ($dayOrder[$b['day']] ?? 99);
            return $d !== 0 ? $d : strcmp($a['time'], $b['time']);
        });

        return $schedule;
    }

    public function waliJadwal(Request $request)
    {
        $user = $request->user();
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();

        $date = $request->query('bulan');
        $tgl = $date ? \Carbon\Carbon::parse($date) : now();

        $timeSlots = self::TIME_SLOTS;
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $dayLabels = self::DAY_MAP;

        $grid = [];
        $jadwalBulan = collect();

        if ($kelas) {
            $activePeriod = AcademicPeriod::where('is_active', true)->first();

            $assignments = TeachingAssignment::where('class_name', $kelas->nama_lengkap)
                ->where('period_id', $activePeriod?->id)
                ->with(['subject', 'customSubject', 'teacher', 'jadwals'])
                ->get();

            foreach ($days as $day) {
                $grid[$day] = [];
                foreach ($timeSlots as $slot => $time) {
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
                        'teacher' => $ta->teacher?->full_name ?? $ta->teacher?->name ?? '-',
                    ];
                }
            }

            $jadwalBulan = Assessment::whereHas('teachingAssignment', fn($q) =>
                $q->where('class_name', $kelas->nama_lengkap)
            )
            ->whereMonth('assessment_date', $tgl->month)
            ->whereYear('assessment_date', $tgl->year)
            ->with('teachingAssignment.subject', 'teachingAssignment.customSubject', 'teachingAssignment.teacher')
            ->orderBy('assessment_date')
            ->get()
            ->groupBy(fn($a) => $a->assessment_date->format('Y-m-d'));
        }

        return view('guru.wali.jadwal', [
            'kelas' => $kelas,
            'days' => $days,
            'dayLabels' => $dayLabels,
            'timeSlots' => $timeSlots,
            'grid' => $grid,
            'jadwalBulan' => $jadwalBulan,
            'calendarMonth' => $tgl,
            'prevBulan' => $tgl->copy()->subMonth()->format('Y-m'),
            'nextBulan' => $tgl->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function waliNilai(Request $request)
    {
        $user = $request->user();
        $kelas = Kelas::where('homeroom_teacher_id', $user->id)->first();

        $students = collect();
        $selectedStudent = null;
        $grades = [];

        if ($kelas) {
            $students = Student::where('class_name', $kelas->nama_lengkap)
                ->where('status', 'active')
                ->orderBy('full_name')
                ->get();

            $studentId = $request->query('student_id');
            if ($studentId) {
                $selectedStudent = $students->firstWhere('id', $studentId);
                if ($selectedStudent) {
                    $grades = $this->computeStudentGrades($selectedStudent);
                }
            }
        }

        $avgScore = $grades ? \App\Helpers\PortalHelper::average(array_column($grades, 'final_score')) : 0;
        $classMaxScore = $grades ? round(max(array_column($grades, 'class_max')), 1) : 0;

        return view('guru.wali.nilai', [
            'kelas' => $kelas,
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'grades' => $grades,
            'avgScore' => round($avgScore, 1),
            'avgLetter' => \App\Helpers\PortalHelper::gradeLetter($avgScore),
            'classMaxScore' => $classMaxScore,
        ]);
    }

    private function computeStudentGrades($student): array
    {
        $period = AcademicPeriod::where('is_active', true)->first();
        if (!$period) return [];

        $assignments = TeachingAssignment::where('period_id', $period->id)
            ->where('class_name', $student->class_name)
            ->with(['subject', 'customSubject', 'assessments' => fn($q) => $q->whereNotNull('published_at')->orderBy('assessment_date')])
            ->get();

        $allAssessmentIds = $assignments->flatMap->assessments->pluck('id');

        $allMyScores = $allAssessmentIds->isNotEmpty()
            ? AssessmentScore::whereIn('assessment_id', $allAssessmentIds)
                ->where('student_id', $student->id)
                ->get()
                ->keyBy('assessment_id')
            : collect();

        $classStudentIds = Student::where('class_name', $student->class_name)
            ->where('status', 'active')
            ->pluck('id');

        $allClassScores = $allAssessmentIds->isNotEmpty()
            ? AssessmentScore::whereIn('assessment_id', $allAssessmentIds)
                ->whereIn('student_id', $classStudentIds)
                ->get()
                ->groupBy('student_id')
            : collect();

        $grades = [];

        foreach ($assignments as $assignment) {
            $assessments = $assignment->assessments;

            $raw = ['quiz' => [], 'homework' => [], 'project' => [], 'assignment' => [], 'uts' => 0, 'uas' => 0];

            foreach ($assessments as $assessment) {
                $score = $allMyScores->get($assessment->id)?->score;
                if ($score === null) continue;

                $comp = $assessment->component;
                if ($comp === 'uts' || $comp === 'uas') {
                    $raw[$comp] = max($raw[$comp], (float) $score);
                } else {
                    $raw[$comp][] = (float) $score;
                }
            }

            $componentScores = \App\Helpers\PortalHelper::componentScores($raw);
            $finalScore = \App\Helpers\PortalHelper::finalScore($raw);

            $subjectName = $assignment->subject?->name ?? $assignment->customSubject?->nama ?? '-';
            $subjectCode = $assignment->subject?->code ?? $assignment->customSubject?->kode ?? '-';
            $kkm = (float) ($assignment->subject?->kkm ?? $assignment->customSubject?->kkm ?? 0);

            $classAvg = 0;
            $classMax = 0;

            if ($classStudentIds->isNotEmpty()) {
                $studentFinals = [];
                foreach ($classStudentIds as $sid) {
                    $r = ['quiz' => [], 'homework' => [], 'project' => [], 'assignment' => [], 'uts' => 0, 'uas' => 0];
                    $studentScores = $allClassScores->get($sid, collect());

                    foreach ($assessments as $a) {
                        $sc = $studentScores->firstWhere('assessment_id', $a->id)?->score;
                        if ($sc === null) continue;
                        $comp = $a->component;
                        if ($comp === 'uts' || $comp === 'uas') {
                            $r[$comp] = max($r[$comp], (float) $sc);
                        } else {
                            $r[$comp][] = (float) $sc;
                        }
                    }

                    $f = \App\Helpers\PortalHelper::finalScore($r);
                    if ($f > 0) $studentFinals[] = $f;
                }

                if ($studentFinals) {
                    $classAvg = round(array_sum($studentFinals) / count($studentFinals), 1);
                    $classMax = round(max($studentFinals), 1);
                }
            }

            $grades[] = [
                'subject' => $subjectName,
                'subject_code' => $subjectCode,
                'kkm' => $kkm,
                'components' => $componentScores,
                'final_score' => $finalScore,
                'letter' => \App\Helpers\PortalHelper::gradeLetter($finalScore),
                'passed' => $finalScore >= $kkm,
                'class_avg' => $classAvg,
                'class_max' => $classMax,
            ];
        }

        return $grades;
    }
}
