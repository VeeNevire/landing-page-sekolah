@extends('layouts.guru')

@section('title', 'Input Nilai — ' . $class . ' ' . $subject->name)

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Input nilai</span>
    <h1>{{ $subject->name }} — {{ $class }}</h1>
    <p>Masukkan nilai untuk siswa pada mata pelajaran ini.</p>
  </div>
  <div class="portal-actions no-print">
    <a class="btn btn-outline" href="{{ route('guru.nilai') }}">Kembali</a>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<section class="portal-panel" style="margin-bottom:20px">
  <div class="portal-panel-header">
    <div><h2>Tambah Penilaian Baru</h2><p>Isi data penilaian, lalu input nilai per siswa di bawah.</p></div>
  </div>
  <form method="POST" action="{{ route('guru.nilai.store', ['class' => $class, 'subject' => $subject->id]) }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:14px;align-items:end">
      <div class="field">
        <label for="title">Judul Penilaian</label>
        <input id="title" name="title" type="text" required placeholder="Contoh: Kuis Bab 1" value="{{ old('title') }}">
      </div>
      <div class="field">
        <label for="component">Komponen</label>
        <select id="component" name="component" required>
          <option value="quiz" @selected(old('component') == 'quiz')>Quiz</option>
          <option value="homework" @selected(old('component') == 'homework')>Tugas Rumah</option>
          <option value="project" @selected(old('component') == 'project')>Proyek</option>
          <option value="uts" @selected(old('component') == 'uts')>UTS</option>
          <option value="uas" @selected(old('component') == 'uas')>UAS</option>
        </select>
      </div>
      <button class="btn btn-primary" type="submit" style="min-height:42px">Simpan Nilai</button>
    </div>

    <div class="table-wrap" style="margin-top:18px">
      <table class="grade-table">
        <thead>
          <tr>
            <th style="width:50px">No</th>
            <th>Nama Siswa</th>
            <th style="width:120px">Nilai (0-100)</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($students as $i => $student)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><strong>{{ $student->full_name }}</strong><br><span style="color:var(--muted);font-size:.8rem">NISN {{ $student->nisn }}</span></td>
              <td>
                <input type="number" name="scores[{{ $student->id }}]" min="0" max="100" step="1"
                       value="{{ old("scores.{$student->id}") }}"
                       placeholder="—"
                       style="width:100%;padding:8px 10px;border:1px solid var(--line);border-radius:10px;background:var(--bg);font-weight:700">
              </td>
            </tr>
          @empty
            <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:30px">Tidak ada siswa di kelas ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </form>
</section>

@if ($assessments->count())
<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Riwayat Penilaian</h2><p>{{ $assessments->count() }} penilaian sudah dibuat.</p></div>
  </div>
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr><th>Judul</th><th>Komponen</th><th>Tanggal</th><th>Publish</th></tr>
      </thead>
      <tbody>
        @foreach ($assessments as $assess)
          <tr>
            <td><strong>{{ $assess->title }}</strong></td>
            <td><span style="text-transform:capitalize">{{ $assess->component }}</span></td>
            <td>{{ $assess->assessment_date->format('d M Y') }}</td>
            <td>
              @if ($assess->published_at)
                <span style="color:var(--success);font-weight:700">Published</span>
              @else
                <span style="color:var(--muted)">Draft</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
@endif
@endsection



