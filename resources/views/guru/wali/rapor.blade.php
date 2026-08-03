@extends('layouts.guru')

@section('title', 'Rapor Wali Kelas')

@push('styles')
<style>
.bento { display:grid; gap:12px }
.bento-4 { grid-template-columns:repeat(4,1fr) }
.bento-2 { grid-template-columns:repeat(2,1fr) }
.b-flex-between { display:flex; align-items:flex-start; justify-content:space-between }

.b-card-stat { padding:16px; border-radius:14px; background:var(--card); border:1px solid var(--line) }
.b-stat-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); margin-bottom:4px }
.b-stat-value { font-size:1.8rem; font-weight:800; line-height:1; margin-bottom:2px }
.b-stat-icon { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; flex-shrink:0 }
.b-stat-icon svg { width:18px; height:18px }

.b-grade-letter { display:inline-block; padding:2px 8px; border-radius:6px; font-size:.72rem; font-weight:700 }
.b-grade-letter.pass { background:color-mix(in srgb,#34C759 12%,var(--card)); color:#34C759 }
.b-grade-letter.fail { background:color-mix(in srgb,#FF3B30 12%,var(--card)); color:#FF3B30 }

.student-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:10px }
.student-card { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:12px; background:var(--card); border:1.5px solid var(--line); cursor:pointer; transition:all .15s ease; text-decoration:none; color:inherit }
.student-card:hover { border-color:var(--primary-3); transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.06) }
.student-card.active { border-color:var(--primary); background:color-mix(in srgb,var(--primary-4) 8%,var(--card)) }
.student-avatar { width:38px; height:38px; border-radius:10px; display:grid; place-items:center; font-weight:800; font-size:.85rem; background:color-mix(in srgb,var(--primary-2) 12%,var(--card)); color:var(--primary-2); flex-shrink:0 }
.student-info { flex:1; min-width:0 }
.student-name { font-weight:700; font-size:.85rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
.student-nisn { font-size:.72rem; color:var(--muted) }
.student-avg-score { text-align:center; flex-shrink:0; margin-left:auto; padding-left:12px; min-width:60px }
.student-avg-value { font-size:1.15rem; font-weight:800; line-height:1 }
.student-avg-label { font-size:.6rem; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; margin-top:2px }

.rapor-table { width:100%; border-collapse:collapse; font-size:.82rem }
.rapor-table th, .rapor-table td { padding:8px 10px; border-bottom:1px solid var(--line); text-align:center }
.rapor-table th { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); background:color-mix(in srgb,var(--bg) 60%,var(--card)) }
.rapor-table td.mapel { text-align:left; font-weight:700 }
.rapor-table td.mapel small { display:block; font-weight:500; color:var(--muted) }

.action-bar { display:flex; gap:8px; flex-wrap:wrap }
.action-bar .btn { min-height:36px; padding:0 14px; display:inline-flex; align-items:center; gap:6px; font-size:.82rem }

@media (max-width:768px) {
  .bento-4 { grid-template-columns:repeat(2,1fr) }
  .bento-2 { grid-template-columns:1fr }
}
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Wali kelas</span>
    <h1>Rapor</h1>
    <p>Buat dan unduh rapor resmi untuk siswa kelas binaan.</p>
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

<section class="portal-panel" style="margin-bottom:20px">
  <div class="portal-panel-header">
    <div>
      <h2>Pilih Siswa</h2>
      <p>Kelas {{ $kelas->nama_lengkap }} &mdash; {{ $students->count() }} siswa aktif</p>
    </div>
    <div class="action-bar">
      <a href="{{ route('guru.wali.rapor.pdf-semua') }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh Semua PDF (ZIP)
      </a>
      @if ($selectedStudent)
      <a href="{{ route('guru.wali.rapor.preview', $selectedStudent->id) }}" target="_blank" class="btn btn-outline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        Lihat &amp; Cetak
      </a>
      <a href="{{ route('guru.wali.rapor.pdf', $selectedStudent->id) }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh PDF Siswa
      </a>
      @endif
    </div>
  </div>

  <div class="student-grid">
    @foreach ($students as $s)
    <a href="{{ route('guru.wali.rapor', ['student_id' => $s->id]) }}" class="student-card {{ $selectedStudent && $selectedStudent->id === $s->id ? 'active' : '' }}">
      <div class="student-avatar">{{ strtoupper(mb_substr($s->full_name ?? 'S', 0, 1)) }}</div>
      <div class="student-info">
        <div class="student-name">{{ $s->full_name }}</div>
        <div class="student-nisn">NISN {{ $s->nisn }}</div>
      </div>
    </a>
    @endforeach
  </div>
</section>

@if ($selectedStudent && $report)

<section class="portal-panel" style="margin-bottom:20px">
  <div class="portal-panel-header">
    <div>
      <h2>Rapor {{ $report['name'] }}</h2>
      <p>NISN {{ $report['nisn'] }} &bull; {{ $report['class'] }} &bull; Semester {{ $report['semester'] }} Tahun Ajaran {{ $report['academic_year'] }}</p>
    </div>
  </div>

  <div class="bento bento-4" style="margin-bottom:16px">
    <div class="b-card-stat">
      <div class="b-flex-between">
        <div>
          <div class="b-stat-label">Rata-rata</div>
          <div class="b-stat-value" style="background:linear-gradient(135deg,var(--primary),var(--primary-2));-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ number_format($report['average'], 1, ',', '.') }}</div>
        </div>
        <div class="b-stat-icon" style="background:linear-gradient(135deg,var(--primary),var(--primary-2));box-shadow:0 4px 12px rgba(107,163,199,0.25)">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
        </div>
      </div>
      <div style="margin-top:6px">
        <span class="b-grade-letter pass">{{ \App\Helpers\PortalHelper::gradeLetter($report['average']) }}</span>
        <span style="font-size:.72rem;color:var(--muted);margin-left:6px">dari {{ count($report['subjects']) }} mapel</span>
      </div>
    </div>

    <div class="b-card-stat">
      <div class="b-flex-between">
        <div>
          <div class="b-stat-label">Tuntas</div>
          <div class="b-stat-value" style="background:linear-gradient(135deg,#34C759,#30D158);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ count(array_filter($report['subjects'], fn($g) => $g['final'] >= $g['kkm'])) }}</div>
        </div>
        <div class="b-stat-icon" style="background:linear-gradient(135deg,#34C759,#30D158);box-shadow:0 4px 12px rgba(52,199,89,0.2)">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
      </div>
      <div style="font-size:.72rem;color:var(--muted);margin-top:6px">dari {{ count($report['subjects']) }} mapel</div>
    </div>

    <div class="b-card-stat">
      <div class="b-flex-between">
        <div>
          <div class="b-stat-label">Perlu Remedial</div>
          <div class="b-stat-value" style="background:linear-gradient(135deg,#FF3B30,#FF453A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ count(array_filter($report['subjects'], fn($g) => $g['final'] < $g['kkm'])) }}</div>
        </div>
        <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF3B30,#FF453A);box-shadow:0 4px 12px rgba(255,59,48,0.2)">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
      </div>
      <div style="font-size:.72rem;color:var(--muted);margin-top:6px">di bawah KKM</div>
    </div>

    <div class="b-card-stat">
      <div class="b-flex-between">
        <div>
          <div class="b-stat-label">Kehadiran</div>
          <div class="b-stat-value" style="background:linear-gradient(135deg,#FF9F0A,#FFB340);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ number_format($report['attendanceRate'], 1, ',', '.') }}%</div>
        </div>
        <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF9F0A,#FFB340);box-shadow:0 4px 12px rgba(255,159,10,0.25)">
          <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
      </div>
      <div style="font-size:.72rem;color:var(--muted);margin-top:6px">{{ $report['attendance']['present'] }} hari hadir</div>
    </div>
  </div>

  <div class="table-wrap">
    <table class="rapor-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Mata Pelajaran</th>
          <th>KKM</th>
          <th>Kuis</th>
          <th>PR</th>
          <th>Tugas</th>
          <th>Proyek</th>
          <th>UTS</th>
          <th>UAS</th>
          <th>Nilai Akhir</th>
          <th>Predikat</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($report['subjects'] as $i => $subject)
        @php $c = $subject['components']; $pass = $subject['final'] >= $subject['kkm']; @endphp
        <tr>
          <td>{{ $i + 1 }}</td>
          <td class="mapel">{{ $subject['name'] }}<small>{{ $subject['teacher'] }}</small></td>
          <td>{{ number_format($subject['kkm'], 0) }}</td>
          <td>{{ $c['quiz'] > 0 ? number_format($c['quiz'], 1, ',', '.') : '-' }}</td>
          <td>{{ $c['homework'] > 0 ? number_format($c['homework'], 1, ',', '.') : '-' }}</td>
          <td>{{ $c['assignment'] > 0 ? number_format($c['assignment'], 1, ',', '.') : '-' }}</td>
          <td>{{ $c['project'] > 0 ? number_format($c['project'], 1, ',', '.') : '-' }}</td>
          <td>{{ $c['uts'] > 0 ? number_format($c['uts'], 1, ',', '.') : '-' }}</td>
          <td>{{ $c['uas'] > 0 ? number_format($c['uas'], 1, ',', '.') : '-' }}</td>
          <td><strong>{{ number_format($subject['final'], 1, ',', '.') }}</strong></td>
          <td><span class="b-grade-letter {{ $pass ? 'pass' : 'fail' }}">{{ $subject['letter'] }}</span></td>
          <td><span class="b-grade-letter {{ $pass ? 'pass' : 'fail' }}">{{ $subject['mastery'] }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

@elseif ($selectedStudent)
<section class="portal-panel">
  <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">Belum ada data rapor untuk siswa ini.</div>
</section>
@endif

@endif
@endsection
