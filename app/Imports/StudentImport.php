<?php

namespace App\Imports;

use App\Mail\ParentAccountMail;
use App\Mail\StudentAcceptedMail;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StudentImport
{
    protected array $header;
    protected array $rows;
    public int $imported = 0;
    public int $skipped = 0;
    public int $errors = 0;
    public array $errorDetails = [];

    public function __construct(protected string $filePath) {}

    public function load(): static
    {
        $contents = file($this->filePath);
        $raw = array_map('str_getcsv', $contents);
        $this->header = array_map('strtolower', array_shift($raw));
        $this->rows = $raw;
        return $this;
    }

    public function run(): static
    {
        $required = ['nisn', 'full_name', 'student_email'];

        foreach ($this->rows as $line => $row) {
            $row = array_pad($row, count($this->header), '');
            $data = array_combine($this->header, $row);

            try {
                DB::transaction(function () use ($data, $line, $required) {
                    $missing = array_filter($required, fn($f) => empty(trim($data[$f] ?? '')));
                    if ($missing) {
                        throw new \Exception('Kolom wajib kosong: ' . implode(', ', $missing));
                    }

                    $nisn = trim($data['nisn']);
                    $fullName = trim($data['full_name']);
                    $studentEmail = trim($data['student_email']);

                    if (Student::where('nisn', $nisn)->exists()) {
                        throw new \Exception("NISN $nisn sudah terdaftar");
                    }

                    if (User::where('email', $studentEmail)->exists()) {
                        throw new \Exception("Email $studentEmail sudah terdaftar");
                    }

                    $nis = $this->generateNis();

                    $password = bin2hex(random_bytes(4));
                    $user = User::create([
                        'name' => $fullName,
                        'full_name' => $fullName,
                        'email' => $studentEmail,
                        'password' => Hash::make($password),
                        'role' => 'student',
                        'is_active' => true,
                    ]);

                    $student = Student::create([
                        'user_id' => $user->id,
                        'nisn' => $nisn,
                        'nis' => $nis,
                        'full_name' => $fullName,
                        'birth_date' => !empty($data['birth_date']) ? trim($data['birth_date']) : null,
                        'class_name' => trim($data['class_name'] ?? $data['kelas'] ?? ''),
                        'program_name' => trim($data['program_name'] ?? $data['program'] ?? ''),
                        'status' => 'active',
                    ]);

                    AuditService::log('student.import', 'Student', $student->id, $fullName);

                    $parentName = trim($data['parent_name'] ?? '');
                    $parentEmail = trim($data['parent_email'] ?? '');
                    if ($parentName && $parentEmail) {
                        $parent = $this->findOrCreateParent($parentName, $parentEmail, $fullName);
                        $relation = trim($data['parent_relation'] ?? 'Orang Tua');
                        $student->parents()->syncWithoutDetaching([$parent->id => [
                            'relationship' => $relation,
                            'is_primary' => true,
                        ]]);
                    }

                    try {
                        Mail::to($studentEmail)->send(new StudentAcceptedMail(
                            studentName: $fullName,
                            className: $student->class_name ?: '-',
                            programName: $student->program_name ?: '-',
                            nis: $nis,
                            password: $password,
                        ));
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim email siswa $studentEmail: " . $e->getMessage());
                    }

                    $this->imported++;
                });
            } catch (\Exception $e) {
                $this->errors++;
                $this->errorDetails[] = "Baris " . ($line + 2) . ": " . $e->getMessage();
            }
        }

        return $this;
    }

    protected function generateNis(): string
    {
        $year = now()->format('Y');
        $lastNis = Student::where('nis', 'like', $year . '%')->max('nis');
        $next = $lastNis ? intval(substr($lastNis, -4)) + 1 : 1;
        return $year . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    protected function findOrCreateParent(string $name, string $email, string $studentName): User
    {
        $parent = User::where('email', $email)->first();
        if ($parent) return $parent;

        $password = bin2hex(random_bytes(4));
        $parent = User::create([
            'name' => $name,
            'full_name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'parent',
            'is_active' => true,
        ]);

        try {
            Mail::to($email)->send(new ParentAccountMail(
                parentName: $name,
                parentEmail: $email,
                password: $password,
                studentName: $studentName,
            ));
        } catch (\Exception $e) {
            Log::error("Gagal kirim email parent $email: " . $e->getMessage());
        }

        return $parent;
    }
}
