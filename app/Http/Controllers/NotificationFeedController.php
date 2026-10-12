<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationFeedController extends Controller
{
    public static function visibleTo(User $user)
    {
        $studentIds = $user->role === 'parent'
            ? $user->students()->where('status', 'active')->select('students.id')
            : $user->studentProfile()->select('students.id');

        return Notification::whereIn('student_id', $studentIds);
    }

    public function index(Request $request)
    {
        $data = $request->validate(['after' => 'required|integer|min:0']);
        $query = self::visibleTo($request->user());
        $map = fn ($n) => ['id' => $n->id, 'title' => $n->title, 'body' => $n->body,
            'student_id' => $n->student_id, 'time' => $n->created_at->format('d-m-Y H:i').' WIB'];
        $new = (clone $query)->where('id', '>', $data['after'])->orderBy('id')->limit(50)->get();

        return response()->json([
            'cursor' => $new->last()?->id ?? $data['after'],
            'new' => $new->map($map),
            'recent' => $query->latest('id')->limit(10)->get()->map($map),
        ])->header('Cache-Control', 'no-store');
    }
}
