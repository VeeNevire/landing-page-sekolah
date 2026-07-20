@extends('layouts.siswa')
@section('title', 'Profil')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Profil Siswa</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Data diri dan informasi akun</p>
</div>

<div class="bento bento-2">
  <div class="b-card" style="text-align:center;padding:28px 24px">
    <div style="width:72px;height:72px;border-radius:20px;background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));display:grid;place-items:center;margin:0 auto 14px;box-shadow:0 4px 16px rgba(107,163,199,0.25)">
      <span style="font-size:1.6rem;font-weight:800;color:#fff">{{ $initials }}</span>
    </div>
    <h2 style="font-size:1.05rem;font-weight:700;color:var(--s-ink);margin:0">{{ $student->full_name }}</h2>
    <p style="font-size:.82rem;color:var(--s-muted);margin:4px 0 0">{{ $student->class_name }} @if($student->program_name) · {{ $student->program_name }} @endif</p>

    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--s-line);text-align:left">
      <div style="display:grid;gap:12px">
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">NISN</span>
          <span style="font-size:.82rem;font-weight:600;color:var(--s-ink)">{{ $student->nisn ?? '-' }}</span>
        </div>
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Status</span>
          <span style="font-size:.72rem;font-weight:600;padding:2px 10px;border-radius:6px;background:rgba(52,199,89,0.1);color:var(--s-success)">Aktif</span>
        </div>
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Semester</span>
          <span style="font-size:.82rem;font-weight:600;color:var(--s-ink)">{{ $period?->academic_year }} {{ ucfirst($period?->semester ?? '-') }}</span>
        </div>
        @if($student->birth_date)
        <div class="b-flex-between" style="padding:0 2px">
          <span style="font-size:.78rem;color:var(--s-muted)">Tanggal Lahir</span>
          <span style="font-size:.82rem;font-weight:600;color:var(--s-ink)">{{ $student->birth_date->translatedFormat('d M Y') }}</span>
        </div>
        @endif
      </div>
    </div>
  </div>

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
  </div>
</div>
@endsection
