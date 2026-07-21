@extends('layouts.siswa')
@section('title', 'Kuis')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Kuis</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Kuis online dari guru</p>
</div>

@if (session('success'))
  <div style="padding:10px 14px;border-radius:10px;background:#d1fae5;color:#065f46;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div style="padding:10px 14px;border-radius:10px;background:#fce4ec;color:#b71c1c;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('error') }}</div>
@endif

@if ($quizzes->count())
  <div style="display:grid;gap:10px">
    @foreach ($quizzes as $quiz)
      @php
        $attemptData = $attemptCounts->get($quiz->id);
        $attemptCount = $attemptData ? $attemptData->count : 0;
        $bestScore = $attemptData ? $attemptData->best_score : null;
        $canTake = $attemptCount < $quiz->max_attempts;
        $isAvailable = (!$quiz->start_date || now()->gte($quiz->start_date)) && (!$quiz->end_date || now()->lte($quiz->end_date));
      @endphp
      <div class="b-card" style="display:flex;align-items:center;gap:14px;padding:14px 16px;cursor:pointer;transition:all .15s" onclick="startQuiz(this)" data-id="{{ $quiz->id }}" data-title="{{ $quiz->title }}" data-soal="{{ $quiz->questions->count() }}" data-time="{{ $quiz->time_limit ?? 0 }}" data-attempt="{{ $attemptCount }}" data-max="{{ $quiz->max_attempts }}" onmouseover="this.style.borderColor='var(--s-primary)'" onmouseout="this.style.borderColor=''">
        <div style="width:40px;height:40px;border-radius:10px;background:color-mix(in srgb,var(--s-primary) 12%,transparent);display:grid;place-items:center;flex-shrink:0">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:8px">
            <h3 style="font-size:.85rem;font-weight:600;color:var(--s-ink);margin:0">{{ $quiz->title }}</h3>
            @if ($bestScore !== null)
              <span style="font-size:.65rem;padding:2px 8px;border-radius:20px;background:#d1fae5;color:#065f46;font-weight:600">Nilai: {{ $bestScore }}</span>
            @endif
          </div>
          <p style="font-size:.72rem;color:var(--s-muted);margin:4px 0 0">
            {{ $quiz->teachingAssignment->subject->name ?? $quiz->teachingAssignment->customSubject->nama ?? '-' }}
            @if ($quiz->time_limit) · {{ $quiz->time_limit }} menit @endif
            · {{ $attemptCount }}/{{ $quiz->max_attempts }} attempts
            @if ($quiz->questions->count()) · {{ $quiz->questions->count() }} soal @endif
          </p>
        </div>
        <div>
          @if ($canTake && $isAvailable)
            <span style="font-size:.72rem;padding:5px 12px;border-radius:8px;background:var(--s-primary);color:#fff;font-weight:600;pointer-events:none">Mulai</span>
          @elseif (!$canTake)
            <span style="font-size:.72rem;color:var(--s-muted);font-weight:600">Habis</span>
          @else
            <span style="font-size:.72rem;color:var(--s-muted)">—</span>
          @endif
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="b-card" style="text-align:center;padding:48px">
    <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">Belum ada kuis</h3>
    <p style="font-size:.82rem;color:var(--s-muted);margin:0">Kuis akan muncul ketika guru mempublikasikannya.</p>
  </div>
@endif

<form id="quiz-start-form" method="POST" style="display:none">
  @csrf
</form>

<script>
function startQuiz(el) {
  const id = el.dataset.id;
  const title = el.dataset.title;
  const totalSoal = parseInt(el.dataset.soal);
  const timeLimit = parseInt(el.dataset.time);
  const attemptCount = parseInt(el.dataset.attempt);
  const maxAttempts = parseInt(el.dataset.max);

  if (attemptCount >= maxAttempts) {
    Swal.fire({ icon: 'info', title: 'Batas Habis', text: 'Kamu sudah mencapai batas maksimal percobaan.', confirmButtonColor: 'var(--s-primary)' });
    return;
  }

  let info = '<div style="text-align:left;line-height:1.8">';
  info += `<strong>${title}</strong><br>`;
  info += `<span style="color:var(--s-muted)">${totalSoal} soal</span>`;
  if (timeLimit > 0) info += ` · <span style="color:var(--s-muted)">${timeLimit} menit</span>`;
  info += ` · <span style="color:var(--s-muted)">Percobaan ${attemptCount + 1}/${maxAttempts}</span>`;
  info += '</div>';

  Swal.fire({
    title: 'Mulai Kuis?',
    html: info,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: 'var(--s-primary)',
    confirmButtonText: 'Ya, Mulai',
    cancelButtonText: 'Batal',
  }).then(result => {
    if (result.isConfirmed) {
      const form = document.getElementById('quiz-start-form');
      form.action = '{{ url("siswa/kuis") }}/' + id + '/mulai';
      form.submit();
    }
  });
}
</script>
@endsection
