@extends('layouts.siswa')
@section('title', 'Hasil Kuis')
@push('styles')
<style>
.hasil-container{max-width:720px;margin:0 auto;padding:4px 0}
.stat-card{flex:1;padding:14px;border-radius:12px;border:1.5px solid var(--s-line);background:var(--s-card);text-align:center}
.stat-card .num{font-size:1.3rem;font-weight:800;line-height:1.2}
.stat-card .lbl{font-size:.7rem;color:var(--s-muted);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-top:2px}
.review-btn{width:36px;height:36px;border-radius:10px;border:1.5px solid var(--s-line);background:var(--s-card);cursor:pointer;display:grid;place-items:center;font-size:.8rem;font-weight:700;transition:all .12s}
.review-btn:hover{border-color:var(--s-primary);transform:scale(1.05)}
.review-btn.correct{background:#d1fae5;border-color:#22c55e;color:#065f46}
.review-btn.wrong{background:#fce4ec;border-color:#ef4444;color:#b71c1c}
.review-btn.pending{background:#fef3c7;border-color:#f59e0b;color:#92400e}
.review-btn.unanswered{border-color:var(--s-line);color:var(--s-muted)}
.opt-chip{display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;font-size:.78rem;transition:all .12s}
.opt-chip.key{background:#d1fae5;color:#065f46;font-weight:600}
.opt-chip.wrong{background:#fce4ec;color:#b71c1c;font-weight:600}
.opt-chip.neutral{color:var(--s-muted)}
.opt-chip.key svg,.opt-chip.wrong svg{flex-shrink:0}
.swal-review .swal2-html-container{text-align:left!important;overflow-y:auto!important;max-height:65vh}
.swal-review .swal2-title{font-size:1rem!important}
</style>
@endpush
@section('content')
@php
  $questions = $attempt->quiz->questions;
  $totalQ = $questions->count();
  $answeredCount = $attempt->answers->count();
  $correctCount = $attempt->answers->where('is_correct', true)->count();
  $wrongCount = $attempt->answers->where('is_correct', false)->count();
  $pendingCount = $attempt->answers->whereNull('is_correct')->count();
  $unanswered = $totalQ - $answeredCount;
  $score = $attempt->total_score;
  $passed = $score !== null && $score >= 75;
  $reviewData = $questions->map(function($q) use ($attempt) {
    $answer = $attempt->answers->firstWhere('quiz_question_id', $q->id);
    $bank = $q->questionBank;
    return [
      'text' => $bank->question_text,
      'type' => $bank->question_type,
      'options' => $bank->options,
      'correct_answer' => $bank->correct_answer,
      'explanation' => $bank->explanation,
      'points' => $q->points,
      'selected_option' => $answer->selected_option ?? null,
      'answer_text' => $answer->answer_text ?? null,
      'is_correct' => $answer ? $answer->is_correct : null,
      'feedback' => $answer->feedback ?? null,
    ];
  });
@endphp
<div class="hasil-container">

<div class="b-card" style="padding:0">
  <div style="padding:16px 20px;border-bottom:1px solid var(--s-line)">
    <a href="{{ route('siswa.kuis.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;color:var(--s-primary);text-decoration:none;margin-bottom:6px;font-weight:600">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      Kembali
    </a>
    <h2 style="font-size:1.1rem;font-weight:800;color:var(--s-ink);margin:0">{{ $attempt->quiz->title }}</h2>
    <p style="font-size:.8rem;color:var(--s-muted);margin:2px 0 0">Attempt {{ $attempt->attempt_number }}</p>
  </div>
  <div style="padding:20px">

  {{-- Summary Stats --}}
  <div style="display:flex;gap:10px;margin-bottom:16px">
    <div class="stat-card">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="2" style="margin-bottom:4px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      <div class="num" style="color:var(--s-ink)">{{ $totalQ }}</div>
      <div class="lbl">Soal</div>
    </div>
    <div class="stat-card">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="2" style="margin-bottom:4px"><polyline points="20 6 9 17 4 12"/></svg>
      <div class="num" style="color:var(--s-ink)">{{ $answeredCount }}</div>
      <div class="lbl">Terjawab</div>
    </div>
    <div class="stat-card">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" style="margin-bottom:4px"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
      <div class="num" style="color:#22c55e">{{ $correctCount }}</div>
      <div class="lbl">Benar</div>
    </div>
    <div class="stat-card">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" style="margin-bottom:4px"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      <div class="num" style="color:#ef4444">{{ $wrongCount + $unanswered }}</div>
      <div class="lbl">Salah</div>
    </div>
  </div>

  {{-- Score Card --}}
  <div class="b-card" style="padding:24px;text-align:center;margin-bottom:16px">
    @if ($score !== null)
      @php $circum = 2 * pi() * 36; $offset = $circum - ($score / 100) * $circum; @endphp
      <div style="position:relative;width:100px;height:100px;margin:0 auto 14px">
        <svg width="100" height="100" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="36" fill="none" stroke="color-mix(in srgb,var(--s-primary) 12%,transparent)" stroke-width="8"/>
          <circle cx="50" cy="50" r="36" fill="none" stroke="{{ $passed ? '#22c55e' : '#ef4444' }}" stroke-width="8" stroke-linecap="round" stroke-dasharray="{{ $circum }}" stroke-dashoffset="{{ $offset }}" transform="rotate(-90 50 50)" style="transition:stroke-dashoffset 1s ease"/>
        </svg>
        <div style="position:absolute;inset:0;display:grid;place-items:center">
          <span style="font-size:1.6rem;font-weight:900;color:{{ $passed ? '#22c55e' : '#ef4444' }}">{{ round($score) }}</span>
        </div>
      </div>
      <h3 style="font-size:1rem;font-weight:700;color:{{ $passed ? '#22c55e' : '#ef4444' }};margin:0 0 4px">{{ $passed ? 'Lulus' : 'Perlu Perbaikan' }}</h3>
    @else
      <div style="position:relative;width:100px;height:100px;margin:0 auto 14px">
        <svg width="100" height="100" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="36" fill="none" stroke="color-mix(in srgb,var(--s-muted) 15%,transparent)" stroke-width="8"/>
          <circle cx="50" cy="50" r="36" fill="none" stroke="var(--s-muted)" stroke-width="8" stroke-linecap="round" stroke-dasharray="{{ 2 * pi() * 36 }}" stroke-dashoffset="0" transform="rotate(-90 50 50)"/>
        </svg>
        <div style="position:absolute;inset:0;display:grid;place-items:center">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
      </div>
      <h3 style="font-size:1rem;font-weight:700;color:var(--s-muted);margin:0 0 4px">Menunggu Nilai</h3>
      <p style="font-size:.8rem;color:var(--s-muted);margin:0">Essay sedang dinilai oleh guru.</p>
    @endif
    <p style="font-size:.75rem;color:var(--s-muted);margin:10px 0 0">Dikumpulkan {{ $attempt->submitted_at?->format('d M Y H:i') ?? '-' }}</p>
  </div>

  {{-- Detail Info --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
    <div class="b-card" style="padding:14px">
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span style="font-size:.72rem;color:var(--s-muted)">Durasi</span>
      </div>
      <div style="font-size:.85rem;font-weight:700;color:var(--s-ink);margin-top:4px">
        @if ($attempt->started_at && $attempt->submitted_at)
          @php
            $diffMins = (int) floor($attempt->started_at->diffInMinutes($attempt->submitted_at));
            $diffSecs = $attempt->started_at->diffInSeconds($attempt->submitted_at) % 60;
          @endphp
          @if ($diffMins > 0){{ $diffMins }} menit @endif{{ $diffSecs }} detik
        @else
          —
        @endif
      </div>
    </div>
    <div class="b-card" style="padding:14px">
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span style="font-size:.72rem;color:var(--s-muted)">Attempt</span>
      </div>
      <div style="font-size:.85rem;font-weight:700;color:var(--s-ink);margin-top:4px">Ke-{{ $attempt->attempt_number }}</div>
    </div>
  </div>

  {{-- Jawaban Summary --}}
  @if ($score !== null || $attempt->answers->count())
  <div style="display:flex;gap:12px;margin-bottom:16px;padding:14px 18px;border-radius:12px;background:color-mix(in srgb,var(--s-primary) 5%,transparent);border:1px solid color-mix(in srgb,var(--s-primary) 10%,transparent)">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px;font-size:.8rem;color:var(--s-ink)">
      <strong style="color:var(--s-primary);margin-right:2px">Ringkasan:</strong>
      <span style="display:inline-flex;align-items:center;gap:3px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg><span style="font-weight:700;color:#22c55e">{{ $correctCount }}</span> benar</span>
      <span style="display:inline-flex;align-items:center;gap:3px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span style="font-weight:700;color:#ef4444">{{ $wrongCount }}</span> salah</span>
      @if ($pendingCount > 0)
      <span style="display:inline-flex;align-items:center;gap:3px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span style="font-weight:700;color:#f59e0b">{{ $pendingCount }}</span> menunggu</span>
      @endif
      <span>dari <strong>{{ $totalQ }}</strong> soal</span>
    </div>
  </div>
  @endif

  {{-- Grid Review --}}
  @if ($showResult)
    <h3 style="font-size:.9rem;font-weight:700;color:var(--s-ink);margin:0 0 12px">Pembahasan</h3>
    <div style="display:grid;grid-template-columns:repeat(10,1fr);gap:6px;margin-bottom:4px" id="reviewGrid">
      @foreach ($questions as $index => $q)
        @php
          $answer = $attempt->answers->firstWhere('quiz_question_id', $q->id);
          $isCorrect = $answer && $answer->is_correct;
          $status = $isCorrect === null ? ($answer ? 'pending' : 'unanswered') : ($isCorrect ? 'correct' : 'wrong');
        @endphp
        <button type="button" class="review-btn {{ $status }}" onclick="openReview({{ $index }})">{{ $index + 1 }}</button>
      @endforeach
    </div>
  @endif

  </div>
</div>
</div>

@if ($showResult)
<script>
const questions = @json($reviewData);

function qHTML(idx) {
  const d = questions[idx];
  const num = idx + 1;
  const total = questions.length;
  let statusHTML, statusClass;
  if (d.is_correct === true) {
    statusHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg><span style="color:#22c55e">Benar</span>';
    statusClass = 'correct';
  } else if (d.is_correct === false) {
    statusHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span style="color:#ef4444">Salah</span>';
    statusClass = 'wrong';
  } else {
    statusHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span style="color:#f59e0b">Menunggu</span>';
    statusClass = 'pending';
  }

  let body = '';

  body += '<div style="display:grid;gap:6px;margin-bottom:10px">';
  if (d.type !== 'essay') {
    const userLabel = d.selected_option
      ? (d.options ? d.options.find(o => o.label === d.selected_option)?.label : d.selected_option)
      : null;
    const keyLabel = d.correct_answer;
    const ansText = userLabel || '<em style="font-style:italic;font-weight:400">Kamu tidak mengisi jawabannya</em>';
    if (d.is_correct === true) {
      body += '<div style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;background:#d1fae5;color:#065f46;font-size:.78rem;font-weight:600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Jawaban kamu: <strong>' + ansText + '</strong></div>';
    } else if (d.is_correct === false) {
      body += '<div style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;background:#fce4ec;color:#b71c1c;font-size:.78rem;font-weight:600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Jawaban kamu: <strong>' + ansText + '</strong></div>';
    } else {
      body += '<div style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;background:#fef3c7;color:#92400e;font-size:.78rem;font-weight:600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Jawaban kamu: <strong>' + (userLabel || '<em style="font-style:italic;font-weight:400">Kamu tidak mengisi jawabannya</em>') + '</strong></div>';
      body += '<div style="display:flex;align-items:center;gap:6px;padding:6px 10px;border-radius:6px;background:#fef3c7;color:#92400e;font-size:.78rem;font-weight:600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Menunggu penilaian guru</div>';
    }
  }
  body += '</div>';

  if (d.type === 'multiple_choice' && d.options) {
    body += '<div style="display:grid;gap:4px">';
    d.options.forEach(o => {
      const isSelected = d.selected_option === o.label;
      const isKey = o.label === d.correct_answer;
      let cls = 'opt-chip';
      let check = '';
      if (isKey && d.selected_option) { cls += ' key'; if (d.is_correct !== false) check = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'; }
      else if (isSelected) { cls += ' wrong'; check = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'; }
      else { cls += ' neutral'; }
      body += '<div class="' + cls + '">' + check + '<span>' + o.label + '. ' + o.text + '</span></div>';
    });
    body += '</div>';
    if (d.is_correct === false) {
      body += '<div style="display:flex;align-items:center;gap:6px;margin-top:8px;padding:6px 10px;border-radius:6px;background:#d1fae5;color:#065f46;font-size:.78rem;font-weight:600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Jawaban benar: <strong>' + d.correct_answer + '</strong></div>';
    }
  } else if (d.type === 'true_false') {
    body += '<div style="display:flex;gap:8px">';
    ['true', 'false'].forEach(v => {
      const label = v === 'true' ? 'True' : 'False';
      const isSelected = d.selected_option === v;
      const isKey = v === d.correct_answer;
      let bg = 'transparent', color = 'var(--s-muted)', check = '';
      if (isKey && d.selected_option) { bg = '#d1fae5'; color = '#065f46'; if (d.is_correct !== false) check = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'; }
      else if (isSelected) { bg = '#fce4ec'; color = '#b71c1c'; check = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'; }
      body += '<div style="display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 20px;border-radius:8px;font-weight:600;font-size:.82rem;flex:1;background:' + bg + ';color:' + color + '">' + check + label + '</div>';
    });
    body += '</div>';
    if (d.is_correct === false) {
      body += '<div style="display:flex;align-items:center;gap:6px;margin-top:8px;padding:6px 10px;border-radius:6px;background:#d1fae5;color:#065f46;font-size:.78rem;font-weight:600"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg> Jawaban benar: <strong>' + d.correct_answer + '</strong></div>';
    }
  } else {
    body += '<div style="display:grid;gap:6px">';
    body += '<div style="font-size:.78rem;font-weight:600;color:var(--s-ink);margin-bottom:2px">Jawaban kamu:</div>';
    body += '<div style="padding:10px 12px;border-radius:8px;background:var(--s-bg);font-size:.8rem;color:var(--s-ink);white-space:pre-wrap;line-height:1.5">';
    if (d.answer_text) body += d.answer_text;
    else body += '<em style="color:var(--s-muted)">Kamu tidak mengisi jawabannya</em>';
    body += '</div>';
    if (d.correct_answer) {
      body += '<div style="display:flex;align-items:center;gap:6px;margin-top:4px;padding:8px 12px;border-radius:8px;background:#d1fae5;color:#065f46;font-size:.78rem;font-weight:600">';
      body += '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
      body += 'Jawaban benar: <strong>' + d.correct_answer + '</strong></div>';
    } else {
      body += '<div style="display:flex;align-items:center;gap:6px;margin-top:4px;padding:8px 12px;border-radius:8px;background:#fef3c7;color:#92400e;font-size:.78rem;font-weight:600">';
      body += '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
      body += 'Menunggu penilaian guru</div>';
    }
    body += '</div>';
  }

  if (d.feedback) {
    body += '<div style="display:flex;align-items:center;gap:6px;margin-top:10px;padding:8px 12px;border-radius:8px;background:color-mix(in srgb,var(--s-primary) 6%,transparent);font-size:.75rem;color:var(--s-primary);font-weight:600">';
    body += '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
    body += 'Feedback: ' + d.feedback + '</div>';
  }

  if (d.explanation) {
    body += '<details style="margin-top:10px">';
    body += '<summary style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--s-muted);cursor:pointer;padding:4px 0">';
    body += '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 16 12 12 12 8"/></svg>';
    body += 'Lihat Pembahasan</summary>';
    body += '<p style="font-size:.78rem;color:var(--s-muted);margin:6px 0 0;line-height:1.6;white-space:pre-wrap;padding:10px 12px;border-radius:8px;background:var(--s-bg)">' + d.explanation + '</p>';
    body += '</details>';
  }

  const nav = '<div style="display:flex;justify-content:space-between;margin-top:16px;padding-top:14px;border-top:1px solid var(--s-line)">' +
    '<button type="button" onclick="openReview(' + (idx - 1) + ')" style="padding:6px 16px;border-radius:8px;border:1.5px solid var(--s-line);background:var(--s-card);cursor:pointer;font-size:.78rem;font-weight:600;color:var(--s-ink);' + (idx === 0 ? 'visibility:hidden' : '') + '">← Sebelumnya</button>' +
    '<button type="button" onclick="openReview(' + (idx + 1) + ')" style="padding:6px 16px;border-radius:8px;border:1.5px solid var(--s-line);background:var(--s-card);cursor:pointer;font-size:.78rem;font-weight:600;color:var(--s-ink);' + (idx === total - 1 ? 'visibility:hidden' : '') + '">Selanjutnya →</button>' +
    '</div>';

  const title = 'Soal #' + num + ' <span style="font-size:.75rem;color:var(--s-muted);font-weight:400">(' + d.points + ' poin)</span> ' + statusHTML;

  return '<p style="font-size:.85rem;font-weight:600;color:var(--s-ink);margin:0 0 12px;line-height:1.6;white-space:pre-wrap">' + d.text + '</p>' +
    body + nav;
}

function openReview(idx) {
  if (idx < 0 || idx >= questions.length) return;
  Swal.fire({
    title: 'Soal #' + (idx + 1),
    html: qHTML(idx),
    showConfirmButton: false,
    showCloseButton: true,
    width: 640,
    padding: '20px',
    customClass: { popup: 'swal-review' },
  });
}
</script>
@endif
@endsection
