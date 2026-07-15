<?php

namespace App\Http\Controllers;

use App\Mail\ParentAccountMail;
use App\Mail\StudentAcceptedMail;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class PPDBController extends Controller
{
    public function start()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role !== 'applicant') {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return view('ppdb.start');
            }
            
            $applicant = $user->applicant;
            
            if (!$applicant) {
                return redirect()->route('ppdb.form', ['step' => 1]);
            }
            
            return match($applicant->status) {
                'paid' => redirect()->route('ppdb.success'),
                'rejected' => redirect()->route('ppdb.status'),
                'verified' => redirect()->route('ppdb.payment'),
                'submitted' => redirect()->route('ppdb.status'),
                default => match($applicant->completion_step) {
                    'completed' => redirect()->route('ppdb.status'),
                    'documents' => redirect()->route('ppdb.upload'),
                    'parent_data' => redirect()->route('ppdb.form', ['step' => 3]),
                    'student_data' => redirect()->route('ppdb.form', ['step' => 2]),
                    default => redirect()->route('ppdb.form', ['step' => 1]),
                },
            };
        }
        
        return view('ppdb.start');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('ppdb.start')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'applicant',
                ]);
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'applicant',
                ]);
            }
        } else {
            $user->update([
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        if (!$user->applicant) {
            Applicant::create([
                'user_id' => $user->id,
                'full_name' => $user->full_name ?? $user->name,
            ]);
        }

        Auth::login($user);

        $user->load('applicant');

        $applicant = $user->applicant;

        return match($applicant->status) {
            'paid' => redirect()->route('ppdb.success'),
            'rejected' => redirect()->route('ppdb.status'),
            'verified' => redirect()->route('ppdb.payment'),
            'submitted' => redirect()->route('ppdb.status'),
            default => match($applicant->completion_step) {
                'completed' => redirect()->route('ppdb.status'),
                'documents' => redirect()->route('ppdb.upload'),
                'parent_data' => redirect()->route('ppdb.form', ['step' => 3]),
                'student_data' => redirect()->route('ppdb.form', ['step' => 2]),
                default => redirect()->route('ppdb.form', ['step' => 1]),
            },
        };
    }

    public function manualRegisterStore(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::create([
            'name' => $request->full_name,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'applicant',
            'google_id' => null,
            'avatar' => null,
        ]);

        Applicant::create([
            'user_id' => $user->id,
            'full_name' => $user->full_name,
        ]);

        Auth::login($user);

        return redirect()->route('ppdb.form', ['step' => 1])
            ->with('success', 'Registrasi berhasil! Silakan lengkapi data pendaftaran.');
    }

    public function showForm(Request $request)
    {
        $applicant = Auth::user()->applicant;

        if (!$applicant) {
            $applicant = Applicant::create([
                'user_id' => Auth::id(),
                'full_name' => Auth::user()->full_name ?? Auth::user()->name,
            ]);
        }

        $currentStep = (int) ($request->query('step') ?? 1);

        return view('ppdb.form', compact('applicant', 'currentStep'));
    }

    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'nickname' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string|max:60',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:L,P',
            'religion' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'asal_sekolah' => 'nullable|string|max:150',
            'nisn' => 'nullable|string|max:20',
        ]);

        $applicant = Auth::user()->applicant;
        $validated['completion_step'] = 'student_data';
        $applicant->update($validated);

        return response()->json(['success' => true, 'next_step' => 2]);
    }

    public function saveStep2(Request $request)
    {
        $validated = $request->validate([
            'jenjang' => 'nullable|in:SMA,SMK',
            'program_diminati' => 'nullable|string|max:120',
            'ayah_name' => 'nullable|string|max:100',
            'ayah_occupation' => 'nullable|string|max:100',
            'ayah_phone' => 'nullable|string|max:20',
            'ayah_email' => 'required|email|max:100',
            'ibu_name' => 'nullable|string|max:100',
            'ibu_occupation' => 'nullable|string|max:100',
            'ibu_phone' => 'nullable|string|max:20',
            'wali_name' => 'nullable|string|max:100',
            'wali_occupation' => 'nullable|string|max:100',
            'wali_phone' => 'nullable|string|max:20',
            'wali_email' => 'nullable|email|max:100',
        ]);

        $applicant = Auth::user()->applicant;
        $validated['completion_step'] = 'parent_data';
        $applicant->update($validated);

        return response()->json(['success' => true, 'next_step' => 3]);
    }

    public function uploadPage()
    {
        $applicant = Auth::user()->applicant;

        if (!$applicant) {
            $applicant = Applicant::create([
                'user_id' => Auth::id(),
                'full_name' => Auth::user()->full_name ?? Auth::user()->name,
                'completion_step' => 'not_started',
            ]);
        }

        $documents = $applicant->documents->keyBy('document_type');

        $requiredDocs = [
            'ijazah' => 'Ijazah / STTB',
            'rapor' => 'Rapor Semester 1-5',
            'kk' => 'Kartu Keluarga',
            'akta' => 'Akta Kelahiran',
            'foto' => 'Pas Foto 3x4',
        ];

        return view('ppdb.upload', compact('applicant', 'documents', 'requiredDocs'));
    }

    public function uploadStore(Request $request)
    {
        try {
            $request->validate([
                'document_type' => 'required|in:ijazah,rapor,kk,akta,foto,sertifikat',
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $applicant = Auth::user()->applicant;
            $file = $request->file('file');
            $type = $request->document_type;

            $existing = ApplicantDocument::where('applicant_id', $applicant->id)
                ->where('document_type', $type)
                ->first();

            if ($existing) {
                Storage::disk('public')->delete($existing->file_path);
                $existing->delete();
            }

            $path = $file->store("ppdb-documents/{$applicant->id}", 'public');

            $document = ApplicantDocument::create([
                'applicant_id' => $applicant->id,
                'document_type' => $type,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'uploaded_at' => now(),
            ]);

            $applicant->update(['completion_step' => 'documents']);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diupload.',
                    'document' => [
                        'id' => $document->id,
                        'document_type' => $document->document_type,
                        'file_name' => $document->file_name,
                        'file_path' => Storage::disk('public')->url($document->file_path),
                        'file_size' => $document->file_size,
                        'uploaded_at' => $document->uploaded_at->format('d M Y H:i'),
                    ]
                ]);
            }

            return back()->with('success', 'Dokumen berhasil diupload.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(' ', $e->validator->errors()->all()),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat upload: ' . $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Terjadi kesalahan saat upload.');
        }
    }

    public function uploadDestroy(ApplicantDocument $document)
    {
        if ($document->applicant_id !== Auth::user()->applicant->id) {
            abort(403);
        }

        $documentType = $document->document_type;
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus.',
                'document_type' => $documentType,
            ]);
        }

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function finalSubmit(Request $request)
    {
        $applicant = Auth::user()->applicant;

        $requiredDocs = ['ijazah', 'rapor', 'kk', 'akta', 'foto'];
        $uploadedDocs = $applicant->documents->pluck('document_type')->toArray();
        $missing = array_diff($requiredDocs, $uploadedDocs);

        if (!empty($missing)) {
            return back()->with('error', 'Harap upload semua dokumen wajib: ' . implode(', ', $missing));
        }

        $applicant->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'completion_step' => 'completed',
        ]);

        if (!empty($applicant->ayah_email)) {
            $parentName = $applicant->ayah_name ?: ($applicant->ibu_name ?: 'Orang Tua');
            $this->createParentAccount(
                $parentName,
                $applicant->ayah_email,
                $applicant->full_name,
            );
        }

        return redirect()->route('ppdb.status')->with('success', 'Pendaftaran berhasil dikirim. Menunggu validasi admin.');
    }

    public function success()
    {
        $applicant = Auth::user()->applicant;
        
        if ($applicant->status !== 'paid') {
            return redirect()->route('ppdb.status');
        }
        
        return view('ppdb.success', compact('applicant'));
    }

    public function status()
    {
        $applicant = Auth::user()->applicant;
        return view('ppdb.status', compact('applicant'));
    }

    public function payment()
    {
        $applicant = Auth::user()->applicant;

        if ($applicant->status !== 'verified') {
            return redirect()->route('ppdb.status');
        }

        return view('ppdb.payment', compact('applicant'));
    }

    public function payProcess(Request $request)
    {
        $applicant = Auth::user()->applicant;

        if ($applicant->status !== 'verified') {
            return redirect()->route('ppdb.status')->with('error', 'Anda belum dapat melakukan konfirmasi pembayaran.');
        }

        $applicant->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $existingStudent = Student::where('nisn', $applicant->nisn)->first();

        if (!$existingStudent) {
            $student = Student::create([
                'nisn' => $applicant->nisn ?? ('PPDB-' . str_pad($applicant->id, 5, '0', STR_PAD_LEFT)),
                'full_name' => $applicant->full_name,
                'birth_date' => $applicant->birth_date,
                'class_name' => '',
                'program_name' => '',
                'status' => 'active',
            ]);

            foreach (['ayah', 'ibu'] as $parentType) {
                $email = $applicant->{$parentType . '_email'};
                $name = $applicant->{$parentType . '_name'};
                if ($email && $name) {
                    $parent = User::where('email', $email)->first();

                    if (!$parent) {
                        $password = (string) random_int(10000000, 99999999);
                        $parent = User::create([
                            'name' => $name,
                            'full_name' => $name,
                            'email' => $email,
                            'password' => Hash::make($password),
                            'role' => 'parent',
                        ]);

                        Mail::to($email)->send(new ParentAccountMail(
                            parentName: $name,
                            parentEmail: $email,
                            password: $password,
                            studentName: $applicant->full_name,
                        ));
                    }

                    $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => $parentType === 'ayah' ? 'Ayah' : 'Ibu', 'is_primary' => $parentType === 'ayah']]);
                }
            }

            Mail::to(Auth::user()->email)->send(new StudentAcceptedMail(
                studentName: $applicant->full_name,
                className: '',
                programName: $applicant->program_diminati ?? '',
            ));
        }

        return redirect()->route('ppdb.success')->with('success', 'Pembayaran berhasil! Selamat, Anda telah resmi diterima sebagai siswa.');
    }

    private function createParentAccount(string $name, string $email, string $studentName): void
    {
        $parent = User::where('email', $email)->first();

        if (!$parent) {
            $password = (string) random_int(10000000, 99999999);
            $parent = User::create([
                'name' => $name,
                'full_name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'parent',
            ]);

            Mail::to($email)->send(new ParentAccountMail(
                parentName: $name,
                parentEmail: $email,
                password: $password,
                studentName: $studentName,
            ));
        }
    }
}
