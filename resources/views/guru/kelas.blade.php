@extends('layouts.guru')

@section('title', 'Kelas Saya')

@php
  $validClasses = collect($classList)->filter(fn($c) => is_array($c));
  $allClassAvg = $validClasses->pluck('subject_averages')->flatten()->filter();
  $overallAvg = $allClassAvg->count() > 0 ? round($allClassAvg->avg(), 1) : '-';
  $allClassAttendance = $validClasses->filter(fn($c) => isset($c['total_attendance_days']) && $c['total_attendance_days'] > 0);
  $overallAttendance = $allClassAttendance->count() > 0
    ? round($allClassAttendance->avg('attendance_rate'), 1)
    : '-';
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Kelas</span>
    <h1>Kelas Saya</h1>
    <p>Daftar kelas yang Anda ajar semester ini.</p>
  </div>
</div>

<section class="portal-kpis" style="grid-template-columns:repeat(4,1fr)">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Kelas</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ count($classList) }}</strong>
    <span class="portal-kpi-note">Kelas aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Siswa</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ collect($classList)->sum('student_count') }}</strong>
    <span class="portal-kpi-note">Siswa aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Rata-rata Nilai</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $overallAvg }}</strong>
    <span class="portal-kpi-note">Semua kelas</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Rata-rata Kehadiran</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $overallAttendance }}%</strong>
    <span class="portal-kpi-note">Semua kelas</span>
  </article>
</section>

{{-- Class Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px">
  @foreach ($classList as $class)
  @php
    $classArr = is_array($class) ? $class : [];
    $allAvg = collect($classArr['subject_averages'] ?? [])->filter()->values();
    $overallClassAvg = $allAvg->count() > 0 ? round($allAvg->avg(), 1) : null;
  @endphp
  <div class="card card-hover" style="padding:0;overflow:hidden;cursor:pointer" data-class="{{ $classArr['name'] ?? '' }}">
    <div style="padding:22px">
      <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px">
        <div>
          <h3 style="margin:0;font-size:1.25rem">{{ $classArr['name'] ?? '' }}</h3>
          <span style="color:var(--muted);font-size:.85rem">{{ $classArr['student_count'] ?? 0 }} siswa aktif</span>
        </div>
        <span style="width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:900;font-size:.82rem;flex-shrink:0">{{ $classArr['student_count'] ?? 0 }}</span>
      </div>

      @if ($overallClassAvg !== null)
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;font-size:.85rem;margin-bottom:5px">
          <span style="font-weight:600;color:var(--muted)">Rata-rata</span>
          <span style="font-weight:800;color:var(--primary-2)">{{ $overallClassAvg }}</span>
        </div>
        <div class="progress-track" style="height:7px"><div class="progress-fill" style="--score:{{ $overallClassAvg }}%"></div></div>
      </div>
      @endif

      @if (($classArr['total_attendance_days'] ?? 0) > 0)
      <div style="display:flex;align-items:center;gap:8px;font-size:.85rem">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span style="font-weight:700;color:{{ ($classArr['attendance_rate'] ?? 0) >= 90 ? 'var(--success)' : (($classArr['attendance_rate'] ?? 0) >= 75 ? '#b45309' : '#ef4444') }}">{{ $classArr['attendance_rate'] ?? 0 }}%</span>
        <span style="color:var(--muted)">kehadiran</span>
      </div>
      @endif
    </div>

    <div style="border-top:1px solid var(--line);padding:12px 22px;display:flex;justify-content:space-between;align-items:center">
      <a href="{{ route('guru.nilai', ['class' => $classArr['name'] ?? '']) }}" class="btn btn-outline" style="min-height:34px;padding:0 16px;font-size:.82rem;border-radius:10px;display:inline-flex;align-items:center;gap:5px" onclick="event.stopPropagation()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
        Input Nilai
      </a>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5"><path d="m9 18 6-6-6-6"/></svg>
    </div>
  </div>
  @endforeach
</div>

{{-- Rekap Nilai --}}
<section class="portal-panel" style="margin-top:28px">
  <div class="portal-panel-header">
    <div>
      <h2 style="margin:0;font-size:1.1rem">Rekap Nilai per Kelas</h2>
      <p style="color:var(--muted);font-size:.85rem;margin:4px 0 0">Rata-rata nilai per mata pelajaran di setiap kelas.</p>
    </div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Kelas</th>
          @php
            $allSubjects = collect($classList)->pluck('subjects')->flatten()
                ->filter(fn($s) => is_array($s) && isset($s['id']))
                ->unique('id')->sortBy('name')->values()->all();
          @endphp
          @foreach ($allSubjects as $subject)
            <th>{{ $subject['name'] ?? '-' }}</th>
          @endforeach
          <th>Rata-rata</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($classList as $class)
          @php
            $c = is_array($class) ? $class : [];
            $classAvgs = collect($c['subject_averages'] ?? [])->filter()->values();
            $classAvg = $classAvgs->count() > 0 ? round($classAvgs->avg(), 1) : '-';
          @endphp
          <tr>
            <td><strong>{{ $c['name'] ?? '' }}</strong></td>
            @foreach ($allSubjects as $subject)
              @php $avg = $c['subject_averages'][$subject['id'] ?? 0] ?? null; @endphp
              <td>
                @if ($avg !== null)
                  <span style="font-weight:700;color:{{ $avg >= 85 ? 'var(--success)' : ($avg >= 75 ? 'var(--primary-2)' : '#ef4444') }}">{{ $avg }}</span>
                @else
                  <span style="color:var(--muted)">-</span>
                @endif
              </td>
            @endforeach
            <td><strong style="color:var(--primary-2)">{{ $classAvg }}</strong></td>
          </tr>
        @empty
          <tr><td colspan="{{ count($allSubjects) + 2 }}" style="text-align:center;padding:20px;color:var(--muted)">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

{{-- Rekap Kehadiran --}}
<section class="portal-panel" style="margin-top:20px">
  <div class="portal-panel-header">
    <div>
      <h2 style="margin:0;font-size:1.1rem">Rekap Kehadiran per Kelas</h2>
      <p style="color:var(--muted);font-size:.85rem;margin:4px 0 0">Ringkasan kehadiran siswa di setiap kelas.</p>
    </div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Kelas</th>
          <th>Hadir</th>
          <th>Sakit</th>
          <th>Izin</th>
          <th>Terlambat</th>
          <th>Alpa</th>
          <th>Rate</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($classList as $class)
          @php $c = is_array($class) ? $class : []; @endphp
          @if (($c['total_attendance_days'] ?? 0) > 0)
          <tr>
            <td><strong>{{ $c['name'] ?? '' }}</strong></td>
            <td style="color:var(--success);font-weight:700">{{ $c['attendance']['present'] ?? 0 }}</td>
            <td>{{ $c['attendance']['sick'] ?? 0 }}</td>
            <td>{{ $c['attendance']['excused'] ?? 0 }}</td>
            <td>{{ $c['attendance']['late'] ?? 0 }}</td>
            <td style="color:#ef4444;font-weight:700">{{ $c['attendance']['unexcused'] ?? 0 }}</td>
            <td>
              <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ ($c['attendance_rate'] ?? 0) >= 90 ? 'var(--success)' : (($c['attendance_rate'] ?? 0) >= 75 ? '#b45309' : '#ef4444') }} 12%,var(--card));color:{{ ($c['attendance_rate'] ?? 0) >= 90 ? 'var(--success)' : (($c['attendance_rate'] ?? 0) >= 75 ? '#b45309' : '#ef4444') }}">{{ $c['attendance_rate'] ?? 0 }}%</span>
            </td>
          </tr>
          @endif
        @empty
          <tr><td colspan="7" style="text-align:center;padding:20px;color:var(--muted)">Belum ada data kehadiran.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

{{-- Detail Modal --}}
<div class="admin-modal-overlay" id="kelasModal">
  <div class="admin-modal-box" style="max-width:620px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Detail Kelas</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="wizard-steps" style="margin-bottom:16px">
      <div class="wizard-step active" id="tabSiswa" onclick="switchTab('siswa')">
        <span class="wizard-step-dot"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
        <span class="wizard-step-label">Siswa</span>
      </div>
      <div class="wizard-step" id="tabNilai" onclick="switchTab('nilai')">
        <span class="wizard-step-dot"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg></span>
        <span class="wizard-step-label">Nilai</span>
      </div>
      <div class="wizard-step" id="tabAbsensi" onclick="switchTab('absensi')">
        <span class="wizard-step-dot"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="wizard-step-label">Absensi</span>
      </div>
    </div>

    <div class="admin-modal-body" style="padding-top:0;max-height:60vh;overflow-y:auto">
      <div id="contentSiswa"></div>
      <div id="contentNilai" style="display:none"></div>
      <div id="contentAbsensi" style="display:none"></div>
    </div>
  </div>
</div>

<style>
.grade-bar{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.grade-bar-label{width:130px;font-size:.85rem;font-weight:600;flex-shrink:0}
.grade-bar-track{flex:1;height:8px;border-radius:99px;background:var(--line);overflow:hidden}
.grade-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--primary),var(--primary-2))}
.grade-bar-value{width:40px;text-align:right;font-size:.85rem;font-weight:800;color:var(--primary-2)}
.attendance-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
.attendance-box{padding:14px;border-radius:12px;border:1px solid var(--line);text-align:center}
.attendance-box strong{display:block;font-size:1.3rem}
.attendance-box span{font-size:.78rem;color:var(--muted)}
</style>

@push('scripts')
<script>
document.querySelectorAll('[data-class]').forEach(card => {
  card.addEventListener('click', function() {
    openDetailModal(this.dataset.class);
  });
});

function openDetailModal(className) {
  document.getElementById('modalTitle').textContent = 'Kelas ' + className;
  switchTab('siswa');
  document.getElementById('kelasModal').classList.add('open');

  fetch('/guru/kelas/' + encodeURIComponent(className) + '/data', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    renderSiswa(data);
    renderNilai(data);
    renderAbsensi(data);
  });
}

function switchTab(tab) {
  document.getElementById('tabSiswa').classList.toggle('active', tab === 'siswa');
  document.getElementById('tabNilai').classList.toggle('active', tab === 'nilai');
  document.getElementById('tabAbsensi').classList.toggle('active', tab === 'absensi');
  document.getElementById('contentSiswa').style.display = tab === 'siswa' ? 'block' : 'none';
  document.getElementById('contentNilai').style.display = tab === 'nilai' ? 'block' : 'none';
  document.getElementById('contentAbsensi').style.display = tab === 'absensi' ? 'block' : 'none';
}

function renderSiswa(data) {
  let html = '<div style="margin-bottom:12px;font-size:.88rem;color:var(--muted)">' + data.students.length + ' siswa aktif</div>';
  html += '<table class="grade-table" style="font-size:.88rem"><thead><tr><th>Nama</th><th>NISN</th><th>Lahir</th></tr></thead><tbody>';
  data.students.forEach(s => {
    html += '<tr><td><strong>' + escHtml(s.full_name) + '</strong></td><td style="font-family:monospace">' + escHtml(s.nisn) + '</td><td style="color:var(--muted)">' + escHtml(s.birth_date || '-') + '</td></tr>';
  });
  html += '</tbody></table>';
  document.getElementById('contentSiswa').innerHTML = html;
}

function renderNilai(data) {
  let html = '';
  if (data.overall_average) {
    html += '<div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding:14px;border-radius:12px;background:color-mix(in srgb,var(--primary-2) 8%,var(--card))"><strong style="font-size:1.5rem;color:var(--primary-2)">' + data.overall_average + '</strong><span style="font-size:.88rem;color:var(--muted)">Rata-rata keseluruhan</span></div>';
  }
  data.subject_grades.forEach(sg => {
    const pct = sg.average ? Math.min(sg.average, 100) : 0;
    html += '<div class="grade-bar">';
    html += '<div class="grade-bar-label">' + escHtml(sg.subject) + '</div>';
    html += '<div class="grade-bar-track"><div class="grade-bar-fill" style="width:' + pct + '%"></div></div>';
    html += '<div class="grade-bar-value">' + (sg.average || '-') + '</div>';
    html += '</div>';
  });
  if (!data.subject_grades.length) {
    html = '<div style="text-align:center;padding:30px;color:var(--muted)">Belum ada data nilai.</div>';
  }
  document.getElementById('contentNilai').innerHTML = html;
}

function renderAbsensi(data) {
  const a = data.attendance || {};
  const total = data.total_attendance_days || 0;
  const rate = data.attendance_rate || 0;
  let html = '<div style="text-align:center;margin-bottom:18px"><strong style="font-size:2rem;color:' + (rate >= 90 ? 'var(--success)' : (rate >= 75 ? '#b45309' : '#ef4444')) + '">' + rate + '%</strong><div style="font-size:.85rem;color:var(--muted)">Tingkat kehadiran</div></div>';
  html += '<div class="attendance-grid">';
  html += '<div class="attendance-box"><strong style="color:var(--success)">' + (a.present || 0) + '</strong><span>Hadir</span></div>';
  html += '<div class="attendance-box"><strong style="color:#b45309">' + ((a.sick || 0) + (a.late || 0)) + '</strong><span>Sakit / Terlambat</span></div>';
  html += '<div class="attendance-box"><strong style="color:var(--primary-2)">' + (a.excused || 0) + '</strong><span>Izin</span></div>';
  html += '<div class="attendance-box"><strong style="color:#ef4444">' + (a.unexcused || 0) + '</strong><span>Alpa</span></div>';
  html += '</div>';
  html += '<div style="text-align:center;margin-top:14px;font-size:.85rem;color:var(--muted)">Total ' + total + ' hari rekaman kehadiran</div>';
  document.getElementById('contentAbsensi').innerHTML = html;
}

function closeModal() {
  document.getElementById('kelasModal').classList.remove('open');
}

function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = str || '';
  return d.innerHTML;
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
document.getElementById('kelasModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
</script>
@endpush
@endsection



