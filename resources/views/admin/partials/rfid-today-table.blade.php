@php
$statusLabels = ['present' => 'Hadir', 'sick' => 'Sakit', 'excused' => 'Izin', 'unexcused' => 'Alpa', 'late' => 'Terlambat'];
$statusColors = ['present' => 'var(--success)', 'sick' => '#3d8baf', 'excused' => '#6366f1', 'unexcused' => 'var(--danger)', 'late' => '#d97706'];
$cards = [
  'active' => ['Siswa Aktif', 'var(--primary-2)', null],
  'recorded' => ['Sudah Tercatat', 'var(--success)', 'recorded'],
  'unrecorded' => ['Belum Tercatat', '#d97706', 'unrecorded'],
  'present' => ['Hadir', 'var(--success)', 'present'],
  'late' => ['Terlambat', '#d97706', 'late'],
];
@endphp
<div class="rfid-summary-grid">
  @foreach($cards as $key => [$label, $color, $target])
    @if($target)
    <a class="rfid-stat-card rfid-stat-link {{ $view === $target ? 'active' : '' }}" href="{{ route('admin.rfid.index', array_merge($filters, $target === 'recorded' ? [] : ['view' => $target])) }}" @if($view === $target) aria-current="true" @endif>
    @else
    <article class="rfid-stat-card">
    @endif
      <span class="rfid-stat-label">{{ $label }}</span>
      <strong class="rfid-stat-value" style="color:{{ $color }}">{{ $summary[$key] }}</strong>
    @if($target)</a>@else</article>@endif
  @endforeach
</div>

<div class="rfid-lastscan">
  <span class="rfid-lastscan-icon" aria-hidden="true">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.01M10 8h8M6 12h.01M10 12h8M6 16h.01M10 16h4"/></svg>
  </span>
  <div class="rfid-lastscan-body">
    <strong>Hasil Scan Terakhir</strong>
    <p>{{ $lastScan['message'] ?? 'Belum ada scan dari perangkat.' }}@if($lastScan['seen_at'] ?? null) <span class="rfid-lastscan-time">· {{ $lastScan['seen_at'] }} WIB</span>@endif</p>
    @if($lastScan['uid'] ?? null)
      <p>UID <code>{{ $lastScan['uid'] }}</code>
        @if($lastScan['unregistered'])
          · <a href="{{ route('admin.rfid.index', ['tab' => 'registration', 'card' => 'unregistered']) }}">Daftarkan kartu ini</a>
        @endif
      </p>
    @endif
  </div>
  <span class="rfid-reader-badge {{ $reader['online'] ? 'online' : 'offline' }}" title="Status reader RFID">
    <span class="rfid-reader-dot" aria-hidden="true"></span>
    {{ $reader['online'] ? 'Reader online' : 'Reader offline' }}
    @if(!$reader['online'])<small>{{ $reader['seen_at'] ? 'terakhir '.$reader['seen_at'] : 'belum pernah terhubung' }}</small>@endif
  </span>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        @if($view === 'unrecorded')
        <tr><th>Siswa</th><th>NIS</th><th>Kelas</th><th>Kartu RFID</th><th>Status</th></tr>
        @else
        <tr><th>Siswa</th><th>NIS</th><th>Kelas</th><th>Jam Masuk</th><th>Status</th><th>Sumber</th></tr>
        @endif
      </thead>
      <tbody>
      @if($view === 'unrecorded')
        @forelse($rows as $student)
        <tr>
          <td>
            <div class="rfid-student-cell">
              <span class="rfid-avatar">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
              <strong>{{ $student->full_name }}</strong>
            </div>
          </td>
          <td>{{ $student->nis ?: $student->nisn }}</td>
          <td><span class="rfid-class-badge">{{ $student->class_name }}</span></td>
          <td>{{ $student->rfid_uid ? 'Terdaftar' : 'Belum punya kartu' }}</td>
          <td><span class="rfid-status-badge" style="background:color-mix(in srgb,#d97706 14%,var(--card));color:#d97706">Belum tercatat</span></td>
        </tr>
        @empty
        <tr>
          <td colspan="5" style="padding:0">
            <div class="empty-state">
              <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <h3>Semua Sudah Tercatat</h3>
              <p>Tidak ada siswa aktif yang belum tercatat hari ini untuk filter ini.</p>
            </div>
          </td>
        </tr>
        @endforelse
      @else
        @forelse($rows as $row)
        @php $student = $row->student; @endphp
        <tr data-attendance-id="{{ $row->id }}">
          <td>
            <div class="rfid-student-cell">
              <span class="rfid-avatar">{{ strtoupper(substr($student->full_name ?? '?', 0, 1)) }}</span>
              <strong>{{ $student->full_name ?? '—' }}</strong>
            </div>
          </td>
          <td>{{ $student->nis ?: $student->nisn }}</td>
          <td><span class="rfid-class-badge">{{ $student->class_name }}</span></td>
          <td>{{ $row->check_in_at?->format('H:i:s') ?? '—' }}</td>
          <td><span class="rfid-status-badge" style="background:color-mix(in srgb,{{ $statusColors[$row->status] ?? '#666' }} 14%,var(--card));color:{{ $statusColors[$row->status] ?? '#666' }}">{{ $statusLabels[$row->status] ?? $row->status }}</span></td>
          <td>{{ $row->source === 'rfid' ? 'RFID' : 'Manual' }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="padding:0">
            <div class="empty-state">
              <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.01M10 8h8M6 12h.01M10 12h8"/></svg>
              </div>
              <h3>Belum Ada Absensi</h3>
              <p>Belum ada absensi yang sesuai dengan filter hari ini.</p>
            </div>
          </td>
        </tr>
        @endforelse
      @endif
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $rows->links('vendor.pagination.admin') }}</div>
</section>
