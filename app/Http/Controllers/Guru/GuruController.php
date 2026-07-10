<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use App\Models\Material;
use App\Models\Student;
use App\Models\TeacherNote;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    private function getAssignments($user)
    {
        return $user->teachingAssignments()
            ->with('subject', 'period')
            ->whereHas('period', fn($q) => $q->where('is_active', true))
            ->get();
    }

    private function getActivePeriod()
    {
        return \App\Models\AcademicPeriod::where('is_active', true)->first();
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

        $schedule = $this->generateSchedule($teachingAssignments);
        $dayNames = ['Senin','Selasa','Rabu','Kamis','Jumat'];
        $today = date('N') <= 5 ? $dayNames[date('N') - 1] : '';
        $todaySchedule = collect($schedule)->where('day', $today)->values();

        return view('guru.dashboard', [
            'teachingAssignments' => $teachingAssignments,
            'classNames' => $classNames,
            'studentsPerClass' => $studentsPerClass,
            'homeroomStudents' => $homeroomStudents,
            'isHomeroom' => $isHomeroom,
            'totalStudents' => $totalStudents,
            'totalClasses' => $classNames->count(),
            'totalSubjects' => $teachingAssignments->pluck('subject_id')->unique()->count(),
            'schedule' => $schedule,
            'todaySchedule' => $todaySchedule,
            'today' => $today,
        ]);
    }

    public function kelas(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $classList = [];
        foreach ($classNames as $class) {
            $students = Student::where('class_name', $class)->where('status', 'active')->get();
            $subjects = $teachingAssignments->where('class_name', $class)
                ->map(fn($a) => ['id' => $a->subject->id, 'name' => $a->subject->name])
                ->unique('id')->values()->all();
            $classList[] = [
                'name' => $class,
                'student_count' => $students->count(),
                'students' => $students,
                'subjects' => $subjects,
            ];
        }

        return view('guru.kelas', [
            'classList' => $classList,
        ]);
    }

    public function nilai(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $classSubjectPairs = $teachingAssignments->map(fn($a) => [
            'class_name' => $a->class_name,
            'subject_id' => $a->subject->id,
            'subject_name' => $a->subject->name,
        ])->unique(fn($p) => $p['class_name'] . '-' . $p['subject_id'])->values();

        return view('guru.nilai', [
            'classNames' => $classNames,
            'pairs' => $classSubjectPairs,
        ]);
    }

    public function nilaiDetail(Request $request, $class, $subject)
    {
        $user = $request->user();
        $students = Student::where('class_name', $class)->where('status', 'active')->get();
        $subjectModel = \App\Models\Subject::findOrFail($subject);

        $assignment = $user->teachingAssignments()
            ->where('class_name', $class)
            ->where('subject_id', $subject)
            ->whereHas('period', fn($q) => $q->where('is_active', true))
            ->first();

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

        $savedDraft = session(" nilai_draft.{$class}.{$subject}", []);

        return view('guru.nilai-detail', [
            'class' => $class,
            'subject' => $subjectModel,
            'students' => $students,
            'assessments' => $assessments,
            'scores' => $scores,
            'savedDraft' => $savedDraft,
        ]);
    }

    public function nilaiStore(Request $request, $class, $subject)
    {
        $user = $request->user();
        $period = $this->getActivePeriod();

        $assignment = $user->teachingAssignments()
            ->where('class_name', $class)
            ->where('subject_id', $subject)
            ->whereHas('period', fn($q) => $q->where('is_active', true))
            ->first();

        if (!$assignment) {
            return back()->with('error', 'Tidak ada penugasan ditemukan.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:160',
            'component' => 'required|in:quiz,homework,project,uts,uas',
            'scores' => 'required|array',
        ]);

        $assessment = Assessment::create([
            'teaching_assignment_id' => $assignment->id,
            'title' => $validated['title'],
            'component' => $validated['component'],
            'assessment_date' => now()->toDateString(),
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

        return back()->with('success', "Nilai \"{$validated['title']}\" berhasil disimpan.");
    }

    public function absensi(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();

        $selectedClass = $request->query('class', $classNames->first());
        $date = $request->query('date', now()->format('Y-m-d'));

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

        return back()->with('success', 'Absensi berhasil disimpan.');
    }

    public function jadwal(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $schedule = $this->generateSchedule($teachingAssignments);

        return view('guru.jadwal', [
            'schedule' => $schedule,
        ]);
    }

    public function catatan(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);
        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();
        $period = $this->getActivePeriod();

        $selectedStudent = $request->query('student');

        $students = Student::where('class_name', 'in', $classNames->toArray())
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

        TeacherNote::create([
            'student_id' => $validated['student_id'],
            'period_id' => $period?->id,
            'author_id' => $request->user()->id,
            'category' => $validated['category'],
            'note' => $validated['note'],
            'follow_up' => $validated['follow_up'] ?? null,
        ]);

        return back()->with('success', 'Catatan berhasil disimpan.');
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
                ->pluck('subject.name')->unique()->implode(', ');
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

        return back()->with('success', "Nilai untuk kelas {$class} berhasil dipublikasikan.");
    }

    public function materi(Request $request)
    {
        $user = $request->user();
        $teachingAssignments = $this->getAssignments($user);

        $materials = Material::with('teachingAssignment.subject')
            ->whereIn('teaching_assignment_id', $teachingAssignments->pluck('id'))
            ->latest()
            ->get();

        $classNames = $teachingAssignments->pluck('class_name')->unique()->values();
        $pairs = $teachingAssignments->map(fn($a) => [
            'assignment_id' => $a->id,
            'class_name' => $a->class_name,
            'subject_name' => $a->subject->name ?? '-',
        ])->unique('assignment_id')->values();

        return view('guru.materi', [
            'materials' => $materials,
            'classNames' => $classNames,
            'pairs' => $pairs,
        ]);
    }

    public function materiStore(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:500',
            'url' => 'nullable|url|max:500',
        ]);

        Material::create($validated);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function materiDestroy(Request $request, \App\Models\Material $material)
    {
        $user = $request->user();

        if (!$user->teachingAssignments()->where('id', $material->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    private function generateSchedule($assignments): array
    {
        $days = ['Senin','Selasa','Rabu','Kamis','Jumat'];
        $times = [
            '07:30 - 08:50',
            '09:00 - 10:20',
            '10:30 - 11:50',
            '13:00 - 14:20',
            '14:30 - 15:50',
        ];

        $schedule = [];
        $assignments->each(function ($a) use (&$schedule, $days, $times) {
            $hash = crc32($a->subject_id . $a->class_name);
            $schedule[] = [
                'day' => $days[$hash % count($days)],
                'time' => $times[($hash >> 4) % count($times)],
                'subject' => $a->subject->name ?? '-',
                'class_name' => $a->class_name,
                'subject_id' => $a->subject_id,
            ];
        });

        usort($schedule, function ($a, $b) use ($days) {
            $d = array_search($a['day'], $days) - array_search($b['day'], $days);
            return $d !== 0 ? $d : strcmp($a['time'], $b['time']);
        });

        return $schedule;
    }
}
