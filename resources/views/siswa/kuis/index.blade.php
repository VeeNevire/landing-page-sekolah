@extends('layouts.siswa')
@section('title', 'Kuis')
@section('content')
@php
  $badgeStyles = [
    'open' => ['bg' => '#d1fae5', 'fg' => '#065f46', 'label' => 'Buka'],
    'upcoming' => ['bg' => '#fef3c7', 'fg' => '#92400e', 'label' => 'Segera'],
    'closed' => ['bg' => '#fce4ec', 'fg' => '#b71c1c', 'label' => 'Ditutup'],
    'completed' => ['bg' => '#dbeafe', 'fg' => '#1e40af', 'label' => 'Selesai'],
    'exhausted' => ['bg' => '#f3f4f6', 'fg' => '#6b7280', 'label' => 'Habis'],
  ];
@endphp

<div style="margin-bottom:16px">
</div>

@if (session('success'))
  <div style="padding:10px 14px;border-radius:10px;background:#d1fae5;color:#065f46;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div style="padding:10px 14px;border-radius:10px;background:#fce4ec;color:#b71c1c;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('error') }}</div>
@endif

@if ($quizCards->count())
  {{-- Filter tabs --}}
  <div class="bento bento-full" style="margin-bottom:12px">
    <div style="display:flex;gap:0;background:var(--s-bg);border-radius:10px;padding:3px;width:100%">
      @php $filters = ['semua' => 'Semua', 'open' => 'Tersedia', 'completed' => 'Selesai', 'upcoming' => 'Segera', 'closed' => 'Ditutup']; @endphp
      @foreach ($filters as $key => $label)
        @php $count = $key === 'semua' ? $quizCards->count() : $quizCards->where('status', $key)->count(); @endphp
        <a href="{{ route('siswa.kuis.index', ['filter' => $key]) }}"
           style="flex:1;text-align:center;padding:6px 10px;border-radius:7px;font-size:.75rem;font-weight:600;text-decoration:none;transition:all .15s;{{ $currentFilter === $key ? 'background:var(--s-card);color:var(--s-ink);box-shadow:0 1px 3px rgba(0,0,0,.08)' : 'color:var(--s-muted)' }}">
          {{ $label }}
          <span style="opacity:.6;margin-left:3px">({{ $count }})</span>
        </a>
      @endforeach
    </div>
  </div>

  {{-- Quiz list --}}
  <div style="display:grid;gap:10px">
    @foreach ($quizCards as $card)
      @php
        $q = $card->quiz;
        $bs = $badgeStyles[$card->status] ?? $badgeStyles['closed'];
        $subjectLabel = $q->teachingAssignment->subject->name ?? $q->teachingAssignment->customSubject->nama ?? '-';
        $isClickable = $card->status === 'open';
      @endphp
      <div class="b-card"
           style="padding:0;overflow:hidden;transition:all .15s;{{ $isClickable ? 'cursor:pointer' : '' }}"
           @if ($isClickable) onclick="startQuiz(this)" @endif
           data-id="{{ $q->id }}"
           data-title="{{ $q->title }}"
           data-soal="{{ $card->questionCount }}"
           data-time="{{ $q->time_limit ?? 0 }}"
           data-attempt="{{ $card->attemptCount }}"
           data-max="{{ $q->max_attempts }}">
        <div style="display:flex;gap:14px;padding:14px 16px">
          {{-- Icon --}}
          <div style="width:42px;height:42px;border-radius:12px;background:color-mix(in srgb,var(--s-primary) 10%,transparent);display:grid;place-items:center;flex-shrink:0;margin-top:2px">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>

          {{-- Content --}}
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px">
              <h3 style="font-size:.85rem;font-weight:700;color:var(--s-ink);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $q->title }}</h3>
              @if ($card->bestScore !== null)
                @if ($card->status === 'completed' || $card->status === 'exhausted')
                  <span style="font-size:.65rem;padding:1px 8px;border-radius:20px;background:{{ $bs['bg'] }};color:{{ $bs['fg'] }};font-weight:700;white-space:nowrap;flex-shrink:0">{{ $bs['label'] }}</span>
                @endif
              @else
                <span style="font-size:.65rem;padding:1px 8px;border-radius:20px;background:{{ $bs['bg'] }};color:{{ $bs['fg'] }};font-weight:700;white-space:nowrap;flex-shrink:0">{{ $bs['label'] }}</span>
              @endif
            </div>

            <p style="font-size:.72rem;color:var(--s-muted);margin:2px 0 0">
              {{ $subjectLabel }}
              @if ($q->time_limit) · {{ $q->time_limit }} menit @endif
              · {{ $card->questionCount }} soal
            </p>

            {{-- Attempts progress --}}
            <div style="display:flex;align-items:center;gap:8px;margin-top:10px">
              @if ($card->bestScore !== null && !$card->canTake)
                <div style="font-size:.7rem;font-weight:600;color:{{ $bs['fg'] }};background:{{ $bs['bg'] }};padding:2px 8px;border-radius:6px">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px">
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                  Nilai terbaik: {{ $card->bestScore }}
                </div>
              @else
                <div style="flex:1;max-width:160px">
                  <div style="display:flex;justify-content:space-between;font-size:.65rem;color:var(--s-muted);margin-bottom:3px">
                    <span>Percobaan</span>
                    <span style="font-weight:600">{{ $card->attemptCount }}/{{ $q->max_attempts }}</span>
                  </div>
                  <div style="height:4px;background:var(--s-bg);border-radius:2px;overflow:hidden">
                    <div style="height:100%;border-radius:2px;background:linear-gradient(90deg,var(--s-primary),var(--s-primary-dark));width:{{ min(100, ($card->attemptCount / $q->max_attempts) * 100) }}%"></div>
                  </div>
                </div>
              @endif

              <div style="margin-left:auto;display:flex;align-items:center;gap:6px">
                @if ($card->status === 'open')
                  <span style="font-size:.72rem;padding:5px 14px;border-radius:8px;background:var(--s-primary);color:#fff;font-weight:600;display:flex;align-items:center;gap:5px">
                    Mulai
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M5 12h14"/>
                      <path d="m12 5 7 7-7 7"/>
                    </svg>
                  </span>
                @elseif ($card->status === 'upcoming')
                  <span style="font-size:.7rem;color:{{ $bs['fg'] }};font-weight:600">
                    {{ $q->start_date ? $q->start_date->format('j M') : '-' }}
                  </span>
                @elseif ($card->status === 'completed' && $card->bestScore !== null)
                  <div style="text-align:right">
                    <span style="font-size:1rem;font-weight:800;color:var(--s-primary)">{{ $card->bestScore }}</span>
                  </div>
                @endif
              </div>
            </div>

            {{-- Deadline --}}
            @if ($card->status === 'open' && $q->end_date)
              <div style="font-size:.68rem;color:var(--s-muted);margin-top:6px">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
                Tenggat: {{ $q->end_date->format('j M Y, H:i') }}
              </div>
            @elseif ($card->status === 'upcoming' && $q->start_date)
              <div style="font-size:.68rem;color:{{ $bs['fg'] }};margin-top:6px">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
                Dibuka: {{ $q->start_date->format('j M Y, H:i') }}
              </div>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="b-card" style="text-align:center;padding:48px">
    <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <polyline points="12 6 12 12 16 14"/>
      </svg>
    </div>
    <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">
      @switch($currentFilter)
        @case('open') Belum ada kuis tersedia @break
        @case('completed') Belum ada kuis selesai @break
        @case('upcoming') Belum ada kuis mendatang @break
        @case('closed') Belum ada kuis ditutup @break
        @default Belum ada kuis
      @endswitch
    </h3>
    <p style="font-size:.82rem;color:var(--s-muted);margin:0">
      @switch($currentFilter)
        @case('semua') Kuis akan muncul ketika guru mempublikasikannya. @break
        @default Coba pilih filter lain.
      @endswitch
    </p>
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
  info += '<strong style="font-size:.95rem">' + title + '</strong><br>';
  info += '<span style="color:var(--s-muted)">' + totalSoal + ' soal</span>';
  if (timeLimit > 0) info += ' · <span style="color:var(--s-muted)">' + timeLimit + ' menit</span>';
  info += ' · <span style="color:var(--s-muted)">Percobaan ' + (attemptCount + 1) + '/' + maxAttempts + '</span>';
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
