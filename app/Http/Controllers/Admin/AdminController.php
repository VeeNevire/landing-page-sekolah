<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Applicant;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Jurusan;
use App\Models\JurusanCustomSubject;
use App\Models\Kelas;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Mail\ParentAccountMail;
use App\Mail\StudentAcceptedMail;
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
            'student' => User::where('role', 'student')->count(),
            'guru' => User::whereIn('role', ['teacher', 'homeroom', 'principal'])->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'tabCounts'));
    }

    public function guru(Request $request)
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        $query = User::whereIn('role', ['teacher', 'homeroom', 'principal']);
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        $query->with(['teachingAssignments' => fn($q) => $q->where('period_id', $activePeriod?->id)])
            ->withCount('homeroomStudents');

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.guru', compact('users'));
    }

    public function guruData(User $user)
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        $user->load(['teachingAssignments' => fn($q) => $q->where('period_id', $activePeriod?->id)->with('subject')]);

        $classNames = $user->teachingAssignments->pluck('class_name')->unique();
        $studentsPerClass = [];
        foreach ($classNames as $class) {
            $count = \App\Models\Student::where('class_name', $class)->where('status', 'active')->count();
            $studentsPerClass[$class] = $count;
        }

        $subjects = $user->teachingAssignments
            ->pluck('subject')
            ->unique('id')
            ->values()
            ->map(fn($s) => ['id' => $s->id, 'code' => $s->code, 'name' => $s->name]);

        $homeroomKelas = \App\Models\Kelas::where('homeroom_teacher_id', $user->id)->first();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'subjects' => $subjects,
            'class_names' => $classNames->values(),
            'students_per_class' => $studentsPerClass,
            'homeroom' => $homeroomKelas ? [
                'nama_lengkap' => $homeroomKelas->nama_lengkap,
                'student_count' => $homeroomKelas->students()->where('status', 'active')->count(),
            ] : null,
        ]);
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
            'role' => 'required|in:parent,teacher,homeroom,admin,principal,student',
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
            'role' => 'required|in:parent,teacher,homeroom,admin,principal,student',
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::min(6)],
        ]);

        $data = [
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
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
        $query = Student::with('homeroomTeacher', 'jurusan', 'kelas');

        if ($jurusanId = $request->query('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }
        if ($kelasId = $request->query('kelas_id')) {
            $query->where('kelas_id', $kelasId);
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

        $students = $query->latest()->paginate(15)->withQueryString();

        $jurusans = Jurusan::where('is_active', true)->orderBy('nama')->get(['id', 'kode', 'nama']);
        $kelasList = Kelas::where('is_active', true)->orderBy('tingkat')->orderBy('nama')->get(['id', 'tingkat', 'nama', 'jurusan_id']);
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

        return view('admin.students', compact('students', 'jurusans', 'kelasList', 'teachers', 'tabCounts', 'applicants', 'applicantStatusCounts'));
    }

    public function studentsCreate()
    {
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        return view('admin.student-form', ['student' => null, 'teachers' => $teachers]);
    }

    public function studentData(Student $student)
    {
        $student->load('jurusan', 'kelas');
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
            'jurusan_id' => $student->jurusan_id,
            'kelas_id' => $student->kelas_id,
            'user_id' => $student->user_id,
            'student_email' => $student->user?->email,
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

    public function checkEmail(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return response()->json(['available' => true]);
        }

        $exists = User::where('email', $email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Email ini sudah terdaftar sebagai ' . User::where('email', $email)->value('role') . '.' : 'Email tersedia.',
        ]);
    }

    public function studentsStore(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'full_name' => 'required|string|max:150',
            'birth_date' => 'nullable|date',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'class_name' => 'nullable|string|max:80',
            'program_name' => 'nullable|string|max:120',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,graduated,inactive',
            'student_email' => 'required|email|unique:users,email',
            'parent_action' => 'required|in:existing,new,none',
            'parent_id' => 'required_if:parent_action,existing|nullable|exists:users,id',
            'parent_name' => 'required_if:parent_action,new|nullable|string|max:255',
            'parent_email' => 'required_if:parent_action,new|nullable|email|unique:users,email',
            'parent_password' => 'required_if:parent_action,new|nullable|min:6',
            'parent_relationship' => 'nullable|string|max:40',
        ]);
        $kelas = !empty($validated['kelas_id']) ? Kelas::with('jurusan')->find($validated['kelas_id']) : null;
        $student = Student::create([
            'nisn' => $validated['nisn'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'jurusan_id' => $validated['jurusan_id'] ?? $kelas?->jurusan_id,
            'kelas_id' => $validated['kelas_id'] ?? null,
            'class_name' => $validated['class_name'] ?? $kelas?->nama_lengkap ?? ($validated['full_name'] . ' (tanpa kelas)'),
            'program_name' => $validated['program_name'] ?? $kelas?->jurusan?->nama ?? '-',
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? $kelas?->homeroom_teacher_id,
            'status' => $validated['status'],
        ]);
        // Generate NIS
        $year = now()->format('Y');
        $lastNis = Student::where('nis', 'like', $year . '%')->max('nis');
        $nextNumber = $lastNis ? intval(substr($lastNis, -4)) + 1 : 1;
        $nis = $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Buat User akun student
        $password = (string) random_int(10000000, 99999999);
        $user = User::create([
            'name' => $validated['full_name'],
            'full_name' => $validated['full_name'],
            'email' => $validated['student_email'],
            'password' => Hash::make($password),
            'role' => 'student',
            'is_active' => true,
        ]);

        $student->update([
            'user_id' => $user->id,
            'nis' => $nis,
        ]);

        // Kirim email kredensial ke siswa
        Mail::to($user->email)->send(new StudentAcceptedMail(
            studentName: $validated['full_name'],
            className: $student->class_name,
            programName: $student->program_name,
            nis: $nis,
            password: $password,
        ));

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

                Mail::to($parent->email)->send(new ParentAccountMail(
                    parentName: $validated['parent_name'],
                    parentEmail: $parent->email,
                    password: $validated['parent_password'],
                    studentName: $validated['full_name'],
                ));
            }

            DB::table('parent_student')->insert([
                'parent_id' => $parentId,
                'student_id' => $student->id,
                'relationship' => $validated['parent_relationship'] ?? 'Orang Tua',
                'is_primary' => true,
            ]);
        }
        AuditService::log('student.create', 'Student', $student->id);

        return response()->json(['success' => true, 'message' => 'Siswa berhasil ditambahkan. Kredensial terkirim ke email ' . $validated['student_email'] . '.']);
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
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'kelas_id' => 'nullable|exists:kelas,id',
            'class_name' => 'nullable|string|max:80',
            'program_name' => 'nullable|string|max:120',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,graduated,inactive',
            'class_name' => 'nullable|string|max:80',
            'program_name' => 'nullable|string|max:120',
            'student_email' => 'nullable|email|unique:users,email,' . ($student->user_id ?? 'NULL'),
            'parent_action' => 'nullable|in:existing,new,none,disconnect',
            'parent_id' => 'required_if:parent_action,existing|nullable|exists:users,id',
            'parent_name' => 'required_if:parent_action,new|nullable|string|max:255',
            'parent_email' => 'required_if:parent_action,new|nullable|email|unique:users,email',
            'parent_password' => 'required_if:parent_action,new|nullable|min:6',
            'parent_relationship' => 'nullable|string|max:40',
            'disconnect_parent_id' => 'nullable|exists:users,id',
        ]);

        $kelas = !empty($validated['kelas_id']) ? Kelas::with('jurusan')->find($validated['kelas_id']) : null;

        $student->update([
            'nisn' => $validated['nisn'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'jurusan_id' => $validated['jurusan_id'] ?? $kelas?->jurusan_id ?? $student->jurusan_id,
            'kelas_id' => $validated['kelas_id'] ?? $student->kelas_id,
            'class_name' => $validated['class_name'] ?? $kelas?->nama_lengkap ?? $student->class_name ?? ($validated['full_name'] . ' (tanpa kelas)'),
            'program_name' => $validated['program_name'] ?? $kelas?->jurusan?->nama ?? $student->program_name ?? '-',
            'homeroom_teacher_id' => $validated['homeroom_teacher_id'] ?? $kelas?->homeroom_teacher_id ?? $student->homeroom_teacher_id,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['student_email']) && $student->user) {
            $student->user->update(['email' => $validated['student_email']]);
        }

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

        return response()->json(['success' => true, 'message' => 'Data siswa berhasil diperbarui.']);
    }

    public function studentsDestroy(Request $request, Student $student)
    {
        AuditService::log('student.delete', 'Student', $student->id);
        $student->delete();

        return response()->json(['success' => true, 'message' => 'Siswa berhasil dihapus.']);
    }

    public function studentResetPassword(Request $request, Student $student)
    {
        if (!$student->user) {
            return response()->json(['success' => false, 'message' => 'Siswa ini tidak memiliki akun pengguna.'], 400);
        }

        $password = (string) random_int(10000000, 99999999);
        $student->user->update(['password' => Hash::make($password)]);

        Mail::to($student->user->email)->send(new StudentAcceptedMail(
            studentName: $student->full_name,
            className: $student->class_name,
            programName: $student->program_name,
            nis: $student->nis,
            password: $password,
        ));

        AuditService::log('student.reset-password', 'Student', $student->id);

        return response()->json(['success' => true, 'message' => 'Password berhasil direset. Kredensial baru terkirim ke email ' . $student->user->email . '.']);

        return back()->with('success', 'Password berhasil direset. Kredensial baru terkirim ke email.');
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

        $subjects = $query->orderBy('code')->paginate(5)->withQueryString();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();

        $jurusans = \App\Models\Jurusan::with('customSubjects')
            ->orderBy('kode')
            ->get();

        return view('admin.subjects', compact('subjects', 'teachers', 'jurusans'));
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

    public function subjectDetail(Subject $subject)
    {
        $activePeriod = \App\Models\AcademicPeriod::where('is_active', true)->first();

        $subject->load('gurus');

        $assignedKelasIds = \App\Models\Kelas::whereHas('subjects', fn($q) => $q->where('subject_id', $subject->id))
            ->pluck('id');

        $allKelas = \App\Models\Kelas::where('is_active', true)
            ->with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get()
            ->groupBy(fn($k) => $k->jurusan?->nama ?? 'Tanpa Jurusan');

        $assignments = \App\Models\TeachingAssignment::where('subject_id', $subject->id)
            ->where('period_id', $activePeriod?->id)
            ->with('teacher')
            ->get()
            ->keyBy('class_name');

        return response()->json([
            'subject' => [
                'id' => $subject->id,
                'code' => $subject->code,
                'name' => $subject->name,
                'kkm' => $subject->kkm,
                'gurus' => $subject->gurus->map(fn($g) => ['id' => $g->id, 'name' => $g->full_name ?? $g->name]),
            ],
            'all_kelas' => $allKelas->map(fn($kelas, $jurusanNama) => [
                'jurusan' => $jurusanNama,
                'kelas' => $kelas->map(fn($k) => [
                    'id' => $k->id,
                    'nama_lengkap' => $k->nama_lengkap,
                    'assigned' => $assignedKelasIds->contains($k->id),
                    'teacher' => isset($assignments[$k->nama_lengkap]) ? [
                        'id' => $assignments[$k->nama_lengkap]->teacher->id,
                        'name' => $assignments[$k->nama_lengkap]->teacher->full_name ?? $assignments[$k->nama_lengkap]->teacher->name,
                    ] : null,
                ]),
            ])->values(),
        ]);
    }

    public function subjectAssignStore(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'kelas_ids' => 'nullable|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);

        $subject->kelas()->sync($validated['kelas_ids'] ?? []);

        AuditService::log('subject.assign', 'Subject', $subject->id);

        return response()->json(['success' => true, 'message' => 'Kelas berhasil diperbarui.']);
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

    public function jurusans(Request $request)
    {
        $query = Jurusan::withCount('kelas');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $jurusans = $query->latest()->paginate(12)->withQueryString();

        return view('admin.jurusans', compact('jurusans'));
    }

    public function jurusanData(Jurusan $jurusan)
    {
        return response()->json($jurusan->load('kelas'));
    }

    public function jurusansStore(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jurusans,kode',
            'nama' => 'required|string|max:120',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $jurusan = Jurusan::create($validated);

        if ($kelasData = $request->input('kelas')) {
            foreach ($kelasData as $item) {
                if (empty($item['tingkat']) || empty($item['nama'])) continue;

                $jurusan->kelas()->create([
                    'tingkat' => $item['tingkat'],
                    'nama' => $item['nama'],
                    'is_active' => true,
                ]);
            }
        }

        AuditService::log('jurusan.create', 'Jurusan', $jurusan->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jurusan berhasil ditambahkan.']);
        }

        return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function jurusansUpdate(Request $request, Jurusan $jurusan)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:jurusans,kode,' . $jurusan->id,
            'nama' => 'required|string|max:120',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $jurusan->update($validated);

        if ($kelasData = $request->input('kelas')) {
            $existingIds = $jurusan->kelas()->pluck('id')->toArray();
            $submittedIds = [];

            foreach ($kelasData as $item) {
                if (empty($item['tingkat']) || empty($item['nama'])) continue;

                if (!empty($item['id'])) {
                    $kelas = $jurusan->kelas()->find($item['id']);
                    if ($kelas) {
                        $kelas->update([
                            'tingkat' => $item['tingkat'],
                            'nama' => $item['nama'],
                        ]);
                        $submittedIds[] = $kelas->id;
                    }
                } else {
                    $kelas = $jurusan->kelas()->create([
                        'tingkat' => $item['tingkat'],
                        'nama' => $item['nama'],
                        'is_active' => true,
                    ]);
                    $submittedIds[] = $kelas->id;
                }
            }

            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                $jurusan->kelas()->whereIn('id', $toDelete)->delete();
            }
        }

        AuditService::log('jurusan.update', 'Jurusan', $jurusan->id);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jurusan berhasil diperbarui.']);
        }

        return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function jurusansDestroy(Request $request, Jurusan $jurusan)
    {
        AuditService::log('jurusan.delete', 'Jurusan', $jurusan->id);
        $jurusan->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jurusan berhasil dihapus.']);
        }

        return back()->with('success', 'Jurusan berhasil dihapus.');
    }

    public function jurusanDetail(Jurusan $jurusan)
    {
        $jurusan->load(['kelas' => function ($q) {
            $q->with(['subjects', 'customSubjects', 'homeroomTeacher']);
        }, 'subjects', 'customSubjects']);

        $allSubjects = Subject::orderBy('name')->get();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])
            ->orderBy('name')
            ->get(['id', 'name', 'full_name']);

        $activePeriod = \App\Models\AcademicPeriod::where('is_active', true)->first();

        // Pool guru per mapel — dari guru_mapel (yang udah di-link di admin/mapel)
        $subjectTeacherPool = [];
        foreach ($allSubjects as $subject) {
            $gurus = $subject->gurus()->get(['users.id', 'users.name', 'users.full_name']);
            $subjectTeacherPool[$subject->id] = $gurus->map(fn($g) => [
                'id' => $g->id,
                'name' => $g->full_name ?? $g->name,
            ]);
        }

        // TeachingAssignment existing per kelas
        $classNames = $jurusan->kelas->map->nama_lengkap;
        $existingAssignments = collect();
        if ($activePeriod && $classNames->isNotEmpty()) {
            $existingAssignments = TeachingAssignment::whereIn('class_name', $classNames)
                ->where('period_id', $activePeriod->id)
                ->get()
                ->groupBy('class_name');
        }

        if (request()->ajax()) {
            return response()->json([
                'jurusan' => $jurusan,
                'allSubjects' => $allSubjects,
                'teachers' => $teachers,
                'subject_teacher_pool' => $subjectTeacherPool,
                'existing_assignments' => $existingAssignments,
            ]);
        }

        return view('admin.jurusans-detail', compact('jurusan', 'allSubjects', 'teachers'));
    }

    public function jurusansSubjectsSave(Request $request, Jurusan $jurusan)
    {
        $kelasSubjects = $request->input('kelas_subjects', []);
        $kelasCustomSubjects = $request->input('kelas_custom_subjects', []);
        $homeroomTeachers = $request->input('homeroom_teachers', []);
        $subjectTeachers = $request->input('subject_teachers', []);

        $activePeriod = \App\Models\AcademicPeriod::where('is_active', true)->first();

        foreach ($jurusan->kelas as $kelas) {
            $kId = (string) $kelas->id;
            $classNamaLengkap = $kelas->nama_lengkap;

            if (isset($kelasSubjects[$kId])) {
                $kelas->subjects()->sync($kelasSubjects[$kId] ?? []);
            }
            if (isset($kelasCustomSubjects[$kId])) {
                $kelas->customSubjects()->sync($kelasCustomSubjects[$kId] ?? []);
            }
            if (array_key_exists($kId, $homeroomTeachers)) {
                $kelas->update(['homeroom_teacher_id' => $homeroomTeachers[$kId] ?: null]);
            }

            // Simpan teacher assignment per mapel (updateOrCreate biar data nilai gak ilang)
            if ($activePeriod && isset($subjectTeachers[$kId])) {
                foreach ($subjectTeachers[$kId] as $subjectId => $teacherId) {
                    if (!empty($teacherId)) {
                        TeachingAssignment::updateOrCreate(
                            [
                                'period_id' => $activePeriod->id,
                                'subject_id' => $subjectId,
                                'class_name' => $classNamaLengkap,
                            ],
                            ['teacher_id' => $teacherId]
                        );
                    }
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata pelajaran berhasil disimpan.']);
        }

        return back()->with('success', 'Mata pelajaran berhasil disimpan.');
    }

    public function jurusanCustomSubjectStore(Request $request, Jurusan $jurusan)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20',
            'nama' => 'required|string|max:120',
            'kkm' => 'nullable|numeric|min:0|max:100',
        ]);

        $subject = $jurusan->customSubjects()->create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata pelajaran jurusan berhasil ditambahkan.', 'subject' => $subject]);
        }

        return back()->with('success', 'Mata pelajaran jurusan berhasil ditambahkan.');
    }

    public function jurusanCustomSubjectDestroy(Request $request, JurusanCustomSubject $customSubject)
    {
        $customSubject->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata pelajaran jurusan berhasil dihapus.']);
        }

        return back()->with('success', 'Mata pelajaran jurusan berhasil dihapus.');
    }

    public function kelasByJurusan(Request $request, Jurusan $jurusan)
    {
        $kelas = $jurusan->kelas()
            ->with('homeroomTeacher')
            ->where('is_active', true)
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get(['id', 'tingkat', 'nama', 'homeroom_teacher_id']);

        $romawi = [10 => 'X', 11 => 'XI', 12 => 'XII'];

        $result = $kelas->map(fn($k) => [
            'id' => $k->id,
            'nama_lengkap' => ($romawi[$k->tingkat] ?? $k->tingkat) . ' ' . $k->nama,
            'homeroom_teacher_id' => $k->homeroom_teacher_id,
            'wali_nama' => $k->homeroomTeacher?->full_name ?: $k->homeroomTeacher?->name ?: null,
        ]);

        return response()->json($result);
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
