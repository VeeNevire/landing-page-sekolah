@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Dashboard guru</span>
    <h1>Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->name }}.</h1>
    <p>Berikut ringkasan kelas dan jadwal mengajar Anda {{ $activePeriod?->semester === 'ganjil' ? 'Ganjil' : 'Genap' }} Tahun Ajaran {{ $activePeriod?->academic_year ?? '-' }}.</p>
  </div>
</div>

<section class="portal-kpis">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Kelas Diajar</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalClasses }}</strong>
    <span class="portal-kpi-note">Kelas aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Siswa</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalStudents }}</strong>
    <span class="portal-kpi-note">Siswa aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Mapel Diampu</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalSubjects }}</strong>
    <span class="portal-kpi-note">Mata pelajaran</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Status</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
    <strong class="portal-kpi-value" style="color:var(--success);font-size:1.4rem">{{ $isHomeroom ? 'Wali Kelas' : 'Guru Mapel' }}</strong>
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
            <span class="activity-icon" style="background:color-mix(in srgb,var(--primary-2) 14%,var(--card))">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
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
            $students = $studentsPerClass[$class] ?? collect();
            $subjectNames = $teachingAssignments->where('class_name', $class)->pluck('subject.name')->unique()->implode(', ');
          @endphp
          <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
            <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:900;font-size:.95rem">{{ $class }}</span>
            <div style="flex:1">
              <strong style="display:block">{{ $class }}</strong>
              <span style="color:var(--muted);font-size:.85rem">{{ $subjectNames }}</span>
            </div>
            <span style="font-weight:800;color:var(--primary-2)">{{ $students->count() }} siswa</span>
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
            <span class="activity-icon" style="background:color-mix(in srgb,var(--accent) 14%,var(--card));color:#6b4c00">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
            <td><span style="background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2);padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem">{{ $item['class_name'] }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection
