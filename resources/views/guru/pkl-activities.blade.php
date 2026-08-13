@extends('layouts.guru')

@section('title', 'Tinjau Jurnal PKL')

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
.pk-date-badge { width:38px; height:38px; border-radius:10px; display:grid; place-items:center; background:color-mix(in srgb,var(--primary-2) 10%,var(--card)); color:var(--primary-2); flex-shrink:0; text-align:center; line-height:1.1 }
.pk-date-badge b { display:block; font-size:.82rem }
.pk-date-badge small { font-size:.6rem; font-weight:700; text-transform:uppercase; color:var(--muted) }
.pk-activity { padding:14px 0; border-bottom:1px solid color-mix(in srgb,var(--line) 60%,transparent); display:flex; gap:14px }
.pk-activity:last-child { border-bottom:none }
.pk-status { display:inline-block; padding:3px 10px; border-radius:99px; font-size:.72rem; font-weight:700 }
.act-note { flex:1; min-width:200px; padding:9px 12px; border-radius:9px; border:1.5px solid var(--line); background:var(--card); color:var(--ink); font-size:.82rem; font-family:inherit }
@media (max-width:768px){ .pk-meta{grid-template-columns:repeat(2,1fr)} }
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Bimbingan PKL</span>
    <h1>Tinjau Jurnal PKL</h1>
    <p>Periksa dan beri persetujuan atas aktivitas PKL yang dicatat siswa.</p>
  </div>
  <a href="{{ route('guru.pkl.index') }}" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div class="pk-card" style="margin-bottom:20px">
  <div class="pk-head">
    <div class="pk-student">
      <div class="pk-avatar">{{ strtoupper(mb_substr($placement->student?->full_name ?? 'S', 0, 1)) }}</div>
      <div style="min-width:0">
        <div class="pk-name">{{ $placement->student?->full_name ?? '-' }}</div>
        <div class="pk-sub">NISN {{ $placement->student?->nisn ?? '-' }} &bull; {{ $placement->student?->class_name ?? '-' }}</div>
      </div>
    </div>
    <span class="pk-status" style="background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)">{{ $placement->status_label }}</span>
  </div>

  <div class="pk-meta">
    <div class="m"><div class="m-label">Perusahaan</div><div class="m-val" style="font-size:.8rem;font-weight:700">{{ $placement->company?->nama ?? '-' }}</div></div>
    <div class="m"><div class="m-label">Rentang PKL</div><div class="m-val" style="font-size:.8rem;font-weight:700">{{ $placement->start_date->format('d M Y') }} — {{ $placement->end_date->format('d M Y') }}</div></div>
    <div class="m"><div class="m-label">Menunggu</div><div class="m-val" style="color:#d97706">{{ $counts['pending'] }}</div></div>
    <div class="m"><div class="m-label">Total Jurnal</div><div class="m-val">{{ $activities->count() }}</div></div>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Daftar Aktivitas</h2><p>Jurnal dicatat oleh siswa dan menunggu persetujuan Anda.</p></div>
  </div>

  @if ($activities->isEmpty())
    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">Belum ada aktivitas PKL yang dicatat siswa ini.</div>
  @else
    <div style="padding:4px 0">
      @foreach ($activities as $a)
        @php
          $statusColor = ['pending' => '#d97706', 'approved' => '#16a34a', 'rejected' => '#dc2626'][$a->status];
        @endphp
        <div class="pk-activity">
          <div class="pk-date-badge">
            <b>{{ $a->tanggal->format('d') }}</b>
            <small>{{ $a->tanggal->translatedFormat('M') }}</small>
          </div>
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:4px">
              <span class="pk-status" style="background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $a->status_label }}</span>
              @if ($a->approver)
              <span style="font-size:.72rem;color:var(--muted)">Diproses {{ $a->responded_at?->format('d M Y H:i') }} oleh {{ $a->approver->full_name ?? $a->approver->name }}</span>
              @endif
            </div>
            <p style="font-size:.9rem;color:var(--ink);margin:0 0 6px;line-height:1.55">{{ $a->aktivitas }}</p>
            @if ($a->keterangan)
            <p style="font-size:.78rem;color:var(--muted);margin:0 0 4px"><strong>Hasil:</strong> {{ $a->keterangan }}</p>
            @endif
            @if ($a->foto_path)
            <div style="margin:8px 0 4px">
              <a href="{{ Storage::url($a->foto_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;font-weight:700;color:var(--primary-2);text-decoration:none">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ $a->foto_name ?? 'Lihat foto' }}
              </a>
            </div>
            @endif

            @if ($a->status === 'pending')
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:10px">
              <form method="POST" action="{{ route('guru.pkl.approve', $a->id) }}" style="display:flex;gap:8px;flex-wrap:wrap;flex:1;align-items:center">
                @csrf
                <input type="text" name="catatan_pembimbing" class="act-note" placeholder="Catatan pembimbing (opsional)" maxlength="1000">
                <button type="submit" class="btn btn-primary" style="min-height:36px;padding:0 16px;font-size:.82rem">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  Setujui
                </button>
              </form>
              <form method="POST" action="{{ route('guru.pkl.reject', $a->id) }}">
                @csrf
                <input type="hidden" name="catatan_pembimbing" value="">
                <button type="submit" class="btn btn-outline" style="min-height:36px;padding:0 16px;font-size:.82rem;color:#dc2626;border-color:color-mix(in srgb,#dc2626 40%,var(--line))">Tolak</button>
              </form>
            </div>
            @elseif ($a->catatan_pembimbing)
            <div style="margin-top:10px;padding:10px 12px;border-radius:10px;background:color-mix(in srgb,var(--bg) 55%,var(--card));border:1px solid var(--line)">
              <strong style="font-size:.72rem;color:{{ $statusColor }};text-transform:uppercase;letter-spacing:.04em">Catatan pembimbing</strong>
              <p style="font-size:.84rem;color:var(--ink);margin:3px 0 0">{{ $a->catatan_pembimbing }}</p>
            </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</section>
@endsection

@push('scripts')
@if (session('success'))
<script>Swal.fire({ icon: 'success', title: 'Berhasil', text: {!! json_encode(session('success')) !!}, confirmButtonColor: '#16a34a' });</script>
@endif
@if (session('error'))
<script>Swal.fire({ icon: 'error', title: 'Gagal', text: {!! json_encode(session('error')) !!}, confirmButtonColor: '#dc2626' });</script>
@endif
@endpush
