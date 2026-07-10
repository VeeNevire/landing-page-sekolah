<?php

namespace App\Http\Controllers\Portal;

use App\Helpers\PortalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private function resolve(Request $request): ?array
    {
        $user = $request->user();
        $students = $user->students()->where('status', 'active')->get();

        if ($students->isEmpty()) {
            return null;
        }

        $studentId = $request->query('student_id', $students->first()->id);
        $selectedStudent = $students->firstWhere('id', $studentId) ?? $students->first();

        $demoStudents = PortalHelper::loadStudents();
        $demoStudent = [];
        foreach ($demoStudents as $ds) {
            if (($ds['nisn'] ?? '') === ($selectedStudent->nisn ?? '')) {
                $demoStudent = $ds;
                break;
            }
        }
        if (!$demoStudent && isset($demoStudents[0])) {
            $demoStudent = $demoStudents[0];
        }

        return [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'selectedStudentId' => $selectedStudent->id,
            'selectedStudentInitials' => strtoupper(substr($selectedStudent->full_name ?? 'S', 0, 1)),
            'demoStudent' => $demoStudent,
        ];
    }

    public function kehadiran(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.kehadiran', array_merge($data ?? [], ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S']));
        }

        $attendance = $data['demoStudent']['attendance'] ?? [];
        $total = ($attendance['present'] ?? 0) + ($attendance['sick'] ?? 0) + ($attendance['excused'] ?? 0) + ($attendance['unexcused'] ?? 0);
        $attendanceRate = $total > 0 ? round(($attendance['present'] ?? 0) / $total * 100, 1) : 0;

        return view('portal.kehadiran', array_merge($data, [
            'attendance' => $attendance,
            'attendanceRate' => $attendanceRate,
            'total' => $total,
        ]));
    }

    public function jadwal(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.jadwal', array_merge($data ?? [], ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S', 'schedule' => []]));
        }

        return view('portal.jadwal', array_merge($data, [
            'schedule' => $data['demoStudent']['schedule'] ?? [],
        ]));
    }

    public function tagihan(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.tagihan', array_merge($data ?? [], ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S', 'billing' => []]));
        }

        $billing = $data['demoStudent']['billing'] ?? [];
        $totalAmount = array_sum(array_column($billing, 'amount'));
        $paidAmount = array_sum(array_map(fn($b) => $b['amount'] ?? 0, array_filter($billing, fn($b) => $b['status'] === 'lunas')));
        $unpaidAmount = $totalAmount - $paidAmount;

        return view('portal.tagihan', array_merge($data, [
            'billing' => $billing,
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'unpaidAmount' => $unpaidAmount,
        ]));
    }

    public function profil(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.profil', array_merge($data ?? [], ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S']));
        }

        return view('portal.profil', $data);
    }

    public function notifikasi(Request $request)
    {
        $data = $this->resolve($request);
        if (!$data) {
            return view('portal.notifikasi', array_merge($data ?? [], ['students' => collect(), 'selectedStudent' => null, 'selectedStudentId' => null, 'selectedStudentInitials' => 'S', 'notifications' => []]));
        }

        return view('portal.notifikasi', array_merge($data, [
            'notifications' => $data['demoStudent']['notifications'] ?? [],
        ]));
    }
}
