@extends('layouts.siswa')
@section('title', 'Profil')
@section('content')
<div class="bento bento-full" style="margin-bottom:16px">
  <div class="b-card b-card-hero" style="padding:26px 28px;position:relative;overflow:hidden">
    <div style="position:absolute;top:-30px;right:-20px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <div style="position:absolute;bottom:-40px;right:70px;width:110px;height:110px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>

    <div style="display:flex;align-items:center;gap:18px;position:relative;z-index:1;flex-wrap:wrap">
      <div style="width:76px;height:76px;border-radius:22px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);display:grid;place-items:center;flex-shrink:0;box-shadow:0 4px 16px rgba(0,0,0,0.08)">
        <span style="font-size:1.7rem;font-weight:800;color:#fff">{{ $initials }}</span>
      </div>
      <div style="min-width:0;flex:1">
        <h2 style="font-size:1.4rem;font-weight:800;color:#fff;margin:0;letter-spacing:-0.02em">{{ $student->full_name }}</h2>
        <div style="display:flex;align-items:center;gap:12px;margin-top:4px;flex-wrap:wrap">
          <span style="font-size:.84rem;font-weight:600;color:rgba(255,255,255,0.8)">{{ $student->class_name }}</span>
          @if($student->program_name)
          <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,0.3)"></span>
          <span style="font-size:.82rem;color:rgba(255,255,255,0.65)">{{ $student->program_name }}</span>
          @endif
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <span style="display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.15)">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span style="font-size:.76rem;font-weight:600;color:#fff">{{ $period?->academic_year }} {{ ucfirst($period?->semester ?? '-') }}</span>
        </span>
        <span style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:20px;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.15)">
          <span style="width:7px;height:7px;border-radius:50%;background:#4ADE80;box-shadow:0 0 0 3px rgba(74,222,128,0.2)"></span>
          <span style="font-size:.76rem;font-weight:600;color:#fff">Aktif</span>
        </span>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-top:22px;position:relative;z-index:1">
      <div style="padding:10px 14px;border-radius:12px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12)">
        <div style="font-size:.68rem;font-weight:600;color:rgba(255,255,255,0.55);text-transform:uppercase">NISN</div>
        <div style="font-size:.9rem;font-weight:700;color:#fff;margin-top:2px">{{ $student->nisn ?? '-' }}</div>
      </div>
      @if($student->birth_date)
      <div style="padding:10px 14px;border-radius:12px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12)">
        <div style="font-size:.68rem;font-weight:600;color:rgba(255,255,255,0.55);text-transform:uppercase">Tanggal Lahir</div>
        <div style="font-size:.9rem;font-weight:700;color:#fff;margin-top:2px">{{ $student->birth_date->translatedFormat('d M Y') }}</div>
      </div>
      @endif
    </div>
  </div>
</div>

<div class="bento bento-2">
  <div class="bento bento-full" style="gap:12px">
    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Wali Kelas</h3>
      @if($student->homeroomTeacher)
      <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;border-radius:12px;background:var(--s-primary-soft);border:1px solid rgba(79,70,229,0.1)">
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));display:grid;place-items:center;color:#fff;font-size:.78rem;font-weight:700;flex-shrink:0">{{ strtoupper(mb_substr($student->homeroomTeacher->full_name ?? $student->homeroomTeacher->name, 0, 1)) }}</div>
        <div>
          <div style="font-size:.85rem;font-weight:600;color:var(--s-ink)">{{ $student->homeroomTeacher->full_name ?? $student->homeroomTeacher->name }}</div>
          <div style="font-size:.72rem;color:var(--s-muted)">{{ $student->homeroomTeacher->email }}</div>
        </div>
      </div>
      @else
      <div style="font-size:.82rem;color:var(--s-muted)">Belum ditentukan</div>
      @endif
    </div>

    @if($parents->count() > 0)
    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Orang Tua / Wali</h3>
      <div style="display:grid;gap:8px">
        @foreach($parents as $p)
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:var(--s-bg);border:1px solid var(--s-line)">
          <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#8B5CF6,#A78BFA);display:grid;place-items:center;color:#fff;font-size:.72rem;font-weight:700;flex-shrink:0">{{ strtoupper(mb_substr($p->full_name ?? $p->name, 0, 1)) }}</div>
          <div style="flex:1;min-width:0">
            <div style="font-size:.83rem;font-weight:600;color:var(--s-ink)">{{ $p->full_name ?? $p->name }}</div>
            <div style="font-size:.72rem;color:var(--s-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $p->email }}</div>
          </div>
          <span style="font-size:.64rem;font-weight:700;text-transform:uppercase;letter-spacing:.02em;padding:3px 8px;border-radius:6px;background:var(--s-primary-soft);color:var(--s-primary-dark);flex-shrink:0">{{ $p->pivot->relationship ?? 'Wali' }}</span>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($behavior->count() > 0)
    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Nilai Sikap</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        @php $aspectLabels = ['discipline' => 'Disiplin', 'responsibility' => 'Tanggung Jawab', 'collaboration' => 'Kolaborasi', 'independence' => 'Kemandirian'] @endphp
        @foreach($behavior as $b)
        <div style="padding:12px;border-radius:10px;background:var(--s-bg);border:1px solid var(--s-line)">
          <div style="font-size:.68rem;font-weight:600;color:var(--s-muted);text-transform:uppercase">{{ $aspectLabels[$b->aspect] ?? $b->aspect }}</div>
          <div style="font-size:1.3rem;font-weight:800;margin-top:2px;color:{{ $b->grade >= 'A' ? 'var(--s-success)' : ($b->grade >= 'B' ? '#FF9F0A' : 'var(--s-muted)') }}">{{ $b->grade }}</div>
          @if($b->note)
          <div style="font-size:.72rem;color:var(--s-muted);margin-top:4px;line-height:1.4">{{ $b->note }}</div>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <div class="bento bento-full" style="gap:12px">
    @if($extracurriculars->count() > 0)
    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Ekstrakurikuler</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        @foreach($extracurriculars as $e)
        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:10px;background:var(--s-bg);border:1px solid var(--s-line)">
          <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#8B5CF6,#A78BFA);display:grid;place-items:center;color:#fff;font-size:.7rem;font-weight:700;flex-shrink:0">{{ strtoupper(mb_substr($e->name, 0, 1)) }}</div>
          <div>
            <div style="font-size:.8rem;font-weight:600;color:var(--s-ink)">{{ $e->name }}</div>
            <div style="font-size:.7rem;color:var(--s-muted)">Nilai: <span style="font-weight:600;color:var(--s-ink)">{{ $e->score }}</span></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Ringkasan Kehadiran</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        @foreach(['present' => ['Hadir', '#34C759'], 'sick' => ['Sakit', '#FF9F0A'], 'excused' => ['Izin', '#007AFF'], 'unexcused' => ['Alpa', '#FF3B30']] as $key => [$label, $color])
        <div style="padding:10px;border-radius:10px;background:var(--s-bg);border:1px solid var(--s-line)">
          <div style="font-size:.66rem;font-weight:600;color:var(--s-muted);text-transform:uppercase">{{ $label }}</div>
          <div style="font-size:1.15rem;font-weight:800;margin-top:2px;color:{{ $color }}">{{ $attendanceBreakdown[$key] ?? 0 }}</div>
        </div>
        @endforeach
      </div>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--s-line)">
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Tingkat kehadiran</span>
          <span style="font-size:.85rem;font-weight:700;color:var(--s-ink)">{{ $attendanceRate }}%</span>
        </div>
      </div>
    </div>

    <div class="b-card" style="padding:16px 18px">
      <h3 class="b-section-title" style="margin-bottom:10px">Info Akun</h3>
      <div style="display:grid;gap:10px">
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Email</span>
          <span style="font-size:.8rem;font-weight:600;color:var(--s-ink)">{{ $account?->email ?? '-' }}</span>
        </div>
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">NIS</span>
          <span style="font-size:.8rem;font-weight:600;color:var(--s-ink)">{{ $student->nis ?? '-' }}</span>
        </div>
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Status</span>
          <span style="font-size:.7rem;font-weight:600;padding:2px 10px;border-radius:6px;background:rgba(52,199,89,0.1);color:var(--s-success)">Aktif</span>
        </div>
        @if($account?->created_at)
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Terdaftar</span>
          <span style="font-size:.8rem;font-weight:600;color:var(--s-ink)">{{ $account->created_at->translatedFormat('d M Y') }}</span>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
