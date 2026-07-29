@extends('layouts.siswa')
@section('title', 'Jadwal')
@push('styles')
<style>
  .sched-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.85rem; min-width:700px }
  .sched-table th { padding:12px 8px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; text-align:center; color:var(--s-muted); border-bottom:2px solid var(--s-line); background:var(--s-bg); position:sticky; top:0; z-index:2 }
  .sched-table td { padding:8px; text-align:center; vertical-align:top; border-bottom:1px solid var(--s-line) }
  .sched-table tr:last-child td { border-bottom:none }
  .sched-time { font-size:.82rem; font-weight:700; color:var(--s-muted); white-space:nowrap; text-align:left !important; width:110px }
  .sched-cell { padding:10px 8px; border-radius:12px; background:color-mix(in srgb,var(--s-primary) 8%,var(--s-card)); border:1px solid color-mix(in srgb,var(--s-primary) 15%,var(--s-line)) }
  .sched-cell .code { font-weight:700; font-size:.85rem; color:var(--s-primary-dark) }
  .sched-cell .subject { font-size:.78rem; font-weight:600; margin-top:2px; color:var(--s-ink) }
  .sched-cell .teacher { font-size:.72rem; color:var(--s-muted); margin-top:2px }

  .cal-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.85rem; min-width:680px }
  .cal-table th { padding:10px 6px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; text-align:center; color:var(--s-muted); border-bottom:2px solid var(--s-line); background:var(--s-bg); position:sticky; top:0; z-index:2 }
  .cal-table td { padding:4px; text-align:center; vertical-align:top; border-bottom:1px solid var(--s-line); min-height:72px; height:72px }
  .cal-table .weekend { opacity:.55 }
  .cal-date { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; font-size:.78rem; font-weight:700; margin-bottom:3px }
  .cal-date.today { background:var(--s-primary); color:#fff; box-shadow:0 2px 6px color-mix(in srgb,var(--s-primary) 40%,transparent) }
  .cal-date.other { color:var(--s-ink) }
  .cal-badge { cursor:pointer; padding:2px 6px; margin:2px 0; border-radius:6px; font-size:.68rem; font-weight:600; line-height:1.4; display:block; text-align:left; transition:all .12s ease; overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
  .cal-badge:hover { transform:translateX(2px); box-shadow:0 2px 8px color-mix(in srgb,var(--s-primary) 15%,transparent) }
  .cal-legend { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; padding-top:12px; border-top:1px solid var(--s-line) }
  .cal-legend-item { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:600; color:var(--s-muted) }
  .cal-legend-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0 }
  .cal-empty { padding:24px; text-align:center; color:var(--s-muted) }
  .cal-empty svg { opacity:.3; margin-bottom:8px }
  .cal-empty p { font-size:.85rem; margin:0 }
</style>
@endpush
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Jadwal Pelajaran</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Kelas {{ $student->class_name }}</p>
</div>

<div class="b-card" style="padding:0;overflow:hidden">
  <div style="overflow-x:auto">
    <table class="sched-table">
      <thead>
        <tr>
          <th style="min-width:110px">Jam</th>
          @foreach ($days as $day)
          <th>{{ ucfirst($day) }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($timeSlots as $slot => $time)
        <tr>
          <td class="sched-time">{{ $time }}</td>
          @foreach ($days as $day)
          <td>
            @php $cell = $grid[$day][$slot] ?? null; @endphp
            @if ($cell)
            <div class="sched-cell">
              <div class="code">{{ $cell['code'] }}</div>
              <div class="subject">{{ $cell['subject'] }}</div>
              <div class="teacher">{{ $cell['teacher'] }}</div>
            </div>
            @else
            <span style="color:var(--s-line);font-size:.78rem">—</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<section class="b-card" style="margin-top:20px;padding:0;overflow:hidden">
  <div style="padding:20px 20px 0">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <div>
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Jadwal Aktivitas Penilaian</h2>
        <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Kalender penilaian (kuis, PR, proyek, ujian).</p>
      </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;background:var(--s-bg);border-radius:12px;padding:8px 12px">
      <a href="{{ request()->fullUrlWithQuery(['bulan' => $prevBulan]) }}" class="s-btn s-btn-outline" style="min-height:34px;padding:0 12px;display:inline-flex;align-items:center;gap:5px;font-size:.8rem;border-radius:8px">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        {{ $calendarMonth->copy()->subMonth()->isoFormat('MMM') }}
      </a>
      <strong style="font-size:.95rem;color:var(--s-ink)">{{ $calendarMonth->isoFormat('MMMM YYYY') }}</strong>
      <a href="{{ request()->fullUrlWithQuery(['bulan' => $nextBulan]) }}" class="s-btn s-btn-outline" style="min-height:34px;padding:0 12px;display:inline-flex;align-items:center;gap:5px;font-size:.8rem;border-radius:8px">
        {{ $calendarMonth->copy()->addMonth()->isoFormat('MMM') }}
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
  </div>

  @php
    $daysInMonth = $calendarMonth->daysInMonth;
    $hariNama = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $today = now()->format('Y-m-d');
    $firstCol = $calendarMonth->dayOfWeek;
    $compLabels = ['quiz'=>'Kuis','homework'=>'PR/Tugas','project'=>'Proyek','uts'=>'UTS','uas'=>'UAS','remedial'=>'Remedial','assignment'=>'Tugas','other'=>'Lainnya'];
    $compColors = ['quiz'=>'#34C759','homework'=>'#007AFF','project'=>'#FF9F0A','uts'=>'#AF52DE','uas'=>'#FF3B30','remedial'=>'#FF6482','assignment'=>'#5856D6','other'=>'#86868B'];
    $compIcons = ['quiz'=>'📝','homework'=>'📖','project'=>'🔧','uts'=>'📋','uas'=>'📄','remedial'=>'🔄','assignment'=>'📝','other'=>'📌'];
  @endphp

  <div style="overflow-x:auto;padding:0 4px 4px">
    @if ($jadwalBulan->isNotEmpty())
    <table class="cal-table">
      <thead>
        <tr>
          <th style="min-width:36px;text-align:center;font-size:.7rem">Pekan</th>
          @foreach ($hariNama as $i => $nama)
          <th style="text-align:center;min-width:88px;font-size:.75rem;{{ $i >= 5 ? 'color:color-mix(in srgb,var(--s-muted) 50%,transparent)' : '' }}">{{ $nama }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php
          $dayNum = 1;
          $weekIdx = 0;
        @endphp
        @while ($dayNum <= $daysInMonth)
        <tr>
          <td style="text-align:center;font-size:.72rem;font-weight:700;color:var(--s-muted)">W{{ $weekIdx + 1 }}</td>
          @for ($col = 0; $col < 7; $col++)
            @php
              $isBlank = ($weekIdx === 0 && $col < $firstCol) || $dayNum > $daysInMonth;
            @endphp
            @if ($isBlank)
            <td class="{{ $col >= 5 ? 'weekend' : '' }}"><span style="color:var(--s-line);font-size:.72rem">—</span></td>
            @else
              @php
                $dateKey = $calendarMonth->format('Y-m') . '-' . str_pad($dayNum, 2, '0', STR_PAD_LEFT);
                $dayAssessments = $jadwalBulan->get($dateKey, collect());
                $isToday = $dateKey === $today;
              @endphp
              <td class="{{ $col >= 5 ? 'weekend' : '' }}" style="{{ $isToday ? 'background:color-mix(in srgb,var(--s-primary) 6%,var(--s-card))' : '' }}">
                <span class="cal-date {{ $isToday ? 'today' : 'other' }}">{{ $dayNum }}</span>
                @foreach ($dayAssessments as $assessment)
                  @php
                    $subjectName = $assessment->teachingAssignment?->subject?->name ?? $assessment->teachingAssignment?->customSubject?->nama ?? '-';
                    $teacherName = $assessment->teachingAssignment?->teacher?->full_name ?? $assessment->teachingAssignment?->teacher?->name ?? '-';
                    $compLabel = $compLabels[$assessment->component] ?? $assessment->component;
                    $compColor = $compColors[$assessment->component] ?? '#86868B';
                    $compIcon = $compIcons[$assessment->component] ?? '📌';
                  @endphp
                  <div class="cal-badge"
                       style="background:color-mix(in srgb,{{ $compColor }} 10%,var(--s-card));color:{{ $compColor }};border:1px solid color-mix(in srgb,{{ $compColor }} 18%,transparent)"
                       onclick="showAssessmentDetail('{{ addslashes($assessment->title) }}', '{{ addslashes($subjectName) }}', '{{ $compLabel }}', '{{ $assessment->assessment_date->isoFormat('D MMM YYYY') }}', '{{ addslashes($teacherName) }}', '{{ $compColor }}','{{ addslashes($assessment->published_at ? 'Published' : 'Draft') }}')"
                       title="{{ $assessment->title }} — {{ $subjectName }}">
                    {{ $compIcon }} {{ $assessment->title }}
                  </div>
                @endforeach
              </td>
              @php $dayNum++; @endphp
            @endif
          @endfor
        </tr>
        @php $weekIdx++; @endphp
        @endwhile
      </tbody>
    </table>
    @else
    <div class="cal-empty">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <p>Belum ada aktivitas penilaian bulan ini.</p>
    </div>
    @endif
  </div>

  @if ($jadwalBulan->isNotEmpty())
  <div style="padding:0 20px 16px">
    <div class="cal-legend">
      @foreach (['quiz'=>'Kuis','homework'=>'PR/Tugas','project'=>'Proyek','uts'=>'UTS','uas'=>'UAS'] as $k => $l)
      <span class="cal-legend-item">
        <span class="cal-legend-dot" style="background:{{ $compColors[$k] }}"></span>
        {{ $l }}
      </span>
      @endforeach
    </div>
  </div>
  @endif
</section>
@endsection

@push('scripts')
<script>
function showAssessmentDetail(title, subject, component, date, teacher, color, status) {
  const icons = {'Kuis':'📝','PR/Tugas':'📖','Proyek':'🔧','UTS':'📋','UAS':'📄','Remedial':'🔄','Tugas':'📝'};
  const icon = icons[component] || '📌';
  Swal.fire({
    title: icon + ' ' + title,
    html: '<div style="display:grid;gap:10px;text-align:left;margin-top:6px">' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--s-bg);border:1px solid var(--s-line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--s-primary) 10%,var(--s-card))">📚</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--s-muted)">Mata Pelajaran</div><div style="font-weight:700;font-size:.92rem;color:var(--s-ink)">' + subject + '</div></div>' +
      '</div>' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--s-bg);border:1px solid var(--s-line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,' + color + ' 12%,var(--s-card))">' + icon + '</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--s-muted)">Komponen</div><div style="font-weight:700;font-size:.92rem;color:' + color + '">' + component + '</div></div>' +
      '</div>' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--s-bg);border:1px solid var(--s-line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--s-primary) 10%,var(--s-card))">📅</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--s-muted)">Tanggal</div><div style="font-weight:700;font-size:.92rem;color:var(--s-ink)">' + date + '</div></div>' +
      '</div>' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--s-bg);border:1px solid var(--s-line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--s-primary) 10%,var(--s-card))">👨‍🏫</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--s-muted)">Guru</div><div style="font-weight:700;font-size:.92rem;color:var(--s-ink)">' + teacher + '</div></div>' +
      '</div>' +
      (status ? '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--s-bg);border:1px solid var(--s-line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--s-primary) 10%,var(--s-card))">' + (status === 'Published' ? '✅' : '⏳') + '</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--s-muted)">Status</div><div style="font-weight:700;font-size:.92rem;color:' + (status === 'Published' ? 'var(--s-success)' : '#b45309') + '">' + status + '</div></div>' +
      '</div>' : '') +
    '</div>',
    showConfirmButton: true,
    confirmButtonText: 'Tutup',
    confirmButtonColor: color,
    width: 480,
    padding: '20px',
    background: 'var(--s-card)',
  });
}
</script>
@endpush
