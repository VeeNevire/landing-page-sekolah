@extends('layouts.portal')

@section('title', 'Kehadiran')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Rekap kehadiran</span>
        <h1>Kehadiran Siswa</h1>
        <p>Pencatatan kehadiran {{ $demoStudent['name'] }} Semester {{ $demoStudent['semester'] }} Tahun Ajaran {{ $demoStudent['academic_year'] }}.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
        <p>Wali Kelas {{ $demoStudent['homeroom_teacher'] }}</p>
      </div>
    </div>

    <section class="portal-kpis" style="margin-bottom:20px">
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Hadir</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>
        <strong class="portal-kpi-value">{{ $attendance['present'] ?? 0 }}</strong>
        <span class="portal-kpi-note good">Hari hadir</span>
      </article>
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Sakit</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg></span></div>
        <strong class="portal-kpi-value">{{ $attendance['sick'] ?? 0 }}</strong>
        <span class="portal-kpi-note">Hari sakit</span>
      </article>
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Izin</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span></div>
        <strong class="portal-kpi-value">{{ $attendance['excused'] ?? 0 }}</strong>
        <span class="portal-kpi-note">Hari izin</span>
      </article>
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Alpha</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span></div>
        <strong class="portal-kpi-value">{{ $attendance['unexcused'] ?? 0 }}</strong>
        <span class="portal-kpi-note" style="color:var(--danger)">Tanpa keterangan</span>
      </article>
    </section>

    <section class="portal-panel">
      <div class="portal-panel-header"><div><h2>Ringkasan Kehadiran</h2><p>Total {{ $total }} hari masa sekolah semester berjalan.</p></div></div>
      <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;margin-bottom:20px">
        <div style="flex:1;min-width:200px">
          <div class="progress-track" style="height:14px;border-radius:99px">
            <div class="progress-fill" style="--score:{{ $attendanceRate }}%;height:100%;border-radius:99px"></div>
          </div>
          <p style="color:var(--muted);font-size:.85rem;margin-top:8px">Tingkat kehadiran <strong style="color:var(--primary-2)">{{ $attendanceRate }}%</strong></p>
        </div>
      </div>
      <div class="attendance-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="attendance-box"><strong style="color:var(--success)">{{ $attendance['present'] ?? 0 }}</strong><span>Hadir</span></div>
        <div class="attendance-box"><strong style="color:#d97706">{{ $attendance['sick'] ?? 0 }}</strong><span>Sakit</span></div>
        <div class="attendance-box"><strong style="color:var(--primary-2)">{{ $attendance['excused'] ?? 0 }}</strong><span>Izin</span></div>
        <div class="attendance-box"><strong style="color:var(--danger)">{{ $attendance['unexcused'] ?? 0 }}</strong><span>Tanpa Keterangan</span></div>
      </div>
    </section>

    <div class="portal-note" style="margin-top:20px">
      <strong>Catatan</strong>
      <p>Kehadiran siswa sangat baik. Tingkat kehadiran {{ $attendanceRate }}% menunjukkan disiplin yang tinggi dalam mengikuti kegiatan belajar mengajar.</p>
    </div>
@endif
@endsection



