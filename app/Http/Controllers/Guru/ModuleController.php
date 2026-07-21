<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\TeachingAssignment;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $ta = TeachingAssignment::findOrFail($validated['teaching_assignment_id']);

        if (!$user->teachingAssignments()->where('id', $ta->id)->exists()) {
            abort(403);
        }

        $maxOrder = CourseModule::where('teaching_assignment_id', $ta->id)->max('order') ?? -1;

        $module = CourseModule::create([
            'teaching_assignment_id' => $ta->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        AuditService::log('module.create', 'CourseModule', $module->id);

        return response()->json([
            'success' => true,
            'message' => 'Module berhasil dibuat.',
            'module' => $module,
        ]);
    }

    public function update(Request $request, CourseModule $module)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $module->teaching_assignment_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $module->update($validated);
        AuditService::log('module.update', 'CourseModule', $module->id);

        return response()->json([
            'success' => true,
            'message' => 'Module berhasil diperbarui.',
            'module' => $module->fresh(),
        ]);
    }

    public function destroy(Request $request, CourseModule $module)
    {
        $user = $request->user();
        if (!$user->teachingAssignments()->where('id', $module->teaching_assignment_id)->exists()) {
            abort(403);
        }

        AuditService::log('module.delete', 'CourseModule', $module->id);
        $module->delete();

        return response()->json([
            'success' => true,
            'message' => 'Module berhasil dihapus.',
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:course_modules,id',
            'modules.*.order' => 'required|integer|min:0',
        ]);

        $user = $request->user();

        foreach ($request->modules as $item) {
            $module = CourseModule::findOrFail($item['id']);
            if (!$user->teachingAssignments()->where('id', $module->teaching_assignment_id)->exists()) {
                abort(403);
            }
            $module->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan module berhasil diperbarui.',
        ]);
    }

    public function reorderMaterials(Request $request)
    {
        $request->validate([
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materials,id',
            'materials.*.order' => 'required|integer|min:0',
        ]);

        $user = $request->user();

        foreach ($request->materials as $item) {
            $material = \App\Models\Material::findOrFail($item['id']);
            if (!$user->teachingAssignments()->where('id', $material->teaching_assignment_id)->exists()) {
                abort(403);
            }
            $material->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan materi berhasil diperbarui.',
        ]);
    }

    public function data(Request $request)
    {
        $request->validate([
            'teaching_assignment_id' => 'required|exists:teaching_assignments,id',
        ]);

        $user = $request->user();
        $ta = TeachingAssignment::findOrFail($request->teaching_assignment_id);

        if (!$user->teachingAssignments()->where('id', $ta->id)->exists()) {
            abort(403);
        }

        $modules = $ta->modules()->with(['materials' => function ($q) {
            $q->orderBy('order');
        }])->get();

        return response()->json([
            'success' => true,
            'modules' => $modules,
        ]);
    }
}
