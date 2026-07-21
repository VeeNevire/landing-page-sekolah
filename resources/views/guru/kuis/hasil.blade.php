@extends('layouts.guru')
@section('title', 'Hasil Kuis')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>{{ $quiz->title }}</h1>
    <p>{{ $ta->class_name }} — {{ $ta->subject->name ?? $ta->customSubject->nama ?? '-' }} | {{ $quiz->questions->count() }} soal</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<a href="{{ route('guru.kuis.index', ['ta_id' => $quiz->teaching_assignment_id]) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:.82rem;color:var(--muted);text-decoration:none;border:1.5px solid var(--line);border-radius:10px;margin-bottom:16px">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
  Kembali
</a>

<div class="table-wrap">
  <table class="grade-table">
    <thead>
      <tr>
        <th style="text-align:left">Siswa</th>
        <th style="text-align:center">Attempt</th>
        <th style="text-align:center">Skor</th>
        <th style="text-align:center">Status</th>
        <th style="text-align:center">Waktu</th>
        <th style="text-align:center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($students as $student)
        @php
          $attempt = $student->quizAttempts->sortByDesc('attempt_number')->first();
        @endphp
        <tr>
          <td><strong style="font-size:.85rem;color:var(--ink)">{{ $student->full_name }}</strong></td>
          <td style="text-align:center">{{ $attempt ? "{$attempt->attempt_number}/{$quiz->max_attempts}" : '0' }}</td>
          <td style="text-align:center">
            @if ($attempt && $attempt->total_score !== null)
              <strong style="font-size:.9rem;color:{{ $attempt->total_score >= 75 ? '#22c55e' : '#ef4444' }}">{{ $attempt->total_score }}</strong>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if ($attempt)
              <span style="display:inline-flex;align-items:center;gap:3px;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:600;background:{{ $attempt->status === 'graded' ? '#d1fae5' : ($attempt->status === 'submitted' ? '#fef3c7' : '#e5e7eb') }};color:{{ $attempt->status === 'graded' ? '#065f46' : ($attempt->status === 'submitted' ? '#92400e' : '#6b7280') }}">
                {{ $attempt->status === 'graded' ? 'Selesai' : ($attempt->status === 'submitted' ? 'Menunggu' : 'Progress') }}
              </span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td style="text-align:center;font-size:.78rem;color:var(--muted)">
            {{ $attempt ? $attempt->submitted_at?->format('d M H:i') ?? '-' : '-' }}
          </td>
          <td style="text-align:center">
            @if ($attempt && $attempt->status === 'submitted')
              <button onclick="openEssayModal({{ $attempt->id }})" style="padding:5px 12px;border-radius:8px;border:1.5px solid var(--primary);color:var(--primary);background:none;cursor:pointer;font-size:.75rem;font-weight:600">Nilai Essay</button>
            @elseif ($attempt && $attempt->status === 'graded')
              <span style="font-size:.75rem;color:var(--muted)">✔ Dinilai</span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- Essay Grading Modal --}}
<div id="essayModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;place-items:center;overflow-y:auto;padding:20px" onclick="if(event.target===this)closeEssayModal()">
  <div id="essayModalContent" style="background:var(--card);border-radius:16px;padding:28px;max-width:700px;width:100%;margin:auto;box-shadow:0 20px 60px rgba(0,0,0,.2)"></div>
</div>

<script>
function openEssayModal(attemptId) {
  fetch('/guru/kuis/' + attemptId + '/nilai-essay-data')
    .then(r => r.json())
    .then(data => {
      let html = '<h3 style="margin:0 0 4px;font-size:1.05rem">Nilai Essay</h3>';
      html += '<p style="font-size:.85rem;color:var(--muted);margin:0 0 18px">' + data.student_name + '</p>';
      html += '<form method="POST" action="/guru/kuis/' + attemptId + '/nilai-essay">@csrf';
      data.answers.forEach(a => {
        html += '<div style="padding:14px;border-radius:12px;border:1.5px solid var(--line);margin-bottom:12px">';
        html += '<strong style="font-size:.82rem;color:var(--ink);display:block;margin-bottom:6px">' + a.question_text + '</strong>';
        html += '<div style="padding:10px;border-radius:8px;background:var(--bg);font-size:.82rem;color:var(--ink);margin-bottom:10px;white-space:pre-wrap">' + (a.answer_text || '<em style="color:var(--muted)">Tidak dijawab</em>') + '</div>';
        html += '<div style="display:grid;grid-template-columns:1fr 2fr;gap:10px">';
        html += '<input type="hidden" name="answers[' + a.id + '][id]" value="' + a.id + '">';
        html += '<input type="number" name="answers[' + a.id + '][score]" placeholder="Skor" min="0" max="' + a.max_points + '" step="0.5" required style="padding:8px 10px;border-radius:8px;border:1.5px solid var(--line);font-size:.8rem">';
        html += '<input type="text" name="answers[' + a.id + '][feedback]" placeholder="Feedback (opsional)" style="padding:8px 10px;border-radius:8px;border:1.5px solid var(--line);font-size:.8rem">';
        html += '</div></div>';
      });
      html += '<div style="display:flex;gap:10px;margin-top:6px">';
      html += '<button class="btn btn-primary" type="submit" style="flex:1">Simpan Nilai</button>';
      html += '<button type="button" onclick="closeEssayModal()" style="padding:10px 20px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);cursor:pointer;font-size:.85rem;font-weight:600;color:var(--muted)">Batal</button>';
      html += '</div></form>';
      document.getElementById('essayModalContent').innerHTML = html;
      document.getElementById('essayModal').style.display = 'grid';
    });
}

function closeEssayModal() {
  document.getElementById('essayModal').style.display = 'none';
}
</script>
@endsection
