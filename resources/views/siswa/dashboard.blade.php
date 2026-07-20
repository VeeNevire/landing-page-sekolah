@extends('layouts.siswa')
@section('title', 'Dashboard')
@section('content')
<div class="bento bento-full" style="margin-bottom:16px">
  <div class="b-card b-card-hero" style="padding:24px 28px;position:relative;overflow:hidden">
    {{-- Decorative circles --}}
    <div style="position:absolute;top:-30px;right:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <div style="position:absolute;bottom:-40px;right:80px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>

    <div style="display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1">
      <div>
        @php
          $hour = now()->hour;
          if ($hour >= 3 && $hour < 11) { $greeting = 'Selamat pagi'; $greetIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>'; }
          elseif ($hour >= 11 && $hour < 15) { $greeting = 'Selamat siang'; $greetIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>'; }
          elseif ($hour >= 15 && $hour < 18) { $greeting = 'Selamat sore'; $greetIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>'; }
          else { $greeting = 'Selamat malam'; $greetIcon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>'; }
        @endphp
        <div style="display:flex;align-items:center;gap:6px;font-size:.78rem;font-weight:500;color:rgba(255,255,255,0.7);margin-bottom:2px">{!! $greetIcon !!} {{ $greeting }},</div>
        <h2 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0;letter-spacing:-0.02em">{{ $student->full_name }}</h2>
        <div style="display:flex;align-items:center;gap:12px;margin-top:4px">
          <span style="font-size:.82rem;color:rgba(255,255,255,0.65)">Semangat belajar hari ini!</span>
          <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,0.3)"></span>
          <span style="font-size:.78rem;font-weight:600;color:rgba(255,255,255,0.65)">{{ $student->class_name }}</span>
          <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,0.3)"></span>
          <span style="font-size:.78rem;color:rgba(255,255,255,0.5)">{{ $subjectCount }} mapel</span>
        </div>
      </div>
      @if(isset($period) && $period)
      <div style="display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:20px;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span style="font-size:.78rem;font-weight:600;color:#fff">{{ $period->academic_year }} {{ ucfirst($period->semester) }}</span>
      </div>
      @endif
    </div>

    {{-- Mini progress bar --}}
    <div style="margin-top:18px;position:relative;z-index:1;max-width:280px">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
        <span style="font-size:.72rem;font-weight:600;color:rgba(255,255,255,0.6)">Rata-rata nilai</span>
        <span style="font-size:.85rem;font-weight:700;color:#fff">{{ $avgScore }}</span>
      </div>
      <div style="height:4px;background:rgba(255,255,255,0.15);border-radius:2px;overflow:hidden">
        <div style="height:100%;border-radius:2px;background:#fff;width:{{ min($avgScore, 100) }}%;transition:width 0.8s ease"></div>
      </div>
    </div>
  </div>
</div>

<div class="bento bento-4" style="margin-bottom:16px">
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Rata-rata</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $avgScore }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));box-shadow:0 4px 12px rgba(107,163,199,0.25)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
      </div>
    </div>
    <div class="b-progress" style="margin-top:10px">
      <div class="b-progress-fill" style="width:{{ min($avgScore, 100) }}%;background:linear-gradient(90deg,var(--s-primary),var(--s-primary-dark))"></div>
    </div>
    <div style="font-size:.7rem;color:var(--s-muted);margin-top:4px">{{ $avgLetter }} · {{ $subjectCount }} mapel</div>
  </div>

  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Kehadiran</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#34C759,#30D158);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $attendanceRate }}%</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#34C759,#30D158);box-shadow:0 4px 12px rgba(52,199,89,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
    </div>
    <div class="b-progress" style="margin-top:10px">
      <div class="b-progress-fill" style="width:{{ $attendanceRate }}%;background:linear-gradient(90deg,#34C759,#30D158)"></div>
    </div>
    <div style="font-size:.7rem;color:var(--s-muted);margin-top:4px">{{ $attendanceBreakdown['present'] ?? 0 }} hadir</div>
  </div>

  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Mapel</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF9F0A,#FFD60A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $subjectCount }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF9F0A,#FFD60A);box-shadow:0 4px 12px rgba(255,159,10,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
      </div>
    </div>
    <div style="font-size:.7rem;color:var(--s-muted);margin-top:4px">mata pelajaran</div>
  </div>

  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Hadir</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF3B30,#FF453A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $attendanceBreakdown['present'] ?? 0 }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF3B30,#FF453A);box-shadow:0 4px 12px rgba(255,59,48,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
    </div>
    <div style="font-size:.7rem;color:var(--s-muted);margin-top:4px">hari</div>
  </div>
</div>

<div class="bento bento-2">
  <div class="b-card" style="padding:0">
    <div class="b-flex-between" style="padding:18px 20px 14px;border-bottom:1px solid var(--s-line)">
      <h3 class="b-section-title">Nilai per Mata Pelajaran</h3>
      <a href="{{ route('siswa.nilai') }}" class="b-link">Lihat semua →</a>
    </div>
    @if(count($grades) > 0)
      <div style="padding:8px 12px">
        @foreach($grades as $g)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 8px;border-radius:10px;transition:background 0.15s ease">
          <div style="display:flex;align-items:center;gap:10px;min-width:0">
            <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));display:grid;place-items:center;color:#fff;font-size:.68rem;font-weight:700;flex-shrink:0">{{ substr($g['subject'], 0, 1) }}</div>
            <div style="min-width:0">
              <div style="font-size:.82rem;font-weight:600;color:var(--s-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $g['subject'] }}</div>
              <div style="font-size:.68rem;color:var(--s-muted)">KKM {{ $g['kkm'] }}</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:.85rem;font-weight:700;color:var(--s-ink)">{{ $g['final_score'] }}</span>
            <span class="b-grade-letter {{ $g['passed'] ? 'pass' : 'fail' }}">{{ $g['letter'] }}</span>
          </div>
        </div>
        @endforeach
      </div>
    @else
      <div style="padding:28px;text-align:center;color:var(--s-muted);font-size:.85rem">Belum ada nilai dipublikasikan.</div>
    @endif
  </div>

  <div class="bento bento-full" style="gap:12px">
    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Jadwal Hari Ini</h3>
      @if($todaySchedule->count() > 0)
        <div style="display:flex;flex-direction:column;gap:6px">
          @foreach($todaySchedule as $s)
          <div class="b-schedule">
            <span class="b-schedule-time">{{ $s['slot'] }}</span>
            <span style="font-size:.82rem;font-weight:600;color:var(--s-ink)">{{ $s['subject'] }}</span>
          </div>
          @endforeach
        </div>
      @else
        <div style="text-align:center;padding:16px;color:var(--s-muted);font-size:.82rem">Tidak ada jadwal hari ini.</div>
      @endif
    </div>

    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Notifikasi</h3>
      @if($notifications->count() > 0)
        <div style="display:flex;flex-direction:column;gap:6px">
          @foreach($notifications as $n)
          <div class="b-notif">
            <div class="b-notif-dot"></div>
            <div>
              <div style="font-size:.82rem;font-weight:600;color:var(--s-ink)">{{ $n->title }}</div>
              <div style="font-size:.72rem;color:var(--s-muted);margin-top:1px">{{ Str::limit($n->body, 50) }}</div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <div style="text-align:center;padding:16px;color:var(--s-muted);font-size:.82rem">Tidak ada notifikasi.</div>
      @endif
    </div>
  </div>
</div>
@endsection
