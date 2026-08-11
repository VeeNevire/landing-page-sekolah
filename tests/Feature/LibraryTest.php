<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function studentUser(): User
    {
        return User::factory()->create(['role' => 'student']);
    }

    public function test_guest_can_view_public_catalog(): void
    {
        Book::create([
            'title' => 'Buku Publik',
            'author' => 'Penulis A',
            'category' => 'Sains',
            'stock' => 1,
            'is_active' => true,
        ]);

        Book::create([
            'title' => 'Buku Tersembunyi',
            'is_active' => false,
        ]);

        $response = $this->get(route('perpustakaan'));

        $response->assertOk();
        $response->assertSee('Buku Publik');
        $response->assertDontSee('Buku Tersembunyi');
    }

    public function test_admin_can_view_library_index(): void
    {
        Book::create(['title' => 'Buku Admin', 'author' => 'Penulis B', 'category' => 'Fiksi']);

        $response = $this->actingAs($this->admin())
            ->get(route('admin.perpustakaan.index'));

        $response->assertOk();
        $response->assertSee('Buku Admin');
    }

    public function test_admin_can_store_book_with_file(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->post(route('admin.perpustakaan.store'), [
                'title' => 'Buku Ebook',
                'author' => 'Penulis',
                'category' => 'Teknologi',
                'year' => '2026',
                'stock' => 3,
                'is_active' => 1,
                'cover' => UploadedFile::fake()->image('cover.jpg'),
                'file' => UploadedFile::fake()->create('ebook.pdf', 100, 'application/pdf'),
            ]);

        $response->assertOk();

        $book = Book::where('title', 'Buku Ebook')->first();
        $this->assertNotNull($book);
        $this->assertNotNull($book->file_path);
        Storage::disk('public')->assertExists($book->file_path);
        Storage::disk('public')->assertExists($book->cover_path);
    }

    public function test_non_student_cannot_download_ebook_file(): void
    {
        Storage::fake('public');

        $book = Book::create([
            'title' => 'Rahasia',
            'is_active' => true,
            'file_path' => 'perpustakaan/files/rahasia.pdf',
            'file_name' => 'rahasia.pdf',
            'file_size' => 100,
        ]);

        Storage::disk('public')->put('perpustakaan/files/rahasia.pdf', 'content');

        $this->get(route('perpustakaan.unduh', $book))->assertRedirect(route('login'));
        $this->actingAs($this->admin())->get(route('perpustakaan.unduh', $book))->assertRedirect(route('admin.dashboard'));
    }

    public function test_student_can_download_ebook_file(): void
    {
        Storage::fake('public');

        $book = Book::create([
            'title' => 'Ebook Siswa',
            'is_active' => true,
            'file_path' => 'perpustakaan/files/ebook.pdf',
            'file_name' => 'ebook.pdf',
            'file_size' => 100,
            'file_type' => 'pdf',
        ]);

        Storage::disk('public')->put('perpustakaan/files/ebook.pdf', '%PDF-1.4');

        $student = $this->studentUser();

        $this->actingAs($student)
            ->get(route('perpustakaan.unduh', $book))
            ->assertOk();
    }

    public function test_admin_can_import_multiple_pdfs_at_once(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->post(route('admin.perpustakaan.import'), [
                'import_files' => [
                    UploadedFile::fake()->create('matematika_dasar.pdf', 200, 'application/pdf'),
                    UploadedFile::fake()->create('belajar_laravel.pdf', 150, 'application/pdf'),
                ],
            ]);

        $response->assertOk();
        $response->assertJson(['imported' => 2]);

        $this->assertSame(2, Book::count());
        $book = Book::where('title', 'Matematika Dasar')->first();
        $this->assertNotNull($book);
        $this->assertNull($book->cover_path);
        $this->assertTrue($book->is_active);
        Storage::disk('public')->assertExists($book->file_path);
    }

    public function test_import_skips_invalid_non_pdf(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->post(route('admin.perpustakaan.import'), [
                'import_files' => [
                    UploadedFile::fake()->create('buku_ok.pdf', 100, 'application/pdf'),
                    UploadedFile::fake()->create('gambar.png', 100, 'image/png'),
                ],
            ]);

        $response->assertOk();
        $response->assertJson(['imported' => 1, 'failed' => 1]);

        $this->assertSame(1, Book::count());
        $this->assertNotNull(Book::where('title', 'Buku Ok')->first());
    }

    public function test_book_without_cover_renders_auto_cover(): void
    {
        Book::create([
            'title' => 'Buku Tanpa Cover',
            'author' => 'Penulis',
            'category' => 'Kategori',
            'is_active' => true,
        ]);

        $response = $this->get(route('perpustakaan'));

        $response->assertOk();
        $response->assertSee('Buku Tanpa Cover');
        $response->assertSee('bcover-auto');
    }
}