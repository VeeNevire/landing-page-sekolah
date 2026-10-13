@php $statusLabels = \App\Http\Controllers\Admin\AttendanceRecapController::STATUSES; @endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Rekap Absensi {{ $period }}</title>
  <style>
    @page { margin: 24px 28px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #1f2937; }
    h1 { font-size: 15px; margin: 0 0 2px; }
    .meta { margin: 0 0 10px; color: #4b5563; }
    .summary { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .summary td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: center; }
    .summary b { display: block; font-size: 12px; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background: #dce8f2; border: 1px solid #9ca3af; padding: 4px; font-size: 9px; }
    table.data td { border: 1px solid #d1d5db; padding: 3px 4px; }
    table.data tr:nth-child(even) td { background: #f9fafb; }
    table.data tfoot td { font-weight: bold; background: #eef2f7; }
    .num { text-align: center; }
    .note { margin-top: 8px; color: #6b7280; font-size: 8.5px; }
  </style>
</head>
<body>
  <h1>Rekap Absensi {{ config('school.name', config('app.name')) }}</h1>
  <p class="meta">Periode {{ $period }} · Kelas: {{ $filters['class'] ?: 'Semua kelas' }}@if($filters['search']) · Pencarian: {{ $filters['search'] }}@endif · Dicetak {{ now()->format('d/m/Y H:i') }} WIB</p>

  <table class="summary">
    <tr>
      <td>Siswa<b>{{ $summary['students'] }}</b></td>
      <td>Hari efektif<b>{{ $summary['days'] }}</b></td>
      @foreach($statusLabels as $key => $label)<td>{{ $label }}<b>{{ $summary[$key] }}</b></td>@endforeach
      <td>Rata-rata kehadiran<b>{{ $summary['percentage'] !== null ? number_format($summary['percentage'], 1, ',', '.').'%' : '-' }}</b></td>
    </tr>
  </table>

  <table class="data">
    <thead>
      <tr><th>No</th><th>Nama</th><th>NIS</th><th>Kelas</th>@foreach($statusLabels as $label)<th>{{ $label }}</th>@endforeach<th>Belum Tercatat</th><th>% Kehadiran</th></tr>
    </thead>
    <tbody>
    @forelse($rows as $i => $row)
      <tr>
        <td class="num">{{ $i + 1 }}</td>
        <td>{{ $row['name'] }}</td>
        <td>{{ $row['nis'] }}</td>
        <td>{{ $row['class'] }}</td>
        @foreach($statusLabels as $key => $label)<td class="num">{{ $row[$key] }}</td>@endforeach
        <td class="num">{{ $row['unrecorded'] }}</td>
        <td class="num">{{ $row['percentage'] !== null ? number_format($row['percentage'], 1, ',', '.').'%' : '-' }}</td>
      </tr>
    @empty
      <tr><td colspan="11" class="num">Tidak ada siswa aktif yang sesuai dengan filter ini.</td></tr>
    @endforelse
    </tbody>
    @if($rows->isNotEmpty())
    <tfoot>
      <tr>
        <td></td><td colspan="3">Total</td>
        @foreach($statusLabels as $key => $label)<td class="num">{{ $summary[$key] }}</td>@endforeach
        <td class="num">{{ $summary['unrecorded'] }}</td>
        <td class="num">{{ $summary['percentage'] !== null ? number_format($summary['percentage'], 1, ',', '.').'%' : '-' }}</td>
      </tr>
    </tfoot>
    @endif
  </table>
  <p class="note">Hari efektif = tanggal dalam periode yang memiliki minimal satu catatan absensi. % Kehadiran = (Hadir + Terlambat) ÷ hari efektif.</p>
</body>
</html>
