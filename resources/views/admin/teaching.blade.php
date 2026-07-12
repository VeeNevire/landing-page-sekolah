@extends('layouts.admin')

@section('title', 'Penugasan Guru')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen penugasan</span>
    <h1>Penugasan Guru</h1>
    <p>atur penugasan guru mengajar per mata pelajaran dan kelas.</p>
  </div>
  <a href="{{ route('admin.teaching.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Penugasan
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:1;min-width:200px">
      <select name="period">
        <option value="">Semua Periode</option>
        @foreach ($periods as $period)
          <option value="{{ $period->id }}" {{ request('period') == $period->id ? 'selected' : '' }}>
            {{ $period->academic_year }} {{ $period->semester }} {{ $period->is_active ? '(Aktif)' : '' }}
          </option>
        @endforeach
      </select>
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request('period'))
      <a href="{{ route('admin.teaching.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Periode</th>
          <th>Mata Pelajaran</th>
          <th>Guru</th>
          <th>Kelas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($assignments as $assignment)
          <tr>
            <td>
              <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">
                {{ $assignment->period->academic_year }} {{ $assignment->period->semester }}
                @if ($assignment->period->is_active) <span style="color:var(--success)">&bull; Aktif</span> @endif
              </span>
            </td>
            <td><strong>{{ $assignment->subject->code }}</strong> — {{ $assignment->subject->name }}</td>
            <td>{{ $assignment->teacher->full_name ?? $assignment->teacher->name }}</td>
            <td><span style="padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem;background:color-mix(in srgb,var(--accent) 10%,var(--card));color:#7a5500">{{ $assignment->class_name }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.teaching.destroy', $assignment) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 10px;font-size:.78rem;color:#ef4444" onclick="return confirm('Hapus penugasan ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Belum ada penugasan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $assignments->links() }}</div>
</section>
@endsection
