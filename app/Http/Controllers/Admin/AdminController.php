<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
            $query->where('role', $role);
        }
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
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

        User::create([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function usersEdit(User $user)
    {
        return view('admin.user-form', ['user' => $user]);
    }

    public function usersUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:parent,teacher,homeroom,admin,principal',
        ]);

        $user->update([
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function usersToggle(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function usersResetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', Password::min(6)],
        ]);

        $user->update(['password' => Hash::make($validated['new_password'])]);
        return back()->with('success', "Password {$user->name} berhasil direset.");
    }

    public function usersDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
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

        return view('admin.students', compact('students', 'classNames'));
    }

    public function studentsCreate()
    {
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        return view('admin.student-form', ['student' => null, 'teachers' => $teachers]);
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
        ]);

        Student::create($validated);
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
        ]);

        $student->update($validated);
        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function studentsDestroy(Student $student)
    {
        $student->delete();
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
        $subjects = Subject::orderBy('code')->paginate(15);
        return view('admin.subjects', compact('subjects'));
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:120',
            'kkm' => 'required|numeric|min:0|max:100',
        ]);

        Subject::create($validated);
        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:120',
            'kkm' => 'required|numeric|min:0|max:100',
        ]);

        $subject->update($validated);
        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function subjectsDestroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function periods(Request $request)
    {
        $periods = AcademicPeriod::withCount(['teachingAssignments', 'teacherNotes', 'behaviorScores'])->latest()->paginate(15);
        return view('admin.periods', compact('periods'));
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
        return back()->with('success', 'Periode akademik berhasil diperbarui.');
    }

    public function periodsDestroy(AcademicPeriod $period)
    {
        $period->delete();
        return back()->with('success', 'Periode akademik berhasil dihapus.');
    }

    public function periodsActivate(AcademicPeriod $period)
    {
        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        $period->update(['is_active' => true]);
        return back()->with('success', "Periode {$period->academic_year} {$period->semester} diaktifkan.");
    }

    public function teaching(Request $request)
    {
        $query = TeachingAssignment::with(['period', 'subject', 'teacher']);
        if ($periodId = $request->query('period')) {
            $query->where('period_id', $periodId);
        }
        $assignments = $query->latest()->paginate(15)->withQueryString();
        $periods = AcademicPeriod::orderByDesc('is_active')->orderByDesc('academic_year')->get();

        return view('admin.teaching', compact('assignments', 'periods'));
    }

    public function teachingCreate()
    {
        $periods = AcademicPeriod::orderByDesc('is_active')->get();
        $subjects = Subject::orderBy('code')->get();
        $teachers = User::whereIn('role', ['teacher', 'homeroom'])->orderBy('name')->get();
        $classNames = Student::distinct()->pluck('class_name')->sort()->values();

        return view('admin.teaching-form', compact('periods', 'subjects', 'teachers', 'classNames'));
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
            return back()->with('error', 'Penugasan ini sudah ada.');
        }

        TeachingAssignment::create($validated);
        return redirect()->route('admin.teaching.index')->with('success', 'Penugasan guru berhasil ditambahkan.');
    }

    public function teachingDestroy(TeachingAssignment $assignment)
    {
        $assignment->delete();
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

        return back()->with('success', 'Hubungan orang tua–siswa berhasil ditambahkan.');
    }

    public function parentStudentDestroy(Request $request)
    {
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

        $logs = $query->latest()->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('admin.audit', compact('logs', 'users'));
    }
}
