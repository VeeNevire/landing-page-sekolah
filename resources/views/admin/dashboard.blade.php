@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Dashboard administrator</span>
    <h1>Selamat datang, {{ auth()->user()->full_name ?? auth()->user()->name }}.</h1>
    <p>Berikut ringkasan data sekolah dan aktivitas sistem.</p>
  </div>
</div>

<section class="portal-kpis">
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Siswa</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalStudents }}</strong>
    <span class="portal-kpi-note">Siswa aktif</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Guru</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalTeachers }}</strong>
    <span class="portal-kpi-note">Guru & wali kelas</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Total Orang Tua</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalParents }}</strong>
    <span class="portal-kpi-note">Akun orang tua</span>
  </article>
  <article class="portal-kpi">
    <div class="portal-kpi-label"><span>Kelas Aktif</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span></div>
    <strong class="portal-kpi-value">{{ $totalClasses }}</strong>
    <span class="portal-kpi-note">Kelas</span>
  </article>
</section>

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Info Sistem</h2>
        <p>Status periode akademik dan ringkasan data.</p>
      </div>
    </div>
    <div style="display:grid;gap:14px">
      <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        <div style="flex:1">
          <strong style="display:block">Periode Aktif</strong>
          <span style="color:var(--muted);font-size:.88rem">{{ $activePeriod ? "{$activePeriod->academic_year} Semester {$activePeriod->semester}" : 'Tidak ada periode aktif' }}</span>
        </div>
        @if ($activePeriod)
          <span style="padding:4px 12px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
        @else
          <span style="padding:4px 12px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444">Nonaktif</span>
        @endif
      </div>
      <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
        <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
        </span>
        <div style="flex:1">
          <strong style="display:block">Total Mata Pelajaran</strong>
          <span style="color:var(--muted);font-size:.88rem">{{ $totalSubjects }} mata pelajaran terdaftar</span>
        </div>
      </div>
      <div onclick="toggleAttendance(event)" style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card);cursor:pointer;transition:.15s" onmouseover="this.style.borderColor='var(--primary-2)'" onmouseout="this.style.borderColor='var(--line)'">
        <span style="flex-shrink:0;width:48px;height:48px;border-radius:14px;display:grid;place-items:center;background:color-mix(in srgb,var(--accent) 12%,var(--card));color:#7a5500">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </span>
        <div style="flex:1">
          <strong style="display:block">Absensi Hari Ini</strong>
          <span style="color:var(--muted);font-size:.88rem">{{ $todayAttendance }} catatan kehadiran &mdash; klik untuk detail</span>
        </div>
        <span id="attendanceArrow" style="color:var(--muted);transition:transform .25s">&#9660;</span>
      </div>
      <div id="attendanceDropdown" style="display:none;margin-top:12px">
        @php
          $statusMeta = [
            'present' => ['label' => 'Hadir', 'color' => '#34C759'],
            'sick' => ['label' => 'Sakit', 'color' => '#FF9F0A'],
            'excused' => ['label' => 'Izin', 'color' => '#007AFF'],
            'unexcused' => ['label' => 'Alpa', 'color' => '#FF3B30'],
            'late' => ['label' => 'Terlambat', 'color' => '#FF9F0A'],
          ];
        @endphp
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
          @foreach (['present','sick','excused','unexcused'] as $status)
            @php
              $meta = $statusMeta[$status];
              $items = $todayAttendanceByStatus->get($status, collect());
              $studentList = $items->map(fn($att) => [
                'name' => $att->student?->full_name ?? 'Unknown',
                'nisn' => $att->student?->nisn ?? '-',
                'class' => $att->student?->class_name ?? '-',
              ])->values()->toArray();
            @endphp
            <div onclick="showAttendanceModal(this)" data-label="{{ $meta['label'] }}" data-color="{{ $meta['color'] }}" data-students='@json($studentList)' style="cursor:pointer;padding:16px;border-radius:14px;border:1.5px solid var(--line);background:var(--card);transition:.15s" onmouseover="this.style.borderColor='{{ $meta['color'] }}';this.style.boxShadow='0 2px 8px {{ $meta['color'] }}22'" onmouseout="this.style.borderColor='var(--line)';this.style.boxShadow='none'">
              <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                  <div style="font-size:.75rem;font-weight:600;color:var(--muted);margin-bottom:4px">{{ $meta['label'] }}</div>
                  <div style="font-size:1.4rem;font-weight:800;background:linear-gradient(135deg,{{ $meta['color'] }},{{ $meta['color'] }}dd);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $items->count() }}</div>
                </div>
                <div style="width:36px;height:36px;border-radius:12px;display:grid;place-items:center;background:color-mix(in srgb,{{ $meta['color'] }} 14%,var(--card))">
                  @if ($status === 'present')
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  @elseif ($status === 'sick')
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2h6v5h5v6h-5v5H9v-5H4V7h5V2z"/></svg>
                  @elseif ($status === 'excused')
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                  @elseif ($status === 'unexcused')
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $meta['color'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <script>
      function toggleAttendance(e) {
        const dd = document.getElementById('attendanceDropdown');
        const arrow = document.getElementById('attendanceArrow');
        const isOpen = dd.style.display !== 'none';
        dd.style.display = isOpen ? 'none' : 'block';
        arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
      }
      function showAttendanceModal(el) {
        const label = el.dataset.label;
        const color = el.dataset.color;
        const students = JSON.parse(el.dataset.students);
        let html = '';
        if (students.length === 0) {
          html = '<div style="text-align:center;padding:24px;color:var(--muted);font-size:.9rem">Tidak ada siswa dengan status <strong>' + label + '</strong> hari ini.</div>';
        } else {
          html = '<div style="display:flex;flex-direction:column;gap:8px">';
          students.forEach(function(s) {
            html += '<div style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--line);background:var(--card)">';
            html += '<span style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.72rem;font-weight:700;color:#fff;background:' + color + ';flex-shrink:0">' + s.name.charAt(0).toUpperCase() + '</span>';
            html += '<div style="flex:1;min-width:0">';
            html += '<div style="font-weight:600;font-size:.85rem;color:var(--ink)">' + s.name + '</div>';
            html += '<div style="font-size:.73rem;color:var(--muted)">NISN ' + s.nisn + ' &bull; ' + s.class + '</div>';
            html += '</div></div>';
          });
          html += '</div>';
        }
        Swal.fire({
          title: 'Siswa ' + label,
          html: html,
          showConfirmButton: true,
          confirmButtonText: 'Tutup',
          confirmButtonColor: color,
          width: 480,
          padding: '24px',
        });
      }
      </script>
    </div>
  </section>

  <section class="portal-panel">
    <div class="portal-panel-header">
      <div>
        <h2>Aktivitas Terbaru</h2>
        <p>Log aktivitas 5 terakhir.</p>
      </div>
    </div>
    @if ($recentAudit->isEmpty())
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Belum ada aktivitas tercatat.</p>
      </div>
    @else
      <div class="activity-feed">
        @foreach ($recentAudit as $log)
          <div class="activity-item">
            <span class="activity-icon" style="background:color-mix(in srgb,var(--primary-2) 14%,var(--card))">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </span>
            <div>
              <strong>{{ $log->user->name ?? 'System' }}</strong>
              <span>{{ $log->action }} &mdash; {{ $log->created_at->diffForHumans() }}</span>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </section>
</div>
@endsection



