@extends('layouts.siswa')
@section('title', $quiz->title)
@push('styles')
<style>
.quiz-nav-btn{width:36px;height:36px;border-radius:10px;border:1.5px solid var(--s-line);background:var(--s-card);cursor:pointer;display:grid;place-items:center;font-size:.8rem;font-weight:700;color:var(--s-muted);transition:all .15s}
.quiz-nav-btn:hover{border-color:var(--s-primary);color:var(--s-primary)}
.quiz-nav-btn.active{background:var(--s-primary);border-color:var(--s-primary);color:#fff}
.quiz-nav-btn.answered{background:color-mix(in srgb,var(--s-primary) 15%,transparent);border-color:var(--s-primary);color:var(--s-primary)}
.quiz-nav-btn.flagged{position:relative}
.quiz-nav-btn.flagged::after{content:'!';position:absolute;top:-4px;right:-4px;width:16px;height:16px;border-radius:50%;background:#f59e0b;color:#fff;font-size:.6rem;display:grid;place-items:center}
</style>
@endpush
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.05rem;font-weight:700;color:var(--s-ink);margin:0">{{ $quiz->title }}</h2>
  <p style="font-size:.78rem;color:var(--s-muted);margin:2px 0 0">{{ $quiz->teachingAssignment->subject->name ?? $quiz->teachingAssignment->customSubject->nama ?? '-' }}</p>
</div>

<div style="display:grid;gap:16px;grid-template-columns:1fr 220px">
  {{-- Main: Question --}}
  <div class="b-card" style="padding:20px">
    <div id="timer-bar" style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding:10px 14px;border-radius:10px;background:color-mix(in srgb,var(--s-primary) 6%,transparent);font-size:.82rem;font-weight:600">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span id="timer-display" style="flex:1">—</span>
      <span style="color:var(--s-muted);font-weight:400" id="question-counter">1 / {{ $questions->count() }}</span>
    </div>

    <form id="quizForm" method="POST" action="{{ route('siswa.kuis.submit', $attempt->id) }}">
      @csrf

      <div id="question-container">
        @foreach ($questions as $index => $question)
          @php
            $bank = $question->questionBank;
            $qNum = $index + 1;
            $existing = $existingAnswers->get($question->id);
          @endphp
          <div class="question-page" data-q="{{ $qNum }}" style="display:{{ $index === 0 ? 'block' : 'none' }}">
            <div style="margin-bottom:16px">
              <span style="font-size:.72rem;font-weight:600;color:var(--s-muted);text-transform:uppercase">Soal {{ $qNum }} dari {{ $questions->count() }}</span>
              <span style="font-size:.72rem;color:var(--s-muted);margin-left:8px">({{ $question->points }} poin)</span>
              <div style="margin-top:10px">
                <p style="font-size:.9rem;font-weight:600;color:var(--s-ink);line-height:1.6;white-space:pre-wrap;margin:0">{{ $bank->question_text }}</p>
              </div>
            </div>

            @if ($bank->question_type === 'multiple_choice')
              <div style="display:grid;gap:8px">
                @foreach ($bank->options ?? [] as $opt)
                  <label style="display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:10px;border:1.5px solid var(--s-line);cursor:pointer;transition:all .15s;background:var(--s-card)" onmouseover="this.style.borderColor='var(--s-primary)'" onmouseout="this.style.borderColor=''">
                    <input type="radio" name="answers[{{ $question->id }}][option]" value="{{ $opt['label'] }}" @checked($existing && $existing->selected_option === $opt['label']) style="width:auto">
                    <span style="font-weight:600;color:var(--s-ink);font-size:.85rem">{{ $opt['label'] }}. {{ $opt['text'] }}</span>
                  </label>
                @endforeach
              </div>
            @elseif ($bank->question_type === 'true_false')
              <div style="display:flex;gap:10px">
                @foreach (['true' => 'True', 'false' => 'False'] as $val => $label)
                  <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:14px;border-radius:10px;border:1.5px solid var(--s-line);cursor:pointer;font-weight:600;color:var(--s-ink);background:var(--s-card);transition:all .15s" onmouseover="this.style.borderColor='var(--s-primary)'" onmouseout="this.style.borderColor=''">
                    <input type="radio" name="answers[{{ $question->id }}][option]" value="{{ $val }}" @checked($existing && $existing->selected_option === $val) style="width:auto">
                    {{ $label }}
                  </label>
                @endforeach
              </div>
            @else
              <textarea name="answers[{{ $question->id }}][text]" placeholder="Tulis jawaban..." style="min-height:120px;font-size:.85rem">{{ $existing->answer_text ?? '' }}</textarea>
            @endif
          </div>
        @endforeach
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;padding-top:16px;border-top:1px solid var(--s-line)">
        <button type="button" onclick="navigateQuestion(-1)" class="btn btn-primary" style="padding:8px 20px;font-size:.8rem;opacity:0;visibility:hidden" id="prevBtn">← Sebelumnya</button>
        <button type="button" onclick="flagQuestion()" style="padding:8px 16px;border-radius:10px;border:1.5px solid var(--s-line);background:var(--s-card);cursor:pointer;font-size:.78rem;color:var(--s-muted)">Ragu-ragu</button>
        <button type="button" onclick="navigateQuestion(1)" class="btn btn-primary" style="padding:8px 20px;font-size:.8rem" id="nextBtn">Selanjutnya →</button>
      </div>

      <button type="submit" style="width:100%;margin-top:16px;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));color:#fff;font-weight:700;font-size:.9rem;cursor:pointer" onclick="return confirmSubmit()">Kumpulkan Jawaban</button>
    </form>
  </div>

  {{-- Sidebar: Navigation --}}
  <div class="b-card" style="padding:16px">
    <h4 style="font-size:.82rem;font-weight:700;color:var(--s-ink);margin:0 0 12px">Navigasi Soal</h4>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px" id="nav-grid">
      @foreach ($questions as $index => $question)
        <button type="button" class="quiz-nav-btn {{ $index === 0 ? 'active' : '' }}" onclick="goToQuestion({{ $index + 1 }})" data-q="{{ $index + 1 }}">{{ $index + 1 }}</button>
      @endforeach
    </div>
    <p style="font-size:.68rem;color:var(--s-muted);margin:12px 0 0;line-height:1.5">
      <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:var(--s-primary);vertical-align:text-bottom;margin-right:3px"></span> Aktif<br>
      <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:color-mix(in srgb,var(--s-primary) 30%,transparent);vertical-align:text-bottom;margin-right:3px"></span> Terjawab<br>
      <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#f59e0b;vertical-align:text-bottom;margin-right:3px"></span> Ragu-ragu
    </p>
  </div>
</div>

<script>
const totalQuestions = {{ $questions->count() }};
let currentQ = 1;
let flagged = new Set();

@if ($endTime)
  const endTime = new Date('{{ $endTime->format('Y-m-d H:i:s') }}').getTime();
  function updateTimer() {
    const now = Date.now();
    const diff = endTime - now;
    if (diff <= 0) {
      document.getElementById('timer-display').textContent = 'Waktu habis!';
      document.getElementById('quizForm').submit();
      return;
    }
    const m = Math.floor(diff / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    document.getElementById('timer-display').textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
  }
  updateTimer();
  setInterval(updateTimer, 1000);
@else
  document.getElementById('timer-bar').style.display = 'none';
@endif

function goToQuestion(n) {
  if (n < 1 || n > totalQuestions) return;
  currentQ = n;
  document.querySelectorAll('.question-page').forEach(p => p.style.display = 'none');
  document.querySelector('.question-page[data-q="' + n + '"]').style.display = 'block';
  document.querySelectorAll('.quiz-nav-btn').forEach(b => b.classList.remove('active'));
  document.querySelector('.quiz-nav-btn[data-q="' + n + '"]').classList.add('active');
  document.getElementById('question-counter').textContent = n + ' / ' + totalQuestions;
  document.getElementById('prevBtn').style.visibility = n === 1 ? 'hidden' : 'visible';
  document.getElementById('prevBtn').style.opacity = n === 1 ? '0' : '1';
  document.getElementById('nextBtn').textContent = n === totalQuestions ? 'Selesai' : 'Selanjutnya →';
  updateNavStatus();
}

function navigateQuestion(dir) {
  const next = currentQ + dir;
  if (next < 1 || next > totalQuestions) return;
  updateAnsweredStatus(currentQ);
  goToQuestion(next);
}

function flagQuestion() {
  const q = currentQ;
  if (flagged.has(q)) { flagged.delete(q); } else { flagged.add(q); }
  const btn = document.querySelector('.quiz-nav-btn[data-q="' + q + '"]');
  btn.classList.toggle('flagged');
}

function updateAnsweredStatus(q) {
  const page = document.querySelector('.question-page[data-q="' + q + '"]');
  const hasInput = page.querySelector('input[type="radio"]:checked, textarea:not(:blank)');
  const btn = document.querySelector('.quiz-nav-btn[data-q="' + q + '"]');
  if (hasInput) btn.classList.add('answered');
}

function updateNavStatus() {
  document.querySelectorAll('.question-page').forEach((page, i) => {
    const q = i + 1;
    const hasInput = page.querySelector('input[type="radio"]:checked, textarea:not(:blank)');
    const btn = document.querySelector('.quiz-nav-btn[data-q="' + q + '"]');
    if (hasInput) btn.classList.add('answered');
  });
}

function confirmSubmit() {
  const unanswered = document.querySelectorAll('.question-page').length - document.querySelectorAll('.quiz-nav-btn.answered').length;
  let msg = 'Kumpulkan jawaban?';
  if (unanswered > 0) msg = unanswered + ' soal belum dijawab. ' + msg;
  return confirm(msg);
}

updateNavStatus();
</script>
@endsection
