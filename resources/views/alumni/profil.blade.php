@extends('layouts.alumni')

@section('title', 'Profil Alumni')

@section('content')
<div class="portal-heading">
  <div>
    <h1>Profil Saya</h1>
    <p>Kelola data diri, status kelulusan, dan lihat transkrip nilai akhir Anda.</p>
  </div>
</div>

<div class="portal-dashboard-grid" style="grid-template-columns: 1.2fr 0.8fr;">
  <div style="display: grid; gap: 20px;">
    <!-- Edit Profil & Status -->
    <div class="portal-panel">
      <div class="portal-panel-header">
        <h2>Data Diri & Status Alumni</h2>
      </div>
      <form action="{{ route('alumni.profil.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
          <div class="field">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
          </div>
          <div class="field">
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
          <div class="field">
            <label for="graduation_year">Tahun Kelulusan</label>
            <input type="number" id="graduation_year" name="graduation_year" value="{{ old('graduation_year', $student->graduation_year) }}" required min="2000" max="2099">
          </div>
          <div class="field">
            <label for="alumni_status">Status Saat Ini</label>
            <select id="alumni_status" name="alumni_status" required style="width: 100%; height: 42px; border-radius: 8px; border: 1px solid var(--line); padding: 0 10px; background: var(--card); color: var(--ink);">
              <option value="working" {{ old('alumni_status', $student->alumni_status) === 'working' ? 'selected' : '' }}>Bekerja / Wirausaha</option>
              <option value="studying" {{ old('alumni_status', $student->alumni_status) === 'studying' ? 'selected' : '' }}>Kuliah / Studi Lanjut</option>
              <option value="looking_for_job" {{ old('alumni_status', $student->alumni_status) === 'looking_for_job' ? 'selected' : '' }}>Mencari Pekerjaan</option>
            </select>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </form>
    </div>

    <!-- CV Upload -->
    <div class="portal-panel">
      <div class="portal-panel-header">
        <h2>Curriculum Vitae (CV)</h2>
        <p>Unggah CV terbaru Anda agar mudah diakses atau dibagikan.</p>
      </div>
      <form action="{{ route('alumni.cv.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="field" style="margin-bottom: 15px;">
          <label for="cv">File CV (PDF, DOC, DOCX - Maks. 2MB)</label>
          <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required>
        </div>
        
        @if($student->cv_path)
          <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span style="font-size: 0.9rem;">CV Anda sudah terunggah. <a href="{{ asset('storage/' . $student->cv_path) }}" target="_blank" style="color: var(--primary-2); font-weight: 600; text-decoration: none;">Download CV</a></span>
          </div>
        @endif

        <button type="submit" class="btn btn-outline">Unggah CV</button>
      </form>
    </div>
  </div>

  <div style="display: grid; gap: 20px; align-content: start;">
    <!-- Info Sekolah -->
    <div class="portal-panel">
      <div class="portal-panel-header">
        <h2>Detail Akademik</h2>
      </div>
      <div style="display: grid; gap: 15px; font-size: 0.9rem;">
        <div style="border-bottom: 1px solid var(--line); padding-bottom: 10px;">
          <span style="color: var(--muted); display: block; font-size: 0.8rem;">NISN / NIS</span>
          <strong>{{ $student->nisn }} / {{ $student->nis ?? '—' }}</strong>
        </div>
        <div style="border-bottom: 1px solid var(--line); padding-bottom: 10px;">
          <span style="color: var(--muted); display: block; font-size: 0.8rem;">Program Keahlian</span>
          <strong>{{ $student->program_name }}</strong>
        </div>
        <div style="border-bottom: 1px solid var(--line); padding-bottom: 10px;">
          <span style="color: var(--muted); display: block; font-size: 0.8rem;">Kelas Terakhir</span>
          <strong>{{ $student->class_name }}</strong>
        </div>
      </div>
    </div>

    <!-- Rata-rata Nilai / Transkrip Singkat -->
    <div class="portal-panel">
      <div class="portal-panel-header">
        <h2>Transkrip Nilai Akhir</h2>
      </div>
      <div style="max-height: 280px; overflow-y: auto; display: grid; gap: 10px; padding-right: 5px;">
        @forelse($subjects as $sub)
          <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: var(--bg); border-radius: 8px; font-size: 0.85rem;">
            <span style="font-weight: 600;">{{ $sub['name'] }}</span>
            <span style="font-weight: 700; color: var(--primary-2)">{{ $sub['avg'] }}</span>
          </div>
        @empty
          <p style="color: var(--muted); font-size: 0.85rem; font-style: italic;">Tidak ada data nilai tersedia.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection