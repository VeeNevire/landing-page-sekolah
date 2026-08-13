<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PklActivity;
use App\Models\PklPlacement;
use App\Services\AuditService;
use Illuminate\Http\Request;

class GuruPklController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $placements = PklPlacement::where('guru_pembimbing_id', $user->id)
            ->with(['student', 'company'])
            ->withCount([
                'activities',
                'activities as pending_count' => fn ($q) => $q->where('status', 'pending'),
                'activities as approved_count' => fn ($q) => $q->where('status', 'approved'),
                'activities as rejected_count' => fn ($q) => $q->where('status', 'rejected'),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->sortBy(fn ($p) => $p->student?->full_name ?? '');

        $totalPending = $placements->sum('pending_count');
        $totalApproved = $placements->sum('approved_count');
        $totalRejected = $placements->sum('rejected_count');
        $totalActivities = $placements->sum('activities_count');

        return view('guru.pkl', [
            'placements' => $placements,
            'totals' => [
                'pending' => $totalPending,
                'approved' => $totalApproved,
                'rejected' => $totalRejected,
                'all' => $totalActivities,
            ],
        ]);
    }

    public function show(Request $request, PklPlacement $placement)
    {
        $this->authorizePlacement($request->user(), $placement);

        $activities = PklActivity::where('placement_id', $placement->id)
            ->with('approver')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $counts = [
            'pending' => $activities->where('status', 'pending')->count(),
            'approved' => $activities->where('status', 'approved')->count(),
            'rejected' => $activities->where('status', 'rejected')->count(),
        ];

        return view('guru.pkl-activities', [
            'placement' => $placement,
            'activities' => $activities,
            'counts' => $counts,
        ]);
    }

    public function approve(Request $request, PklActivity $activity)
    {
        $this->authorizeActivity($request->user(), $activity);

        if ($activity->status !== 'pending') {
            return back()->with('error', 'Aktivitas ini sudah diproses.');
        }

        $note = $request->input('catatan_pembimbing');

        $activity->update([
            'status' => 'approved',
            'catatan_pembimbing' => $note,
            'approved_by' => $request->user()->id,
            'responded_at' => now(),
        ]);

        $this->notify($activity, 'success', 'disetujui', $note);
        AuditService::log('pkl-activity.approve', 'PklActivity', $activity->id, $activity->placement?->student?->full_name, $request->user()->id);

        return back()->with('success', 'Aktivitas PKL '.($activity->placement?->student?->full_name ?? 'siswa').' disetujui.');
    }

    public function reject(Request $request, PklActivity $activity)
    {
        $this->authorizeActivity($request->user(), $activity);

        if ($activity->status !== 'pending') {
            return back()->with('error', 'Aktivitas ini sudah diproses.');
        }

        $note = $request->input('catatan_pembimbing');

        $activity->update([
            'status' => 'rejected',
            'catatan_pembimbing' => $note,
            'approved_by' => $request->user()->id,
            'responded_at' => now(),
        ]);

        $this->notify($activity, 'warning', 'ditolak', $note);
        AuditService::log('pkl-activity.reject', 'PklActivity', $activity->id, $activity->placement?->student?->full_name, $request->user()->id);

        return back()->with('success', 'Aktivitas PKL '.($activity->placement?->student?->full_name ?? 'siswa').' ditolak.');
    }

    private function authorizePlacement($user, PklPlacement $placement): void
    {
        abort_unless($placement->guru_pembimbing_id === $user->id, 403, 'Anda bukan pembimbing PKL siswa ini.');
    }

    private function authorizeActivity($user, PklActivity $activity): void
    {
        abort_unless($activity->placement?->guru_pembimbing_id === $user->id, 403, 'Anda bukan pembimbing PKL siswa ini.');
    }

    private function notify(PklActivity $activity, string $type, string $action, ?string $note): void
    {
        $studentId = $activity->placement?->student_id;
        if (! $studentId) {
            return;
        }

        $body = "Aktivitas PKL tanggal {$activity->tanggal->format('d M Y')} telah {$action}.";
        if ($note) {
            $body .= " Catatan pembimbing: {$note}";
        }

        Notification::create([
            'student_id' => $studentId,
            'type' => $type,
            'title' => "Aktivitas PKL {$action}",
            'body' => $body,
        ]);
    }
}
