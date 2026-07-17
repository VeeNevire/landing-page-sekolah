@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Dashboard administrator</span>
    <h1>Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->name }}.</h1>
    <p>Berikut ringkasan data sekolah dan aktivitas sistem.</p>
  </div>
</div>

<section class="portal-kpis">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Siswa</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalStudents }}</strong>
    <span class="portal-kpi-note">Siswa aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Guru</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalTeachers }}</strong>
    <span class="portal-kpi-note">Guru & wali kelas</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Orang Tua</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalParents }}</strong>
    <span class="portal-kpi-note">Akun orang tua</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Kelas Aktif</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalClasses }}</strong>
    <span class="portal-kpi-note">Kelas</span>
  </article>
</section>

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Info Sistem</h2>
        <p>Status periode akademik dan ringkasan data.</p>
      </div>
    </div>
    <div style="display:grid;gap:14px">
      <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        <div style="flex:1">
          <strong style="display:block">Periode Aktif</strong>
          <span style="color:var(--muted);font-size:.88rem">{{ $activePeriod ? "{$activePeriod->academic_year} Semester {$activePeriod->semester}" : 'Tidak ada periode aktif' }}</span>
        </div>
        @if ($activePeriod)
          <span style="padding:4px 12px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
        @else
          <span style="padding:4px 12px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444">Nonaktif</span>
        @endif
      </div>
      <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
        </span>
        <div style="flex:1">
          <strong style="display:block">Total Mata Pelajaran</strong>
          <span style="color:var(--muted);font-size:.88rem">{{ $totalSubjects }} mata pelajaran terdaftar</span>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--accent) 12%,var(--card));color:#7a5500">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        <div style="flex:1">
          <strong style="display:block">Absensi Hari Ini</strong>
          <span style="color:var(--muted);font-size:.88rem">{{ $todayAttendance }} catatan kehadiran</span>
        </div>
      </div>
    </div>
  </section>

  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Aktivitas Terbaru</h2>
        <p>Log aktivitas 5 terakhir.</p>
      </div>
    </div>
    @if ($recentAudit->isEmpty())
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Belum ada aktivitas tercatat.</p>
      </div>
    @else
      <div class="activity-feed">
        @foreach ($recentAudit as $log)
          <div class="activity-item">
            <span class="activity-icon" style="background:color-mix(in srgb,var(--primary-2) 14%,var(--card))">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </span>
            <div>
              <strong>{{ $log->user->name ?? 'System' }}</strong>
              <span>{{ $log->action }} &mdash; {{ $log->created_at->diffForHumans() }}</span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection



