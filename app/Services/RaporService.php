<?php

namespace App\Services;

use App\Helpers\PortalHelper;
use App\Models\AcademicPeriod;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\TeacherNote;
use App\Models\TeachingAssignment;
use App\Models\User;

class RaporService
{
    public function buildReport(Student $student, ?AcademicPeriod $period = null): array
    {
        $period = $period ?? AcademicPeriod::where('is_active', true)->first();

        $subjects = $this->computeSubjects($student, $period);
        $average = $this->computeAverage($subjects);

        $allKkms = array_map(fn($s) => (float) ($s['kkm'] ?? 75), $subjects);
        $averageKkm = $allKkms ? round(array_sum($allKkms) / count($allKkms), 1) : 75;

        $attendance = $this->computeAttendance($student);

        $classRanks = $this->computeClassRanks($student, $period);
        $kelas = $this->resolveKelas($student);

        $behavior = $student->behaviorScores
            ->where('period_id', $period?->id)
            ->pluck('grade', 'aspect')
            ->toArray();

        $behaviorDetails = $student->behaviorScores
            ->where('period_id', $period?->id)
            ->map(fn($b) => [
                'aspect' => $b->aspect,
                'grade' => $b->grade,
                'note' => $b->note,
            ])
            ->values()
            ->toArray();

        $extracurricular = $student->extracurriculars->map(fn($e) => [
            'name' => $e->name,
            'score' => $e->score,
            'note' => $e->note,
        ])->values()->toArray();

        $note = TeacherNote::where('student_id', $student->id)
            ->where('visible_to_parent', true)
            ->latest('created_at')
            ->first();

        $homeroom = $this->resolveHomeroomTeacher($student);

        return [
            'id' => $student->id,
            'student' => $student,
            'name' => $student->full_name,
            'initials' => strtoupper(mb_substr($student->full_name ?? 'S', 0, 2)),
            'nisn' => $student->nisn,
            'nis' => $student->nis,
            'class' => $student->class_name,
            'program' => $student->program_name,
            'birth_place' => $student->birth_place ?? null,
            'birth_date' => $student->birth_date?->format('d-m-Y'),
            'homeroom_teacher' => $homeroom?->full_name ?? $homeroom?->name ?? '-',
            'academic_year' => $period?->academic_year ?? '-',
            'semester' => $period?->semester === 'ganjil' ? 'Ganjil' : 'Genap',
            'kkm' => $averageKkm,
            'subjects' => $subjects,
            'average' => $average,
            'attendanceRate' => $attendance['rate'],
            'attendance' => $attendance['breakdown'],
            'behavior' => $behavior,
            'behavior_details' => $behaviorDetails,
            'extracurricular' => $extracurricular,
            'teacher_note' => $note?->note ?? '',
            'class_size' => $classRanks['class_size'],
            'rank' => $classRanks['rank'],
            'promotion' => $this->computePromotion($subjects, $kelas),
            'principal' => $this->principal(),
            'period' => $period,
        ];
    }

    public function subjectWeights(): array
    {
        return PortalHelper::WEIGHTS;
    }

    private function computeSubjects(Student $student, ?AcademicPeriod $period): array
    {
        $assignments = TeachingAssignment::where('class_name', $student->class_name)
            ->where('period_id', $period?->id)
            ->with(['subject', 'customSubject', 'teacher', 'assessments' => fn($q) => $q->whereNotNull('published_at')->orderBy('assessment_date')])
            ->get();

        $allAssessmentIds = $assignments->flatMap->assessments->pluck('id');

        $myScores = $allAssessmentIds->isNotEmpty()
            ? AssessmentScore::whereIn('assessment_id', $allAssessmentIds)
                ->where('student_id', $student->id)
                ->get()
                ->keyBy('assessment_id')
            : collect();

        $result = [];

        foreach ($assignments as $assignment) {
            $raw = ['quiz' => [], 'homework' => [], 'project' => [], 'assignment' => [], 'uts' => 0, 'uas' => 0];

            foreach ($assignment->assessments as $assessment) {
                $score = $myScores->get($assessment->id)?->score;
                if ($score === null) continue;

                $comp = $assessment->component;
                if ($comp === 'uts' || $comp === 'uas') {
                    $raw[$comp] = max($raw[$comp], (float) $score);
                } elseif (array_key_exists($comp, $raw)) {
                    $raw[$comp][] = (float) $score;
                }
            }

            $subject = $assignment->subject ?? $assignment->customSubject;
            $weights = PortalHelper::effectiveWeights($subject, $assignment);
            $components = PortalHelper::componentScores($raw);
            $finalScore = PortalHelper::finalScore($raw, $weights);
            $kkm = (float) ($assignment->subject?->kkm ?? $assignment->customSubject?->kkm ?? 75);

            $result[] = [
                'kkm' => $kkm,
                'code' => $assignment->subject?->code ?? $assignment->customSubject?->kode ?? '-',
                'name' => $assignment->subject?->name ?? $assignment->customSubject?->nama ?? '-',
                'teacher' => $assignment->teacher?->full_name ?? $assignment->teacher?->name ?? '-',
                'quiz' => $raw['quiz'],
                'homework' => $raw['homework'],
                'project' => $raw['project'],
                'assignment' => $raw['assignment'],
                'uts' => $components['uts'],
                'uas' => $components['uas'],
                'components' => $components,
                'weights' => $weights,
                'final' => $finalScore,
                'letter' => PortalHelper::gradeLetter($finalScore),
                'mastery' => $finalScore >= $kkm ? 'Tuntas' : 'Belum Tuntas',
                'note' => '',
            ];
        }

        return $result;
    }

    private function computeAverage(array $subjects): float
    {
        if (!$subjects) return 0;
        $values = array_map(fn($s) => (float) ($s['final'] ?? 0), $subjects);
        return round(array_sum($values) / count($values), 1);
    }

    private function computeAttendance(Student $student): array
    {
        $breakdown = Attendance::where('student_id', $student->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = [
            'present' => $breakdown['present'] ?? 0,
            'sick' => $breakdown['sick'] ?? 0,
            'excused' => $breakdown['excused'] ?? 0,
            'unexcused' => $breakdown['unexcused'] ?? 0,
            'late' => $breakdown['late'] ?? 0,
        ];

        $total = array_sum($counts);
        $rate = $total > 0 ? round($counts['present'] / $total * 100, 1) : 0;

        return [
            'breakdown' => $counts,
            'total' => $total,
            'rate' => $rate,
        ];
    }

    private function computeClassRanks(Student $student, ?AcademicPeriod $period): array
    {
        $classSize = Student::where('class_name', $student->class_name)
            ->where('status', 'active')
            ->count();

        $studentIds = Student::where('class_name', $student->class_name)
            ->where('status', 'active')
            ->pluck('id');

        if ($studentIds->isEmpty()) {
            return ['class_size' => $classSize, 'rank' => null];
        }

        $assignments = TeachingAssignment::where('period_id', $period?->id)
            ->where('class_name', $student->class_name)
            ->with(['subject', 'customSubject', 'assessments' => fn($q) => $q->whereNotNull('published_at')])
            ->get();

        $allAssessmentIds = $assignments->flatMap->assessments->pluck('id');

        $allScores = $allAssessmentIds->isNotEmpty()
            ? AssessmentScore::whereIn('assessment_id', $allAssessmentIds)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('student_id')
            : collect();

        $averages = [];

        foreach ($studentIds as $sid) {
            $studentScores = $allScores->get($sid, collect());
            $finalScores = [];

            foreach ($assignments as $assignment) {
                $raw = ['quiz' => [], 'homework' => [], 'project' => [], 'assignment' => [], 'uts' => 0, 'uas' => 0];

                foreach ($assignment->assessments as $assessment) {
                    $score = $studentScores->firstWhere('assessment_id', $assessment->id)?->score;
                    if ($score === null) continue;

                    $comp = $assessment->component;
                    if ($comp === 'uts' || $comp === 'uas') {
                        $raw[$comp] = max($raw[$comp], (float) $score);
                    } elseif (array_key_exists($comp, $raw)) {
                        $raw[$comp][] = (float) $score;
                    }
                }

                $subject = $assignment->subject ?? $assignment->customSubject;
                $weights = PortalHelper::effectiveWeights($subject, $assignment);
                $final = PortalHelper::finalScore($raw, $weights);

                if ($final > 0) {
                    $finalScores[] = $final;
                }
            }

            if ($finalScores) {
                $averages[$sid] = round(array_sum($finalScores) / count($finalScores), 1);
            }
        }

        $myAverage = $averages[$student->id] ?? null;

        if ($myAverage === null) {
            return ['class_size' => $classSize, 'rank' => null];
        }

        $higher = count(array_filter($averages, fn($a) => $a > $myAverage));

        return ['class_size' => $classSize, 'rank' => $higher + 1];
    }

    private function computePromotion(array $subjects, ?Kelas $kelas): ?array
    {
        if (empty($subjects)) {
            return null;
        }

        $allPass = true;
        foreach ($subjects as $subject) {
            if (($subject['final'] ?? 0) < ($subject['kkm'] ?? 75)) {
                $allPass = false;
                break;
            }
        }

        if (!$allPass) {
            return ['status' => 'not_promoted', 'next_class' => null];
        }

        $next = match ($kelas?->tingkat) {
            10 => 'XI',
            11 => 'XII',
            default => null,
        };

        if (!$next) {
            return null;
        }

        return ['status' => 'promoted', 'next_class' => $next];
    }

    private function resolveKelas(Student $student): ?Kelas
    {
        if ($student->kelas) {
            return $student->kelas;
        }

        return Kelas::all()->firstWhere('nama_lengkap', $student->class_name);
    }

    private function resolveHomeroomTeacher(Student $student): ?User
    {
        $homeroom = $student->homeroomTeacher;

        if (!$homeroom) {
            $homeroom = $this->resolveKelas($student)?->homeroomTeacher;
        }

        return $homeroom;
    }

    private function principal(): array
    {
        $name = config('school.principal_name');
        $nip = config('school.principal_nip');
        $credentials = config('school.principal_credentials');

        $user = User::where('role', 'principal')->first();
        if (!$name && $user) {
            $name = $user->full_name ?? $user->name;
        }

        return [
            'name' => $name ?: '-',
            'nip' => $nip ?: '-',
            'credentials' => $credentials ?: '',
        ];
    }
}
