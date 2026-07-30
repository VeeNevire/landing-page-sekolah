@extends('layouts.guru')

@section('title', 'Nilai Wali Kelas')

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

.b-grade { padding:16px; border-radius:14px; background:var(--card); border:1px solid var(--line) }
.b-grade-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px }
.b-grade-icon { width:40px; height:40px; border-radius:12px; display:grid; place-items:center; color:#fff; font-weight:800; font-size:.82rem; flex-shrink:0 }
.b-grade-name { font-weight:700; font-size:.9rem }
.b-grade-kkm { font-size:.72rem; color:var(--muted); margin-top:1px }
.b-grade-score { font-size:1.3rem; font-weight:800; line-height:1; color:var(--ink) }

.b-comp-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:6px; padding:12px 0; border-top:1px solid var(--line); border-bottom:1px solid var(--line) }
.b-comp-item { text-align:center }
.b-comp-label { font-size:.68rem; font-weight:600; color:var(--muted); margin-bottom:2px }
.b-comp-value { font-size:.88rem; font-weight:700; color:var(--ink) }

.b-progress { height:6px; border-radius:4px; background:var(--bg); overflow:hidden; margin-top:10px }
.b-progress-fill { height:100%; border-radius:4px; transition:width .4s ease }

.student-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:10px }
.student-card { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:12px; background:var(--card); border:1.5px solid var(--line); cursor:pointer; transition:all .15s ease; text-decoration:none; color:inherit }
.student-card:hover { border-color:var(--primary-3); transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.06) }
.student-card.active { border-color:var(--primary); background:color-mix(in srgb,var(--primary-4) 8%,var(--card)) }
.student-avatar { width:38px; height:38px; border-radius:10px; display:grid; place-items:center; font-weight:800; font-size:.85rem; background:color-mix(in srgb,var(--primary-2) 12%,var(--card)); color:var(--primary-2); flex-shrink:0 }
.student-info { flex:1; min-width:0 }
.student-name { font-weight:700; font-size:.85rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
.student-nisn { font-size:.72rem; color:var(--muted) }
.student-check { color:var(--primary); flex-shrink:0 }

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
    <h1>Nilai Wali Kelas</h1>
    <p>Lihat nilai dan laporan akademik siswa kelas binaan.</p>
  </div>
</div>

@if (!$kelas)
<section class="portal-panel">
  <div style="padding:2rem;text-align:center;color:var(--muted)">
    <h3 style="font-size:1rem;margin:0 0 4px;color:var(--ink)">Belum ada kelas binaan</h3>
    <p style="font-size:.88rem;margin:0">Anda belum ditetapkan sebagai wali kelas.</p>
  </div>
</section>
@elseif ($selectedStudent)

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <div>
    <h2 style="font-size:1.1rem;font-weight:700;margin:0;color:var(--ink)">Laporan Nilai</h2>
    <p style="font-size:.82rem;color:var(--muted);margin:2px 0 0">{{ $selectedStudent->full_name }} — NISN {{ $selectedStudent->nisn }} — {{ $selectedStudent->class_name }}</p>
  </div>
  <a href="{{ route('guru.wali.nilai') }}" class="btn btn-outline" style="min-height:36px;padding:0 14px;display:inline-flex;align-items:center;gap:6px;font-size:.82rem;flex-shrink:0">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali
  </a>
</div>

@if (empty($grades))
<section class="portal-panel">
  <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">Belum ada data nilai untuk siswa ini.</div>
</section>
@else
<div class="bento bento-4" style="margin-bottom:16px">
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Rata-rata</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,var(--primary),var(--primary-2));-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $avgScore }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,var(--primary),var(--primary-2));box-shadow:0 4px 12px rgba(107,163,199,0.25)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
      </div>
    </div>
    <div style="margin-top:6px">
      <span class="b-grade-letter pass">{{ $avgLetter }}</span>
      <span style="font-size:.72rem;color:var(--muted);margin-left:6px">dari {{ count($grades) }} mapel</span>
    </div>
  </div>

  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Lulus</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#34C759,#30D158);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ count(array_filter($grades, fn($g) => $g['passed'])) }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#34C759,#30D158);box-shadow:0 4px 12px rgba(52,199,89,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
    </div>
    <div style="font-size:.72rem;color:var(--muted);margin-top:6px">dari {{ count($grades) }} mapel</div>
  </div>

  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Perlu Perbaikan</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF3B30,#FF453A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ count(array_filter($grades, fn($g) => !$g['passed'])) }}</div>
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
        <div class="b-stat-label">Nilai Tertinggi di Kelas</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF9F0A,#FFB340);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $classMaxScore }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF9F0A,#FFB340);box-shadow:0 4px 12px rgba(255,159,10,0.25)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
    </div>
    <div style="font-size:.72rem;color:var(--muted);margin-top:6px">skor tertinggi di kelas</div>
  </div>
</div>

<div class="bento bento-2">
  @foreach($grades as $g)
  <div class="b-grade">
    <div class="b-grade-header">
      <div style="display:flex;align-items:center;gap:10px">
        <div class="b-grade-icon" style="background:linear-gradient(135deg,var(--primary),var(--primary-2))">{{ substr($g['subject'], 0, 2) }}</div>
        <div>
          <div class="b-grade-name">{{ $g['subject'] }}</div>
          <div class="b-grade-kkm">KKM {{ $g['kkm'] }}</div>
        </div>
      </div>
      <div style="text-align:right">
        <div class="b-grade-score">{{ $g['final_score'] }}</div>
        <span class="b-grade-letter {{ $g['passed'] ? 'pass' : 'fail' }}" style="margin-top:2px">{{ $g['letter'] }}</span>
      </div>
    </div>

    <div class="b-comp-grid">
      @foreach(['quiz' => 'Kuis', 'homework' => 'PR', 'project' => 'Proyek', 'uts' => 'UTS', 'uas' => 'UAS'] as $key => $label)
      <div class="b-comp-item">
        <div class="b-comp-label">{{ $label }}</div>
        <div class="b-comp-value" style="color:{{ ($g['components'][$key] ?? 0) > 0 ? 'var(--ink)' : 'var(--line)' }}">{{ ($g['components'][$key] ?? 0) > 0 ? number_format($g['components'][$key], 1) : '-' }}</div>
      </div>
      @endforeach
    </div>

    <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--line)">
      <div class="b-flex-between" style="margin-bottom:4px">
        <span style="font-size:.78rem;font-weight:600;color:var(--ink)">Nilai Akhir</span>
        <span style="font-size:.82rem;font-weight:700;color:var(--ink)">{{ $g['final_score'] }}</span>
      </div>
      <div class="b-progress">
        <div class="b-progress-fill" style="width:{{ min($g['final_score'], 100) }}%;background:{{ $g['passed'] ? 'linear-gradient(90deg,#34C759,#30D158)' : 'linear-gradient(90deg,#FF3B30,#FF453A)' }}"></div>
      </div>
      <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--line);font-size:.78rem;color:var(--muted)">
        <span>Rata-rata kelas: <strong style="color:var(--ink)">{{ $g['class_avg'] ?: '-' }}</strong></span>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

@else
<section class="portal-panel">
  <div class="portal-panel-header">
    <div>
      <h2>Daftar Siswa</h2>
      <p>Kelas {{ $kelas->nama_lengkap }} — {{ $students->count() }} siswa aktif</p>
    </div>
  </div>

  <div class="student-grid">
    @foreach ($students as $s)
    <a href="{{ route('guru.wali.nilai', ['student_id' => $s->id]) }}" class="student-card">
      <div class="student-avatar">{{ strtoupper(mb_substr($s->full_name ?? 'S', 0, 1)) }}</div>
      <div class="student-info">
        <div class="student-name">{{ $s->full_name }}</div>
        <div class="student-nisn">NISN {{ $s->nisn }}</div>
      </div>
    </a>
    @endforeach
  </div>
</section>
@endif
@endsection