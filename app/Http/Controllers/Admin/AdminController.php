<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Applicant;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Mail\ParentAccountMail;
use App\Services\AuditService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = Student::where('status', 'active')->count();
        $totalTeachers = User::whereIn('role', ['teacher', 'homeroom'])->count();
        $totalParents = User::where('role', 'parent')->count();
        $totalClasses = Student::where('status', 'active')->distinct('class_name')->count('class_name');
        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        $totalSubjects = Subject::count();
        $recentAudit = AuditLog::with('user')->latest()->take(5)->get();
        $todayAttendance = Attendance::whereDate('attendance_date', today())->count();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalParents',
            'totalClasses', 'activePeriod', 'totalSubjects',
            'recentAudit', 'todayAttendance'
        ));
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($role = $request->query('role')) {
            $roles = explode(',', $role);
            $query->whereIn('role', $roles);
        }
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        $users = $query->latest()->paginate(15)->withQueryString();

        $tabCounts = [
            'all' => User::count(),
            'parent' => User::where('role', 'parent')->count(),
            'guru' => User::whereIn('role', ['teacher', 'homeroom', 'principal'])->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'tabCounts'));
    }

    public function usersCreate()
    {
        return view('admin.user-form', ['user' => null]);
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => 'required|in:parent,teacher,homeroom,admin,principal',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        AuditService::log('user.create', 'User', $user->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil ditambahkan.', 'user' => $user]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function usersEdit(User $user)
    {
        return view('admin.user-form', ['user' => $user]);
    }

    public function userData(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
        ]);
    }

    public function usersUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:parent,teacher,homeroom,admin,principal',
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::min(6)],
        ]);

        $data = [
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($validated['password']) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        AuditService::log('user.update', 'User', $user->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil diperbarui.', 'user' => $user]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function usersToggle(Request $request, User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditService::log('user.toggle', 'User', $user->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Akun {$user->name} berhasil {$status}.", 'is_active' => $user->is_active]);
        }

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function usersResetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', Password::min(6)],
        ]);

        $user->update(['password' => Hash::make($validated['new_password'])]);

        AuditService::log('user.reset-password', 'User', $user->id);
        return back()->with('success', "Password {$user->name} berhasil direset.");
    }

    public function usersDestroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.']);
            }
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        AuditService::log('user.delete', 'User', $user->id);
        $user->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus.']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function students(Request $request)
    {
        $query = Student::query();
        if ($class = $request->query('class')) {
            $query->where('class_name', $class);
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->with('homeroomTeacher')->latest()->paginate(15)->withQueryString();
        $classNames = Student::distinct()->pluck('class_name')->sort()->values();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();

        $tabCounts = [
            'all' => Student::count(),
            'active' => Student::where('status', 'active')->count(),
            'graduated' => Student::where('status', 'graduated')->count(),
            'inactive' => Student::where('status', 'inactive')->count(),
        ];

        $applicantQuery = Applicant::with('user');
        if ($applicantStatus = $request->query('status')) {
            $applicantQuery->where('status', $applicantStatus);
        }
        if ($applicantSearch = $request->query('search')) {
            $applicantQuery->where(function ($q) use ($applicantSearch) {
                $q->where('full_name', 'like', "%{$applicantSearch}%")
                  ->orWhere('asal_sekolah', 'like', "%{$applicantSearch}%");
            });
        }
        $applicants = $applicantQuery->latest()->paginate(15, ['*'], 'applicants_page')->withQueryString();

        $applicantStatusCounts = [
            'all' => Applicant::count(),
            'draft' => Applicant::where('status', 'draft')->count(),
            'submitted' => Applicant::where('status', 'submitted')->count(),
            'verified' => Applicant::where('status', 'verified')->count(),
            'paid' => Applicant::where('status', 'paid')->count(),
            'rejected' => Applicant::where('status', 'rejected')->count(),
        ];

        return view('admin.students', compact('students', 'classNames', 'teachers', 'tabCounts', 'applicants', 'applicantStatusCounts'));
    }

    public function studentsCreate()
    {
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        return view('admin.student-form', ['student' => null, 'teachers' => $teachers]);
    }

    public function studentData(Student $student)
    {
        $parents = $student->parents()->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->full_name ?: $p->name,
            'email' => $p->email,
            'pivot' => [
                'relationship' => $p->pivot->relationship,
                'is_primary' => $p->pivot->is_primary,
            ],
        ]);

        return response()->json([
            'id' => $student->id,
            'nisn' => $student->nisn,
            'full_name' => $student->full_name,
            'birth_date' => $student->birth_date?->format('Y-m-d'),
            'class_name' => $student->class_name,
            'program_name' => $student->program_name,
            'homeroom_teacher_id' => $student->homeroom_teacher_id,
            'status' => $student->status,
            'parents' => $parents,
        ]);
    }

    public function parentsList()
    {
        $parents = User::where('role', 'parent')
            ->withCount('students')
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->full_name ?: $p->name,
                'email' => $p->email,
                'students_count' => $p->students_count,
            ]);

        return response()->json($parents);
    }

    public function studentsStore(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'full_name' => 'required|string|max:150',
            'birth_date' => 'nullable|date',
            'class_name' => 'required|string|max:80',
            'program_name' => 'required|string|max:120',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,graduated,inactive',
            'parent_action' => 'required|in:existing,new,none',
            'parent_id' => 'required_if:parent_action,existing|nullable|exists:users,id',
            'parent_name' => 'required_if:parent_action,new|nullable|string|max:255',
            'parent_email' => 'required_if:parent_action,new|nullable|email|unique:users,email',
            'parent_password' => 'required_if:parent_action,new|nullable|min:6',
            'parent_relationship' => 'nullable|string|max:40',
        ]);

        $student = Student::create([
            'nisn' => $validated['nisn'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'class_name' => $validated['class_name'],
            'program_name' => $validated['program_name'],
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? null,
            'status' => $validated['status'],
        ]);

        if (($validated['parent_action'] ?? 'none') !== 'none') {
            if ($validated['parent_action'] === 'existing') {
                $parentId = $validated['parent_id'];
            } else {
                $parent = User::create([
                    'name' => $validated['parent_name'],
                    'full_name' => $validated['parent_name'],
                    'email' => $validated['parent_email'],
                    'password' => Hash::make($validated['parent_password']),
                    'role' => 'parent',
                    'is_active' => true,
                ]);
                $parentId = $parent->id;
            }

            DB::table('parent_student')->insert([
                'parent_id' => $parentId,
                'student_id' => $student->id,
                'relationship' => $validated['parent_relationship'] ?? 'Orang Tua',
                'is_primary' => true,
            ]);
        }

        AuditService::log('student.create', 'Student', $student->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Siswa berhasil ditambahkan.']);
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function studentsEdit(Student $student)
    {
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        return view('admin.student-form', ['student' => $student, 'teachers' => $teachers]);
    }

    public function studentsUpdate(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'full_name' => 'required|string|max:150',
            'birth_date' => 'nullable|date',
            'class_name' => 'required|string|max:80',
            'program_name' => 'required|string|max:120',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,graduated,inactive',
            'parent_action' => 'nullable|in:existing,new,none,disconnect',
            'parent_id' => 'required_if:parent_action,existing|nullable|exists:users,id',
            'parent_name' => 'required_if:parent_action,new|nullable|string|max:255',
            'parent_email' => 'required_if:parent_action,new|nullable|email|unique:users,email',
            'parent_password' => 'required_if:parent_action,new|nullable|min:6',
            'parent_relationship' => 'nullable|string|max:40',
            'disconnect_parent_id' => 'nullable|exists:users,id',
        ]);

        $student->update([
            'nisn' => $validated['nisn'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'class_name' => $validated['class_name'],
            'program_name' => $validated['program_name'],
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['disconnect_parent_id'])) {
            $student->parents()->detach($validated['disconnect_parent_id']);
        }

        if (!empty($validated['parent_action']) && $validated['parent_action'] !== 'none') {
            if ($validated['parent_action'] === 'existing') {
                $parentId = $validated['parent_id'];
            } else {
                $parent = User::create([
                    'name' => $validated['parent_name'],
                    'full_name' => $validated['parent_name'],
                    'email' => $validated['parent_email'],
                    'password' => Hash::make($validated['parent_password']),
                    'role' => 'parent',
                    'is_active' => true,
                ]);
                $parentId = $parent->id;
            }

            if (!$student->parents()->where('parent_id', $parentId)->exists()) {
                DB::table('parent_student')->insert([
                    'parent_id' => $parentId,
                    'student_id' => $student->id,
                    'relationship' => $validated['parent_relationship'] ?? 'Orang Tua',
                    'is_primary' => true,
                ]);
            }
        }

        AuditService::log('student.update', 'Student', $student->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data siswa berhasil diperbarui.']);
        }

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function studentsDestroy(Request $request, Student $student)
    {
        AuditService::log('student.delete', 'Student', $student->id);
        $student->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Siswa berhasil dihapus.']);
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function studentImportForm()
    {
        return view('admin.student-import');
    }

    public function studentImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map('strtolower', array_shift($rows));

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            $nisn = trim($data['nisn'] ?? '');
            $fullName = trim($data['full_name'] ?? $data['nama'] ?? '');

            if (!$nisn || !$fullName) {
                $skipped++;
                continue;
            }

            Student::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'full_name' => $fullName,
                    'birth_date' => !empty($data['birth_date']) ? $data['birth_date'] : null,
                    'class_name' => trim($data['class_name'] ?? $data['kelas'] ?? ''),
                    'program_name' => trim($data['program_name'] ?? $data['program'] ?? ''),
                    'status' => 'active',
                ]
            );
            $imported++;
        }

        return redirect()->route('admin.students.index')
            ->with('success', "Import selesai: {$imported} siswa diimpor, {$skipped} dilewati.");
    }

    public function subjects(Request $request)
    {
        $query = Subject::with('gurus');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('code')->paginate(15)->withQueryString();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();

        return view('admin.subjects', compact('subjects', 'teachers'));
    }

    public function subjectData(Subject $subject)
    {
        return response()->json([
            'id' => $subject->id,
            'code' => $subject->code,
            'name' => $subject->name,
            'kkm' => $subject->kkm,
            'guru_ids' => $subject->gurus->pluck('id')->toArray(),
        ]);
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:120',
            'kkm' => 'required|numeric|min:0|max:100',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:users,id',
        ]);

        $subject = Subject::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'kkm' => $validated['kkm'],
        ]);

        $activePeriod = \App\Models\AcademicPeriod::where('is_active', true)->first();
        if (!empty($validated['guru_ids']) && $activePeriod) {
            $subject->gurus()->syncWithPivotValues($validated['guru_ids'], ['semester_id' => $activePeriod->id]);
        }

        AuditService::log('subject.create', 'Subject', $subject->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata pelajaran berhasil ditambahkan.']);
        }

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:120',
            'kkm' => 'required|numeric|min:0|max:100',
            'guru_ids' => 'nullable|array',
            'guru_ids.*' => 'exists:users,id',
        ]);

        $subject->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'kkm' => $validated['kkm'],
        ]);

        $activePeriod = \App\Models\AcademicPeriod::where('is_active', true)->first();
        $subject->gurus()->syncWithPivotValues($validated['guru_ids'] ?? [], ['semester_id' => $activePeriod?->id]);

        AuditService::log('subject.update', 'Subject', $subject->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata pelajaran berhasil diperbarui.']);
        }

        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function subjectsDestroy(Request $request, Subject $subject)
    {
        AuditService::log('subject.delete', 'Subject', $subject->id);
        $subject->gurus()->detach();
        $subject->teachingAssignments()->delete();
        $subject->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata pelajaran berhasil dihapus.']);
        }

        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function periods(Request $request)
    {
        $query = AcademicPeriod::withCount(['teachingAssignments', 'teacherNotes', 'behaviorScores']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('academic_year', 'like', "%{$search}%")
                  ->orWhere('semester', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('is_active', $status === 'active');
        }

        $periods = $query->latest()->paginate(15)->withQueryString();

        $tabCounts = [
            'all' => AcademicPeriod::count(),
            'active' => AcademicPeriod::where('is_active', true)->count(),
            'inactive' => AcademicPeriod::where('is_active', false)->count(),
        ];

        return view('admin.periods', compact('periods', 'tabCounts'));
    }

    public function periodData(AcademicPeriod $period)
    {
        return response()->json([
            'id' => $period->id,
            'academic_year' => $period->academic_year,
            'semester' => $period->semester,
            'start_date' => $period->start_date->format('Y-m-d'),
            'end_date' => $period->end_date->format('Y-m-d'),
            'is_active' => $period->is_active,
            'teaching_assignments_count' => $period->teaching_assignments_count,
        ]);
    }

    public function periodsStore(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        AcademicPeriod::create($validated);

        AuditService::log('period.create', 'AcademicPeriod');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Periode akademik berhasil ditambahkan.']);
        }

        return back()->with('success', 'Periode akademik berhasil ditambahkan.');
    }

    public function periodsUpdate(Request $request, AcademicPeriod $period)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $period->update($validated);

        AuditService::log('period.update', 'AcademicPeriod', $period->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Periode akademik berhasil diperbarui.']);
        }

        return back()->with('success', 'Periode akademik berhasil diperbarui.');
    }

    public function periodsDestroy(Request $request, AcademicPeriod $period)
    {
        AuditService::log('period.delete', 'AcademicPeriod', $period->id);
        $period->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Periode akademik berhasil dihapus.']);
        }

        return back()->with('success', 'Periode akademik berhasil dihapus.');
    }

    public function periodsActivate(Request $request, AcademicPeriod $period)
    {
        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        AuditService::log('period.activate', 'AcademicPeriod', $period->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Periode {$period->academic_year} {$period->semester} diaktifkan."]);
        }

        return back()->with('success', "Periode {$period->academic_year} {$period->semester} diaktifkan.");
    }

    public function teaching(Request $request)
    {
        $query = TeachingAssignment::with(['period', 'subject', 'teacher']);

        if ($periodId = $request->query('period')) {
            $query->where('period_id', $periodId);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('teacher', fn($tq) => $tq->where('name', 'like', "%{$search}%")->orWhere('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                  ->orWhere('class_name', 'like', "%{$search}%");
            });
        }

        $assignments = $query->latest()->paginate(15)->withQueryString();
        $periods = AcademicPeriod::orderByDesc('is_active')->orderByDesc('academic_year')->get();
        $subjects = Subject::orderBy('code')->get();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        $classNames = Student::distinct()->pluck('class_name')->sort()->values();

        return view('admin.teaching', compact('assignments', 'periods', 'subjects', 'teachers', 'classNames'));
    }

    public function teachingCreate()
    {
        $periods = AcademicPeriod::orderByDesc('is_active')->get();
        $subjects = Subject::orderBy('code')->get();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        $classNames = Student::distinct()->pluck('class_name')->sort()->values();

        return view('admin.teaching-form', compact('periods', 'subjects', 'teachers', 'classNames'));
    }

    public function teachingData(TeachingAssignment $assignment)
    {
        $assignment->load(['period', 'subject', 'teacher']);

        return response()->json([
            'id' => $assignment->id,
            'period_id' => $assignment->period_id,
            'subject_id' => $assignment->subject_id,
            'teacher_id' => $assignment->teacher_id,
            'class_name' => $assignment->class_name,
            'period_label' => $assignment->period->academic_year . ' ' . ucfirst($assignment->period->semester),
            'subject_label' => $assignment->subject->code . ' — ' . $assignment->subject->name,
            'teacher_label' => $assignment->teacher->full_name ?? $assignment->teacher->name,
        ]);
    }

    public function teachingStore(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:academic_periods,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string|max:80',
        ]);

        $exists = TeachingAssignment::where($validated)->exists();
        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Penugasan ini sudah ada.']);
            }
            return back()->with('error', 'Penugasan ini sudah ada.');
        }

        TeachingAssignment::create($validated);

        AuditService::log('teaching.create', 'TeachingAssignment');

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penugasan guru berhasil ditambahkan.']);
        }

        return redirect()->route('admin.teaching.index')->with('success', 'Penugasan guru berhasil ditambahkan.');
    }

    public function teachingUpdate(Request $request, TeachingAssignment $assignment)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:academic_periods,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'class_name' => 'required|string|max:80',
        ]);

        $exists = TeachingAssignment::where($validated)->where('id', '!=', $assignment->id)->exists();
        if ($exists) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Penugasan ini sudah ada.']);
            }
            return back()->with('error', 'Penugasan ini sudah ada.');
        }

        $assignment->update($validated);

        AuditService::log('teaching.update', 'TeachingAssignment', $assignment->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penugasan berhasil diperbarui.']);
        }

        return redirect()->route('admin.teaching.index')->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function teachingDestroy(Request $request, TeachingAssignment $assignment)
    {
        AuditService::log('teaching.delete', 'TeachingAssignment', $assignment->id);
        $assignment->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penugasan berhasil dihapus.']);
        }

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }

    public function parentStudent(Request $request)
    {
        $parents = User::where('role', 'parent')->with('students')->orderBy('name')->get();
        $students = Student::where('status', 'active')->orderBy('full_name')->get();
        $parentStudent = \DB::table('parent_student')
            ->join('users', 'parent_student.parent_id', '=', 'users.id')
            ->join('students', 'parent_student.student_id', '=', 'students.id')
            ->select('parent_student.*', 'users.name as parent_name', 'users.full_name as parent_full_name', 'users.email as parent_email', 'students.full_name as student_name', 'students.class_name as student_class')
            ->get();

        return view('admin.parent-student', compact('parents', 'students', 'parentStudent'));
    }

    public function parentStudentStore(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'required|exists:users,id',
            'student_id' => 'required|exists:students,id',
            'relationship' => 'nullable|string|max:40',
            'is_primary' => 'boolean',
        ]);

        $exists = \DB::table('parent_student')
            ->where('parent_id', $validated['parent_id'])
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Hubungan ini sudah terdaftar.');
        }

        \DB::table('parent_student')->insert([
            'parent_id' => $validated['parent_id'],
            'student_id' => $validated['student_id'],
            'relationship' => $validated['relationship'] ?? null,
            'is_primary' => $validated['is_primary'] ?? false,
        ]);

        AuditService::log('parent-student.create', 'ParentStudent', $validated['student_id']);
        return back()->with('success', 'Hubungan orang tua–siswa berhasil ditambahkan.');
    }

    public function parentStudentDestroy(Request $request)
    {
        AuditService::log('parent-student.delete', 'ParentStudent', $request->student_id);
        \DB::table('parent_student')
            ->where('parent_id', $request->parent_id)
            ->where('student_id', $request->student_id)
            ->delete();

        return back()->with('success', 'Hubungan berhasil dihapus.');
    }

    public function audit(Request $request)
    {
        $query = AuditLog::with('user');
        if ($userId = $request->query('user_id')) {
            $query->where('user_id', $userId);
        }
        if ($action = $request->query('action')) {
            $query->where('action', 'like', "%{$action}%");
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        if ($role = $request->query('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $role));
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        $tabCounts = [
            'all' => AuditLog::count(),
            'admin' => AuditLog::whereHas('user', fn($q) => $q->where('role', 'admin'))->count(),
            'guru' => AuditLog::whereHas('user', fn($q) => $q->whereIn('role', ['teacher', 'homeroom', 'principal']))->count(),
            'parent' => AuditLog::whereHas('user', fn($q) => $q->where('role', 'parent'))->count(),
        ];

        return view('admin.audit', compact('logs', 'users', 'tabCounts'));
    }

    public function applicants(Request $request)
    {
        $query = Applicant::with('user');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah', 'like', "%{$search}%");
            });
        }

        $applicants = $query->latest()->paginate(20)->withQueryString();

        $statusCounts = [
            'all' => Applicant::count(),
            'draft' => Applicant::where('status', 'draft')->count(),
            'submitted' => Applicant::where('status', 'submitted')->count(),
            'verified' => Applicant::where('status', 'verified')->count(),
            'paid' => Applicant::where('status', 'paid')->count(),
            'rejected' => Applicant::where('status', 'rejected')->count(),
        ];

        return view('admin.applicants', compact('applicants', 'statusCounts'));
    }

    public function applicantData(Applicant $applicant)
    {
        return response()->json($applicant->load('documents'));
    }

    public function applicantStatus(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,verified,paid,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $applicant->update($validated);

        AuditService::log('applicant.status_update', 'Applicant', $applicant->id);

        return response()->json(['success' => true, 'message' => 'Status pendaftar berhasil diperbarui.']);
    }

    public function applicantsBulkStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:draft,submitted,verified,accepted,rejected',
        ]);

        $ids = explode(',', $validated['ids']);
        $applicants = Applicant::whereIn('id', $ids)->get();

        foreach ($applicants as $applicant) {
            $applicant->update(['status' => $validated['status']]);

            if ($validated['status'] === 'accepted') {
                $student = Student::create([
                    'user_id' => $applicant->user_id,
                    'nisn' => $applicant->nisn ?? ('PPDB-' . str_pad($applicant->id, 5, '0', STR_PAD_LEFT)),
                    'full_name' => $applicant->full_name,
                    'birth_date' => $applicant->birth_date,
                    'class_name' => $applicant->jenjang === 'SMK' ? 'X ' . ($applicant->program_diminati ?? 'Baru') : 'X ' . ($applicant->program_diminati ?? 'Baru'),
                    'program_name' => $applicant->program_diminati ?? ($applicant->jenjang ?? ''),
                    'status' => 'active',
                ]);

                if ($applicant->user_id) {
                    $applicant->user->update(['role' => 'student']);
                }

                foreach (['ayah', 'ibu'] as $parentType) {
                    $email = $applicant->{$parentType . '_email'};
                    $name = $applicant->{$parentType . '_name'};
                    if ($email && $name) {
                        $parent = User::where('email', $email)->first();

                        if (!$parent) {
                            $password = (string) random_int(10000000, 99999999);
                            $parent = User::create([
                                'name' => $name,
                                'full_name' => $name,
                                'email' => $email,
                                'password' => Hash::make($password),
                                'role' => 'parent',
                            ]);

                            Mail::to($email)->send(new ParentAccountMail(
                                parentName: $name,
                                parentEmail: $email,
                                password: $password,
                                studentName: $applicant->full_name,
                            ));
                        }

                        $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => $parentType === 'ayah' ? 'Ayah' : 'Ibu', 'is_primary' => $parentType === 'ayah']]);
                    }
                }

                AuditService::log('applicant.bulk-accept', 'Applicant', $applicant->id);
            } else {
                AuditService::log('applicant.bulk-status', 'Applicant', $applicant->id);
            }
        }

        return back()->with('success', count($applicants) . ' pendaftar berhasil diperbarui ke status ' . $validated['status'] . '.');
    }

    public function applicantDestroy(Applicant $applicant)
    {
        foreach ($applicant->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        AuditService::log('applicant.deleted', 'Applicant', $applicant->id);

        $applicant->delete();

        return response()->json(['success' => true, 'message' => 'Pendaftar berhasil dihapus.']);
    }
}
