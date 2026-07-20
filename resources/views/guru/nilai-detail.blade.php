@extends('layouts.guru')

@section('title', 'Input Nilai — ' . $class . ' ' . $subject->name)

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Input nilai</span>
    <h1>{{ $subject->name }} — {{ $class }}</h1>
    <p>Masukkan nilai untuk siswa pada mata pelajaran ini.</p>
  </div>
  <div class="portal-actions no-print">
    <a class="btn btn-outline" href="{{ route('guru.nilai') }}">Kembali</a>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<section class="portal-panel" style="margin-bottom:20px">
  <div class="portal-panel-header">
    <div><h2>Tambah Penilaian Baru</h2><p>Isi data penilaian, lalu input nilai per siswa di bawah.</p></div>
  </div>
  <form method="POST" action="{{ route('guru.nilai.store', ['class' => $class, 'subject' => ($isCustom ?? false) ? 'cs_' . $subject->id : $subject->id]) }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:14px;align-items:end">
      <div class="field">
        <label for="title">Judul Penilaian</label>
        <input id="title" name="title" type="text" required placeholder="Contoh: Kuis Bab 1" value="{{ old('title') }}">
      </div>
      <div class="field">
        <label for="component">Komponen</label>
        <select id="component" name="component" required>
          <option value="quiz" @selected(old('component') == 'quiz')>Quiz</option>
          <option value="homework" @selected(old('component') == 'homework')>Tugas Rumah</option>
          <option value="project" @selected(old('component') == 'project')>Proyek</option>
          <option value="uts" @selected(old('component') == 'uts')>UTS</option>
          <option value="uas" @selected(old('component') == 'uas')>UAS</option>
        </select>
      </div>
      <button class="btn btn-primary" type="submit" style="min-height:42px">Simpan Nilai</button>
    </div>

    <div class="table-wrap" style="margin-top:18px">
      <table class="grade-table">
        <thead>
          <tr>
            <th style="width:50px">No</th>
            <th>Nama Siswa</th>
            <th style="width:120px">Nilai (0-100)</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($students as $i => $student)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><strong>{{ $student->full_name }}</strong><br><span style="color:var(--muted);font-size:.8rem">NISN {{ $student->nisn }}</span></td>
              <td>
                <input type="number" name="scores[{{ $student->id }}]" min="0" max="100" step="1"
                       value="{{ old("scores.{$student->id}") }}"
                       placeholder="—"
                       style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:10px;background:var(--bg);font-weight:700">
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px">Tidak ada siswa di kelas ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </form>
</section>

@if ($assessments->count())
<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Riwayat Penilaian</h2><p>{{ $assessments->count() }} penilaian sudah dibuat.</p></div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr><th>Judul</th><th>Komponen</th><th>Tanggal</th><th>Publish</th></tr>
      </thead>
      <tbody>
        @foreach ($assessments as $assess)
          <tr style="cursor:pointer" onclick="openNilaiDetail({{ $assess->id }})">
            <td><strong>{{ $assess->title }}</strong></td>
            <td><span style="text-transform:capitalize">{{ $assess->component }}</span></td>
            <td>{{ $assess->assessment_date->format('d M Y') }}</td>
            <td>
              @if ($assess->published_at)
                <span style="color:var(--success);font-weight:700">Published</span>
              @else
                <span style="color:var(--muted)">Draft</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endif

@push('styles')
<style>
.nilai-modal-body { max-height: 60vh; overflow-y: auto; }
.nilai-summary { display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px }
.nilai-summary-item { padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line);text-align:center }
.nilai-summary-label { font-size:.72rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.03em }
.nilai-summary-value { font-size:1.05rem;font-weight:700;color:var(--ink);margin-top:2px }
.nilai-siswa-row { display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;transition:background .1s ease }
.nilai-siswa-row:hover { background:var(--bg) }
.nilai-siswa-row + .nilai-siswa-row { border-top:1px solid var(--line) }
</style>
@endpush

@php
  $assessmentsData = $assessments->map(function($a) {
    return ['id' => $a->id, 'title' => $a->title, 'component' => $a->component, 'date' => $a->assessment_date->format('d M Y'), 'published' => $a->published_at ? true : false];
  })->values();
  $studentsData = $students->map(function($s) {
    return ['id' => $s->id, 'name' => $s->full_name];
  })->values();
@endphp

@push('scripts')
<script>
const ASSESSMENTS = @json($assessmentsData);
const SCORES = @json($scores);
const STUDENTS = @json($studentsData);
const KKM = {{ $subject->kkm ?? 75 }};

const ICON_CHECK = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
const ICON_CROSS = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
const ICON_USERS = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
const ICON_CALENDAR = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
const ICON_CHART = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>';
const ICON_TARGET = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>';
const ICON_CLIPBOARD = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';

function openNilaiDetail(assessmentId) {
  const ass = ASSESSMENTS.find(a => a.id === assessmentId);
  if (!ass) return;
  const scoresMap = SCORES[assessmentId] || {};
  const allScores = STUDENTS.map(s => scoresMap[s.id]).filter(v => v !== undefined).map(Number);
  const avg = allScores.length ? (allScores.reduce((a, b) => a + b, 0) / allScores.length).toFixed(1) : '-';
  const lulus = allScores.filter(v => v >= KKM).length;
  const total = STUDENTS.length;

  let rows = '';
  STUDENTS.forEach((s, i) => {
    const score = scoresMap[s.id];
    const pass = score !== undefined && score >= KKM;
    const iconSvg = score !== undefined ? (pass ? ICON_CHECK : ICON_CROSS) : '';
    const iconColor = score !== undefined ? (pass ? '#10b981' : '#ef4444') : '';
    rows += `<div class="nilai-siswa-row">
      <span style="width:28px;font-size:.78rem;font-weight:600;color:var(--muted);flex-shrink:0">${i + 1}</span>
      <span style="flex:1;font-size:.85rem;font-weight:600;color:var(--ink)">${s.name}</span>
      <span style="width:50px;text-align:right;font-size:.95rem;font-weight:700;color:${score !== undefined ? (pass ? 'var(--success)' : 'var(--danger)') : 'var(--line)'}">${score !== undefined ? score : '—'}</span>
      <span style="width:24px;text-align:center;flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;color:${iconColor}">${iconSvg}</span>
    </div>`;
  });

  const statusLabel = ass.published
    ? '<span style="display:inline-flex;align-items:center;gap:4px;color:var(--success);font-weight:700">' + ICON_CHECK + ' Published</span>'
    : '<span style="display:inline-flex;align-items:center;gap:4px;color:var(--muted);font-weight:600">' + ICON_CLIPBOARD + ' Draft</span>';

  Swal.fire({
    title: '<span style="display:inline-flex;align-items:center;gap:8px">' + ICON_CLIPBOARD + ' ' + ass.title + '</span>',
    html: `<div>
      <div class="nilai-summary">
        <div class="nilai-summary-item">
          <div class="nilai-summary-label">` + ICON_CLIPBOARD + ` Komponen</div>
          <div class="nilai-summary-value" style="text-transform:capitalize">${ass.component}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label">` + ICON_CALENDAR + ` Tanggal</div>
          <div class="nilai-summary-value">${ass.date}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label">` + ICON_CLIPBOARD + ` Status</div>
          <div class="nilai-summary-value">${statusLabel}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label">` + ICON_TARGET + ` KKM</div>
          <div class="nilai-summary-value">${KKM}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label">` + ICON_USERS + ` Siswa</div>
          <div class="nilai-summary-value">${total}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label">` + ICON_CHART + ` Rata-rata</div>
          <div class="nilai-summary-value">${avg}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label" style="color:var(--success)">` + ICON_CHECK + ` Lulus</div>
          <div class="nilai-summary-value" style="color:var(--success)">${lulus}</div>
        </div>
        <div class="nilai-summary-item">
          <div class="nilai-summary-label" style="color:var(--danger)">` + ICON_CROSS + ` Tidak Lulus</div>
          <div class="nilai-summary-value" style="color:var(--danger)">${total - lulus}</div>
        </div>
      </div>
      <div class="nilai-modal-body">${rows}</div>
    </div>`,
    confirmButtonText: 'Tutup',
    confirmButtonColor: '#6b7280',
    width: 520,
  });
}
</script>
@endpush
@endsection



