@extends('layouts.siswa')
@section('title', 'Jadwal')
@push('styles')
<style>
  .sched-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.85rem; min-width:700px }
  .sched-table th { padding:12px 8px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; text-align:center; color:var(--s-muted); border-bottom:2px solid var(--s-line); background:var(--s-bg) }
  .sched-table td { padding:8px; text-align:center; vertical-align:top; border-bottom:1px solid var(--s-line) }
  .sched-table tr:last-child td { border-bottom:none }
  .sched-time { font-size:.82rem; font-weight:700; color:var(--s-muted); white-space:nowrap; text-align:left !important; width:110px }
  .sched-cell { padding:10px 8px; border-radius:12px; background:color-mix(in srgb,var(--s-primary) 8%,var(--s-card)); border:1px solid color-mix(in srgb,var(--s-primary) 15%,var(--s-line)) }
  .sched-cell .code { font-weight:700; font-size:.85rem; color:var(--s-primary-dark) }
  .sched-cell .subject { font-size:.78rem; font-weight:600; margin-top:2px; color:var(--s-ink) }
  .sched-cell .teacher { font-size:.72rem; color:var(--s-muted); margin-top:2px }
</style>
@endpush
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Jadwal Pelajaran</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Kelas {{ $student->class_name }}</p>
</div>

<div class="b-card" style="padding:0;overflow:hidden">
  <div style="overflow-x:auto">
    <table class="sched-table">
      <thead>
        <tr>
          <th style="min-width:110px">Jam</th>
          @foreach ($days as $day)
          <th>{{ ucfirst($day) }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($timeSlots as $slot => $time)
        <tr>
          <td class="sched-time">{{ $time }}</td>
          @foreach ($days as $day)
          <td>
            @php $cell = $grid[$day][$slot] ?? null; @endphp
            @if ($cell)
            <div class="sched-cell">
              <div class="code">{{ $cell['code'] }}</div>
              <div class="subject">{{ $cell['subject'] }}</div>
              <div class="teacher">{{ $cell['teacher'] }}</div>
            </div>
            @else
            <span style="color:var(--s-line);font-size:.78rem">—</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
