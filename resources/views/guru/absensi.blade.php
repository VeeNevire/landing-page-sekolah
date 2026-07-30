@extends('layouts.guru')

@section('title', 'Absensi')

@push('styles')
<style>
.tab-bar { display:flex; gap:0; background:var(--bg); border-radius:20px 20px 0 0; padding:12px 20px 0; margin:-23px -23px 20px -23px; border-bottom:none }
.tab-btn { padding:10px 20px; font-size:.82rem; font-weight:700; color:var(--muted); font-family:inherit; text-decoration:none; transition:all .12s ease; cursor:pointer; border:none; background:transparent; border-radius:10px 10px 0 0 }
.tab-btn:hover { color:var(--ink); background:color-mix(in srgb,var(--card) 60%,transparent) }
.tab-btn.active { color:var(--primary-2); background:var(--card) }

.rekap-hadir { color:#34C759; font-weight:700 }
.rekap-sakit { color:#FF9F0A; font-weight:700 }
.rekap-izin { color:#007AFF; font-weight:700 }
.rekap-alpha { color:#FF3B30; font-weight:700 }
.rekap-terlambat { color:#BF5AF2; font-weight:700 }

.rekap-rate { display:inline-flex; align-items:center; gap:4px; padding:2px 10px; border-radius:6px; font-size:.78rem; font-weight:700 }
.rekap-rate.good { background:color-mix(in srgb,#34C759 12%,var(--card)); color:#34C759 }
.rekap-rate.ok { background:color-mix(in srgb,#FF9F0A 12%,var(--card)); color:#FF9F0A }
.rekap-rate.bad { background:color-mix(in srgb,#FF3B30 12%,var(--card)); color:#FF3B30 }
.detail-btn { padding:5px 12px; border-radius:7px; border:1.5px solid var(--line); background:var(--card); font-size:.73rem; font-weight:600; color:var(--primary-2); cursor:pointer; font-family:inherit; text-decoration:none; transition:all .12s ease; display:inline-flex; align-items:center; gap:4px }
.detail-btn:hover { border-color:var(--primary-2); background:color-mix(in srgb,var(--primary-4) 8%,var(--card)) }
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Absensi siswa</span>
    <h1>Absensi</h1>
    <p>Catat kehadiran siswa per mata pelajaran untuk kelas yang Anda ajar.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<section class="portal-panel">
  <div class="tab-bar">
    <a href="{{ route('guru.absensi', ['tab' => 'catat', 'class' => $selectedClass, 'subject' => $selectedSubjectId, 'date' => $date]) }}" class="tab-btn {{ $tab === 'catat' ? 'active' : '' }}">Catat Kehadiran</a>
    <a href="{{ route('guru.absensi', ['tab' => 'rekap', 'class' => $selectedClass]) }}" class="tab-btn {{ $tab === 'rekap' ? 'active' : '' }}">Rekap Kehadiran</a>
  </div>

@if ($tab === 'catat')

  <form method="GET" action="{{ route('guru.absensi') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:20px">
    <input type="hidden" name="tab" value="catat">
    <div class="field" style="margin:0">
      <label>Kelas</label>
      <select name="class" onchange="this.form.submit()">
        @foreach ($classNames as $class)
          <option value="{{ $class }}" @selected($selectedClass === $class)>{{ $class }}</option>
        @endforeach
      </select>
    </div>
<div class="field" style="margin:0">
      <label>Mapel</label>
      <select name="subject" onchange="this.form.submit()">
        @foreach ($subjectList as $subj)
          <option value="{{ $subj['id'] }}" @selected($selectedSubjectId === $subj['id'])>{{ $subj['name'] }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="margin:0">
      <label>Tanggal</label>
      <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
    </div>
  </form>

  <form method="POST" action="{{ route('guru.absensi.store') }}">
    @csrf
<input type="hidden" name="class_name" value="{{ $selectedClass }}">
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="subject" value="{{ $selectedSubjectId }}">

    @if ($selectedSubjectName)
    <div style="margin-bottom:16px;padding:12px 16px;background:var(--accent-bg, #eef2ff);border-radius:12px;font-weight:700;display:flex;align-items:center;gap:8px">
      <span style="font-size:1.1rem">Mata Pelajaran:</span>
      <span style="font-size:1.3rem;color:var(--primary)">{{ $selectedSubjectName }}</span>
    </div>
    @endif

    <div class="table-wrap">
      <table class="grade-table">
        <thead>
          <tr>
            <th style="width:50px">No</th>
            <th>Nama Siswa</th>
            <th style="width:200px;text-align:center">Status Kehadiran</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($students as $i => $student)
            @php $current = $existing[$student->id] ?? null @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>
                <strong>{{ $student->full_name }}</strong><br>
                <span style="color:var(--muted);font-size:.8rem">NISN {{ $student->nisn }}</span>
              </td>
              <td>
                <div class="absensi-group" data-student="{{ $student->id }}" style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap">
                  @foreach (['present' => 'Hadir', 'sick' => 'Sakit', 'excused' => 'Izin', 'unexcused' => 'Alpha', 'late' => 'Terlambat'] as $val => $label)
                    <label class="absensi-label {{ $val }} {{ $current === $val ? 'active' : '' }}" onclick="selectAbsensi(this, '{{ $student->id }}', '{{ $val }}')">
                      <input type="radio" name="status[{{ $student->id }}]" value="{{ $val }}" {{ $current === $val ? 'checked' : '' }} style="display:none">
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px">Tidak ada siswa di kelas ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($students->count())
      <div style="margin-top:18px;display:flex;justify-content:flex-end">
        <button class="btn btn-primary" type="submit">Simpan Absensi</button>
      </div>
    @endif
  </form>

@else

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
    <div>
      <h2 style="font-size:1rem;font-weight:700;margin:0;color:var(--ink)">Rekap Kehadiran</h2>
      <p style="font-size:.82rem;color:var(--muted);margin:2px 0 0">Rekapitulasi kehadiran siswa kelas {{ $selectedClass }}.</p>
    </div>
    <div class="field" style="margin:0;width:auto">
      <select name="class" onchange="window.location='?tab=rekap&class='+this.value" style="min-width:160px">
        @foreach ($classNames as $class)
          <option value="{{ $class }}" @selected($selectedClass === $class)>{{ $class }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th style="width:40px">No</th>
          <th>Nama Siswa</th>
          <th style="text-align:center;width:64px"><span class="rekap-hadir">H</span></th>
          <th style="text-align:center;width:64px"><span class="rekap-sakit">S</span></th>
          <th style="text-align:center;width:64px"><span class="rekap-izin">I</span></th>
          <th style="text-align:center;width:64px"><span class="rekap-alpha">A</span></th>
          <th style="text-align:center;width:72px"><span class="rekap-terlambat">T</span></th>
          <th style="text-align:center;width:64px">Total</th>
          <th style="text-align:center;width:100px">Kehadiran</th>
          <th style="text-align:center;width:60px">Detail</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($students as $i => $s)
          @php $r = $attendanceRecap[$s->id] ?? null @endphp
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>
              <strong>{{ $s->full_name }}</strong><br>
              <span style="color:var(--muted);font-size:.8rem">NISN {{ $s->nisn }}</span>
            </td>
            <td style="text-align:center;font-weight:700">{{ $r ? $r['present'] : 0 }}</td>
            <td style="text-align:center;font-weight:700;color:#FF9F0A">{{ $r ? $r['sick'] : 0 }}</td>
            <td style="text-align:center;font-weight:700;color:#007AFF">{{ $r ? $r['excused'] : 0 }}</td>
            <td style="text-align:center;font-weight:700;color:#FF3B30">{{ $r ? $r['unexcused'] : 0 }}</td>
            <td style="text-align:center;font-weight:700;color:#BF5AF2">{{ $r ? $r['late'] : 0 }}</td>
            <td style="text-align:center;font-weight:700">{{ $r ? $r['total'] : 0 }}</td>
            <td style="text-align:center">
              @if ($r && $r['total'] > 0)
                @php
                  $rate = $r['present_rate'];
                  $rateClass = $rate >= 90 ? 'good' : ($rate >= 75 ? 'ok' : 'bad');
                @endphp
                <span class="rekap-rate {{ $rateClass }}">{{ $rate }}%</span>
              @else
                <span style="color:var(--line);font-size:.82rem">—</span>
              @endif
            </td>
            <td style="text-align:center">
              <a href="{{ route('guru.absensi.detail', $s->id) }}" class="detail-btn">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Detail
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="10" style="text-align:center;color:var(--muted);padding:30px">Tidak ada data siswa.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

@endif

</section>

@push('scripts')
<script>
function selectAbsensi(label, studentId, value) {
  const group = label.closest('.absensi-group');
  group.querySelectorAll('.absensi-label').forEach(l => l.classList.remove('active'));
  label.classList.add('active');
  label.querySelector('input[type="radio"]').checked = true;
}
</script>
@endpush
@endsection



