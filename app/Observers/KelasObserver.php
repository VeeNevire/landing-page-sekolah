<?php

namespace App\Observers;

use App\Models\Kelas;

class KelasObserver
{
    public function updated(Kelas $kelas): void
    {
        if ($kelas->isDirty('homeroom_teacher_id')) {
            $kelas->students()->update(['homeroom_teacher_id' => $kelas->homeroom_teacher_id]);
        }
    }
}