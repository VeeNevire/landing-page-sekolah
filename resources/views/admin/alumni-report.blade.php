@extends('layouts.admin')

@section('title', 'Laporan Nilai — ' . $student->full_name)

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Alumni</span>
    <h1>Laporan Nilai — {{ $student->full_name }}</h1>
    <p>NISN {{ $student->nisn }} &bull; {{ $student->program_name }} &bull; {{ $student->class_name }}</p>
  </div>
  <div class="portal-actions no-print">
    <a class="btn btn-outline" href="{{ route('admin.alumni.index') }}">Kembali</a>
    <button class="btn btn-outline" onclick="window.print()">Cetak</button>
  </div>
</div>

<section class="portal-kpis" style="margin-bottom:20px">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Rata-rata Akhir</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $overallAvg }}</strong>
    <span class="portal-kpi-note">Semua mata pelajaran</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Mapel</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg></span></div>
    <strong class="portal-kpi-value">{{ count($subjects) }}</strong>
    <span class="portal-kpi-note">Nilai telah direkap</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Predikat</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $overallAvg >= 90 ? 'A' : ($overallAvg >= 85 ? 'A-' : ($overallAvg >= 80 ? 'B+' : ($overallAvg >= 75 ? 'B' : ($overallAvg >= 70 ? 'C+' : ($overallAvg >= 65 ? 'C' : 'D'))))) }}</strong>
    <span class="portal-kpi-note good">Ketuntasan</span>
  </article>
</section>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Nilai Mata Pelajaran</h2><p>Rekap seluruh nilai assessment selama masa studi.</p></div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Mata Pelajaran</th>
          <th>Nilai Akhir</th>
          <th>Predikat</th>
          <th>KKM</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subjects as $subject)
        <tr>
          <td><strong>{{ $subject['name'] }}</strong></td>
          <td><strong>{{ $subject['avg'] }}</strong></td>
          <td><span class="grade-badge {{ \App\Helpers\PortalHelper::gradeClass($subject['avg']) }}">{{ $subject['grade'] }}</span></td>
          <td>{{ $subject['kkm'] }}</td>
          <td><span class="{{ $subject['avg'] >= $subject['kkm'] ? 'status-pass' : 'status-remedial' }}">{{ $subject['status'] }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted)">Belum ada data nilai.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection