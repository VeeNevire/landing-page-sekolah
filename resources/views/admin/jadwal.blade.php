@extends('layouts.admin')

@section('title', 'Jadwal')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Akademik</span>
    <h1>Jadwal &amp; Data</h1>
    <p>Kelola jadwal mengajar, daftar kelas, dan guru.</p>
  </div>
</div>

@push('styles')
  <style>
    .tab-btn {
      padding: 8px 12px;
      font-size: .9rem;
      font-weight: 400;
      border: none;
      background: none;
      box-shadow: none;
      border-radius: 0;
      cursor: pointer;
      color: var(--muted);
      transition: color .15s;
    }
    .tab-btn:hover {
      color: var(--ink);
      background: none;
      border: none;
      box-shadow: none;
    }
    .tab-btn.active {
      color: var(--ink);
      font-weight: 600;
      background: none;
      border: none;
      box-shadow: none;
      border-radius: 0;
      text-decoration: underline;
      text-underline-offset: 4px;
      text-decoration-thickness: 2px;
    }
  </style>
@endpush

<section class="portal-panel">
    <div class="tab-nav" style="display:flex;gap:0;border-bottom:1px solid var(--line);margin-bottom:1.25rem;position:sticky;top:0;z-index:5;background:var(--card)">
      <button class="tab-btn active" data-tab="jadwal">Jadwal</button>
      <button class="tab-btn" data-tab="kelas">Kelas</button>
      <button class="tab-btn" data-tab="guru">Guru</button>
    </div>

  <div class="tab-panel" id="tab-jadwal">
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
        <div class="field" style="flex:1;min-width:160px;margin:0">
          <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Kelas</label>
          <select name="class" onchange="this.form.submit()" style="min-height:42px">
            @foreach ($classNames as $cn)
              <option value="{{ $cn }}" {{ $selectedClass == $cn ? 'selected' : '' }}>{{ $cn }}</option>
            @endforeach
          </select>
        </div>
        <div style="align-self:end">
          <button type="button" class="btn btn-primary" onclick="openAddModal()" style="min-height:42px;display:inline-flex;align-items:center;gap:6px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Jadwal
          </button>
        </div>
      </form>
    </div>

    <div class="table-wrap">
      <table class="grade-table" style="min-width:800px">
        <thead>
          <tr>
            <th style="min-width:110px">Jam</th>
            @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
              <th style="text-transform:capitalize;text-align:center">{{ $day }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @php $timeSlots = [1=>'07:00–08:30', 2=>'08:30–10:00', 3=>'10:15–11:45', 4=>'12:30–14:00', 5=>'14:00–15:30']; @endphp
          @foreach ($timeSlots as $slot => $time)
          <tr>
            <td style="font-size:.82rem;font-weight:700;color:var(--muted);white-space:nowrap">{{ $time }}</td>
            @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
              @php $cell = $grid[$day][$slot] ?? null; @endphp
              <td style="text-align:center;min-width:120px;position:relative">
                @if ($cell)
                  @php $detailData = json_encode([
                    'jadwal_id' => $cell['jadwal_id'],
                    'code' => $cell['code'],
                    'subject' => $cell['subject'],
                    'teacher' => $cell['teacher'],
                    'day' => $day,
                    'time_slot' => $cell['time_slot'],
                    'time' => $time,
                    'class' => $selectedClass,
                  ]); @endphp
                  <div onclick='openDetailModal({{ $detailData }})' style="padding:10px 8px;border-radius:10px;background:color-mix(in srgb,var(--primary-2) 8%,var(--card));border:1px solid color-mix(in srgb,var(--primary-2) 15%,var(--line));cursor:pointer">
                    <div style="font-weight:700;font-size:.88rem;color:var(--primary-2)">{{ $cell['code'] }}</div>
                    <div style="font-size:.78rem;font-weight:600;margin-top:2px">{{ $cell['subject'] }}</div>
                    <div style="font-size:.72rem;color:var(--muted);margin-top:2px">{{ $cell['teacher'] }}</div>
                    <button type="button" onclick="event.stopPropagation();confirmDelete({{ $cell['jadwal_id'] }}, '{{ addslashes($cell['subject']) }}')" title="Hapus jadwal" style="position:absolute;top:4px;right:4px;width:20px;height:20px;border-radius:50%;border:none;background:color-mix(in srgb,var(--danger) 12%,transparent);color:var(--danger);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1">&times;</button>
                  </div>
                @else
                  <span style="color:var(--line);font-size:.75rem">—</span>
                @endif
              </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="tab-panel" id="tab-kelas" style="display:none">
    <div id="kelasListView">
      @php $grouped = $kelasList->groupBy(fn($k) => $k->jurusan?->nama ?? 'Tanpa Jurusan'); @endphp
      @foreach ($grouped as $jurusanName => $kelasGroup)
      <div style="margin-bottom:1.5rem">
        <h3 style="font-size:.95rem;font-weight:700;margin:0 0 .75rem;color:var(--ink)">{{ $jurusanName }}</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px">
          @foreach ($kelasGroup as $kelas)
          <div style="padding:14px 16px;border-radius:12px;background:var(--bg);border:1px solid var(--line);cursor:pointer" onclick="openKelasGuru('{{ $kelas->nama_lengkap }}')">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
              <strong style="font-size:.95rem">{{ $kelas->nama_lengkap }}</strong>
              @if ($kelas->is_active)
              <span style="font-size:.72rem;padding:3px 8px;border-radius:6px;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success);font-weight:600">Aktif</span>
              @else
              <span style="font-size:.72rem;padding:3px 8px;border-radius:6px;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444;font-weight:600">Nonaktif</span>
              @endif
            </div>
            <div style="font-size:.82rem;color:var(--muted);display:flex;gap:16px">
              <span>{{ $kelas->students_count }} siswa</span>
              @if ($kelas->homeroomTeacher)
              <span>Wali: {{ $kelas->homeroomTeacher->full_name ?? $kelas->homeroomTeacher->name }}</span>
              @else
              <span style="color:#ef4444">Belum ada wali</span>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endforeach
      @if ($kelasList->isEmpty())
      <p style="text-align:center;padding:2rem;color:var(--muted)">Tidak ada data kelas.</p>
      @endif
    </div>
  </div>

  <div class="tab-panel" id="tab-guru" style="display:none">
    <div id="guruListView">
      <div class="table-wrap">
        <table class="grade-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Mapel</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($guruList as $guru)
            <tr style="cursor:pointer" onclick="openGuruJadwal({{ $guru->id }})">
              <td>{{ $loop->iteration }}</td>
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.8rem;flex-shrink:0">{{ strtoupper(substr($guru->name, 0, 1)) }}</span>
                  <div>
                    <strong style="display:block;font-size:.85rem">{{ $guru->full_name ?: $guru->name }}</strong>
                    <span style="font-size:.75rem;color:var(--muted)">{{ '@' . $guru->name }}</span>
                  </div>
                </div>
              </td>
              <td style="font-size:.85rem">{{ $guru->email }}</td>
              <td>
                @php
                  $roleColors = ['teacher'=>'#0369a1','homeroom'=>'#0d9488','principal'=>'#7c3aed'];
                  $roleLabels = ['teacher'=>'Guru','homeroom'=>'Wali Kelas','principal'=>'Kepsek'];
                @endphp
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $roleColors[$guru->role] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$guru->role] ?? '#666' }}">{{ $roleLabels[$guru->role] ?? $guru->role }}</span>
              </td>
              <td style="font-size:.85rem;color:var(--muted)">{{ $guru->teachingAssignments->count() }} mapel</td>
              <td>
                @if ($guru->is_active)
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
                @else
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444">Nonaktif</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada guru ditemukan.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div id="guruScheduleView" style="display:none">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem">
        <button onclick="backToGuruList()" class="btn btn-outline" style="min-height:36px;padding:0 14px;display:inline-flex;align-items:center;gap:6px;font-size:.82rem;flex-shrink:0">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          Kembali
        </button>
        <div style="text-align:right;max-width:50%;overflow:hidden;white-space:nowrap;text-overflow:ellipsis">
          <strong id="guruScheduleName" style="font-size:.95rem"></strong>
          <span id="guruScheduleMapel" style="font-size:.82rem;color:var(--muted)"></span>
        </div>
      </div>
      <div class="table-wrap">
        <table class="grade-table" style="min-width:800px">
          <thead>
            <tr>
              <th style="min-width:110px">Jam</th>
              @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
                <th style="text-transform:capitalize;text-align:center">{{ $day }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody id="guruScheduleGrid"></tbody>
        </table>
      </div>
      <div style="margin-top:1.5rem">
        <h3 style="font-size:.95rem;font-weight:700;margin:0 0 .75rem">Daftar Jadwal</h3>
        <div class="table-wrap">
          <table class="grade-table">
            <thead>
              <tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Kelas</th></tr>
            </thead>
            <tbody id="guruJadwalListBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.getElementById('tab-' + this.dataset.tab).style.display = '';
  });
});

document.querySelector('.tab-btn.active')?.click();

const GURU_DATA = @json($guruData);
const TIME_SLOTS = {1:'07:00–08:30', 2:'08:30–10:00', 3:'10:15–11:45', 4:'12:30–14:00', 5:'14:00–15:30'};
const DAYS = ['senin','selasa','rabu','kamis','jumat'];

function openGuruJadwal(id) {
  const guru = GURU_DATA[id];
  if (!guru) return;
  if (!guru.jadwalList.length) {
    Swal.fire({ icon: 'info', title: 'Belum Ada Jadwal', text: 'Belum ada jadwal untuk ' + guru.name + '.', confirmButtonText: 'Tutup', confirmButtonColor: '#6b7280' });
    return;
  }
  document.getElementById('guruListView').style.display = 'none';
  document.getElementById('guruScheduleView').style.display = '';
  document.getElementById('guruScheduleName').textContent = guru.name;
  document.getElementById('guruScheduleMapel').textContent = guru.mapel.length ? `\u00a0\u00a0\u2014\u00a0\u00a0${guru.mapel.join(', ')}` : '';

  let html = '';
  for (let slot = 1; slot <= 5; slot++) {
    const time = TIME_SLOTS[slot];
    html += '<tr>';
    html += `<td style="font-size:.82rem;font-weight:700;color:var(--muted);white-space:nowrap">${time}</td>`;
    DAYS.forEach(day => {
      const cell = guru.schedule[day]?.[slot] || null;
      if (cell) {
        html += `<td style="text-align:center;min-width:120px;padding:10px 8px;border-radius:10px;background:color-mix(in srgb,var(--primary-2) 8%,var(--card));border:1px solid color-mix(in srgb,var(--primary-2) 15%,var(--line))">`;
        html += `<div style="font-weight:700;font-size:.88rem;color:var(--primary-2)">${cell.code}</div>`;
        html += `<div style="font-size:.78rem;font-weight:600;margin-top:2px">${cell.subject}</div>`;
        html += `<div style="font-size:.72rem;color:var(--muted);margin-top:2px">${cell.class}</div>`;
        html += '</td>';
      } else {
        html += `<td style="text-align:center;color:var(--line);font-size:.75rem">—</td>`;
      }
    });
    html += '</tr>';
  }
  document.getElementById('guruScheduleGrid').innerHTML = html;

  const listHtml = guru.jadwalList.map(item => `
    <tr>
      <td><strong>${item.day}</strong></td>
      <td>${item.timeLabel}</td>
      <td>${item.subject}</td>
      <td><span style="background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2);padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem">${item.class}</span></td>
    </tr>
  `).join('');
  document.getElementById('guruJadwalListBody').innerHTML = listHtml;
}

function backToGuruList() {
  document.getElementById('guruScheduleView').style.display = 'none';
  document.getElementById('guruListView').style.display = '';
}

const KELAS_DATA = @json($kelasData);

function openKelasGuru(className) {
  const data = KELAS_DATA[className];
  if (!data || !data.guru.length) {
    Swal.fire({ icon: 'info', title: 'Belum Ada Jadwal', text: 'Belum ada jadwal untuk ' + className + '.', confirmButtonText: 'Tutup', confirmButtonColor: '#6b7280' });
    return;
  }
  const list = data.guru.map(g =>
    `<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line);margin-bottom:6px">
      <strong style="font-size:.88rem">${g.nama}</strong>
      <span style="font-size:.82rem;color:var(--muted)">${g.mapel}</span>
    </div>`
  ).join('');
  Swal.fire({
    title: `Guru — ${className}`,
    html: `<div style="text-align:left">${list || '<p style="color:var(--muted)">Tidak ada guru.</p>'}</div>`,
    confirmButtonText: 'Tutup',
    confirmButtonColor: '#6b7280',
  });
}

function openAddModal() {
  Swal.fire({
    title: 'Tambah Jadwal',
    html: `
      <form id="jadwalForm" style="text-align:left">
        @csrf
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Mapel & Guru</label>
          <select id="modalTeachingAssignment" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Mapel --</option>
            @foreach ($subjects as $subject)
              <option value="{{ $subject['teaching_assignment_id'] }}">{{ $subject['label'] }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Hari</label>
          <select id="modalDay" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Hari --</option>
            @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
              <option value="{{ $day }}">{{ ucfirst($day) }}</option>
            @endforeach
          </select>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Jam ke-</label>
          <select id="modalSlot" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Slot --</option>
            @foreach ([1=>'07:00–08:30', 2=>'08:30–10:00', 3=>'10:15–11:45', 4=>'12:30–14:00', 5=>'14:00–15:30'] as $slot => $time)
              <option value="{{ $slot }}">Slot {{ $slot }} ({{ $time }})</option>
            @endforeach
          </select>
        </div>
      </form>
    `,
    confirmButtonText: 'Simpan',
    confirmButtonColor: '#0b3b75',
    showCancelButton: true,
    cancelButtonText: 'Batal',
    cancelButtonColor: '#6b7280',
    reverseButtons: true,
    preConfirm: () => {
      const taId = document.getElementById('modalTeachingAssignment').value;
      const day = document.getElementById('modalDay').value;
      const timeSlot = document.getElementById('modalSlot').value;
      if (!taId || !day || !timeSlot) {
        Swal.showValidationMessage('Semua field harus diisi');
        return false;
      }
      return { teaching_assignment_id: taId, day, time_slot: timeSlot };
    }
  }).then((result) => {
    if (!result.isConfirmed) return;
    const data = result.value;
    fetch('{{ route("admin.jadwal.store") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1500, showConfirmButton: false })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
      }
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
    });
  });
}

function confirmDelete(jadwalId, subjectName) {
  Swal.fire({
    title: 'Hapus Jadwal?',
    html: `Jadwal <strong>${subjectName}</strong> akan dihapus.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    confirmButtonColor: '#dc2626',
    cancelButtonText: 'Batal',
    cancelButtonColor: '#6b7280',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) return;
    fetch(`{{ url('admin/jadwal') }}/${jadwalId}`, {
      method: 'DELETE',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json'
      }
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1500, showConfirmButton: false })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
      }
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
    });
  });
}

function openDetailModal(d) {
  Swal.fire({
    title: 'Detail Jadwal',
    html: `
      <div style="text-align:left">
        <div style="display:grid;gap:10px">
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:70px;color:var(--muted);font-size:.85rem">Kelas</span>
            <span style="font-weight:700;font-size:.9rem">${d.class}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:70px;color:var(--muted);font-size:.85rem">Hari</span>
            <span style="font-weight:700;font-size:.9rem;text-transform:capitalize">${d.day}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:70px;color:var(--muted);font-size:.85rem">Waktu</span>
            <span style="font-weight:700;font-size:.9rem">${d.time}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:70px;color:var(--muted);font-size:.85rem">Mapel</span>
            <span style="font-weight:700;font-size:.9rem">${d.code} — ${d.subject}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:70px;color:var(--muted);font-size:.85rem">Guru</span>
            <span style="font-weight:700;font-size:.9rem">${d.teacher}</span>
          </div>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Edit',
    confirmButtonColor: '#0b3b75',
    cancelButtonText: 'Tutup',
    cancelButtonColor: '#6b7280',
    reverseButtons: true,
    showDenyButton: true,
    denyButtonText: 'Hapus',
    denyButtonColor: '#dc2626',
  }).then((result) => {
    if (result.isConfirmed) {
      openEditModal(d);
    } else if (result.isDenied) {
      confirmDelete(d.jadwal_id, d.subject);
    }
  });
}

function openEditModal(d) {
  const days = ['senin','selasa','rabu','kamis','jumat'];
  const slots = {1:'07:00–08:30', 2:'08:30–10:00', 3:'10:15–11:45', 4:'12:30–14:00', 5:'14:00–15:30'};

  const dayOptions = days.map(day =>
    `<option value="${day}" ${d.day === day ? 'selected' : ''}>${day.charAt(0).toUpperCase() + day.slice(1)}</option>`
  ).join('');

  const slotOptions = Object.entries(slots).map(([slot, time]) =>
    `<option value="${slot}" ${String(d.time_slot) === slot ? 'selected' : ''}>Slot ${slot} (${time})</option>`
  ).join('');

  Swal.fire({
    title: 'Edit Jadwal',
    html: `
      <form id="editJadwalForm" style="text-align:left">
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Hari</label>
          <select id="editDay" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Hari --</option>
            ${dayOptions}
          </select>
        </div>
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:#333">Jam ke-</label>
          <select id="editSlot" required style="width:100%;padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none">
            <option value="">-- Pilih Slot --</option>
            ${slotOptions}
          </select>
        </div>
      </form>
    `,
    confirmButtonText: 'Simpan',
    confirmButtonColor: '#0b3b75',
    showCancelButton: true,
    cancelButtonText: 'Batal',
    cancelButtonColor: '#6b7280',
    reverseButtons: true,
    preConfirm: () => {
      const day = document.getElementById('editDay').value;
      const timeSlot = document.getElementById('editSlot').value;
      if (!day || !timeSlot) {
        Swal.showValidationMessage('Semua field harus diisi');
        return false;
      }
      return { day, time_slot: timeSlot };
    }
  }).then((result) => {
    if (!result.isConfirmed) return;
    const data = result.value;
    fetch(`{{ url('admin/jadwal') }}/${d.jadwal_id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1500, showConfirmButton: false })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
      }
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan server.' });
    });
  });
}
</script>
@endpush
