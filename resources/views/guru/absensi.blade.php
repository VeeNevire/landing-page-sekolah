@extends('layouts.guru')

@section('title', 'Absensi')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Absensi siswa</span>
    <h1>Absensi</h1>
    <p>Catat kehadiran siswa untuk kelas yang Anda ajar.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Catat Kehadiran</h2><p>Pilih kelas dan tanggal untuk mulai mengisi absensi.</p></div>
  </div>

  <form method="GET" action="{{ route('guru.absensi') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:20px">
    <div class="field" style="margin:0">
      <label>Kelas</label>
      <select name="class" onchange="this.form.submit()">
        @foreach ($classNames as $class)
          <option value="{{ $class }}" @selected($selectedClass === $class)>{{ $class }}</option>
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



