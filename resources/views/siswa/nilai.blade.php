@extends('layouts.siswa')
@section('title', 'Nilai')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Laporan Nilai</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Ringkasan nilai per mata pelajaran</p>
</div>

<div class="bento bento-4" style="margin-bottom:16px">
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Rata-rata</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $avgScore }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));box-shadow:0 4px 12px rgba(107,163,199,0.25)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
      </div>
    </div>
    <div style="margin-top:6px">
      <span class="b-grade-letter pass">{{ $avgLetter }}</span>
      <span style="font-size:.72rem;color:var(--s-muted);margin-left:6px">dari {{ count($grades) }} mapel</span>
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
    <div style="font-size:.72rem;color:var(--s-muted);margin-top:6px">dari {{ count($grades) }} mapel</div>
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
    <div style="font-size:.72rem;color:var(--s-muted);margin-top:6px">di bawah KKM</div>
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
    <div style="font-size:.72rem;color:var(--s-muted);margin-top:6px">skor tertinggi di kelas</div>
  </div>
</div>

<div class="bento bento-full" style="margin-bottom:16px">
  <div class="b-card" style="padding:16px 20px">
    @php $weightOrder = ['quiz' => 'Kuis', 'homework' => 'PR', 'assignment' => 'Tugas', 'project' => 'Proyek', 'uts' => 'UTS', 'uas' => 'UAS']; @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M8 21h8"/><path d="m3 7 4 4-4 4"/><path d="m21 7-4 4 4 4"/></svg>
        <h3 style="font-size:.88rem;font-weight:700;color:var(--s-ink);margin:0">Pembobotan Nilai</h3>
      </div>
      <span style="font-size:.72rem;font-weight:600;color:var(--s-muted)">Total 100%</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:8px">
      @foreach($weightOrder as $key => $label)
      <div style="padding:10px 12px;border-radius:10px;background:var(--s-bg);border:1px solid var(--s-line);text-align:center">
        <div style="font-size:.66rem;font-weight:600;color:var(--s-muted);text-transform:uppercase">{{ $label }}</div>
        <div style="font-size:1.05rem;font-weight:800;margin-top:2px;color:var(--s-primary-dark)">{{ round($weights[$key] * 100) }}%</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

<div class="bento bento-grade">
  @foreach($grades as $g)
  <div class="b-grade">
    <div class="b-grade-header">
      <div style="display:flex;align-items:center;gap:10px">
        <div class="b-grade-icon" style="background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark))">{{ substr($g['subject'], 0, 2) }}</div>
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
      @foreach(['quiz' => 'Kuis', 'homework' => 'PR', 'assignment' => 'Tugas', 'project' => 'Proyek', 'uts' => 'UTS', 'uas' => 'UAS'] as $key => $label)
      <div class="b-comp-item">
        <div class="b-comp-label">{{ $label }}</div>
        <div class="b-comp-value" style="color:{{ $g['components'][$key] > 0 ? 'var(--s-ink)' : 'var(--s-line)' }}">{{ $g['components'][$key] > 0 ? number_format($g['components'][$key], 1) : '-' }}</div>
      </div>
      @endforeach
    </div>

    <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--s-line)">
      <div class="b-flex-between" style="margin-bottom:4px">
        <span style="font-size:.78rem;font-weight:600;color:var(--s-ink)">Nilai Akhir</span>
        <span style="font-size:.82rem;font-weight:700;color:var(--s-ink)">{{ $g['final_score'] }}</span>
      </div>
      <div class="b-progress">
        <div class="b-progress-fill {{ $g['passed'] ? '' : '' }}" style="width:{{ min($g['final_score'], 100) }}%;background:{{ $g['passed'] ? 'linear-gradient(90deg,#34C759,#30D158)' : 'linear-gradient(90deg,#FF3B30,#FF453A)' }}"></div>
      </div>
      <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--s-line);font-size:.78rem;color:var(--s-muted)">
        <span>Rata-rata kelas: <strong style="color:var(--s-ink)">{{ $g['class_avg'] ?: '-' }}</strong></span>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
