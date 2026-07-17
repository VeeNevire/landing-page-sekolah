@extends('layouts.guru')

@section('title', 'Publikasi Nilai')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Publikasi</span>
    <h1>Publikasi Nilai</h1>
    <p>Publikasikan nilai siswa agar dapat dilihat oleh orang tua melalui portal.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Status Publikasi</h2><p>Publikasikan semua penilaian per kelas sekaligus.</p></div>
  </div>
  <div style="display:grid;gap:14px">
    @forelse ($classList as $class)
      <div style="display:flex;align-items:center;gap:18px;padding:18px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="width:50px;height:50px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:900;font-size:.9rem;flex-shrink:0">{{ $class['name'] }}</span>
        <div style="flex:1">
          <strong style="display:block">{{ $class['name'] }}</strong>
          <span style="color:var(--muted);font-size:.85rem">{{ $class['student_count'] }} siswa &bull; {{ $class['subjects'] }}</span>
        </div>
        <div style="text-align:right">
          <span style="display:block;font-weight:700;font-size:.85rem;margin-bottom:4px">
            {{ $class['published_count'] }}/{{ $class['total_assessments'] }} dipublikasikan
          </span>
          @if ($class['all_published'])
            <span class="status-pass">Semua Published</span>
          @elseif ($class['total_assessments'] > 0)
            <form method="POST" action="{{ route('guru.publikasi.store', ['class' => $class['name']]) }}" style="display:inline" onsubmit="return confirm('Publikasikan semua nilai kelas {{ $class['name'] }}?')">
              @csrf
              <button class="btn btn-primary" type="submit" style="min-height:34px;padding:0 16px;font-size:.82rem;border-radius:10px">Publikasikan Semua</button>
            </form>
          @else
            <span style="color:var(--muted);font-size:.82rem">Belum ada penilaian</span>
          @endif
        </div>
      </div>
    @empty
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Tidak ada kelas untuk dipublikasikan.</p>
      </div>
    @endforelse
  </div>
</section>
@endsection



