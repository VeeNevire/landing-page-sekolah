@extends('layouts.admin')

@section('title', $user ? 'Edit Pengguna' : 'Tambah Pengguna')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen pengguna</span>
    <h1>{{ $user ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h1>
    <p>{{ $user ? 'Perbarui data pengguna.' : 'Buat akun pengguna baru.' }}</p>
  </div>
</div>

<section class="portal-panel" style="max-width:640px">
  <form method="POST" action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}" style="padding:24px">
    @csrf
    @if ($user) @method('PUT') @endif

    <div class="field">
      <label for="name">Nama Panggilan <span style="color:#ef4444">*</span></label>
      <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required placeholder="Nama tampilan">
      @error('name') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div class="field" style="margin-top:14px">
      <label for="full_name">Nama Lengkap</label>
      <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $user->full_name ?? '') }}" placeholder="Nama lengkap (opsional)">
    </div>

    <div class="field" style="margin-top:14px">
      <label for="email">Email <span style="color:#ef4444">*</span></label>
      <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required placeholder="email@contoh.com">
      @error('email') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div class="field" style="margin-top:14px">
      <label for="role">Role <span style="color:#ef4444">*</span></label>
      <select id="role" name="role" required>
        @php $roleLabels = ['admin' => 'Admin', 'teacher' => 'Guru', 'homeroom' => 'Wali Kelas', 'parent' => 'Orang Tua', 'principal' => 'Kepala Sekolah']; @endphp
        @foreach ($roleLabels as $val => $label)
          @if (auth()->user()->role === 'principal' && $val === 'principal')
            @continue
          @endif
          <option value="{{ $val }}" {{ old('role', $user->role ?? '') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      @error('role') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    @if (!$user)
    <div class="field" style="margin-top:14px">
      <label for="password">Password <span style="color:#ef4444">*</span></label>
      <input id="password" name="password" type="password" required placeholder="Minimal 6 karakter">
      @error('password') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div class="field" style="margin-top:14px">
      <label for="password_confirmation">Konfirmasi Password <span style="color:#ef4444">*</span></label>
      <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi password">
    </div>
    @endif

    <div style="display:flex;gap:10px;margin-top:24px">
      <button type="submit" class="btn btn-primary">{{ $user ? 'Simpan Perubahan' : 'Buat Pengguna' }}</button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</section>

@if ($user)
<section class="portal-panel" style="max-width:640px;margin-top:20px">
  <div style="padding:24px">
    <h3 style="margin:0 0 16px">Reset Password</h3>
    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" style="display:flex;gap:10px;align-items:end;flex-wrap:wrap">
      @csrf
      <div class="field" style="flex:1;min-width:200px;margin:0">
        <label for="new_password">Password Baru</label>
        <input id="new_password" name="new_password" type="password" required placeholder="Minimal 6 karakter">
      </div>
      <button type="submit" class="btn btn-outline" style="min-height:42px" onclick="return confirm('Yakin reset password {{ $user->name }}?')">Reset Password</button>
    </form>
  </div>
</section>

@if ($user->id !== auth()->id())
<section class="portal-panel" style="max-width:640px;margin-top:20px;border:1px solid color-mix(in srgb,#ef4444 20%,var(--line))">
  <div style="padding:24px">
    <h3 style="margin:0 0 8px;color:#ef4444">Hapus Pengguna</h3>
    <p style="color:var(--muted);margin:0 0 16px;font-size:.9rem">Tindakan ini tidak dapat dibatalkan. Semua data terkait pengguna akan ikut terhapus.</p>
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
      @csrf @method('DELETE')
      <button type="submit" class="btn" style="background:#ef4444;color:#fff;border:none" onclick="return confirm('Yakin hapus pengguna {{ $user->name }}?')">Hapus Pengguna</button>
    </form>
  </div>
</section>
@endif
@endsection



