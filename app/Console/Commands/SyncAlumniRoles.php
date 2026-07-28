<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('alumni:sync-roles')]
#[Description('Sync alumni roles - update graduated students to alumni role')]
class SyncAlumniRoles extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing alumni roles...');

        $graduatedStudents = Student::where('status', 'graduated')
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', 'alumni');
            })
            ->with('user')
            ->get();

        if ($graduatedStudents->isEmpty()) {
            $this->info('No students to sync. All graduated students already have alumni role.');
            return Command::SUCCESS;
        }

        $this->info("Found {$graduatedStudents->count()} graduated students with non-alumni role.");

        $bar = $this->output->createProgressBar($graduatedStudents->count());
        $bar->start();

        $updated = 0;
        foreach ($graduatedStudents as $student) {
            if ($student->user) {
                $student->user->update(['role' => 'alumni']);
                $updated++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully updated {$updated} users to alumni role.");

        return Command::SUCCESS;
    }
}
