@extends('layouts.guru')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Jadwal</span>
    <h1>Jadwal Mengajar</h1>
    <p>Jadwal lengkap mengajar Anda semester ini.</p>
  </div>
</div>

@php
  $days = ['Senin','Selasa','Rabu','Kamis','Jumat'];
  $times = [
    '07:30 - 08:50',
    '09:00 - 10:20',
    '10:30 - 11:50',
    '13:00 - 14:20',
    '14:30 - 15:50',
  ];
  $grid = [];
  foreach ($schedule as $item) {
      $grid[$item['day']][$item['time']] = $item;
  }
@endphp

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Jadwal Mingguan</h2><p>{{ count($schedule) }} jam pelajaran per minggu.</p></div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th style="width:100px">Jam</th>
          @foreach ($days as $day)
            <th style="text-align:center">{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($times as $time)
          <tr>
            <td style="font-size:.82rem;font-weight:700;color:var(--muted)">{{ $time }}</td>
            @foreach ($days as $day)
              <td style="text-align:center">
                @if (isset($grid[$day][$time]))
                  @php $item = $grid[$day][$time] @endphp
                  <div style="padding:10px;border-radius:10px;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));border:1px solid color-mix(in srgb,var(--primary-2) 20%,var(--line))">
                    <strong style="font-size:.85rem">{{ $item['subject'] }}</strong><br>
                    <span style="font-size:.78rem;color:var(--muted)">{{ $item['class_name'] }}</span>
                  </div>
                @else
                  <span style="color:var(--line);font-size:.8rem">—</span>
                @endif
              </td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

<section class="portal-panel" style="margin-top:20px">
  <div class="portal-panel-header">
    <div><h2>Daftar Jadwal</h2><p>Semua jadwal mengajar dalam format daftar.</p></div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Kelas</th></tr>
      </thead>
      <tbody>
        @foreach ($schedule as $item)
          <tr>
            <td><strong>{{ $item['day'] }}</strong></td>
            <td>{{ $item['time'] }}</td>
            <td>{{ $item['subject'] }}</td>
            <td><span style="background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2);padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem">{{ $item['class_name'] }}</span></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection



