@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen pengguna</span>
    <h1>Kelola Pengguna</h1>
    <p>Kelola akun guru, orang tua, administrator, dan wali kelas.</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Pengguna
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:2;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email...">
    </div>
    <div class="field" style="flex:1;min-width:150px">
      <select name="role">
        <option value="">Semua Role</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Guru</option>
        <option value="homeroom" {{ request('role') === 'homeroom' ? 'selected' : '' }}>Wali Kelas</option>
        <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>Orang Tua</option>
        <option value="principal" {{ request('role') === 'principal' ? 'selected' : '' }}>Kepala Sekolah</option>
      </select>
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request('search') || request('role'))
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Login Terakhir</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <span style="width:36px;height:36px;border-radius:10px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.82rem;flex-shrink:0">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div>
                  <strong style="display:block;font-size:.92rem">{{ $user->full_name ?: $user->name }}</strong>
                  <span style="font-size:.8rem;color:var(--muted)">{{ $user->name }}</span>
                </div>
              </div>
            </td>
            <td>{{ $user->email }}</td>
            <td>
              @php
                $roleColors = ['admin' => '#4338ca', 'teacher' => '#0369a1', 'homeroom' => '#0d9488', 'parent' => '#b45309', 'principal' => '#7c3aed'];
                $roleLabels = ['admin' => 'Admin', 'teacher' => 'Guru', 'homeroom' => 'Wali Kelas', 'parent' => 'Orang Tua', 'principal' => 'Kepsek'];
              @endphp
              <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $roleColors[$user->role] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$user->role] ?? '#666' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
            </td>
            <td>
              @if ($user->is_active)
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
              @else
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444">Nonaktif</span>
              @endif
            </td>
            <td style="font-size:.85rem;color:var(--muted)">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }}</td>
            <td>
              <div style="display:flex;gap:6px;flex-wrap:wrap">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline" style="min-height:32px;padding:0 12px;font-size:.8rem">Edit</a>
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 12px;font-size:.8rem;color:{{ $user->is_active ? '#ef4444' : 'var(--success)' }}">
                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada pengguna ditemukan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $users->links() }}</div>
</section>
@endsection
