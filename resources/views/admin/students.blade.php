@extends('layouts.admin')

@section('title', 'Kelola Siswa')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen siswa</span>
    <h1>Kelola Siswa</h1>
    <p>Kelola data siswa, assign wali kelas, dan import data.</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('admin.students.import') }}" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Import CSV
    </a>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Siswa
    </a>
  </div>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:2;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NISN...">
    </div>
    <div class="field" style="flex:1;min-width:150px">
      <select name="class">
        <option value="">Semua Kelas</option>
        @foreach ($classNames as $class)
          <option value="{{ $class }}" {{ request('class') === $class ? 'selected' : '' }}>{{ $class }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:130px">
      <select name="status">
        <option value="">Semua Status</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
        <option value="graduated" {{ request('status') === 'graduated' ? 'selected' : '' }}>Lulus</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request('search') || request('class') || request('status'))
      <a href="{{ route('admin.students.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Siswa</th>
          <th>NISN</th>
          <th>Kelas</th>
          <th>Program</th>
          <th>Wali Kelas</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($students as $student)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <span style="width:36px;height:36px;border-radius:10px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.82rem;flex-shrink:0">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
                <div>
                  <strong style="display:block;font-size:.92rem">{{ $student->full_name }}</strong>
                  <span style="font-size:.8rem;color:var(--muted)">{{ $student->birth_date?->format('d M Y') ?? '-' }}</span>
                </div>
              </div>
            </td>
            <td style="font-family:monospace;font-size:.85rem">{{ $student->nisn }}</td>
            <td><span style="padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $student->class_name }}</span></td>
            <td style="font-size:.88rem">{{ $student->program_name }}</td>
            <td style="font-size:.88rem">{{ $student->homeroomTeacher?->full_name ?? $student->homeroomTeacher?->name ?? '-' }}</td>
            <td>
              @php
                $statusColors = ['active' => 'var(--success)', 'graduated' => 'var(--primary-2)', 'inactive' => '#ef4444'];
                $statusLabels = ['active' => 'Aktif', 'graduated' => 'Lulus', 'inactive' => 'Nonaktif'];
              @endphp
              <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $statusColors[$student->status] ?? '#666' }} 12%,var(--card));color:{{ $statusColors[$student->status] ?? '#666' }}">{{ $statusLabels[$student->status] ?? $student->status }}</span>
            </td>
            <td>
              <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline" style="min-height:32px;padding:0 12px;font-size:.8rem">Edit</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada siswa ditemukan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $students->links() }}</div>
</section>
@endsection
