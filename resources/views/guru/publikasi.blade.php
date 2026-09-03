@extends('layouts.guru')

@section('title', 'Publikasi')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Publikasi</span>
    <h1>{{ ['nilai' => 'Publikasi Nilai', 'rencana-ujian' => 'Publikasi Rencana Ujian', 'absensi' => 'Publikasi Absensi', 'template' => 'Template Bot'][$tab] ?? 'Publikasi' }}</h1>
    <p>Publikasikan informasi akademik agar dapat dilihat oleh orang tua melalui portal.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:flex;gap:4px;margin-bottom:16px;border-bottom:1px solid var(--line)">
  @foreach(['nilai' => 'Publikasi Nilai', 'rencana-ujian' => 'Publikasi Rencana Ujian', 'absensi' => 'Publikasi Absensi', 'template' => 'Template Bot'] as $tabKey => $tabLabel)
  <a href="{{ route('guru.publikasi', ['tab' => $tabKey]) }}" style="padding:11px 18px;border-bottom:3px solid {{ $tab === $tabKey ? 'var(--primary-2)' : 'transparent' }};color:{{ $tab === $tabKey ? 'var(--primary-2)' : 'var(--muted)' }};font-weight:800;text-decoration:none;white-space:nowrap">{{ $tabLabel }}</a>
  @endforeach
</div>

@if (request('tab') === 'template')
<section class="portal-panel">
  <div class="portal-panel-header"><div><h2>Template Bot</h2><p>Pilih template pesan yang sudah disiapkan Admin untuk publikasi nilai.</p></div></div>
  <div style="display:grid;gap:12px">
  @forelse ($telegramTemplates as $template)
    <div style="padding:16px;border:1px solid var(--line);border-radius:12px;background:var(--card)">
      <strong>{{ $template->name }}</strong><div style="color:var(--muted);font-size:.82rem">Bot: {{ $template->bot->name }}</div>
      <pre style="white-space:pre-wrap;margin:10px 0 0;color:var(--muted);font:inherit">{{ $template->body }}</pre>
    </div>
  @empty <p style="color:var(--muted)">Belum ada template aktif dari Admin.</p> @endforelse
  </div>
</section>
@elseif ($tab === 'nilai')

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Status Publikasi</h2><p>Publikasikan semua penilaian per kelas sekaligus.</p></div>
  </div>
  <div style="display:grid;gap:14px">
@forelse ($classList as $class)
      @php $tingkat = explode(' ', $class['name'])[0]; @endphp
      <div style="display:flex;align-items:center;gap:18px;padding:18px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="width:50px;height:50px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:900;font-size:.9rem;flex-shrink:0">{{ $tingkat }}</span>
        <div style="flex:1">
          <strong style="display:block">{{ $class['name'] }}</strong>
          <span style="color:var(--muted);font-size:.85rem">{{ $class['student_count'] }} siswa &bull; {{ is_array($class['subjects']) ? implode(', ', $class['subjects']) : $class['subjects'] }}</span>
        </div>
        <div style="text-align:right">
          <span style="display:block;font-weight:700;font-size:.85rem;margin-bottom:4px">
            {{ $class['published_count'] }}/{{ $class['total_assessments'] }} dipublikasikan
          </span>
          @if ($class['all_published'])
            <span class="status-pass">Semua Published</span>
          @elseif ($class['total_assessments'] > 0)
            <form method="POST" action="{{ route('guru.publikasi.store', ['class' => $class['name']]) }}" style="display:inline-flex;align-items:center;gap:8px" onsubmit="return confirm('Publikasikan semua nilai kelas {{ $class['name'] }}?')">
              @csrf
              @if ($telegramTemplates->isNotEmpty())
              <select name="telegram_template_id" style="min-height:34px;padding:0 8px;font-size:.75rem;border:1px solid var(--line);border-radius:9px;background:var(--card);color:var(--ink)">
                <option value="">Tanpa Telegram</option>
                @foreach ($telegramTemplates as $template)<option value="{{ $template->id }}">{{ $template->name }}</option>@endforeach
              </select>
              @endif
              @if ($telegramBots->isNotEmpty())
              <select name="telegram_bot_id" style="min-height:34px;padding:0 8px;font-size:.75rem;border:1px solid var(--line);border-radius:9px;background:var(--card);color:var(--ink)"><option value="">Pilih Bot</option>@foreach($telegramBots as $bot)<option value="{{ $bot->id }}">{{ $bot->name }}</option>@endforeach</select>
              @endif
              <button class="btn btn-primary" type="submit" style="min-height:34px;padding:0 16px;font-size:.82rem;border-radius:10px">Publikasikan Semua</button>
            </form>
          @else
            <span style="color:var(--muted);font-size:.82rem">Belum ada penilaian</span>
          @endif
        </div>
      </div>
    @empty
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Tidak ada kelas untuk dipublikasikan.</p>
      </div>
    @endforelse
  </div>
</section>
@else
<section class="portal-panel">
  <div class="portal-panel-header"><div><h2>{{ $tab === 'rencana-ujian' ? 'Rencana Ujian' : 'Absensi' }}</h2><p>Gunakan template bot yang sudah disiapkan Admin saat laporan dipublikasikan.</p></div></div>
  <div style="display:grid;gap:14px">
  @forelse($classList as $class)
    @php $key = ($tab === 'rencana-ujian' ? 'exam_plan' : 'attendance').'|'.$class['name']; $published = $reportPublications->get($key); $total = $tab === 'rencana-ujian' ? $class['exam_total'] : $class['attendance_total']; $count = $tab === 'rencana-ujian' ? $class['exam_published'] : ($published ? $total : 0); @endphp
    <div style="display:flex;align-items:center;gap:18px;padding:18px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
      <div style="flex:1"><strong>{{ $class['name'] }}</strong><span style="display:block;color:var(--muted);font-size:.82rem">{{ $total }} data tersedia · {{ $count }}/{{ $total }} dipublikasikan</span></div>
      @if($published || ($tab === 'rencana-ujian' && $class['exam_total'] > 0 && $class['exam_published'] === $class['exam_total']))
        <span class="status-pass">Sudah Published</span>
      @elseif($total > 0)
        <form method="POST" action="{{ route('guru.publikasi.report.store', ['type' => $tab === 'rencana-ujian' ? 'exam_plan' : 'attendance', 'class' => $class['name']]) }}" style="display:flex;align-items:center;gap:8px" onsubmit="return confirm('Publikasikan laporan kelas ini?')">@csrf
          @if($telegramTemplates->isNotEmpty())<select name="telegram_template_id" style="min-height:34px;padding:0 8px;border:1px solid var(--line);border-radius:9px;background:var(--card);color:var(--ink)"><option value="">Tanpa Telegram</option>@foreach($telegramTemplates as $template)<option value="{{ $template->id }}">{{ $template->name }}</option>@endforeach</select>@endif
          @if($telegramBots->isNotEmpty())<select name="telegram_bot_id" style="min-height:34px;padding:0 8px;border:1px solid var(--line);border-radius:9px;background:var(--card);color:var(--ink)"><option value="">Pilih Bot</option>@foreach($telegramBots as $bot)<option value="{{ $bot->id }}">{{ $bot->name }}</option>@endforeach</select>@endif
          <button class="btn btn-primary" type="submit" style="min-height:34px;padding:0 16px;font-size:.82rem;border-radius:10px">Publikasikan Semua</button>
        </form>
      @else <span style="color:var(--muted);font-size:.82rem">Belum ada data</span> @endif
    </div>
  @empty <p style="color:var(--muted)">Tidak ada kelas untuk dipublikasikan.</p> @endforelse
  </div>
</section>
@endif
@endsection



