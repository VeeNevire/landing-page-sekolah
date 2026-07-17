@extends('layouts.admin')

@section('title', $student ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen siswa</span>
    <h1>{{ $student ? 'Edit Siswa' : 'Tambah Siswa Baru' }}</h1>
    <p>{{ $student ? 'Perbarui data siswa.' : 'Daftarkan siswa baru.' }}</p>
  </div>
</div>

<section class="portal-panel" style="max-width:640px">
  <form method="POST" action="{{ $student ? route('admin.students.update', $student) : route('admin.students.store') }}" style="padding:24px">
    @csrf
    @if ($student) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
      <div class="field">
        <label for="nisn">NISN <span style="color:#ef4444">*</span></label>
        <input id="nisn" name="nisn" type="text" value="{{ old('nisn', $student->nisn ?? '') }}" required placeholder="Nomor induk siswa">
        @error('nisn') <small style="color:#ef4444">{{ $message }}</small> @enderror
      </div>
      <div class="field">
        <label for="full_name">Nama Lengkap <span style="color:#ef4444">*</span></label>
        <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $student->full_name ?? '') }}" required placeholder="Nama lengkap siswa">
        @error('full_name') <small style="color:#ef4444">{{ $message }}</small> @enderror
      </div>
    </div>

    <div class="field" style="margin-top:14px">
      <label for="birth_date">Tanggal Lahir</label>
      <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d') ?? '') }}">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
      <div class="field">
        <label for="class_name">Kelas <span style="color:#ef4444">*</span></label>
        <input id="class_name" name="class_name" type="text" value="{{ old('class_name', $student->class_name ?? '') }}" required placeholder="Contoh: XI RPL 1">
        @error('class_name') <small style="color:#ef4444">{{ $message }}</small> @enderror
      </div>
      <div class="field">
        <label for="program_name">Program <span style="color:#ef4444">*</span></label>
        <input id="program_name" name="program_name" type="text" value="{{ old('program_name', $student->program_name ?? '') }}" required placeholder="Contoh: Rekayasa Perangkat Lunak">
        @error('program_name') <small style="color:#ef4444">{{ $message }}</small> @enderror
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
      <div class="field">
        <label for="homeroom_teacher_id">Wali Kelas</label>
        <select id="homeroom_teacher_id" name="homeroom_teacher_id">
          <option value="">-- Pilih Wali Kelas --</option>
          @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id', $student->homeroom_teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>
              {{ $teacher->full_name ?: $teacher->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="status">Status <span style="color:#ef4444">*</span></label>
        <select id="status" name="status" required>
          <option value="active" {{ old('status', $student->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="graduated" {{ old('status', $student->status ?? '') === 'graduated' ? 'selected' : '' }}>Lulus</option>
          <option value="inactive" {{ old('status', $student->status ?? '') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:24px">
      <button type="submit" class="btn btn-primary">{{ $student ? 'Simpan Perubahan' : 'Tambah Siswa' }}</button>
      <a href="{{ route('admin.students.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</section>

@if ($student)
<section class="portal-panel" style="max-width:640px;margin-top:20px;border:1px solid color-mix(in srgb,#ef4444 20%,var(--line))">
  <div style="padding:24px">
    <h3 style="margin:0 0 8px;color:#ef4444">Hapus Siswa</h3>
    <p style="color:var(--muted);margin:0 0 16px;font-size:.9rem">Semua data terkait (nilai, kehadiran, catatan) akan ikut terhapus.</p>
    <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
      @csrf @method('DELETE')
      <button type="submit" class="btn" style="background:#ef4444;color:#fff;border:none" onclick="return confirm('Yakin hapus siswa {{ $student->full_name }}?')">Hapus Siswa</button>
    </form>
  </div>
</section>
@endif
@endsection



