@extends('layouts.admin')

@section('title', 'Audit Log')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Keamanan sistem</span>
    <h1>Audit Log</h1>
    <p>Pantau semua aktivitas dan perubahan data dalam sistem.</p>
  </div>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:1;min-width:180px">
      <select name="user_id">
        <option value="">Semua Pengguna</option>
        @foreach ($users as $user)
          <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:180px">
      <input type="text" name="action" value="{{ request('action') }}" placeholder="Cari aksi (contoh: login, create...)">
    </div>
    <div class="field" style="flex:0;min-width:150px">
      <input type="date" name="from" value="{{ request('from') }}" placeholder="Dari tanggal">
    </div>
    <div class="field" style="flex:0;min-width:150px">
      <input type="date" name="to" value="{{ request('to') }}" placeholder="Sampai tanggal">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request()->hasAny(['user_id', 'action', 'from', 'to']))
      <a href="{{ route('admin.audit.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
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
          <th>ID Entitas</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($logs as $log)
          <tr>
            <td style="font-size:.85rem;white-space:nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
            <td>
              <div style="display:flex;align-items:center;gap:6px">
                <span style="width:28px;height:28px;border-radius:8px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.7rem;flex-shrink:0">{{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}</span>
                <span style="font-size:.88rem">{{ $log->user->name ?? 'System' }}</span>
              </div>
            </td>
            <td>
              <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2);font-family:monospace">{{ $log->action }}</span>
            </td>
            <td style="font-size:.85rem;color:var(--muted)">{{ $log->entity_type ?? '-' }}</td>
            <td style="font-size:.85rem;font-family:monospace">{{ $log->entity_id ?? '-' }}</td>
            <td style="font-size:.82rem;font-family:monospace;color:var(--muted)">{{ $log->ip_address ?? '-' }}</td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada log ditemukan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $logs->links() }}</div>
</section>
@endsection
