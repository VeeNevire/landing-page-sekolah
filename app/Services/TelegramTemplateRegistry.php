<?php

namespace App\Services;

class TelegramTemplateRegistry
{
    public function for(string $reportType): array
    {
        return match ($reportType) {
            'grade' => $this->grade(),
            'exam_plan' => $this->examPlan(),
            'attendance' => $this->attendance(),
            default => [],
        };
    }

    public function allKeys(string $reportType): array
    {
        return array_column($this->for($reportType), 'key');
    }

    private function grade(): array
    {
        return [
            ['key' => 'nama_siswa', 'label' => 'Nama Siswa', 'example' => 'Budi Santoso'],
            ['key' => 'mata_pelajaran', 'label' => 'Mata Pelajaran', 'example' => 'Matematika'],
            ['key' => 'penilaian', 'label' => 'Nama Penilaian', 'example' => 'Ulangan Harian 1'],
            ['key' => 'nilai', 'label' => 'Nilai', 'example' => '85'],
            ['key' => 'nilai_maksimal', 'label' => 'Nilai Maksimal', 'example' => '100'],
            ['key' => 'kkm', 'label' => 'KKM', 'example' => '75'],
            ['key' => 'status', 'label' => 'Status Nilai', 'example' => 'Tuntas'],
            ['key' => 'guru', 'label' => 'Nama Guru', 'example' => 'Ibu Sari'],
            ['key' => 'tanggal', 'label' => 'Tanggal', 'example' => '04-09-2026'],
            ['key' => 'catatan', 'label' => 'Catatan Guru', 'example' => '-'],
            ['key' => 'link_detail', 'label' => 'Link Detail InvestaSchool', 'example' => 'https://sekolah.test/portal/laporan'],
            ['key' => 'nama_sekolah', 'label' => 'Nama Sekolah', 'example' => config('school.name', config('app.name'))],
        ];
    }

    private function examPlan(): array
    {
        return [
            ['key' => 'nama_siswa', 'label' => 'Nama Siswa', 'example' => 'Budi Santoso'],
            ['key' => 'mata_pelajaran', 'label' => 'Mata Pelajaran', 'example' => 'Matematika'],
            ['key' => 'penilaian', 'label' => 'Nama Penilaian', 'example' => 'Penilaian Tengah Semester'],
            ['key' => 'tanggal', 'label' => 'Tanggal', 'example' => '04-09-2026'],
            ['key' => 'guru', 'label' => 'Nama Guru', 'example' => 'Ibu Sari'],
            ['key' => 'catatan', 'label' => 'Catatan', 'example' => '-'],
            ['key' => 'link_detail', 'label' => 'Link Detail InvestaSchool', 'example' => 'https://sekolah.test/portal/laporan'],
            ['key' => 'nama_sekolah', 'label' => 'Nama Sekolah', 'example' => config('school.name', config('app.name'))],
        ];
    }

    private function attendance(): array
    {
        return [
            ['key' => 'nama_siswa', 'label' => 'Nama Siswa', 'example' => 'Budi Santoso'],
            ['key' => 'tanggal', 'label' => 'Tanggal', 'example' => '04-09-2026'],
            ['key' => 'status', 'label' => 'Status Kehadiran', 'example' => 'Hadir'],
            ['key' => 'catatan', 'label' => 'Catatan', 'example' => '-'],
            ['key' => 'guru', 'label' => 'Nama Guru', 'example' => 'Ibu Sari'],
            ['key' => 'link_detail', 'label' => 'Link Detail InvestaSchool', 'example' => 'https://sekolah.test/portal/kehadiran'],
            ['key' => 'nama_sekolah', 'label' => 'Nama Sekolah', 'example' => config('school.name', config('app.name'))],
        ];
    }
}
