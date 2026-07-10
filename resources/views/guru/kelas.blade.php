@extends('layouts.guru')

@section('title', 'Kelas Saya')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Kelas</span>
    <h1>Kelas Saya</h1>
    <p>Daftar kelas yang Anda ajar semester ini.</p>
  </div>
</div>

<section class="portal-kpis">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Kelas</span></div>
    <strong class="portal-kpi-value">{{ count($classList) }}</strong>
    <span class="portal-kpi-note">Kelas aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Siswa</span></div>
    <strong class="portal-kpi-value">{{ collect($classList)->sum('student_count') }}</strong>
    <span class="portal-kpi-note">Siswa aktif</span>
  </article>
</section>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px">
  @foreach ($classList as $class)
    <div class="card card-hover" style="padding:0;overflow:hidden">
      <div style="padding:22px 22px 0">
        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:14px">
          <div>
            <h3 style="margin:0;font-size:1.3rem">{{ $class['name'] }}</h3>
            <p style="color:var(--muted);margin:4px 0 0;font-size:.88rem">{{ $class['student_count'] }} siswa aktif</p>
          </div>
          <span style="width:44px;height:44px;border-radius:13px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:900;font-size:.8rem;flex-shrink:0">{{ $class['student_count'] }}</span>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px">
          @foreach ($class['subjects'] as $subject)
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--accent) 14%,var(--card));color:#7a5500">{{ $subject['name'] }}</span>
          @endforeach
        </div>
      </div>
      <div style="border-top:1px solid var(--line);padding:14px 22px;display:flex;flex-wrap:wrap;gap:6px">
        @foreach ($class['students']->take(6) as $student)
          <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:8px;background:var(--bg);font-size:.8rem;font-weight:600">
            <span style="width:20px;height:20px;border-radius:50%;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 15%,var(--card));color:var(--primary-2);font-size:.6rem;font-weight:800">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
            {{ $student->full_name }}
          </span>
        @endforeach
        @if ($class['student_count'] > 6)
          <span style="padding:4px 10px;border-radius:8px;background:var(--bg);font-size:.8rem;color:var(--muted)">+{{ $class['student_count'] - 6 }} lainnya</span>
        @endif
      </div>
      <div style="border-top:1px solid var(--line);padding:12px 22px;display:flex;gap:8px">
        @foreach ($class['subjects'] as $subject)
          <a href="{{ route('guru.nilai.detail', ['class' => $class['name'], 'subject' => $subject['id']]) }}" class="btn btn-outline" style="min-height:36px;padding:0 14px;font-size:.82rem;border-radius:10px">Input Nilai {{ $subject['name'] }}</a>
        @endforeach
      </div>
    </div>
  @endforeach
</div>
@endsection
