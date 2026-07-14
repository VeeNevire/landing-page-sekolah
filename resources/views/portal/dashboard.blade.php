@extends('layouts.portal')

@section('title', 'Ringkasan')

@section('content')
@if (!$selectedStudent)
<div class="portal-empty">
  <h2>Belum ada siswa terdaftar</h2>
  <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
</div>
@else
<div class="portal-heading">
  <div>
    <span class="kicker">Ringkasan semester</span>
    <h1>Halo, {{ auth()->user()->full_name ?? auth()->user()->name }}.</h1>
    <p>Berikut perkembangan terbaru {{ $demoStudent['name'] }} pada Semester {{ $demoStudent['semester'] }} Tahun Ajaran {{ $demoStudent['academic_year'] }}.</p>
  </div>
  <div class="portal-actions no-print">
    <a class="btn btn-outline" href="{{ route('portal.laporan', ['student_id' => $selectedStudentId]) }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <polyline points="14 2 14 8 20 8" />
        <line x1="16" y1="13" x2="8" y2="13" />
        <line x1="16" y1="17" x2="8" y2="17" />
      </svg>
      Lihat Laporan Lengkap
    </a>
    <a class="btn btn-primary" href="{{ route('portal.laporan.csv', ['student_id' => $selectedStudentId]) }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="7 10 12 15 17 10" />
        <line x1="12" y1="15" x2="12" y2="3" />
      </svg>
      Unduh CSV
    </a>
  </div>
</div>

<section class="portal-kpis">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Rata-rata Nilai</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m18 20 4-4-4-4" />
          <path d="M20 16H9a4 4 0 0 1-4-4V4" />
          <line x1="2" y1="20" x2="17" y2="20" />
        </svg></span></div>
    <strong class="portal-kpi-value">{{ number_format($average, 1, ',', '.') }}</strong>
    <span class="portal-kpi-note good">Predikat {{ \App\Helpers\PortalHelper::gradeLetter($average) }} • di atas KKM {{ $demoStudent['kkm'] }}</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Kehadiran</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
          <line x1="16" y1="2" x2="16" y2="6" />
          <line x1="8" y1="2" x2="8" y2="6" />
          <line x1="3" y1="10" x2="21" y2="10" />
        </svg></span></div>
    <strong class="portal-kpi-value">{{ number_format($attendanceRate, 1, ',', '.') }}%</strong>
    <span class="portal-kpi-note good">{{ $demoStudent['attendance']['present'] }} hari hadir</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Ketuntasan Tugas</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
          <polyline points="22 4 12 14.01 9 11.01" />
        </svg></span></div>
    <strong class="portal-kpi-value">{{ number_format($completion, 0) }}%</strong>
    <span class="portal-kpi-note good">Seluruh tugas tercatat telah dinilai</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Karakter</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg></span></div>
    <strong class="portal-kpi-value">{{ $demoStudent['behavior']['discipline'] ?? '-' }}</strong>
    <span class="portal-kpi-note">Disiplin dan tanggung jawab sangat baik</span>
  </article>
</section>

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Tren Nilai Akademik</h2>
        <p>Perkembangan rata-rata hasil belajar antarsemester.</p>
      </div>
      <span class="grade-badge {{ \App\Helpers\PortalHelper::gradeClass($average) }}">{{ \App\Helpers\PortalHelper::gradeLetter($average) }}</span>
    </div>
    <div class="chart-wrap">
      <svg class="line-chart" viewBox="0 0 730 270" role="img" aria-label="Grafik tren nilai">
        <defs>
          <linearGradient id="areaFill" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#1457a6" stop-opacity=".25" />
            <stop offset="100%" stop-color="#1457a6" stop-opacity="0" />
          </linearGradient>
        </defs>
        <line class="chart-grid" x1="55" y1="55" x2="675" y2="55" />
        <line class="chart-grid" x1="55" y1="110" x2="675" y2="110" />
        <line class="chart-grid" x1="55" y1="165" x2="675" y2="165" />
        <line class="chart-grid" x1="55" y1="220" x2="675" y2="220" />
        @if (count($points))
        <polygon class="chart-area" points="{{ implode(' ', $points) }} 675,220 55,220" />
        <polyline class="chart-line" points="{{ implode(' ', $points) }}" />
        @foreach ($labels as $item)
        <circle class="chart-dot" cx="{{ $item['x'] }}" cy="{{ $item['y'] }}" r="6" />
        <text class="chart-label" x="{{ $item['x'] }}" y="{{ $item['y'] - 15 }}" text-anchor="middle">{{ $item['score'] }}</text>
        <text class="chart-label" x="{{ $item['x'] }}" y="250" text-anchor="middle">{{ $item['label'] }}</text>
        @endforeach
        @endif
      </svg>
    </div>
  </section>

  <aside class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h3>Catatan Wali Kelas</h3>
        <p>{{ $demoStudent['homeroom_teacher'] }}</p>
      </div><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
        </svg></span>
    </div>
    <div class="portal-note">
      <strong>Evaluasi perkembangan</strong>
      <p>{{ $demoStudent['teacher_note'] }}</p>
    </div>
    <a class="text-link" style="display:inline-block;margin-top:17px" href="{{ route('portal.laporan', ['student_id' => $selectedStudentId]) }}#catatan">Lihat tindak lanjut &rarr;</a>
  </aside>
</div>

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Ringkasan Mata Pelajaran</h2>
        <p>Nilai akhir berdasarkan bobot komponen penilaian.</p>
      </div>
      <a class="text-link" href="{{ route('portal.laporan', ['student_id' => $selectedStudentId]) }}">Lihat detail &rarr;</a>
    </div>
    <div class="subject-list">
      @foreach ($subjects as $subject)
      @php $score = \App\Helpers\PortalHelper::finalScore($subject); @endphp
      <div>
        <div class="subject-progress-head"><strong>{{ $subject['name'] }}</strong><strong>{{ number_format($score, 1, ',', '.') }}</strong></div>
        <div class="progress-track">
          <div class="progress-fill" style="--score:{{ min(100, $score) }}%"></div>
        </div>
      </div>
      @endforeach
    </div>
  </section>

  <div style="display:grid;gap:20px">
    <section class="portal-panel">
      <div class="portal-panel-header">
        <div>
          <h3>Kehadiran</h3>
          <p>Rekap semester berjalan.</p>
        </div><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
            <line x1="16" y1="2" x2="16" y2="6" />
            <line x1="8" y1="2" x2="8" y2="6" />
            <line x1="3" y1="10" x2="21" y2="10" />
          </svg></span>
      </div>
      <div class="attendance-grid">
        <div class="attendance-box"><strong>{{ $demoStudent['attendance']['present'] }}</strong><span>Hadir</span></div>
        <div class="attendance-box"><strong>{{ $demoStudent['attendance']['sick'] }}</strong><span>Sakit</span></div>
        <div class="attendance-box"><strong>{{ $demoStudent['attendance']['excused'] }}</strong><span>Izin</span></div>
        <div class="attendance-box"><strong>{{ $demoStudent['attendance']['unexcused'] }}</strong><span>Tanpa Keterangan</span></div>
      </div>
    </section>

    <section class="portal-panel">
      <div class="portal-panel-header">
        <div>
          <h3>Aktivitas Terbaru</h3>
          <p>Pembaruan dari guru dan sekolah.</p>
        </div>
      </div>
      <div class="activity-feed">
        @foreach ($demoStudent['activities'] as $activity)
        <div class="activity-item">
          @php $icons = ['check'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>','flask'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2" />
            <path d="M8.5 2h7" />
            <line x1="7" y1="12" x2="17" y2="12" />
          </svg>','trophy'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="6" />
            <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />
          </svg>','note'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
          </svg>','science'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v8" />
            <path d="M8 12h8" />
          </svg>','book'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20" />
          </svg>','mic'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="14" height="14" x="5" y="1" rx="2.5" ry="2.5" />
            <path d="M12 20v-6" />
            <line x1="9" y1="20" x2="15" y2="20" />
          </svg>']; @endphp
          <span class="activity-icon">{!! $icons[$activity['icon']] ?? '' !!}</span>
          <div><strong>{{ $activity['title'] }}</strong><span>{{ $activity['time'] }}</span></div>
        </div>
        @endforeach
      </div>
    </section>
  </div>
</div>
@endif
@endsection