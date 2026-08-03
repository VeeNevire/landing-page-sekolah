@extends('layouts.guru')

@section('title', 'Izin / Sakit Siswa')

@push('styles')
<style>
.izin-header-grid { display:grid; gap:12px }
.izin-4 { grid-template-columns:repeat(4,1fr) }
.izin-2 { grid-template-columns:repeat(2,1fr) }
.iz-flex { display:flex; align-items:flex-start; justify-content:space-between }
.iz-card { padding:16px; border-radius:14px; background:var(--card); border:1px solid var(--line) }
.iz-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); margin-bottom:4px }
.iz-value { font-size:1.8rem; font-weight:800; line-height:1 }
.iz-badge { display:inline-block; padding:3px 10px; border-radius:99px; font-size:.72rem; font-weight:700 }

.req-card { padding:16px; border-radius:14px; background:var(--card); border:1px solid var(--line); margin-bottom:12px }
.req-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px }
.req-student { display:flex; align-items:center; gap:10px; min-width:0 }
.req-avatar { width:38px; height:38px; border-radius:10px; display:grid; place-items:center; font-weight:800; font-size:.85rem; background:color-mix(in srgb,var(--primary-2) 12%,var(--card)); color:var(--primary-2); flex-shrink:0 }
.req-name { font-weight:700; font-size:.88rem }
.req-sub { font-size:.74rem; color:var(--muted) }
.req-meta { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:12px }
.req-meta .m { padding:10px; border-radius:10px; background:color-mix(in srgb,var(--bg) 55%,var(--card)); border:1px solid var(--line) }
.req-meta .m-label { font-size:.68rem; font-weight:600; color:var(--muted); margin-bottom:2px }
.req-meta .m-val { font-size:.86rem; font-weight:700; color:var(--ink) }
.req-reason { font-size:.84rem; color:var(--muted); margin-bottom:12px }
.response-note { font-size:.84rem; padding:10px 12px; border-radius:10px; background:color-mix(in srgb,var(--bg) 55%,var(--card)); border:1px solid var(--line); margin-top:10px }

.act-row { display:flex; gap:8px; flex-wrap:wrap; align-items:center }
.act-note { flex:1; min-width:200px; padding:9px 12px; border-radius:9px; border:1.5px solid var(--line); background:var(--card); color:var(--ink); font-size:.82rem; font-family:inherit }
@media (max-width:768px){ .izin-4,.izin-2{grid-template-columns:1fr} .req-meta{grid-template-columns:1fr} .iz-flex{flex-direction:column} }
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Wali kelas</span>
    <h1>Izin / Sakit</h1>
    <p>Tinjau dan setujui pengajuan izin/sakit siswa kelas binaan.</p>
  </div>
</div>

@if (!$kelas)
<section class="portal-panel">
  <div style="padding:2rem;text-align:center;color:var(--muted)">
    <h3 style="font-size:1rem;margin:0 0 4px;color:var(--ink)">Belum ada kelas binaan</h3>
    <p style="font-size:.88rem;margin:0">Anda belum ditetapkan sebagai wali kelas.</p>
  </div>
</section>
@else

<div class="izin-header-grid izin-4" style="margin-bottom:20px">
  <div class="iz-card">
    <div class="iz-flex">
      <div><div class="iz-label">Menunggu</div><div class="iz-value" style="color:#d97706">{{ $counts['pending'] }}</div></div>
    </div>
  </div>
  <div class="iz-card">
    <div><div class="iz-label">Disetujui</div><div class="iz-value" style="color:#16a34a">{{ $counts['approved'] }}</div></div>
  </div>
  <div class="iz-card">
    <div><div class="iz-label">Ditolak</div><div class="iz-value" style="color:#dc2626">{{ $counts['rejected'] }}</div></div>
  </div>
  <div class="iz-card">
    <div><div class="iz-label">Total</div><div class="iz-value">{{ $requests->count() }}</div></div>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Daftar Pengajuan</h2><p>Kelas {{ $kelas->nama_lengkap }} &mdash; pengajuan yang ditinjau hanya yang diajukan orang tua melalui portal.</p></div>
  </div>

  @if ($requests->isEmpty())
    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">Belum ada pengajuan izin/sakit.</div>
  @else
    @foreach ($requests as $r)
      @php
        $statusColor = ['pending' => '#d97706', 'approved' => '#16a34a', 'rejected' => '#dc2626'][$r->status];
      @endphp
      <div class="req-card">
        <div class="req-head">
          <div class="req-student">
            <div class="req-avatar">{{ strtoupper(mb_substr($r->student?->full_name ?? 'S', 0, 1)) }}</div>
            <div style="min-width:0">
              <div class="req-name">{{ $r->student?->full_name ?? '-' }}</div>
              <div class="req-sub">NISN {{ $r->student?->nisn }} &bull; {{ $r->student?->class_name }}</div>
            </div>
          </div>
          <span class="iz-badge" style="background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $r->status_label }}</span>
        </div>

        <div class="req-meta">
          <div class="m"><div class="m-label">Jenis</div><div class="m-val">{{ $r->type_label }}</div></div>
          <div class="m"><div class="m-label">Rentang tanggal</div><div class="m-val">{{ $r->start_date->format('d M Y') }} - {{ $r->end_date->format('d M Y') }}</div></div>
          <div class="m"><div class="m-label">Lampiran</div><div class="m-val">
            @if ($r->attachment_path)
              <a href="javascript:void(0)" onclick="previewLampiran(this)" data-preview-url="{{ route('download.izin.preview', $r->id) }}" data-download-url="{{ route('download.izin', $r->id) }}" data-name="{{ $r->attachment_name }}" style="color:var(--primary-2);font-weight:700">Lihat Lampiran</a>
            @else
              -
            @endif
          </div></div>
        </div>

        <div class="req-reason"><strong style="color:var(--ink)">Keterangan:</strong> {{ $r->reason }}</div>

        @if ($r->status === 'pending')
          <div class="act-row">
            <form method="POST" action="{{ route('guru.wali.izin.approve', $r->id) }}" style="display:flex;gap:8px;flex-wrap:wrap;flex:1;align-items:center">
              @csrf
              <input type="text" name="response_note" class="act-check" placeholder="Catatan (opsional)" maxlength="500">
              <button type="submit" class="btn btn-primary" style="min-height:36px;padding:0 16px;font-size:.82rem">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Setujui &amp; Catat Absen
              </button>
            </form>
            <form method="POST" action="{{ route('guru.wali.izin.reject', $r->id) }}">
              @csrf
              <input type="hidden" name="response_note" value="">
              <button type="submit" class="btn btn-outline" style="min-height:36px;padding:0 16px;font-size:.82rem;color:#dc2626;border-color:color-mix(in srgb,#dc2626 40%,var(--line))">Tolak</button>
            </form>
          </div>
        @else
          @if ($r->response_note)
            <div class="response-note"><strong>Catatan wali kelas:</strong> {{ $r->response_note }}</div>
          @endif
        @endif
      </div>
    @endforeach
  @endif
</section>
@endif
@endsection

@push('scripts')
<script>
function previewLampiran(el) {
  var url = el.getAttribute('data-preview-url');
  var dl = el.getAttribute('data-download-url');
  var name = el.getAttribute('data-name') || 'Lampiran';

  Swal.fire({
    title: '<span style="font-size:1.05rem">Lampiran &mdash; ' + name + '</span>',
    html:
      '<iframe src="' + url + '" style="width:100%;height:58vh;border:1px solid var(--line);border-radius:10px;background:#fff"></iframe>' +
      '<div style="margin-top:12px;text-align:center"><a href="' + dl + '" class="btn btn-outline" style="min-height:36px;padding:0 18px;display:inline-flex;align-items:center;gap:6px;font-size:.82rem">Unduh</a></div>',
    width: '760px',
    showConfirmButton: false,
    showCloseButton: true,
  });
}

@if (session('success'))
Swal.fire({ icon: 'success', title: 'Berhasil', text: {!! json_encode(session('success')) !!}, confirmButtonColor: '#16a34a' });
@endif
@if (session('error'))
  Swal.fire({ icon: 'error', title: 'Gagal', text: {!! json_encode(session('error')) !!}, confirmButtonColor: '#dc2626' });
@endif
</script>
@endpush