@php
$statusLabels = ['present' => 'Hadir', 'sick' => 'Sakit', 'excused' => 'Izin', 'unexcused' => 'Alpa', 'late' => 'Terlambat'];
$statusColors = ['present' => 'var(--success)', 'sick' => '#3d8baf', 'excused' => '#6366f1', 'unexcused' => 'var(--danger)', 'late' => '#d97706'];
@endphp
<div class="rfid-summary-grid">
  @foreach(['active' => ['Siswa Aktif', 'var(--primary-2)'], 'recorded' => ['Sudah Tercatat', 'var(--success)'], 'unrecorded' => ['Belum Tercatat', '#d97706'], 'present' => ['Hadir', 'var(--success)']] as $key => $meta)
  <article class="rfid-stat-card">
    <span class="rfid-stat-label">{{ $meta[0] }}</span>
    <strong class="rfid-stat-value" style="color:{{ $meta[1] }}">{{ $summary[$key] }}</strong>
  </article>
  @endforeach
</div>

<div class="rfid-lastscan">
  <span class="rfid-lastscan-icon" aria-hidden="true">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.01M10 8h8M6 12h.01M10 12h8M6 16h.01M10 16h4"/></svg>
  </span>
  <div>
    <strong>Hasil Scan Terakhir</strong>
    <p>{{ $lastScan['message'] ?? 'Belum ada scan dari perangkat.' }}@if($lastScan['seen_at'] ?? null) <span class="rfid-lastscan-time">· {{ $lastScan['seen_at'] }} WIB</span>@endif</p>
  </div>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr><th>Siswa</th><th>NIS</th><th>Kelas</th><th>Jam Masuk</th><th>Status</th><th>Sumber</th></tr>
      </thead>
      <tbody>
      @forelse($rows as $row)
        @php $student = $row->student; @endphp
        <tr>
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
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $rows->links('vendor.pagination.admin') }}</div>
</section>
