@extends('layouts.admin')

@section('title', 'Mapel Guru')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen pengajaran</span>
    <h1>Mapel Guru</h1>
    <p>Atur mata pelajaran, guru pengajar, dan kelas.</p>
  </div>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;align-items:end">
    <div class="field" style="flex:1;min-width:200px;margin:0">
      <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Semester</label>
      <select name="semester_id" onchange="this.form.submit()" style="min-height:42px">
        @foreach ($periods as $period)
          <option value="{{ $period->id }}" {{ $semesterId == $period->id ? 'selected' : '' }}>
            {{ $period->academic_year }} {{ ucfirst($period->semester) }} {{ $period->is_active ? '(Aktif)' : '' }}
          </option>
        @endforeach
      </select>
    </div>
    <form method="POST" action="{{ route('admin.penugasan.copy') }}" style="display:inline" onsubmit="return confirm('Salin semua penugasan dari semester sebelumnya ke semester aktif? Data yang sudah ada akan dilewatkan.')">
      @csrf
      <button type="submit" class="btn btn-outline" style="min-height:42px;display:inline-flex;align-items:center;gap:6px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8.3a4 4 0 0 0-1.172-2.872L3 3"/><path d="m15 9 6-6"/></svg>
        Copy dari Semester Lalu
      </button>
    </form>
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Mata Pelajaran</th>
          <th>Guru</th>
          <th>Kelas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($assignments as $index => $assignment)
        <tr>
          <td style="text-align:center">{{ $index + 1 }}</td>
          <td><strong>{{ $assignment->subject->code }}</strong> — {{ $assignment->subject->name }}</td>
          <td style="font-size:.88rem">{{ $assignment->teacher->full_name ?? $assignment->teacher->name }}</td>
          <td>{{ $assignment->class_name ?? '-' }}</td>
          <td>
            <button type="button" class="btn btn-outline" title="Hapus penugasan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $assignment->id }}, '{{ addslashes($assignment->subject->name) }}', '{{ addslashes($assignment->teacher->full_name ?? $assignment->teacher->name) }}')">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            </button>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Belum ada penugasan di semester ini.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<section class="portal-panel" style="margin-top:20px;max-width:800px">
  <div style="padding:24px">
    <h3 style="margin:0 0 16px">Tambah Penugasan Baru</h3>
    <form method="POST" action="{{ route('admin.penugasan.store') }}" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">
      @csrf
      <input type="hidden" name="semester_id" value="{{ $semesterId }}">

      <div class="field" style="flex:1;min-width:180px;margin:0">
        <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Mata Pelajaran</label>
        <select name="mapel_id" required style="min-height:42px">
          <option value="">-- Pilih Mapel --</option>
          @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->code }} — {{ $subject->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="flex:1;min-width:180px;margin:0">
        <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Guru</label>
        <select name="guru_id" required style="min-height:42px">
          <option value="">-- Pilih Guru --</option>
          @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}">{{ $teacher->full_name ?: $teacher->name }} ({{ $teacher->role }})</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="flex:1;min-width:140px;margin:0">
        <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Kelas</label>
        <select name="class_name" style="min-height:42px">
          <option value="">-- Pilih Kelas --</option>
          @foreach ($classes as $class)
            <option value="{{ $class }}">{{ $class }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-primary" style="min-height:42px">Tambah</button>
    </form>
  </div>
</section>
@endsection



