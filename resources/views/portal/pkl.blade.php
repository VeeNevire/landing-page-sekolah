@extends('layouts.portal')

@section('title', 'PKL / Aktivitas')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
<div class="portal-heading">
  <div>
    <span class="kicker">Praktik Kerja Lapangan</span>
    <h1>PKL / Aktivitas Siswa</h1>
    <p>Pantau penempatan dan jurnal aktivitas PKL {{ $demoStudent['name'] }}.</p>
  </div>
  @if ($activePlacement)
  <a class="btn btn-primary" href="{{ route('portal.pkl.csv', ['student_id' => $selectedStudentId]) }}" style="min-height:38px;padding:0 16px;display:inline-flex;align-items:center;gap:6px;font-size:.85rem;flex-shrink:0">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
    Unduh CSV
  </a>
  @endif
</div>

<div class="report-profile">
  <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
  <div>
    <h2>{{ $demoStudent['name'] }}</h2>
    <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
    <p>Wali Kelas {{ $demoStudent['homeroom_teacher'] }}</p>
  </div>
</div>

@if ($placements->isEmpty())
  <section class="portal-panel">
    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">
      Belum ada data penempatan PKL untuk {{ $demoStudent['name'] }}.
    </div>
  </section>
@else

<section class="portal-kpis" style="margin-bottom:20px">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Jurnal</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $stats['total'] }}</strong>
    <span class="portal-kpi-note">Aktivitas tercatat</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Disetujui</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $stats['approved'] }}</strong>
    <span class="portal-kpi-note good">Oleh pembimbing</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Menunggu</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $stats['pending'] }}</strong>
    <span class="portal-kpi-note">Menunggu persetujuan</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Ditolak</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $stats['rejected'] }}</strong>
    <span class="portal-kpi-note" style="color:var(--danger)">Perlu perbaikan</span>
  </article>
</section>

<section class="portal-panel" style="margin-bottom:20px">
  <div class="portal-panel-header">
    <div><h2>Penempatan PKL</h2><p>Riwayat penempatan siswa di perusahaan mitra.</p></div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Perusahaan</th>
          <th>Bidang</th>
          <th>Pembimbing</th>
          <th>Rentang PKL</th>
          <th>Status</th>
          <th>Jurnal</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($placements as $p)
          @php
            $statusColor = ['active' => '#16a34a', 'selesai' => '#2563eb', 'batal' => '#dc2626'][$p->status];
          @endphp
          <tr>
            <td>
              <strong>{{ $p->company?->nama ?? '-' }}</strong>
              @if ($p->company?->kota)
              <div style="font-size:.76rem;color:var(--muted)">{{ $p->company->kota }}</div>
              @endif
            </td>
            <td style="font-size:.85rem">{{ $p->company?->bidang ?? '-' }}</td>
            <td style="font-size:.85rem">{{ $p->guruPembimbing?->full_name ?? $p->guruPembimbing?->name ?? '-' }}</td>
            <td style="font-size:.85rem">{{ $p->start_date->format('d M Y') }} — {{ $p->end_date->format('d M Y') }}</td>
            <td><span style="display:inline-block;padding:3px 10px;border-radius:99px;font-size:.72rem;font-weight:700;background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $p->status_label }}</span></td>
            <td style="text-align:center"><strong>{{ $p->activities_count }}</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Jurnal Aktivitas</h2><p>{{ $activePlacement ? 'Penempatan: ' . ($activePlacement->company?->nama ?? '-') : 'Belum ada penempatan aktif.' }}</p></div>
  </div>

  @if ($activities->isEmpty())
    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">Belum ada aktivitas PKL yang dicatat.</div>
  @else
    <div class="table-wrap">
      <table class="grade-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Kegiatan</th>
            <th>Hasil</th>
            <th>Foto</th>
            <th>Status</th>
            <th>Catatan Pembimbing</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($activities as $a)
            @php
              $statusColor = ['pending' => '#d97706', 'approved' => '#16a34a', 'rejected' => '#dc2626'][$a->status];
            @endphp
            <tr>
              <td style="white-space:nowrap;font-size:.85rem"><strong>{{ $a->tanggal->translatedFormat('d M Y') }}</strong></td>
              <td style="max-width:320px;font-size:.86rem">{{ $a->aktivitas }}</td>
              <td style="font-size:.82rem;color:var(--muted)">{{ $a->keterangan ?: '-' }}</td>
              <td>
                @if ($a->foto_path)
                <a href="{{ Storage::url($a->foto_path) }}" target="_blank" class="text-link" style="font-size:.82rem">{{ $a->foto_name ?? 'Lihat foto' }}</a>
                @else
                <span style="color:var(--line)">-</span>
                @endif
              </td>
              <td><span style="display:inline-block;padding:3px 10px;border-radius:99px;font-size:.72rem;font-weight:700;background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $a->status_label }}</span></td>
              <td style="color:var(--muted);font-size:.82rem;max-width:220px">{{ $a->catatan_pembimbing ?: '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</section>
@endif
@endif
@endsection