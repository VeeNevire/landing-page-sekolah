@extends('layouts.portal')

@section('title', 'Jadwal Pelajaran')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Jadwal pelajaran</span>
        <h1>Jadwal Kelas</h1>
        <p>Jadwal pelajaran {{ $demoStudent['name'] }} — {{ $demoStudent['class'] }} Semester {{ $demoStudent['semester'] }}.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
      </div>
    </div>

    @php
      $days = ['Senin','Selasa','Rabu','Kamis','Jumat'];
      $grouped = [];
      foreach ($schedule as $item) {
          $grouped[$item['day']][] = $item;
      }
    @endphp

    <section class="portal-panel">
      <div class="portal-panel-header"><div><h2>Jadwal Mingguan</h2><p>{{ count($schedule) }} jam pelajaran per minggu.</p></div></div>
      <div class="table-wrap">
        <table class="grade-table">
          <thead>
            <tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Guru</th></tr>
          </thead>
          <tbody>
            @foreach ($days as $day)
              @if (isset($grouped[$day]))
                @foreach ($grouped[$day] as $i => $item)
                  <tr>
                    @if ($i === 0)
                      <td rowspan="{{ count($grouped[$day]) }}" style="font-weight:800;vertical-align:top;border-right:2px solid var(--line)">{{ $day }}</td>
                    @endif
                    <td>{{ $item['time'] }}</td>
                    <td><strong>{{ $item['subject'] }}</strong></td>
                    <td style="color:var(--muted);font-size:.88rem">{{ $item['teacher'] }}</td>
                  </tr>
                @endforeach
              @endif
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
@endif
@endsection
