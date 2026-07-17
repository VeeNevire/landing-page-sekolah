@extends('layouts.admin')

@section('title', 'Audit Log')

@php
$currentRole = request('role', '');
$roleColors = ['admin' => '#4338ca', 'teacher' => '#0369a1', 'homeroom' => '#0d9488', 'parent' => '#b45309', 'principal' => '#7c3aed'];
$roleLabels = ['admin' => 'Admin', 'teacher' => 'Guru', 'homeroom' => 'Wali Kelas', 'parent' => 'Orang Tua', 'principal' => 'Kepsek'];

$actionLabels = [
'auth.login' => 'Login',
'auth.logout' => 'Logout',
'user.create' => 'Membuat pengguna',
'user.update' => 'Mengedit pengguna',
'user.toggle' => 'Toggle status pengguna',
'user.reset-password' => 'Reset password',
'user.delete' => 'Menghapus pengguna',
'student.create' => 'Membuat siswa',
'student.update' => 'Mengedit siswa',
'student.delete' => 'Menghapus siswa',
'subject.create' => 'Membuat mata pelajaran',
'subject.update' => 'Mengedit mata pelajaran',
'subject.delete' => 'Menghapus mata pelajaran',
'period.create' => 'Membuat periode',
'period.update' => 'Mengedit periode',
'period.delete' => 'Menghapus periode',
'period.activate' => 'Mengaktifkan periode',
'teaching.create' => 'Membuat penugasan',
'teaching.update' => 'Mengedit penugasan',
'teaching.delete' => 'Menghapus penugasan',
'assessment.create' => 'Input nilai',
'attendance.record' => 'Input absensi',
'teacher-note.create' => 'Menulis catatan',
'grade.publish' => 'Mempublikasi nilai',
'material.create' => 'Upload materi',
'material.delete' => 'Menghapus materi',
'parent-student.create' => 'Menghubungkan orang tua-siswa',
'parent-student.delete' => 'Memutuskan orang tua-siswa',
];

$actionColors = [
'auth.login' => 'var(--success)', 'auth.logout' => 'var(--muted)',
'user.create' => 'var(--success)', 'user.update' => 'var(--primary-2)',
'user.delete' => '#ef4444', 'user.toggle' => '#b45309',
'student.create' => 'var(--success)', 'student.update' => 'var(--primary-2)',
'student.delete' => '#ef4444',
'subject.create' => 'var(--success)', 'subject.update' => 'var(--primary-2)',
'subject.delete' => '#ef4444',
'period.create' => 'var(--success)', 'period.update' => 'var(--primary-2)',
'period.delete' => '#ef4444', 'period.activate' => '#b45309',
'teaching.create' => 'var(--success)', 'teaching.update' => 'var(--primary-2)',
'teaching.delete' => '#ef4444',
'assessment.create' => 'var(--primary-2)', 'attendance.record' => 'var(--primary-2)',
'teacher-note.create' => 'var(--primary-2)', 'grade.publish' => '#b45309',
'material.create' => 'var(--success)', 'material.delete' => '#ef4444',
'parent-student.create' => 'var(--success)', 'parent-student.delete' => '#ef4444',
];
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Keamanan sistem</span>
    <h1>Audit Log</h1>
    <p>Pantau semua aktivitas dan perubahan data dalam sistem.</p>
  </div>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.audit.index', array_filter(['user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', array_filter(['role' => 'admin', 'user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === 'admin' ? 'active' : '' }}">
    Admin <span class="tab-count">{{ $tabCounts['admin'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', array_filter(['role' => 'teacher', 'user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === 'teacher' ? 'active' : '' }}">
    Guru <span class="tab-count">{{ $tabCounts['guru'] }}</span>
  </a>
  <a href="{{ route('admin.audit.index', array_filter(['role' => 'parent', 'user_id' => request('user_id'), 'action' => request('action'), 'from' => request('from'), 'to' => request('to')])) }}"
    class="tab-btn {{ $currentRole === 'parent' ? 'active' : '' }}">
    Orang Tua <span class="tab-count">{{ $tabCounts['parent'] }}</span>
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    @if ($currentRole)
    <input type="hidden" name="role" value="{{ $currentRole }}">
    @endif
    <div class="field" style="flex:1;min-width:180px">
      <select name="user_id">
        <option value="">Semua Pengguna</option>
        @foreach ($users as $user)
        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name ?: $user->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:180px">
      <input type="text" name="action" value="{{ request('action') }}" placeholder="Cari aksi...">
    </div>
    <div class="field" style="flex:0;min-width:150px">
      <input type="date" name="from" value="{{ request('from') }}">
    </div>
    <div class="field" style="flex:0;min-width:150px">
      <input type="date" name="to" value="{{ request('to') }}">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request()->hasAny(['user_id', 'action', 'from', 'to']))
    <a href="{{ route('admin.audit.index', array_filter(['role' => $currentRole])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Pengguna</th>
          <th>Aksi</th>
          <th>Entitas</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($logs as $log)
        <tr>
          <td style="font-size:.85rem;white-space:nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="width:28px;height:28px;border-radius:8px;display:grid;place-items:center;background:color-mix(in srgb,{{ $roleColors[$log->user->role ?? 'parent'] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$log->user->role ?? 'parent'] ?? '#666' }};font-weight:800;font-size:.7rem;flex-shrink:0">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</span>
              <div>
                <span style="font-size:.88rem;display:block">{{ $log->user->name ?? 'System' }}</span>
                @if ($log->user)
                <span style="padding:2px 6px;border-radius:6px;font-size:.68rem;font-weight:700;background:color-mix(in srgb,{{ $roleColors[$log->user->role] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$log->user->role] ?? '#666' }}">{{ $roleLabels[$log->user->role] ?? $log->user->role }}</span>
                @endif
              </div>
            </div>
          </td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $actionColors[$log->action] ?? 'var(--primary-2)' }} 12%,var(--card));color:{{ $actionColors[$log->action] ?? 'var(--primary-2)' }}">{{ $actionLabels[$log->action] ?? $log->action }}</span>
          </td>
          <td style="font-size:.82rem;color:var(--muted)">
            {{ $log->entity_type ? class_basename($log->entity_type) : '-' }}
            @if ($log->entity_id) <span style="font-family:monospace">#{{ $log->entity_id }}</span> @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada log ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $logs->links('vendor.pagination.admin') }}</div>
</section>
@endsection


