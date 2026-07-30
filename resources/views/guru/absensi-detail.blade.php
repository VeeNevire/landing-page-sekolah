@extends('layouts.guru')

@section('title', 'Detail Kehadiran — ' . $student->full_name)

@push('styles')
<style>
.bento { display:grid; gap:12px }
.bento-4 { grid-template-columns:repeat(4,1fr) }
.bento-2 { grid-template-columns:repeat(2,1fr) }
.b-flex-between { display:flex; align-items:flex-start; justify-content:space-between }
.b-card-stat { padding:16px; border-radius:14px; background:var(--card); border:1px solid var(--line) }
.b-stat-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); margin-bottom:4px }
.b-stat-value { font-size:1.8rem; font-weight:800; line-height:1; margin-bottom:2px }
.b-stat-icon { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; flex-shrink:0 }
.b-stat-icon svg { width:18px; height:18px }
.b-card { border-radius:14px; background:var(--card); border:1px solid var(--line) }
.b-section-title { font-size:.88rem; font-weight:700; margin:0; color:var(--ink) }

@media (max-width:768px) {
  .bento-4 { grid-template-columns:repeat(2,1fr) }
  .bento-2 { grid-template-columns:1fr }
}
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Absensi siswa</span>
    <h1>Detail Kehadiran</h1>
    <p>{{ $student->full_name }} — NISN {{ $student->nisn }} — {{ $student->class_name }}</p>
  </div>
  <div class="portal-actions no-print">
    <a class="btn btn-outline" href="{{ route('guru.absensi', ['tab' => 'rekap', 'class' => $student->class_name]) }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      Kembali
    </a>
  </div>
</div>

<div class="bento bento-4" style="margin-bottom:16px">
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Hadir</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#34C759,#30D158);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $breakdown['present'] ?? 0 }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#34C759,#30D158);box-shadow:0 4px 12px rgba(52,199,89,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
    </div>
  </div>
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Sakit</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF9F0A,#FFD60A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $breakdown['sick'] ?? 0 }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF9F0A,#FFD60A);box-shadow:0 4px 12px rgba(255,159,10,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
    </div>
  </div>
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Izin</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#007AFF,#0A84FF);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $breakdown['excused'] ?? 0 }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#007AFF,#0A84FF);box-shadow:0 4px 12px rgba(0,122,255,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
      </div>
    </div>
  </div>
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Alpa</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF3B30,#FF453A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $breakdown['unexcused'] ?? 0 }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF3B30,#FF453A);box-shadow:0 4px 12px rgba(255,59,48,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </div>
    </div>
  </div>
</div>

<div class="bento bento-2">
  <div class="b-card" style="padding:0">
    <div style="padding:16px 18px 12px;border-bottom:1px solid var(--line)">
      <h3 class="b-section-title">Riwayat Kehadiran</h3>
    </div>
    @if($recentAttendance->count() > 0)
      <div style="padding:8px 12px">
        @php
          $colors = ['present'=>'#34C759','sick'=>'#FF9F0A','excused'=>'#007AFF','late'=>'#FF9F0A','unexcused'=>'#FF3B30'];
          $labels = ['present'=>'Hadir','sick'=>'Sakit','excused'=>'Izin','late'=>'Terlambat','unexcused'=>'Alpa'];
          $icons = ['present'=>'✓','sick'=>'S','excused'=>'I','late'=>'L','unexcused'=>'A'];
        @endphp
        @foreach($recentAttendance->take(10) as $a)
        <div class="b-flex-between" style="padding:8px 6px;border-radius:8px">
          <div style="display:flex;align-items:center;gap:10px">
            <span style="width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:.65rem;font-weight:700;color:#fff;background:{{ $colors[$a->status] ?? '#86868B' }}">{{ $icons[$a->status] ?? '?' }}</span>
            <span style="font-size:.82rem;color:var(--ink)">{{ $a->attendance_date->translatedFormat('d M Y') }}</span>
          </div>
          <span style="font-size:.72rem;font-weight:600;color:{{ $colors[$a->status] ?? '#86868B' }}">{{ $labels[$a->status] ?? $a->status }}</span>
        </div>
        @endforeach
      </div>
    @else
      <div style="padding:28px;text-align:center;color:var(--muted);font-size:.85rem">Belum ada riwayat kehadiran.</div>
    @endif
  </div>

  <div class="b-card" style="padding:18px">
    <h3 class="b-section-title" style="margin-bottom:14px">Tingkat Kehadiran</h3>
    <div style="display:flex;align-items:center;gap:24px">
      <div style="position:relative;width:90px;height:90px;flex-shrink:0">
        <svg width="90" height="90" viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="16" fill="none" stroke="var(--bg)" stroke-width="3"/>
          <circle cx="18" cy="18" r="16" fill="none" stroke="url(#grad)" stroke-width="3" stroke-dasharray="{{ $rate * 1.0048 }} {{ 100.48 - $rate * 1.0048 }}" style="stroke-linecap:round;transform:rotate(-90deg);transform-origin:50% 50%"/>
          <defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#34C759"/><stop offset="100%" stop-color="#30D158"/></linearGradient></defs>
        </svg>
        <div style="position:absolute;inset:0;display:grid;place-items:center">
          <span style="font-size:1.2rem;font-weight:800;color:var(--ink)">{{ $rate }}%</span>
        </div>
      </div>
      <div>
        <div style="font-size:.82rem;color:var(--ink);font-weight:500">{{ $breakdown['present'] ?? 0 }} hadir dari {{ $total }} hari</div>
        <div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:8px;font-size:.72rem;color:var(--muted)">
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:6px;height:6px;border-radius:50%;background:#34C759"></span>Hadir {{ $breakdown['present'] ?? 0 }}</span>
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:6px;height:6px;border-radius:50%;background:#FF9F0A"></span>Sakit {{ $breakdown['sick'] ?? 0 }}</span>
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:6px;height:6px;border-radius:50%;background:#007AFF"></span>Izin {{ $breakdown['excused'] ?? 0 }}</span>
          <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:6px;height:6px;border-radius:50%;background:#FF3B30"></span>Alpa {{ $breakdown['unexcused'] ?? 0 }}</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
