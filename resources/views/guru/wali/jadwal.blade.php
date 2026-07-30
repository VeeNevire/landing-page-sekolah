@extends('layouts.guru')

@section('title', 'Jadwal Wali Kelas')

@push('styles')
<style>
.wali-sched-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.85rem; min-width:700px }
.wali-sched-table th { padding:12px 8px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; text-align:center; color:var(--muted); border-bottom:2px solid var(--line); background:var(--card); position:sticky; top:0; z-index:2 }
.wali-sched-table td { padding:8px; text-align:center; vertical-align:top; border-bottom:1px solid var(--line) }
.wali-sched-table tr:last-child td { border-bottom:none }
.wali-sched-time { font-size:.82rem; font-weight:700; color:var(--muted); white-space:nowrap; text-align:left !important; width:110px }
.wali-sched-cell { padding:10px 8px; border-radius:12px; background:color-mix(in srgb,var(--primary-2) 8%,var(--card)); border:1px solid color-mix(in srgb,var(--primary-2) 15%,var(--line)); cursor:default }
.wali-sched-cell .code { font-weight:700; font-size:.85rem; color:var(--primary-2) }
.wali-sched-cell .subject { font-size:.78rem; font-weight:600; margin-top:2px }
.wali-sched-cell .teacher { font-size:.72rem; color:var(--muted); margin-top:2px }

.wali-cal-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.85rem; min-width:680px }
.wali-cal-table th { padding:10px 6px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; text-align:center; color:var(--muted); border-bottom:2px solid var(--line); background:var(--card); position:sticky; top:0; z-index:2 }
.wali-cal-table td { padding:4px; text-align:center; vertical-align:top; border-bottom:1px solid var(--line); min-height:72px; height:72px }
.wali-cal-date { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; font-size:.78rem; font-weight:700; margin-bottom:3px }
.wali-cal-date.today { background:var(--primary); color:#fff; box-shadow:0 2px 6px color-mix(in srgb,var(--primary) 40%,transparent) }
.wali-cal-badge { cursor:pointer; padding:2px 6px; margin:2px 0; border-radius:6px; font-size:.68rem; font-weight:600; line-height:1.4; display:block; text-align:left; transition:all .12s ease; overflow:hidden; text-overflow:ellipsis; white-space:nowrap }
.wali-cal-badge:hover { transform:translateX(2px); box-shadow:0 2px 8px color-mix(in srgb,var(--primary) 15%,transparent) }
.wali-cal-legend { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; padding-top:12px; border-top:1px solid var(--line) }
.wali-cal-legend-item { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:600; color:var(--muted) }
.wali-cal-legend-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0 }
.wali-cal-empty { padding:24px; text-align:center; color:var(--muted) }
.wali-cal-empty svg { opacity:.3; margin-bottom:8px }
.wali-cal-empty p { font-size:.85rem; margin:0 }
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Wali kelas</span>
    <h1>Jadwal Wali Kelas</h1>
    <p>Jadwal pelajaran dan aktivitas penilaian untuk kelas binaan.</p>
  </div>
</div>

@if (!$kelas)
<section class="portal-panel">
  <div style="padding:2rem;text-align:center;color:var(--muted)">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.3;margin-bottom:12px"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
    <h3 style="font-size:1rem;margin:0 0 4px;color:var(--ink)">Belum ada kelas binaan</h3>
    <p style="font-size:.88rem;margin:0">Anda belum ditetapkan sebagai wali kelas.</p>
  </div>
</section>
@else

<section class="portal-panel">
  <div class="portal-panel-header">
    <div>
      <h2>Jadwal Pelajaran</h2>
      <p>Kelas {{ $kelas->nama_lengkap }}</p>
    </div>
  </div>

  <div style="overflow-x:auto">
    <table class="wali-sched-table">
      <thead>
        <tr>
          <th style="min-width:110px">Jam</th>
          @foreach ($days as $day)
          <th>{{ $dayLabels[$day] ?? ucfirst($day) }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($timeSlots as $slot => $time)
        <tr>
          <td class="wali-sched-time">{{ $time }}</td>
          @foreach ($days as $day)
          <td>
            @php $cell = $grid[$day][$slot] ?? null; @endphp
            @if ($cell)
            <div class="wali-sched-cell">
              <div class="code">{{ $cell['code'] }}</div>
              <div class="subject">{{ $cell['subject'] }}</div>
              <div class="teacher">{{ $cell['teacher'] }}</div>
            </div>
            @else
            <span style="color:var(--line);font-size:.78rem">—</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

<section class="portal-panel" style="margin-top:20px">
  <div style="padding:20px 20px 0">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <div>
        <h2 style="font-size:1.1rem;font-weight:700;margin:0">Jadwal Aktivitas Penilaian</h2>
        <p style="font-size:.82rem;color:var(--muted);margin:2px 0 0">Kalender penilaian (kuis, PR, proyek, ujian).</p>
      </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;background:var(--bg);border-radius:12px;padding:8px 12px">
      <a href="{{ request()->fullUrlWithQuery(['bulan' => $prevBulan]) }}" class="btn btn-outline" style="min-height:34px;padding:0 12px;display:inline-flex;align-items:center;gap:5px;font-size:.8rem">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        {{ $calendarMonth->copy()->subMonth()->isoFormat('MMM') }}
      </a>
      <strong style="font-size:.95rem">{{ $calendarMonth->isoFormat('MMMM YYYY') }}</strong>
      <a href="{{ request()->fullUrlWithQuery(['bulan' => $nextBulan]) }}" class="btn btn-outline" style="min-height:34px;padding:0 12px;display:inline-flex;align-items:center;gap:5px;font-size:.8rem">
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
    <table class="wali-cal-table">
      <thead>
        <tr>
          <th style="min-width:36px;text-align:center;font-size:.7rem">Pekan</th>
          @foreach ($hariNama as $i => $nama)
          <th style="text-align:center;min-width:88px;font-size:.75rem">{{ $nama }}</th>
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
          <td style="text-align:center;font-size:.72rem;font-weight:700;color:var(--muted)">W{{ $weekIdx + 1 }}</td>
          @for ($col = 0; $col < 7; $col++)
            @php
              $isBlank = ($weekIdx === 0 && $col < $firstCol) || $dayNum > $daysInMonth;
            @endphp
            @if ($isBlank)
            <td><span style="color:var(--line);font-size:.72rem">—</span></td>
            @else
              @php
                $dateKey = $calendarMonth->format('Y-m') . '-' . str_pad($dayNum, 2, '0', STR_PAD_LEFT);
                $dayAssessments = $jadwalBulan->get($dateKey, collect());
                $isToday = $dateKey === $today;
              @endphp
              <td style="{{ $isToday ? 'background:color-mix(in srgb,var(--primary) 6%,var(--card))' : '' }}">
                <span class="wali-cal-date {{ $isToday ? 'today' : '' }}">{{ $dayNum }}</span>
                @foreach ($dayAssessments as $assessment)
                  @php
                    $subjectName = $assessment->teachingAssignment?->subject?->name ?? $assessment->teachingAssignment?->customSubject?->nama ?? '-';
                    $teacherName = $assessment->teachingAssignment?->teacher?->full_name ?? $assessment->teachingAssignment?->teacher?->name ?? '-';
                    $compLabel = $compLabels[$assessment->component] ?? $assessment->component;
                    $compColor = $compColors[$assessment->component] ?? '#86868B';
                    $compIcon = $compIcons[$assessment->component] ?? '📌';
                  @endphp
                  <div class="wali-cal-badge"
                       style="background:color-mix(in srgb,{{ $compColor }} 10%,var(--card));color:{{ $compColor }};border:1px solid color-mix(in srgb,{{ $compColor }} 18%,transparent)"
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
    <div class="wali-cal-empty">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <p>Belum ada aktivitas penilaian bulan ini.</p>
    </div>
    @endif
  </div>

  @if ($jadwalBulan->isNotEmpty())
  <div style="padding:0 20px 16px">
    <div class="wali-cal-legend">
      @foreach (['quiz'=>'Kuis','homework'=>'PR/Tugas','project'=>'Proyek','uts'=>'UTS','uas'=>'UAS'] as $k => $l)
      <span class="wali-cal-legend-item">
        <span class="wali-cal-legend-dot" style="background:{{ $compColors[$k] }}"></span>
        {{ $l }}
      </span>
      @endforeach
    </div>
  </div>
  @endif
</section>
@endif
@endsection

@push('scripts')
<script>
function showAssessmentDetail(title, subject, component, date, teacher, color, status) {
  const icons = {'Kuis':'📝','PR/Tugas':'📖','Proyek':'🔧','UTS':'📋','UAS':'📄','Remedial':'🔄','Tugas':'📝'};
  const icon = icons[component] || '📌';
  Swal.fire({
    title: icon + ' ' + title,
    html: '<div style="display:grid;gap:10px;text-align:left;margin-top:6px">' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--bg);border:1px solid var(--line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--primary) 10%,var(--card))">📚</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--muted)">Mata Pelajaran</div><div style="font-weight:700;font-size:.92rem">' + subject + '</div></div>' +
      '</div>' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--bg);border:1px solid var(--line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,' + color + ' 12%,var(--card))">' + icon + '</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--muted)">Komponen</div><div style="font-weight:700;font-size:.92rem;color:' + color + '">' + component + '</div></div>' +
      '</div>' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--bg);border:1px solid var(--line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--primary) 10%,var(--card))">📅</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--muted)">Tanggal</div><div style="font-weight:700;font-size:.92rem">' + date + '</div></div>' +
      '</div>' +
      '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--bg);border:1px solid var(--line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--primary) 10%,var(--card))">👨‍🏫</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--muted)">Guru</div><div style="font-weight:700;font-size:.92rem">' + teacher + '</div></div>' +
      '</div>' +
      (status ? '<div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:var(--bg);border:1px solid var(--line)">' +
        '<span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:1rem;background:color-mix(in srgb,var(--primary) 10%,var(--card))">' + (status === 'Published' ? '✅' : '⏳') + '</span>' +
        '<div><div style="font-weight:600;font-size:.82rem;color:var(--muted)">Status</div><div style="font-weight:700;font-size:.92rem;color:' + (status === 'Published' ? 'var(--success)' : '#b45309') + '">' + status + '</div></div>' +
      '</div>' : '') +
    '</div>',
    showConfirmButton: true,
    confirmButtonText: 'Tutup',
    confirmButtonColor: color,
    width: 480,
    padding: '20px',
  });
}
</script>
@endpush