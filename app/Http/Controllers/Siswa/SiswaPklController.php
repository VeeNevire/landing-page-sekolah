<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\PklActivity;
use App\Models\PklPlacement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaPklController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->studentProfile;

        if (! $student || $student->status !== 'active') {
            return redirect()->route('siswa.dashboard')->with('error', 'Akun siswa tidak valid.');
        }

        $period = AcademicPeriod::where('is_active', true)->first();

        $placements = PklPlacement::where('student_id', $student->id)
            ->with('company', 'guruPembimbing')
            ->withCount(['activities', 'activities as approved_count' => fn ($q) => $q->where('status', 'approved')])
            ->orderByDesc('start_date')
            ->get();

        $activePlacement = $placements->firstWhere('status', 'active') ?? $placements->first();

        $activities = collect();
        if ($activePlacement) {
            $activities = PklActivity::where('placement_id', $activePlacement->id)
                ->with('approver')
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get();
        }

        $stats = [
            'total' => $activePlacement?->activities_count ?? 0,
            'approved' => $activePlacement?->approved_count ?? 0,
            'pending' => $activities->where('status', 'pending')->count(),
            'rejected' => $activities->where('status', 'rejected')->count(),
        ];

        return view('siswa.pkl', [
            'student' => $student,
            'period' => $period,
            'initials' => strtoupper(mb_substr($student->full_name ?? 'S', 0, 1)),
            'placements' => $placements,
            'activePlacement' => $activePlacement,
            'activities' => $activities,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $student = $user->studentProfile;

        if (! $student || $student->status !== 'active') {
            return back()->with('error', 'Akun siswa tidak valid.');
        }

        $placement = PklPlacement::where('student_id', $student->id)
            ->where('status', 'active')
            ->find($request->input('placement_id'));

        if (! $placement) {
            return back()->with('error', 'Anda belum memiliki penempatan PKL yang aktif.');
        }

        $validated = $request->validate([
            'placement_id' => 'required|integer',
            'tanggal' => [
                'required', 'date',
                'after_or_equal:'.$placement->start_date->format('Y-m-d'),
                'before_or_equal:'.now()->format('Y-m-d'),
            ],
            'aktivitas' => 'required|string|max:2000',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'tanggal.after_or_equal' => 'Tanggal aktivitas tidak boleh sebelum tanggal mulai PKL.',
            'tanggal.before_or_equal' => 'Tanggal aktivitas tidak boleh di masa depan.',
        ]);

        $fotoPath = null;
        $fotoName = null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoPath = $file->store('pkl/'.$placement->id, 'public');
            $fotoName = $file->getClientOriginalName();
        }

        PklActivity::create([
            'placement_id' => $placement->id,
            'tanggal' => $validated['tanggal'],
            'aktivitas' => $validated['aktivitas'],
            'keterangan' => $validated['keterangan'] ?? null,
            'foto_path' => $fotoPath,
            'foto_name' => $fotoName,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Aktivitas PKL berhasil dicatat dan menunggu persetujuan pembimbing.');
    }

    public function destroy(Request $request, PklActivity $activity)
    {
        $user = $request->user();
        $student = $user->studentProfile;

        abort_unless($student, 403);

        $placement = PklPlacement::where('student_id', $student->id)->find($activity->placement_id);
        abort_unless($placement, 403, 'Aktivitas bukan milik Anda.');

        if ($activity->status !== 'pending') {
            return back()->with('error', 'Aktivitas yang sudah diproses tidak dapat dihapus.');
        }

        if ($activity->foto_path) {
            Storage::disk('public')->delete($activity->foto_path);
        }

        $activity->delete();

        return back()->with('success', 'Aktivitas PKL berhasil dihapus.');
    }
}
