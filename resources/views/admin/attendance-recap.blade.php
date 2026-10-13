@extends('layouts.admin')
@section('title', 'Rekap Absensi')
@section('content')
@php
  $statusLabels = \App\Http\Controllers\Admin\AttendanceRecapController::STATUSES;
  $statusColors = ['present' => 'var(--success)', 'late' => '#d97706', 'sick' => '#3d8baf', 'excused' => '#6366f1', 'unexcused' => 'var(--danger)'];
  $exportQuery = array_filter($filters);
@endphp
<div class="portal-heading">
  <div>
    <span class="kicker">Kehadiran siswa</span>
    <h1>Rekap Absensi</h1>
    <p>Rekap kehadiran per siswa periode {{ \Illuminate\Support\Carbon::parse($filters['from'])->format('d/m/Y') }} – {{ \Illuminate\Support\Carbon::parse($filters['to'])->format('d/m/Y') }}.</p>
  </div>
  <div class="recap-export">
    <a class="btn btn-outline" href="{{ route('admin.attendance-recap.pdf', $exportQuery) }}">Unduh PDF</a>
    <a class="btn btn-primary" href="{{ route('admin.attendance-recap.excel', $exportQuery) }}">Unduh Excel</a>
  </div>
</div>

@if($errors->any())<p role="alert" style="color:var(--danger)">{{ $errors->first() }}</p>@endif

<form method="GET" class="admin-toolbar">
  <div class="field"><label for="recap-from">Dari</label><input type="date" id="recap-from" name="from" value="{{ $filters['from'] }}" max="{{ today()->toDateString() }}"></div>
  <div class="field"><label for="recap-to">Sampai</label><input type="date" id="recap-to" name="to" value="{{ $filters['to'] }}" max="{{ today()->toDateString() }}"></div>
  <div class="field"><label for="recap-class">Kelas</label><select id="recap-class" name="class"><option value="">Semua kelas</option>@foreach($classes as $class)<option @selected($filters['class'] === $class)>{{ $class }}</option>@endforeach</select></div>
  <div class="field"><label for="recap-search">Cari siswa</label><input id="recap-search" name="search" value="{{ $filters['search'] }}" placeholder="Nama, NIS atau NISN"></div>
  <button class="btn btn-primary">Terapkan</button>
  <a class="btn btn-outline" href="{{ route('admin.attendance-recap.index') }}">Reset</a>
</form>

<div class="recap-presets">
  @foreach([
    'Hari ini' => [today(), today()],
    '7 hari terakhir' => [today()->subDays(6), today()],
    'Bulan ini' => [today()->startOfMonth(), today()],
    'Bulan lalu' => [today()->subMonthNoOverflow()->startOfMonth(), today()->subMonthNoOverflow()->endOfMonth()],
  ] as $label => [$from, $to])
    <a href="{{ route('admin.attendance-recap.index', array_filter(['from' => $from->toDateString(), 'to' => $to->toDateString(), 'class' => $filters['class'], 'search' => $filters['search']])) }}"
      @class(['active' => $filters['from'] === $from->toDateString() && $filters['to'] === $to->toDateString()])>{{ $label }}</a>
  @endforeach
</div>

<div class="recap-summary">
  <article><span>Siswa</span><strong>{{ $summary['students'] }}</strong></article>
  <article><span>Hari efektif</span><strong>{{ $summary['days'] }}</strong></article>
  @foreach($statusLabels as $key => $label)
  <article><span>{{ $label }}</span><strong style="color:{{ $statusColors[$key] }}">{{ $summary[$key] }}</strong></article>
  @endforeach
  <article><span>Rata-rata kehadiran</span><strong>{{ $summary['percentage'] !== null ? number_format($summary['percentage'], 1, ',', '.').'%' : '—' }}</strong></article>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table recap-table">
      <thead>
        <tr><th>No</th><th>Siswa</th><th>NIS</th><th>Kelas</th>@foreach($statusLabels as $label)<th class="num">{{ $label }}</th>@endforeach<th class="num">Belum Tercatat</th><th class="num">% Kehadiran</th></tr>
      </thead>
      <tbody>
      @forelse($rows as $i => $row)
        <tr>
          <td>{{ $offset + $i + 1 }}</td>
          <td><strong>{{ $row['name'] }}</strong></td>
          <td>{{ $row['nis'] }}</td>
          <td>{{ $row['class'] }}</td>
          @foreach($statusLabels as $key => $label)<td class="num">{{ $row[$key] ?: '·' }}</td>@endforeach
          <td class="num">{{ $row['unrecorded'] ?: '·' }}</td>
          <td class="num">
            @if($row['percentage'] === null) —
            @else <span class="recap-pct {{ $row['percentage'] >= 90 ? 'good' : ($row['percentage'] >= 75 ? 'warn' : 'bad') }}">{{ number_format($row['percentage'], 1, ',', '.') }}%</span>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="11" style="padding:0"><div class="empty-state"><h3>Tidak Ada Siswa</h3><p>Tidak ada siswa aktif yang sesuai dengan filter ini.</p></div></td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $rows->links('vendor.pagination.admin') }}</div>
</section>
<p class="recap-note"><strong>Hari efektif</strong> = tanggal dalam periode yang memiliki minimal satu catatan absensi (hari libur otomatis tidak dihitung). <strong>% Kehadiran</strong> = (Hadir + Terlambat) ÷ hari efektif. Data mencakup absensi RFID dan input manual guru.</p>
@endsection

@push('styles')
<style>
  .recap-export{display:flex;gap:8px;flex-wrap:wrap;align-items:flex-start}
  .recap-presets{display:flex;gap:8px;flex-wrap:wrap;margin:-6px 0 16px}
  .recap-presets a{padding:6px 12px;border-radius:999px;border:1px solid var(--line);font-size:.82rem;font-weight:700;color:var(--muted);text-decoration:none}
  .recap-presets a.active,.recap-presets a:hover{border-color:var(--primary-2);color:var(--primary-2)}
  .recap-summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin:0 0 18px}
  .recap-summary article{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:14px 16px}
  .recap-summary span{display:block;color:var(--muted);font-size:.78rem;font-weight:700}
  .recap-summary strong{display:block;font-size:1.5rem;font-weight:900;margin-top:4px}
  .recap-table .num{text-align:center;font-variant-numeric:tabular-nums}
  .recap-pct{padding:3px 8px;border-radius:8px;font-weight:800;font-size:.8rem}
  .recap-pct.good{background:color-mix(in srgb,var(--success) 14%,var(--card));color:var(--success)}
  .recap-pct.warn{background:color-mix(in srgb,#d97706 14%,var(--card));color:#d97706}
  .recap-pct.bad{background:color-mix(in srgb,var(--danger) 12%,var(--card));color:var(--danger)}
  .recap-note{color:var(--muted);font-size:.82rem;margin-top:12px}
</style>
@endpush
