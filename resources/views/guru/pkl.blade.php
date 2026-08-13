@extends('layouts.guru')

@section('title', 'PKL / Bimbingan Siswa')

@push('styles')
<style>
.pk-card { padding:16px; border-radius:14px; background:var(--card); border:1px solid var(--line); margin-bottom:12px }
.pk-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px }
.pk-student { display:flex; align-items:center; gap:12px; min-width:0 }
.pk-avatar { width:42px; height:42px; border-radius:12px; display:grid; place-items:center; font-weight:800; font-size:.9rem; background:color-mix(in srgb,var(--primary-2) 12%,var(--card)); color:var(--primary-2); flex-shrink:0 }
.pk-name { font-weight:700; font-size:.92rem; color:var(--ink) }
.pk-sub { font-size:.76rem; color:var(--muted) }
.pk-meta { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:12px }
.pk-meta .m { padding:10px; border-radius:10px; background:color-mix(in srgb,var(--bg) 55%,var(--card)); border:1px solid var(--line) }
.pk-meta .m-label { font-size:.68rem; font-weight:600; color:var(--muted); margin-bottom:2px }
.pk-meta .m-val { font-size:.88rem; font-weight:800; color:var(--ink) }
.pk-status { display:inline-block; padding:3px 10px; border-radius:99px; font-size:.72rem; font-weight:700 }
@media (max-width:768px){ .pk-meta{grid-template-columns:repeat(2,1fr)} }
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Bimbingan PKL</span>
    <h1>PKL / Bimbingan Siswa</h1>
    <p>Pantau dan setujui jurnal aktivitas PKL siswa yang Anda bimbing.</p>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
  <div class="pk-meta" style="display:block;padding:16px;border-radius:14px;background:var(--card);border:1px solid var(--line)">
    <div class="m-label">Menunggu</div>
    <div class="m-val" style="color:#d97706">{{ $totals['pending'] }}</div>
  </div>
  <div style="padding:16px;border-radius:14px;background:var(--card);border:1px solid var(--line)">
    <div style="font-size:.68rem;font-weight:600;color:var(--muted);margin-bottom:2px">Disetujui</div>
    <div style="font-size:1.4rem;font-weight:800;color:#16a34a;line-height:1">{{ $totals['approved'] }}</div>
  </div>
  <div style="padding:16px;border-radius:14px;background:var(--card);border:1px solid var(--line)">
    <div style="font-size:.68rem;font-weight:600;color:var(--muted);margin-bottom:2px">Ditolak</div>
    <div style="font-size:1.4rem;font-weight:800;color:#dc2626;line-height:1">{{ $totals['rejected'] }}</div>
  </div>
  <div style="padding:16px;border-radius:14px;background:var(--card);border:1px solid var(--line)">
    <div style="font-size:.68rem;font-weight:600;color:var(--muted);margin-bottom:2px">Total Jurnal</div>
    <div style="font-size:1.4rem;font-weight:800;color:var(--ink);line-height:1">{{ $totals['all'] }}</div>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Siswa Bimbingan</h2><p>Daftar penempatan PKL yang Anda bimbing sebagai guru pembimbing.</p></div>
  </div>

  @if ($placements->isEmpty())
    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">
      Belum ada siswa yang Anda bimbing untuk PKL.<br>
      <span style="font-size:.8rem">Hubungi admin untuk menetapkan Anda sebagai guru pembimbing pada penempatan PKL siswa.</span>
    </div>
  @else
    @foreach ($placements as $p)
      @php
        $pending = $p->pending_count;
        $statusColor = ['active' => '#16a34a', 'selesai' => '#2563eb', 'batal' => '#dc2626'][$p->status];
      @endphp
      <div class="pk-card">
        <div class="pk-head">
          <div class="pk-student">
            <div class="pk-avatar">{{ strtoupper(mb_substr($p->student?->full_name ?? 'S', 0, 1)) }}</div>
            <div style="min-width:0">
              <div class="pk-name">{{ $p->student?->full_name ?? '-' }}</div>
              <div class="pk-sub">NISN {{ $p->student?->nisn ?? '-' }} &bull; {{ $p->student?->class_name ?? '-' }}</div>
            </div>
          </div>
          <span class="pk-status" style="background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $p->status_label }}</span>
        </div>

        <div class="pk-meta">
          <div class="m"><div class="m-label">Perusahaan</div><div class="m-val" style="font-size:.8rem;font-weight:700">{{ $p->company?->nama ?? '-' }}</div></div>
          <div class="m"><div class="m-label">Rentang PKL</div><div class="m-val" style="font-size:.8rem;font-weight:700">{{ $p->start_date->format('d M Y') }} — {{ $p->end_date->format('d M Y') }}</div></div>
          <div class="m"><div class="m-label">Menunggu</div><div class="m-val" style="color:#d97706">{{ $pending }}</div></div>
          <div class="m"><div class="m-label">Total Jurnal</div><div class="m-val">{{ $p->activities_count }}</div></div>
        </div>

        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <a href="{{ route('guru.pkl.show', $p->id) }}" class="btn btn-primary" style="min-height:36px;padding:0 16px;font-size:.82rem;display:inline-flex;align-items:center;gap:6px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            Tinjau Jurnal
            @if ($pending > 0)<span style="padding:1px 8px;border-radius:99px;background:rgba(255,255,255,.25);font-size:.7rem">{{ $pending }}</span>@endif
          </a>
          <span style="font-size:.76rem;color:var(--muted)">
            {{ $p->approved_count }} disetujui &middot; {{ $p->rejected_count }} ditolak
          </span>
        </div>
      </div>
    @endforeach
  @endif
</section>
@endsection
