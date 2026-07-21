@extends('layouts.guru')
@section('title', 'Nilai Tugas')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>{{ $assignment->title }}</h1>
    <p>{{ $ta->class_name }} — {{ $ta->subject->name ?? $ta->customSubject->nama ?? '-' }} @if($assignment->due_date) | Due: {{ $assignment->due_date->format('d M Y H:i') }} @endif</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div class="portal-panel" style="padding:12px 18px;margin-bottom:20px">
  <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
    <a href="{{ route('guru.tugas.index', ['ta_id' => $assignment->teaching_assignment_id]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:.82rem;color:var(--muted);text-decoration:none;border:1.5px solid var(--line);border-radius:10px">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      Kembali
    </a>
    @if ($assignment->attachment_path)
      <a href="{{ route('download.assignment', $assignment) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:.82rem;color:var(--primary);text-decoration:none;border:1.5px solid var(--line);border-radius:10px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download Lampiran
      </a>
    @endif
    <span style="font-size:.82rem;color:var(--muted)">{{ $students->count() }} siswa — Skor maks: {{ $assignment->max_score }}</span>
  </div>
</div>

<div class="table-wrap">
  <table class="grade-table">
    <thead>
      <tr>
        <th style="text-align:left">Siswa</th>
        <th style="text-align:center">Status</th>
        <th style="text-align:center">Waktu</th>
        <th style="text-align:center">File</th>
        <th style="text-align:center">Nilai</th>
        <th style="text-align:left">Feedback</th>
        <th style="text-align:center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($students as $student)
        @php
          $submission = $student->submissions->first();
          $grade = $submission?->grade;
          $submitted = !is_null($submission);
        @endphp
        <tr>
          <td>
            <strong style="font-size:.85rem;color:var(--ink)">{{ $student->full_name }}</strong>
          </td>
          <td style="text-align:center">
            @if ($submitted)
              <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:#d1fae5;color:#065f46">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $submission->is_late ? 'Terlambat' : 'Selesai' }}
              </span>
            @else
              <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:#fef3c7;color:#92400e">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Belum
              </span>
            @endif
          </td>
          <td style="text-align:center;font-size:.78rem;color:var(--muted)">
            {{ $submitted ? $submission->submitted_at->format('d M H:i') : '-' }}
          </td>
          <td style="text-align:center">
            @if ($submitted)
              <a href="{{ route('download.submission', $submission) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:.75rem;color:var(--primary);text-decoration:none;font-weight:600">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download
              </a>
            @else
              <span style="font-size:.78rem;color:var(--muted)">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if ($grade)
              <strong style="font-size:.9rem;color:var(--ink)">{{ $grade->score }}</strong>
              <span style="font-size:.72rem;color:var(--muted)">/{{ $assignment->max_score }}</span>
            @else
              <span style="font-size:.78rem;color:var(--muted)">—</span>
            @endif
          </td>
          <td style="max-width:180px">
            @if ($grade && $grade->feedback)
              <span style="font-size:.78rem;color:var(--muted);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $grade->feedback }}</span>
            @else
              <span style="font-size:.78rem;color:var(--muted)">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if ($submitted)
              <button onclick="openGradeModal({{ $student->id }}, {{ $submission->id }}, '{{ addslashes($student->full_name) }}', {{ $grade->score ?? 'null' }}, '{{ addslashes($grade->feedback ?? '') }}')" style="padding:6px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--card);cursor:pointer;font-size:.78rem;color:var(--ink);font-weight:600">
                {{ $grade ? 'Ubah' : 'Nilai' }}
              </button>
            @else
              <span style="font-size:.78rem;color:var(--muted)">—</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Grade Modal --}}
<div id="gradeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;place-items:center" onclick="if(event.target===this)closeGradeModal()">
  <div style="background:var(--card);border-radius:16px;padding:28px;max-width:460px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.2)">
    <h3 style="margin:0 0 4px;font-size:1.05rem">Nilai Tugas</h3>
    <p id="grade-student-name" style="font-size:.85rem;color:var(--muted);margin:0 0 18px"></p>
    <form id="gradeForm" method="POST">
      @csrf
      <div class="field" style="margin-bottom:14px">
        <label for="score">Skor (0 — {{ $assignment->max_score }})</label>
        <input id="grade-score" name="score" type="number" min="0" max="{{ $assignment->max_score }}" step="0.5" required placeholder="0">
      </div>
      <div class="field" style="margin-bottom:18px">
        <label for="feedback">Feedback (opsional)</label>
        <textarea id="grade-feedback" name="feedback" placeholder="Komentar untuk siswa..." style="min-height:80px"></textarea>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn-primary" type="submit" style="flex:1">Simpan Nilai</button>
        <button type="button" onclick="closeGradeModal()" style="padding:10px 20px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);cursor:pointer;font-size:.85rem;font-weight:600;color:var(--muted)">Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
function openGradeModal(studentId, submissionId, name, score, feedback) {
  document.getElementById('grade-student-name').textContent = name;
  document.getElementById('grade-score').value = score || '';
  document.getElementById('grade-feedback').value = feedback || '';
  document.getElementById('gradeForm').action = '{{ url('guru/submissions') }}/' + submissionId + '/grade';
  document.getElementById('gradeModal').style.display = 'grid';
}

function closeGradeModal() {
  document.getElementById('gradeModal').style.display = 'none';
}
</script>
@endsection
