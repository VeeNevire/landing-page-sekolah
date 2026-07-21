<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\Student;
use App\Models\Submission;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function materi(Request $request, Material $material)
    {
        $user = $request->user();

        if (!$material->file_path) {
            abort(404, 'File tidak ditemukan.');
        }

        $ta = $material->teachingAssignment;

        $isTeacher = $user->teachingAssignments()->where('id', $ta->id)->exists();
        $isStudent = Student::where('user_id', $user->id)
            ->where('class_name', $ta->class_name)
            ->exists();

        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($material->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $filename = $material->file_name ?? basename($material->file_path);

        return Storage::disk('public')->download($material->file_path, $filename);
    }

    public function preview(Request $request, Material $material)
    {
        $user = $request->user();

        if (!$material->file_path) {
            abort(404);
        }

        $ta = $material->teachingAssignment;

        $isTeacher = $user->teachingAssignments()->where('id', $ta->id)->exists();
        $isStudent = Student::where('user_id', $user->id)
            ->where('class_name', $ta->class_name)
            ->exists();

        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($material->file_path)) {
            abort(404);
        }

        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        $ext = strtolower(pathinfo($material->file_name ?? '', PATHINFO_EXTENSION));

        if (in_array($ext, $previewableTypes)) {
            return response()->file(Storage::disk('public')->path($material->file_path));
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }

    public function assignment(Request $request, Assignment $assignment)
    {
        $user = $request->user();

        if (!$assignment->attachment_path) {
            abort(404);
        }

        $ta = $assignment->teachingAssignment;

        $isTeacher = $user->teachingAssignments()->where('id', $ta->id)->exists();
        $isStudent = Student::where('user_id', $user->id)
            ->where('class_name', $ta->class_name)
            ->exists();

        if (!$isTeacher && !$isStudent) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($assignment->attachment_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($assignment->attachment_path, $assignment->attachment_name);
    }

    public function submission(Request $request, Submission $submission)
    {
        $user = $request->user();

        if (!$submission->file_path) {
            abort(404);
        }

        $assignment = $submission->assignment;
        $ta = $assignment->teachingAssignment;

        $isTeacher = $user->teachingAssignments()->where('id', $ta->id)->exists();
        $isOwner = Student::where('user_id', $user->id)
            ->where('id', $submission->student_id)
            ->exists();

        if (!$isTeacher && !$isOwner) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($submission->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($submission->file_path, $submission->file_name);
    }
}
