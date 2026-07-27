<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('students:sync-homeroom {--dry-run : Show what would be updated without saving}')]
#[Description('Sync all students homeroom_teacher_id with their kelas homeroom_teacher_id')]
class SyncStudentHomeroomTeacher extends Command
{
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $kelasWithWali = Kelas::whereNotNull('homeroom_teacher_id')
            ->withCount('students')
            ->get();

        if ($kelasWithWali->isEmpty()) {
            $this->info('Tidak ada kelas yang memiliki wali kelas.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$kelasWithWali->count()} kelas dengan wali kelas.");
        
        $totalUpdated = 0;
        $totalStudents = 0;

        $bar = $this->output->createProgressBar();
        $bar->start();

        foreach ($kelasWithWali as $kelas) {
            $students = Student::where('kelas_id', $kelas->id)
                ->where(function ($q) use ($kelas) {
                    $q->whereNull('homeroom_teacher_id')
                        ->orWhere('homeroom_teacher_id', '!=', $kelas->homeroom_teacher_id);
                });

            $count = $students->count();
            $totalStudents += $count;

            if ($count > 0) {
                if (!$dryRun) {
                    $students->update(['homeroom_teacher_id' => $kelas->homeroom_teacher_id]);
                }
                $totalUpdated += $count;
                $bar->setMessage(" {$kelas->nama_lengkap}: {$count} siswa");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->warn("[DRY RUN] Akan memperbarui {$totalUpdated} dari {$totalStudents} siswa.");
        } else {
            $this->info("Berhasil memperbarui {$totalUpdated} dari {$totalStudents} siswa.");
        }

        return self::SUCCESS;
    }
}