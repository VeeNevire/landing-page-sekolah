@extends('layouts.admin')

@section('title', 'Tambah Penugasan Guru')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen penugasan</span>
    <h1>Tambah Penugasan Baru</h1>
    <p>atur guru mengajar untuk mata pelajaran dan kelas tertentu.</p>
  </div>
</div>

<section class="portal-panel" style="max-width:560px">
  <form method="POST" action="{{ route('admin.teaching.store') }}" style="padding:24px">
    @csrf

    <div class="field">
      <label for="period_id">Periode Akademik <span style="color:#ef4444">*</span></label>
      <select id="period_id" name="period_id" required>
        <option value="">-- Pilih Periode --</option>
        @foreach ($periods as $period)
          <option value="{{ $period->id }}" {{ old('period_id') == $period->id ? 'selected' : '' }}>
            {{ $period->academic_year }} Semester {{ ucfirst($period->semester) }} {{ $period->is_active ? '(Aktif)' : '' }}
          </option>
        @endforeach
      </select>
      @error('period_id') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div class="field" style="margin-top:14px">
      <label for="subject_id">Mata Pelajaran <span style="color:#ef4444">*</span></label>
      <select id="subject_id" name="subject_id" required>
        <option value="">-- Pilih Mata Pelajaran --</option>
        @foreach ($subjects as $subject)
          <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
            {{ $subject->code }} — {{ $subject->name }} (KKM: {{ $subject->kkm }})
          </option>
        @endforeach
      </select>
      @error('subject_id') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div class="field" style="margin-top:14px">
      <label for="teacher_id">Guru <span style="color:#ef4444">*</span></label>
      <select id="teacher_id" name="teacher_id" required>
        <option value="">-- Pilih Guru --</option>
        @foreach ($teachers as $teacher)
          <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
            {{ $teacher->full_name ?: $teacher->name }} ({{ $teacher->role }})
          </option>
        @endforeach
      </select>
      @error('teacher_id') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div class="field" style="margin-top:14px">
      <label for="class_name">Kelas <span style="color:#ef4444">*</span></label>
      <select id="class_name" name="class_name" required>
        <option value="">-- Pilih Kelas --</option>
        @foreach ($classNames as $class)
          <option value="{{ $class }}" {{ old('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
        @endforeach
      </select>
      @error('class_name') <small style="color:#ef4444">{{ $message }}</small> @enderror
    </div>

    <div style="display:flex;gap:10px;margin-top:24px">
      <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
      <a href="{{ route('admin.teaching.index') }}" class="btn btn-outline">Batal</a>
    </div>
  </form>
</section>
@endsection
