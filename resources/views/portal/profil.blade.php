@extends('layouts.portal')

@section('title', 'Profil Siswa')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Profil siswa</span>
        <h1>Profil {{ $demoStudent['name'] }}</h1>
        <p>Data lengkap siswa pada Semester {{ $demoStudent['semester'] }} Tahun Ajaran {{ $demoStudent['academic_year'] }}.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
      </div>
    </div>

    <div class="portal-dashboard-grid">
      <section class="portal-panel">
        <div class="portal-panel-header"><div><h2>Data Siswa</h2><p>Informasi akademik dan identitas.</p></div></div>
        <div style="display:grid;gap:14px">
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Nama Lengkap</span><strong>{{ $demoStudent['name'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">NISN</span><strong>{{ $demoStudent['nisn'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Kelas</span><strong>{{ $demoStudent['class'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Program</span><strong>{{ $demoStudent['program'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Semester</span><strong>{{ $demoStudent['semester'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Tahun Ajaran</span><strong>{{ $demoStudent['academic_year'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0"><span style="color:var(--muted)">Wali Kelas</span><strong>{{ $demoStudent['homeroom_teacher'] }}</strong></div>
        </div>
      </section>

      <div style="display:grid;gap:20px">
        <section class="portal-panel">
          <div class="portal-panel-header"><div><h3>Karakter & Sikap</h3><p>Penilaian dari wali kelas.</p></div></div>
          <div class="competency-list">
            @foreach ($demoStudent['behavior'] as $label => $value)
              <div class="competency-row">
                <span class="competency-label">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                <span class="competency-value">{{ $value }}</span>
              </div>
            @endforeach
          </div>
        </section>

        <section class="portal-panel">
          <div class="portal-panel-header"><div><h3>Ekstrakurikuler</h3><p>Kegiatan di luar kelas.</p></div></div>
          <div class="activity-feed">
            @foreach ($demoStudent['extracurricular'] as $item)
              <div class="activity-item">
                <span class="activity-icon" style="background:color-mix(in srgb,var(--accent) 15%,var(--card))">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                </span>
                <div><strong>{{ $item['name'] }} &bull; {{ $item['score'] }}</strong><span>{{ $item['note'] }}</span></div>
              </div>
            @endforeach
          </div>
        </section>
      </div>
    </div>
@endif
@endsection
