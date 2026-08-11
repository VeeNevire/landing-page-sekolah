<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::published();

        if ($search = trim($request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category = trim($request->query('category', ''))) {
            $query->where('category', $category);
        }

        $books = $query->latest()->paginate(12)->withQueryString();

        $categories = Book::published()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('perpustakaan', [
            'books' => $books,
            'categories' => $categories,
            'isStudent' => auth()->check() && auth()->user()->role === 'student',
        ]);
    }

    public function baca(Book $book)
    {
        $this->authorizeAccess($book);

        $ext = strtolower(pathinfo($book->file_name ?? '', PATHINFO_EXTENSION));
        $inline = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);

        return $inline
            ? response()->file(Storage::disk('public')->path($book->file_path))
            : Storage::disk('public')->download($book->file_path, $book->file_name);
    }

    public function unduh(Book $book)
    {
        $this->authorizeAccess($book);

        return Storage::disk('public')->download($book->file_path, $book->file_name);
    }

    private function authorizeAccess(Book $book): void
    {
        if (!$book->file_path) {
            abort(404, 'File tidak ditemukan.');
        }

        if (!auth()->check() || auth()->user()->role !== 'student') {
            abort(403, 'File hanya dapat diakses oleh siswa.');
        }

        if (!Storage::disk('public')->exists($book->file_path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }
    }
}