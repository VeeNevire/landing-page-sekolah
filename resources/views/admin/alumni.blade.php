@extends('layouts.admin')

@section('title', 'Alumni')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen</span>
    <h1>Daftar Alumni</h1>
    <p>Data siswa yang telah lulus, status, dan berkas kelulusan.</p>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div><h2>Alumni</h2><p>{{ $alumni->count() }} alumni terdaftar.</p></div>
  </div>

  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th style="width:40px">No</th>
          <th>Nama</th>
          <th>NISN</th>
          <th>Program</th>
          <th>Nilai Akhir</th>
          <th>Tahun Lulus</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($alumni as $i => $s)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td><strong>{{ $s->full_name }}</strong></td>
          <td style="font-family:monospace">{{ $s->nisn }}</td>
          <td>{{ $s->program_name }}</td>
          <td>
            @if ($s->final_score)
              <span style="font-weight:800;color:{{ $s->final_score >= 85 ? 'var(--success)' : ($s->final_score >= 75 ? 'var(--primary-2)' : '#ef4444') }}">{{ $s->final_score }}</span>
            @else
              <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            <span id="tahun-{{ $s->id }}">{{ $s->graduation_year ?? '—' }}</span>
          </td>
          <td>
            @php
              $statusColors = ['working' => '#0d9488', 'studying' => '#4338ca'];
              $statusLabels = ['working' => 'Kerja', 'studying' => 'Kuliah'];
            @endphp
            @if ($s->alumni_status)
              <span id="status-badge-{{ $s->id }}" style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $statusColors[$s->alumni_status] ?? '#666' }} 12%,var(--card));color:{{ $statusColors[$s->alumni_status] ?? '#666' }}">{{ $statusLabels[$s->alumni_status] ?? $s->alumni_status }}</span>
            @else
              <span id="status-badge-{{ $s->id }}" style="color:var(--muted)">—</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px">
              @if($s->user && $s->user->role === 'alumni')
              <a href="{{ route('alumni.dashboard') }}" onclick="event.preventDefault(); window.open(this.href, '_blank');" title="Buka Portal Alumni" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#059669;text-decoration:none" onmouseover="this.style.background='color-mix(in srgb,#059669 10%,var(--card))'" onmouseout="this.style.background='var(--card)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6M14 10l6.1-6.1M9 21H3v-6M10 14l-6.1 6.1"/></svg>
              </a>
              @endif
              <button type="button" onclick="editAlumni({{ $s->id }})" title="Edit Status" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:var(--primary-2)" onmouseover="this.style.background='color-mix(in srgb,var(--primary-2) 10%,var(--card))'" onmouseout="this.style.background='var(--card)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
              </button>
              <button type="button" onclick="uploadCv({{ $s->id }}, '{{ addslashes($s->full_name) }}')" title="CV" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:#7a5500" onmouseover="this.style.background='color-mix(in srgb,#7a5500 10%,var(--card))'" onmouseout="this.style.background='var(--card)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              </button>
              <a href="{{ route('admin.alumni.report', $s->id) }}" target="_blank" title="Laporan Nilai" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:var(--accent);text-decoration:none" onmouseover="this.style.background='color-mix(in srgb,var(--accent) 10%,var(--card))'" onmouseout="this.style.background='var(--card)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
              </a>
              <button type="button" onclick="detailAlumni({{ $s->id }})" title="Detail" style="width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;color:var(--primary-2)" onmouseover="this.style.background='color-mix(in srgb,var(--primary-2) 10%,var(--card))'" onmouseout="this.style.background='var(--card)'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--muted)">Belum ada data alumni.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function editAlumni(id) {
  fetch('{{ url('admin/alumni') }}/' + id + '/data')
    .then(r => r.json())
    .then(data => {
      const s = data.student;
      Swal.fire({
        title: 'Edit Alumni — ' + s.full_name,
        html: `<form id="editAlumniForm" style="text-align:left">
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:var(--ink)">Status</label>
            <select id="edit-status" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:.9rem;outline:none;background:var(--bg)">
              <option value="">— Pilih Status —</option>
              <option value="working" ${s.alumni_status === 'working' ? 'selected' : ''}>Kerja</option>
              <option value="studying" ${s.alumni_status === 'studying' ? 'selected' : ''}>Kuliah</option>
            </select>
          </div>
          <div>
            <label style="display:block;font-size:.85rem;font-weight:700;margin-bottom:5px;color:var(--ink)">Tahun Lulus</label>
            <input id="edit-tahun" type="number" min="2000" max="2099" value="${s.graduation_year || ''}" placeholder="Contoh: 2026" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:.9rem;outline:none;background:var(--bg)">
          </div>
        </form>`,
        confirmButtonText: 'Simpan',
        confirmButtonColor: '#0b3b75',
        showCancelButton: true,
        cancelButtonText: 'Batal',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        preConfirm: () => {
          const status = document.getElementById('edit-status').value;
          const tahun = document.getElementById('edit-tahun').value;
          return { alumni_status: status || null, graduation_year: tahun || null };
        }
      }).then((result) => {
        if (!result.isConfirmed) return;
        fetch('{{ url('admin/alumni') }}/' + id, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
          body: JSON.stringify(result.value)
        })
        .then(r => r.json())
        .then(d => {
          if (d.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1200, showConfirmButton: false })
              .then(() => location.reload());
          } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
          }
        });
      });
    });
}

function uploadCv(id, name) {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.pdf,.doc,.docx';
  input.onchange = function() {
    const file = input.files[0];
    if (!file) return;
    if (file.size > 2048 * 1024) {
      Swal.fire({ icon: 'error', title: 'Terlalu Besar', text: 'Maksimal 2MB.' });
      return;
    }
    const formData = new FormData();
    formData.append('cv', file);
    fetch('{{ url('admin/alumni') }}/' + id + '/cv', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF },
      body: formData
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1200, showConfirmButton: false })
          .then(() => location.reload());
      } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
      }
    });
  };
  input.click();
}

function detailAlumni(id) {
  fetch('{{ url('admin/alumni') }}/' + id + '/data')
    .then(r => r.json())
    .then(data => {
      const s = data.student;
      const statusLabels = {working:'Kerja',studying:'Kuliah'};
      const cvHtml = s.cv_path
        ? '<a href="' + s.cv_path + '" target="_blank" style="color:var(--primary-2);font-weight:600">Lihat CV</a>'
        : '<span style="color:var(--muted)">Belum upload</span>';

      let subjectsHtml = '';
      if (data.subjects.length) {
        subjectsHtml = '<div style="margin-top:12px"><strong style="font-size:.85rem;display:block;margin-bottom:8px">Nilai per Mapel</strong>';
        data.subjects.forEach(sub => {
          subjectsHtml += '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--line);font-size:.82rem">';
          subjectsHtml += '<span>' + sub.name + '</span>';
          subjectsHtml += '<span style="font-weight:700">' + sub.avg + '</span>';
          subjectsHtml += '</div>';
        });
        subjectsHtml += '</div>';
      }

      Swal.fire({
        title: s.full_name,
        html: `<div style="text-align:left;display:grid;gap:10px">
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:80px;color:var(--muted);font-size:.85rem">NISN</span>
            <span style="font-weight:700;font-family:monospace">${s.nisn}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:80px;color:var(--muted);font-size:.85rem">Program</span>
            <span style="font-weight:700">${s.program_name}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:80px;color:var(--muted);font-size:.85rem">Kelas</span>
            <span style="font-weight:700">${s.class_name}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:80px;color:var(--muted);font-size:.85rem">Tahun Lulus</span>
            <span style="font-weight:700">${s.graduation_year || '—'}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:80px;color:var(--muted);font-size:.85rem">Status</span>
            <span style="font-weight:700">${statusLabels[s.alumni_status] || '—'}</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--bg);border:1px solid var(--line)">
            <span style="font-weight:600;min-width:80px;color:var(--muted);font-size:.85rem">CV</span>
            <span style="font-weight:700">${cvHtml}</span>
          </div>
          ${subjectsHtml}
        </div>`,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#6b7280',
        width: 500,
      });
    });
}
</script>
@endpush
@endsection