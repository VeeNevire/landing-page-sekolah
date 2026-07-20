@extends('layouts.siswa')
@section('title', 'Jadwal')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Jadwal Pelajaran</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Jadwal pelajaran mingguan</p>
</div>

<div class="b-card" style="padding:0;overflow:hidden">
  <div style="overflow-x:auto">
    <table class="b-table">
      <thead>
        <tr>
          <th style="width:50px;text-align:center"></th>
          @foreach($days as $day)
          <th style="text-align:center;min-width:110px">{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php $palette = ['#95BDD7','#34C759','#FF9F0A','#FF3B30','#007AFF','#8B5CF6','#64D2FF','#FF6482'] @endphp
        @for($slot = 1; $slot <= 3; $slot++)
        <tr>
          <td style="text-align:center;font-size:.72rem;font-weight:600;color:var(--s-muted);width:50px">{{ $slot }}</td>
          @foreach($days as $day)
          <td style="text-align:center">
            @php $match = $jadwal[$day]->firstWhere('slot', $slot) @endphp
            @if($match)
              @php $idx = abs(crc32($match['subject'])) % 8; $c = $palette[$idx]; @endphp
              <div class="b-sched-cell" style="background:color-mix(in srgb,{{ $c }} 10%,transparent);color:{{ $c }};border:1px solid color-mix(in srgb,{{ $c }} 15%,transparent)">{{ $match['subject'] }}</div>
            @else
              <span style="color:var(--s-line);font-size:.78rem">—</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endfor
      </tbody>
    </table>
  </div>
</div>
@endsection
