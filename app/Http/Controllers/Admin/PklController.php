<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PklCompany;
use App\Models\PklPlacement;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PklController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'companies');
        $search = $request->query('search');
        $status = $request->query('status');

        $companies = collect();
        if ($tab === 'companies') {
            $query = PklCompany::withCount('placements');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('bidang', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%")
                        ->orWhere('kontak_person', 'like', "%{$search}%");
                });
            }
            $companies = $query->latest()->paginate(15)->withQueryString();
        }

        $placements = collect();
        if ($tab === 'placements') {
            $query = PklPlacement::with(['student', 'company', 'guruPembimbing'])
                ->withCount('activities');

            if ($status) {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', fn ($sq) => $sq->where('full_name', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"))
                        ->orWhereHas('company', fn ($cq) => $cq->where('nama', 'like', "%{$search}%"));
                });
            }

            $placements = $query->latest()->paginate(15)->withQueryString();
        }

        $companyTotal = PklCompany::count();
        $placementTotal = PklPlacement::count();

        $allCompanies = PklCompany::orderBy('nama')->get();
        $students = Student::where('status', 'active')->orderBy('full_name')->get();
        $teachers = User::whereIn('role', ['teacher', 'homeroom', 'principal'])->orderBy('full_name')->get();

        return view('admin.pkl', compact(
            'tab', 'companies', 'placements', 'companyTotal', 'placementTotal',
            'allCompanies', 'students', 'teachers'
        ));
    }

    public function companyData(PklCompany $company)
    {
        return response()->json($company);
    }

    public function companiesStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bidang' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:100',
            'kontak_person' => 'nullable|string|max:255',
            'kontak_telepon' => 'nullable|string|max:30',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $company = PklCompany::create($validated);
        AuditService::log('pkl-company.create', 'PklCompany', $company->id, $company->nama);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Perusahaan PKL berhasil ditambahkan.']);
        }

        return back()->with('success', 'Perusahaan PKL berhasil ditambahkan.');
    }

    public function companiesUpdate(Request $request, PklCompany $company)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bidang' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:100',
            'kontak_person' => 'nullable|string|max:255',
            'kontak_telepon' => 'nullable|string|max:30',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $company->update($validated);
        AuditService::log('pkl-company.update', 'PklCompany', $company->id, $company->nama);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Perusahaan PKL berhasil diperbarui.']);
        }

        return back()->with('success', 'Perusahaan PKL berhasil diperbarui.');
    }

    public function companiesDestroy(Request $request, PklCompany $company)
    {
        if ($company->placements()->count() > 0) {
            $message = 'Perusahaan ini masih memiliki penempatan PKL. Hapus atau pindahkan penempatan terlebih dahulu.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }

            return back()->with('error', $message);
        }

        AuditService::log('pkl-company.delete', 'PklCompany', $company->id, $company->nama);
        $company->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Perusahaan PKL berhasil dihapus.']);
        }

        return back()->with('success', 'Perusahaan PKL berhasil dihapus.');
    }

    public function placementData(PklPlacement $placement)
    {
        return response()->json([
            'id' => $placement->id,
            'student_id' => $placement->student_id,
            'company_id' => $placement->company_id,
            'guru_pembimbing_id' => $placement->guru_pembimbing_id,
            'start_date' => $placement->start_date->format('Y-m-d'),
            'end_date' => $placement->end_date->format('Y-m-d'),
            'status' => $placement->status,
            'catatan' => $placement->catatan,
            'activities_count' => $placement->activities_count,
        ]);
    }

    public function studentsData()
    {
        $activePlacementStudentIds = PklPlacement::active()->pluck('student_id')->all();

        $students = Student::where('status', 'active')
            ->orderBy('class_name')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'nisn', 'class_name', 'program_name'])
            ->map(fn ($student) => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'nisn' => $student->nisn,
                'class_name' => $student->class_name,
                'program_name' => $student->program_name,
                'has_active_placement' => in_array($student->id, $activePlacementStudentIds),
            ]);

        return response()->json([
            'students' => $students,
            'programs' => $students->pluck('program_name')->filter()->unique()->sort()->values(),
            'classes' => $students->pluck('class_name')->filter()->unique()->sort()->values(),
        ]);
    }

    public function placementsStore(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'company_id' => 'required|exists:pkl_companies,id',
            'guru_pembimbing_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,selesai,batal',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $studentIds = array_values(array_unique($validated['student_ids']));
        $alreadyPlaced = [];

        if ($validated['status'] === 'active') {
            $existing = PklPlacement::active()->whereIn('student_id', $studentIds)->pluck('student_id')->all();
            $alreadyPlaced = array_values(array_intersect($studentIds, $existing));
            $studentIds = array_values(array_diff($studentIds, $existing));
        }

        if (empty($studentIds)) {
            $message = 'Semua siswa terpilih sudah memiliki penempatan PKL yang aktif.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }

            return back()->with('error', $message);
        }

        $created = DB::transaction(function () use ($validated, $studentIds) {
            $count = 0;
            foreach ($studentIds as $studentId) {
                PklPlacement::create(array_merge($validated, ['student_id' => $studentId]));
                $count++;
            }

            return $count;
        });

        AuditService::log('pkl-placement.create.batch', 'PklPlacement', null, $created.' siswa');

        $skipped = count($alreadyPlaced);
        $message = $created.' penempatan PKL berhasil ditambahkan.';
        if ($skipped > 0) {
            $message .= ' '.$skipped.' siswa dilewati karena sudah memiliki penempatan aktif.';
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function placementsUpdate(Request $request, PklPlacement $placement)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'company_id' => 'required|exists:pkl_companies,id',
            'guru_pembimbing_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,selesai,batal',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $existing = PklPlacement::where('student_id', $validated['student_id'])
            ->where('id', '!=', $placement->id)
            ->whereIn('status', ['active'])
            ->exists();

        if ($existing) {
            $message = 'Siswa ini sudah memiliki penempatan PKL yang aktif.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }

            return back()->with('error', $message);
        }

        $placement->update($validated);
        AuditService::log('pkl-placement.update', 'PklPlacement', $placement->id, $placement->student?->full_name);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penempatan PKL berhasil diperbarui.']);
        }

        return back()->with('success', 'Penempatan PKL berhasil diperbarui.');
    }

    public function placementsDestroy(Request $request, PklPlacement $placement)
    {
        if ($placement->activities()->count() > 0) {
            $message = 'Penempatan ini sudah memiliki catatan aktivitas. Hapus aktivitas terlebih dahulu.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }

            return back()->with('error', $message);
        }

        AuditService::log('pkl-placement.delete', 'PklPlacement', $placement->id, $placement->student?->full_name);
        $placement->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Penempatan PKL berhasil dihapus.']);
        }

        return back()->with('success', 'Penempatan PKL berhasil dihapus.');
    }
}
