@extends('layouts.siswa')
@section('title', 'Hasil Kuis')
@section('content')
<div style="margin-bottom:16px">
  <a href="{{ route('siswa.kuis.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;color:var(--s-primary);text-decoration:none;margin-bottom:10px;font-weight:600">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali ke daftar kuis
  </a>
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">{{ $attempt->quiz->title }}</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:4px 0 0">Attempt {{ $attempt->attempt_number }}</p>
</div>

<div style="display:grid;gap:16px">
  {{-- Score Card --}}
  <div class="b-card" style="padding:24px;text-align:center">
    @if ($attempt->total_score !== null)
      <div style="width:80px;height:80px;border-radius:50%;display:grid;place-items:center;margin:0 auto 12px;background:{{ $attempt->total_score >= 75 ? 'linear-gradient(135deg,#34C759,#30D158)' : 'linear-gradient(135deg,#FF3B30,#FF453A)' }}">
        <span style="font-size:1.5rem;font-weight:900;color:#fff">{{ round($attempt->total_score) }}</span>
      </div>
      <h3 style="font-size:1rem;font-weight:700;color:var(--s-ink);margin:0 0 4px">{{ $attempt->total_score >= 75 ? 'Lulus' : 'Perlu Perbaikan' }}</h3>
    @else
      <div style="width:80px;height:80px;border-radius:50%;display:grid;place-items:center;margin:0 auto 12px;background:color-mix(in srgb,var(--s-muted) 20%,transparent)">
        <span style="font-size:1.5rem;font-weight:900;color:var(--s-muted)">—</span>
      </div>
      <h3 style="font-size:1rem;font-weight:700;color:var(--s-ink);margin:0 0 4px">Menunggu Nilai</h3>
      <p style="font-size:.82rem;color:var(--s-muted);margin:0">Essay sedang dinilai oleh guru.</p>
    @endif
    <p style="font-size:.78rem;color:var(--s-muted);margin:8px 0 0">Dikumpulkan: {{ $attempt->submitted_at?->format('d M Y H:i') ?? '-' }}</p>
  </div>

  {{-- Review Answers --}}
  @if ($showResult)
    <div class="b-card" style="padding:18px">
      <h3 style="font-size:.88rem;font-weight:700;color:var(--s-ink);margin:0 0 14px">Pembahasan</h3>
      <div style="display:grid;gap:12px">
        @foreach ($attempt->quiz->questions as $index => $q)
          @php
            $bank = $q->questionBank;
            $answer = $attempt->answers->firstWhere('quiz_question_id', $q->id);
            $isCorrect = $answer && $answer->is_correct;
            $userAnswer = $answer->selected_option ?? $answer->answer_text ?? '-';
          @endphp
          <div style="padding:14px;border-radius:12px;border:1.5px solid {{ $isCorrect === null ? 'var(--s-line)' : ($isCorrect ? '#d1fae5' : '#fce4ec') }};background:var(--s-card)">
            <div style="display:flex;align-items:flex-start;gap:10px">
              <span style="font-size:.85rem;font-weight:700;color:var(--s-ink);flex-shrink:0">#{{ $index + 1 }}</span>
              <div style="flex:1;min-width:0">
                <p style="font-size:.83rem;font-weight:600;color:var(--s-ink);margin:0 0 8px;line-height:1.5">{{ $bank->question_text }}</p>
                
                @if ($bank->question_type === 'multiple_choice')
                  <div style="display:grid;gap:4px">
                    @foreach ($bank->options ?? [] as $opt)
                      @php
                        $isSelected = $answer && $answer->selected_option === $opt['label'];
                        $isKey = $opt['label'] === $bank->correct_answer;
                      @endphp
                      <div style="padding:6px 10px;border-radius:6px;font-size:.78rem;background:{{ $isKey ? '#d1fae5' : ($isSelected && !$isKey ? '#fce4ec' : 'transparent') }};color:{{ $isKey ? '#065f46' : ($isSelected && !$isKey ? '#b71c1c' : 'var(--s-muted)') }};{{ $isSelected || $isKey ? 'font-weight:600' : '' }}">
                        {{ $opt['label'] }}. {{ $opt['text'] }}
                        @if ($isKey) <span style="font-size:.65rem;margin-left:4px">✓</span> @endif
                      </div>
                    @endforeach
                  </div>
                @elseif ($bank->question_type === 'true_false')
                  <div style="display:flex;gap:8px">
                    @foreach (['true' => 'True', 'false' => 'False'] as $val => $label)
                      @php
                        $isSelected = $answer && $answer->selected_option === $val;
                        $isKey = $val === $bank->correct_answer;
                      @endphp
                      <div style="padding:6px 14px;border-radius:6px;font-size:.78rem;background:{{ $isKey ? '#d1fae5' : ($isSelected && !$isKey ? '#fce4ec' : 'transparent') }};color:{{ $isKey ? '#065f46' : ($isSelected && !$isKey ? '#b71c1c' : 'var(--s-muted)') }};{{ $isSelected || $isKey ? 'font-weight:600' : '' }}">
                        {{ $label }}
                        @if ($isKey) <span style="font-size:.65rem;margin-left:4px">✓</span> @endif
                      </div>
                    @endforeach
                  </div>
                @else
                  <div style="padding:10px;border-radius:8px;background:var(--s-bg);font-size:.8rem;color:var(--s-ink);margin-bottom:6px;white-space:pre-wrap">{{ $userAnswer }}</div>
                  @if ($bank->correct_answer)
                    <div style="font-size:.75rem;color:var(--s-muted)">
                      <strong>Kunci:</strong> {{ $bank->correct_answer }}
                    </div>
                  @endif
                @endif

                @if ($answer && $answer->feedback)
                  <div style="margin-top:6px;font-size:.75rem;color:var(--s-primary);font-weight:600">
                    Feedback: {{ $answer->feedback }}
                  </div>
                @endif

                @if ($bank->explanation)
                  <details style="margin-top:6px">
                    <summary style="font-size:.72rem;color:var(--s-muted);cursor:pointer">Lihat Pembahasan</summary>
                    <p style="font-size:.78rem;color:var(--s-muted);margin:6px 0 0;line-height:1.5;white-space:pre-wrap">{{ $bank->explanation }}</p>
                  </details>
                @endif
              </div>
              <span style="font-size:.8rem;font-weight:700;flex-shrink:0;color:{{ $isCorrect === null ? 'var(--s-muted)' : ($isCorrect ? '#22c55e' : '#ef4444') }}">
                {{ $isCorrect === null ? '-' : ($isCorrect ? '✓' : '✗') }}
              </span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>
@endsection
