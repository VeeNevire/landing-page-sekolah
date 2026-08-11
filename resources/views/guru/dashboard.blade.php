@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<section class="portal-hero glass-panel">
  <div>
    <span class="portal-hero-kicker">Dashboard guru</span>
    <h1>Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->name }}</h1>
    <p>Ringkasan kelas dan jadwal mengajar Anda untuk
      {{ $activePeriod?->semester === 'ganjil' ? 'Semester Ganjil' : 'Semester Genap' }}
      Tahun Ajaran {{ $activePeriod?->academic_year ?? '-' }}.</p>
  </div>
  <div class="portal-hero-side">
    <div class="portal-hero-chip">
      <span class="kpi-chip teal">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </span>
      <div>
        <strong>Periode Aktif</strong>
        <span>{{ $activePeriod ? "{$activePeriod->academic_year} Semester {$activePeriod->semester}" : 'Tidak ada periode aktif' }}</span>
      </div>
      @if ($activePeriod)
        <span class="portal-hero-chip-badge ok">Aktif</span>
      @else
        <span class="portal-hero-chip-badge warn">Nonaktif</span>
      @endif
    </div>
    <div class="portal-hero-chip">
      <span class="kpi-chip amber">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </span>
      <div>
        <strong>Hari Ini</strong>
        <span>{{ $today ?: 'Akhir pekan' }} &mdash; {{ $todaySchedule->count() }} jadwal</span>
      </div>
    </div>
  </div>
</section>

<section class="portal-kpis">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Kelas Diajar</span><span class="kpi-chip teal"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalClasses }}</strong>
    <span class="portal-kpi-note">Kelas aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Siswa</span><span class="kpi-chip green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalStudents }}</strong>
    <span class="portal-kpi-note">Siswa aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Mapel Diampu</span><span class="kpi-chip slate"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalSubjects }}</strong>
    <span class="portal-kpi-note">Mata pelajaran</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Status</span><span class="kpi-chip amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
    <strong class="portal-kpi-value" style="color:var(--success);font-size:1.3rem">{{ $isHomeroom ? 'Wali Kelas' : 'Guru Mapel' }}</strong>
    <span class="portal-kpi-note">{{ $isHomeroom ? ($homeroomStudents->first()?->class_name ?? '-') : 'Mengajar' }}</span>
  </article>
</section>

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Jadwal Hari Ini</h2>
        <p>{{ $today ?: 'Akhir pekan — tidak ada jadwal.' }}</p>
      </div>
    </div>
    @if ($todaySchedule->isEmpty())
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Tidak ada jadwal mengajar hari ini.</p>
      </div>
    @else
      <div class="activity-feed">
        @foreach ($todaySchedule as $item)
          <div class="activity-item">
            <span class="activity-icon blue">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
              <strong>{{ $item['subject'] }} — {{ $item['class_name'] }}</strong>
              <span>{{ $item['time'] }}</span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </section>

  <div style="display:grid;gap:20px">
    <section class="portal-panel">
      <div class="portal-panel-header">
        <div>
          <h2>Kelas yang Diajar</h2>
          <p>{{ $totalClasses }} kelas aktif semester ini.</p>
        </div>
      </div>
      <div style="display:grid;gap:12px">
        @foreach ($classNames as $class)
          @php
            $gradeLevel = explode(' ', $class)[0];
            $students = $studentsPerClass[$class] ?? collect();
            $subjectNames = $teachingAssignments->where('class_name', $class)->pluck('subject.name')->unique()->implode(', ');
          @endphp
          <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid rgba(255,255,255,.6);background:rgba(255,255,255,.45)">
            <span class="kpi-chip slate" style="width:46px;height:46px;font-weight:900;font-size:.95rem;color:#fff">{{ $gradeLevel }}</span>
            <div style="flex:1">
              <strong style="display:block">{{ $class }}</strong>
              <span style="color:var(--muted);font-size:.85rem">{{ $subjectNames }}</span>
            </div>
            <span style="font-weight:800;color:var(--primary)">{{ $students->count() }} siswa</span>
          </div>
        @endforeach
      </div>
    </section>

    @if ($isHomeroom)
    <section class="portal-panel">
      <div class="portal-panel-header">
        <div>
          <h3>Wali Kelas — {{ $homeroomStudents->first()?->class_name ?? '-' }}</h3>
          <p>{{ $homeroomStudents->count() }} siswa di kelas Anda.</p>
        </div>
      </div>
      <div class="activity-feed">
        @foreach ($homeroomStudents->take(5) as $student)
          <div class="activity-item">
            <span class="activity-icon green">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <div>
              <strong>{{ $student->full_name }}</strong>
              <span>NISN {{ $student->nisn }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </section>
    @endif
  </div>
</div>

<section class="portal-panel" style="margin-top:20px">
  <div class="portal-panel-header">
    <div>
      <h2>Semua Jadwal Mengajar</h2>
      <p>Jadwal lengkap untuk semua kelas dan mata pelajaran.</p>
    </div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Kelas</th></tr>
      </thead>
      <tbody>
        @foreach ($schedule as $item)
          <tr>
            <td><strong>{{ $item['day'] }}</strong></td>
            <td>{{ $item['time'] }}</td>
            <td>{{ $item['subject'] }}</td>
            <td><span style="background:color-mix(in srgb,var(--primary-2) 12%,#fff);color:var(--primary);padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem">{{ $item['class_name'] }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection
