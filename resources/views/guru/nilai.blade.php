@extends('layouts.guru')

@section('title', 'Input Nilai')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Penilaian</span>
    <h1>Input Nilai</h1>
    <p>Pilih kelas dan mata pelajaran untuk menginput nilai siswa.</p>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Pilih Kelas & Mata Pelajaran</h2><p>Pilih pasangan kelas-mapel yang ingin diinput nilainya.</p></div>
  </div>

  @if (session('success'))
    <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
  @endif

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px">
    @forelse ($pairs as $pair)
      <a href="{{ route('guru.nilai.detail', ['class' => $pair['class_name'], 'subject' => $pair['subject_id']]) }}"
         class="card card-hover" style="text-decoration:none;padding:20px">
        <div style="display:flex;align-items:center;gap:14px">
          <span style="width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:900;font-size:.82rem;flex-shrink:0">{{ strtoupper(substr($pair['subject_name'], 0, 2)) }}</span>
          <div>
            <strong style="display:block">{{ $pair['subject_name'] }}</strong>
            <span style="color:var(--muted);font-size:.85rem">Kelas {{ $pair['class_name'] }}</span>
          </div>
        </div>
      </a>
    @empty
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Tidak ada penugasan mengajar ditemukan.</p>
      </div>
    @endforelse
  </div>
</section>
@endsection



