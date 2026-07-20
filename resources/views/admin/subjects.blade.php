@extends('layouts.admin')

@section('title', 'Kelola Mata Pelajaran')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen akademik</span>
    <h1>Mata Pelajaran</h1>
    <p>Kelola daftar mata pelajaran, guru pengampu, dan nilai KKM.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Mata Pelajaran
  </button>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:1;min-width:250px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama mata pelajaran...">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search'))
      <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="subjectsTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Kode</th>
          <th>Nama Mata Pelajaran</th>
          <th>Guru Pengampu</th>
          <th>KKM</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subjects as $subject)
        <tr id="row-{{ $subject->id }}"
            data-id="{{ $subject->id }}"
            data-code="{{ $subject->code }}"
            data-name="{{ $subject->name }}"
            data-kkm="{{ $subject->kkm }}"
            data-guru_ids="{{ $subject->gurus->pluck('id')->toJson() }}">
          <td style="text-align:center">{{ $loop->iteration + ($subjects->currentPage() - 1) * $subjects->perPage() }}</td>
          <td style="font-family:monospace;font-weight:700">{{ $subject->code }}</td>
          <td>{{ $subject->name }}</td>
          <td>
            <div style="display:flex;flex-wrap:wrap;gap:4px">
              @forelse ($subject->gurus as $guru)
                <span style="padding:3px 8px;border-radius:6px;font-size:.72rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)">{{ $guru->full_name ?: $guru->name }}</span>
              @empty
                <span style="font-size:.82rem;color:var(--muted)">-</span>
              @endforelse
            </div>
          </td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.82rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $subject->kkm }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Lihat kelas pengguna" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:var(--primary-2)" onclick="openDetailModal({{ $subject->id }})">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
              </button>
              <button type="button" class="btn btn-outline" title="Edit" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $subject->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $subject->id }}, '{{ addslashes($subject->name) }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada mata pelajaran.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $subjects->links('vendor.pagination.admin') }}</div>
</section>

{{-- Mapel Per Jurusan --}}
<section class="portal-panel" style="margin-top:1.5rem">
  <div style="display:flex;align-items:center;justify-content:space-between;padding:0 0 1rem">
    <div>
      <h2 style="font-size:1.1rem;font-weight:700;margin:0;color:var(--ink)">Mata Pelajaran Per Jurusan</h2>
      <p style="font-size:.85rem;color:var(--muted);margin:4px 0 0">Kelola pelajaran khusus per jurusan</p>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:8px">
    @foreach ($jurusans as $jurusan)
    <div class="jurusan-cs-group" data-jurusan-id="{{ $jurusan->id }}">
      <div class="jurusan-cs-header" onclick="toggleJurusanCS(this)">
        <div style="display:flex;align-items:center;gap:10px">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="cs-chevron"><polyline points="9 18 15 12 9 6"/></svg>
          <strong style="font-size:.92rem">{{ $jurusan->kode }} — {{ $jurusan->nama }}</strong>
          <span style="font-size:.78rem;color:var(--muted);font-weight:400">({{ $jurusan->customSubjects->count() }} mapel)</span>
        </div>
        <button type="button" class="btn btn-outline" onclick="event.stopPropagation();showAddCSForm(this)" style="min-height:30px;padding:0 12px;font-size:.8rem;display:inline-flex;align-items:center;gap:4px">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Tambah
        </button>
      </div>
      <div class="jurusan-cs-body" style="display:none">
        @forelse ($jurusan->customSubjects as $cs)
        <div class="jurusan-cs-item" data-cs-id="{{ $cs->id }}">
          <span style="font-weight:600;font-size:.85rem"><strong>{{ $cs->kode }}</strong> — {{ $cs->nama }}@if($cs->kkm) <span style="font-weight:400;color:var(--muted);font-size:.78rem">(KKM {{ $cs->kkm }})</span>@endif</span>
          <button type="button" onclick="confirmDeleteCS({{ $jurusan->id }}, {{ $cs->id }}, '{{ addslashes($cs->nama) }}')" class="btn btn-outline" style="min-height:28px;min-width:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" title="Hapus">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        @empty
        <p style="margin:8px 0;font-size:.85rem;color:var(--muted);padding:8px 14px">Belum ada pelajaran jurusan.</p>
        @endforelse
        <div class="jurusan-cs-form" style="display:none;margin-top:8px;padding-top:10px;border-top:1px solid var(--line)">
          <div style="display:grid;grid-template-columns:1fr 2fr 1fr;gap:8px;margin-bottom:8px">
            <input type="text" class="cs-form-kode" placeholder="Kode" style="padding:.5rem .6rem;border-radius:8px;font-size:.82rem;outline:none;background:var(--card);border:1.5px solid var(--line);color:var(--ink);font-family:inherit;text-transform:uppercase">
            <input type="text" class="cs-form-nama" placeholder="Nama mata pelajaran" style="padding:.5rem .6rem;border-radius:8px;font-size:.82rem;outline:none;background:var(--card);border:1.5px solid var(--line);color:var(--ink);font-family:inherit">
            <input type="number" class="cs-form-kkm" placeholder="KKM" step="0.01" min="0" max="100" style="padding:.5rem .6rem;border-radius:8px;font-size:.82rem;outline:none;background:var(--card);border:1.5px solid var(--line);color:var(--ink);font-family:inherit">
          </div>
          <div style="display:flex;gap:6px">
            <button type="button" class="btn btn-primary" onclick="saveCS(this)" style="min-height:32px;padding:0 14px;font-size:.82rem">Simpan</button>
            <button type="button" class="btn btn-outline" onclick="cancelCSForm(this)" style="min-height:32px;padding:0 14px;font-size:.82rem">Batal</button>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</section>

{{-- Modal --}}
<div class="admin-modal-overlay" id="subjectModal">
  <div class="admin-modal-box" style="max-width:540px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Mata Pelajaran</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form id="subjectForm" onsubmit="submitForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" id="formSubjectId" value="">

        <div style="display:grid;grid-template-columns:1fr 2fr 1fr;gap:14px">
          <div class="field">
            <label for="m_code">Kode <span style="color:#ef4444">*</span></label>
            <input id="m_code" name="code" type="text" required placeholder="MTK" style="text-transform:uppercase">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label for="m_name">Nama <span style="color:#ef4444">*</span></label>
            <input id="m_name" name="name" type="text" required placeholder="Matematika">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label for="m_kkm">KKM <span style="color:#ef4444">*</span></label>
            <input id="m_kkm" name="kkm" type="number" required value="75" min="0" max="100" step="0.01">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
        </div>

        <div class="field" style="margin-top:14px">
          <label>Guru Pengampu</label>
          <input type="text" id="guruSearch" placeholder="Cari nama guru..." oninput="filterGurus()" style="width:100%;padding:8px 12px;border:1px solid var(--line);border-radius:10px;font-size:.88rem;background:var(--card);margin-bottom:8px">
          <div id="guruCheckboxList" style="max-height:200px;overflow-y:auto;border:1px solid var(--line);border-radius:10px;padding:6px">
            @foreach ($teachers as $teacher)
            <label class="guru-check-item">
              <input type="checkbox" name="guru_ids[]" value="{{ $teacher->id }}" onchange="updateGuruCount()">
              <span>{{ $teacher->full_name ?: $teacher->name }}</span>
              <small>({{ $teacher->role }})</small>
            </label>
            @endforeach
          </div>
          <small style="color:var(--muted);font-size:.78rem"><span id="guruCount">0</span> guru dipilih</small>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Detail Modal --}}
<div class="admin-modal-overlay" id="detailModal">
  <div class="admin-modal-box" style="max-width:560px">
    <div class="admin-modal-header">
      <h2 id="detailTitle">Detail Mata Pelajaran</h2>
      <button class="admin-modal-close" onclick="closeDetailModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="admin-modal-body" id="detailBody">
      <div style="text-align:center;padding:2rem;color:var(--muted)">Memuat...</div>
    </div>
    <div class="admin-modal-footer" id="detailFooter" style="display:none">
      <button type="button" class="btn btn-outline" onclick="closeDetailModal()">Tutup</button>
      <button type="button" class="btn btn-primary" onclick="saveDetailSubjects()">Simpan</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

function openCreateModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Mata Pelajaran';
  document.getElementById('modalSubmitBtn').textContent = 'Simpan';
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('formSubjectId').value = '';
  document.getElementById('subjectForm').action = '{{ route("admin.subjects.store") }}';
  document.getElementById('m_code').value = '';
  document.getElementById('m_name').value = '';
  document.getElementById('m_kkm').value = '75';
  clearSelectedGurus();
  document.getElementById('guruSearch').value = '';
  filterGurus();
  clearErrors();
  document.getElementById('subjectModal').classList.add('open');
}

function openEditModal(subjectId) {
  clearErrors();
  clearSelectedGurus();
  document.getElementById('guruSearch').value = '';
  filterGurus();
  document.getElementById('modalTitle').textContent = 'Edit Mata Pelajaran';
  document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('formSubjectId').value = subjectId;
  document.getElementById('subjectForm').action = '/admin/subjects/' + subjectId;

  fetch('/admin/subjects/' + subjectId + '/data', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('m_code').value = data.code;
    document.getElementById('m_name').value = data.name;
    document.getElementById('m_kkm').value = data.kkm;
    data.guru_ids.forEach(id => {
      const cb = document.querySelector('#guruCheckboxList input[type="checkbox"][value="' + id + '"]');
      if (cb) cb.checked = true;
    });
    updateGuruCount();
    document.getElementById('subjectModal').classList.add('open');
  });
}

function clearSelectedGurus() {
  document.querySelectorAll('#guruCheckboxList input[type="checkbox"]').forEach(cb => cb.checked = false);
  updateGuruCount();
}

function filterGurus() {
  const query = document.getElementById('guruSearch').value.toLowerCase();
  document.querySelectorAll('#guruCheckboxList label').forEach(label => {
    const text = label.textContent.toLowerCase();
    label.style.display = text.includes(query) ? '' : 'none';
  });
}

function updateGuruCount() {
  const count = document.querySelectorAll('#guruCheckboxList input[type="checkbox"]:checked').length;
  document.getElementById('guruCount').textContent = count;
}

function closeModal() {
  document.getElementById('subjectModal').classList.remove('open');
  clearErrors();
}

function clearErrors() {
  document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
  document.querySelectorAll('.admin-modal-box .field input').forEach(el => el.style.borderColor = '');
}

function showFieldError(fieldName, message) {
  const form = document.getElementById('subjectForm');
  const input = form.querySelector('[name="' + fieldName + '"]');
  if (input) {
    input.style.borderColor = '#ef4444';
    const errorEl = input.closest('.field').querySelector('.field-error');
    if (errorEl) { errorEl.textContent = message; errorEl.style.display = 'block'; }
  }
}

function submitForm(e) {
  e.preventDefault();
  clearErrors();
  const form = document.getElementById('subjectForm');
  const formData = new FormData(form);

  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOKEN,
    }
  })
  .then(r => r.json().then(json => ({ ok: r.ok, json })))
  .then(({ ok, json }) => {
    if (ok && json.success) {
      closeModal();
      Swal.fire({
        toast: true, position: 'top-end', icon: 'success', title: json.message,
        showConfirmButton: false, timer: 3000, timerProgressBar: true,
      }).then(() => { location.reload(); });
    } else if (json.errors) {
      Object.entries(json.errors).forEach(([field, messages]) => {
        showFieldError(field, messages[0]);
      });
    } else {
      Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch(() => {
    Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
  });
}

function confirmDelete(subjectId, subjectName) {
  Swal.fire({
    title: 'Hapus Mata Pelajaran?',
    html: '<strong>' + subjectName + '</strong> akan dihapus permanen. Penugasan guru terkait juga akan ikut terhapus.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Hapus',
    cancelButtonText: 'Batal',
    customClass: { container: 'swal2-danger' },
  }).then(result => {
    if (result.isConfirmed) {
      fetch('/admin/subjects/' + subjectId, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        }
      })
      .then(r => r.json())
      .then(json => {
        if (json.success) {
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 })
            .then(() => { location.reload(); });
        } else {
          Swal.fire('Gagal', json.message, 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
    }
  });
}

const ICON_SCHOOL = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>';

let _detailSubjectId = null;

function openDetailModal(subjectId) {
  _detailSubjectId = subjectId;
  const modal = document.getElementById('detailModal');
  const body = document.getElementById('detailBody');
  const footer = document.getElementById('detailFooter');
  body.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--muted)">Memuat...</div>';
  footer.style.display = 'flex';
  modal.classList.add('open');

  fetch('/admin/subjects/' + subjectId + '/detail', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(d => {
    document.getElementById('detailTitle').textContent = d.subject.code + ' — ' + d.subject.name;
    let html = '';

    // Header
    html += '<div style="display:flex;align-items:center;gap:14px;margin-bottom:1.5rem;padding:1.25rem;background:var(--bg);border-radius:12px;border:1px solid var(--line)">';
    html += '<span style="font-family:monospace;font-weight:800;font-size:.85rem;padding:8px 16px;border-radius:8px;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)">' + d.subject.code + '</span>';
    html += '<div><strong style="font-size:1.05rem;display:block">' + d.subject.name + '</strong>';
    html += '<span style="font-size:.82rem;color:var(--muted)">KKM ' + d.subject.kkm + ' &middot; ' + d.subject.gurus.length + ' guru pengampu</span></div>';
    html += '</div>';

    // Guru Pengampu
    if (d.subject.gurus.length) {
      html += '<div style="margin-bottom:1.25rem">';
      html += '<h3 style="font-size:.9rem;font-weight:700;margin:0 0 .5rem;color:var(--ink)">Guru Pengampu</h3>';
      html += '<div style="display:flex;flex-wrap:wrap;gap:6px">';
      d.subject.gurus.forEach(g => {
        html += '<span style="padding:5px 12px;border-radius:8px;font-size:.82rem;font-weight:600;background:color-mix(in srgb,var(--primary-2) 8%,var(--card));color:var(--primary-2);border:1px solid color-mix(in srgb,var(--primary-2) 15%,var(--line))">' + g.name + '</span>';
      });
      html += '</div></div>';
    }

    // Kelas per Jurusan (checkbox)
    html += '<h3 style="font-size:.9rem;font-weight:700;margin:0 0 .5rem;color:var(--ink)"><span style="display:inline-flex;vertical-align:middle;margin-right:6px">' + ICON_SCHOOL + '</span>Dipelajari di</h3>';

    if (d.all_kelas.length) {
      html += '<div style="display:flex;flex-direction:column;gap:6px">';
      d.all_kelas.forEach(jg => {
        html += '<div style="border:1px solid var(--line);border-radius:10px;overflow:hidden">';
        html += '<div style="padding:8px 12px;font-size:.82rem;font-weight:700;background:var(--bg);color:var(--ink);border-bottom:1px solid var(--line)">' + jg.jurusan + '</div>';
        html += '<div style="padding:6px 12px">';
        jg.kelas.forEach(k => {
          html += '<label class="subject-check-item" style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:8px;cursor:pointer">';
          html += '<input type="checkbox" class="kelas-assign-cb" value="' + k.id + '" ' + (k.assigned ? 'checked' : '') + ' style="accent-color:#4338ca;width:16px;height:16px">';
          html += '<span style="flex:1;font-weight:600;font-size:.85rem;color:var(--ink)">' + k.nama_lengkap + '</span>';
          if (k.teacher) {
            html += '<span style="font-size:.78rem;color:var(--muted)">' + k.teacher.name + '</span>';
          } else {
            html += '<span style="font-size:.78rem;color:var(--line)">—</span>';
          }
          html += '</label>';
        });
        html += '</div></div>';
      });
      html += '</div>';
    } else {
      html += '<p style="color:var(--muted);font-size:.85rem">Tidak ada kelas tersedia.</p>';
    }

    body.innerHTML = html;
  })
  .catch(() => {
    body.innerHTML = '<div style="text-align:center;padding:2rem;color:#ef4444">Gagal memuat data.</div>';
  });
}

function saveDetailSubjects() {
  const subjectId = _detailSubjectId;
  if (!subjectId) return;

  const kelasIds = [];
  document.querySelectorAll('.kelas-assign-cb:checked').forEach(cb => {
    kelasIds.push(cb.value);
  });

  const formData = new FormData();
  formData.append('_token', CSRF_TOKEN);
  kelasIds.forEach(id => formData.append('kelas_ids[]', id));

  fetch('/admin/subjects/' + subjectId + '/assign', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOKEN,
    }
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 2000 })
        .then(() => location.reload());
    } else {
      Swal.fire('Gagal', d.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
}

function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('open');
  _detailSubjectId = null;
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closeDetailModal(); } });
document.getElementById('subjectModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
document.getElementById('detailModal')?.addEventListener('click', function(e) { if (e.target === this) closeDetailModal(); });

// === Mapel Per Jurusan ===
function toggleJurusanCS(header) {
  const body = header.nextElementSibling;
  const chevron = header.querySelector('.cs-chevron');
  const isOpen = body.style.display !== 'none';
  body.style.display = isOpen ? 'none' : 'block';
  if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
}

function showAddCSForm(btn) {
  const group = btn.closest('.jurusan-cs-group');
  const body = group.querySelector('.jurusan-cs-body');
  const form = group.querySelector('.jurusan-cs-form');
  body.style.display = 'block';
  form.style.display = 'block';
  form.querySelector('.cs-form-kode').focus();
  const chevron = group.querySelector('.cs-chevron');
  if (chevron) chevron.style.transform = 'rotate(90deg)';
}

function cancelCSForm(btn) {
  const form = btn.closest('.jurusan-cs-form');
  form.querySelector('.cs-form-kode').value = '';
  form.querySelector('.cs-form-nama').value = '';
  form.querySelector('.cs-form-kkm').value = '';
  form.style.display = 'none';
}

function saveCS(btn) {
  const group = btn.closest('.jurusan-cs-group');
  const jurusanId = group.dataset.jurusanId;
  const kode = group.querySelector('.cs-form-kode').value.trim();
  const nama = group.querySelector('.cs-form-nama').value.trim();
  const kkm = group.querySelector('.cs-form-kkm').value.trim();

  if (!kode) { Swal.fire('', 'Kode mapel wajib diisi.', 'warning'); return; }
  if (!nama) { Swal.fire('', 'Nama mapel wajib diisi.', 'warning'); return; }

  fetch('/admin/jurusans/' + jurusanId + '/custom-subjects', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' },
    body: JSON.stringify({ kode: kode, nama: nama, kkm: kkm || null })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      group.querySelector('.cs-form-kode').value = '';
      group.querySelector('.cs-form-nama').value = '';
      group.querySelector('.cs-form-kkm').value = '';
      const form = group.querySelector('.jurusan-cs-form');
      form.style.display = 'none';
      const body = group.querySelector('.jurusan-cs-body');
      const empty = body.querySelector('p');
      if (empty && empty.textContent.includes('Belum ada')) empty.remove();
      const kkmText = d.subject.kkm ? ' <span style="font-weight:400;color:var(--muted);font-size:.78rem">(KKM ' + d.subject.kkm + ')</span>' : '';
      const item = document.createElement('div');
      item.className = 'jurusan-cs-item';
      item.dataset.csId = d.subject.id;
      item.innerHTML = '<span style="font-weight:600;font-size:.85rem"><strong>' + (d.subject.kode || '') + '</strong> — ' + d.subject.nama + kkmText + '</span>' +
        '<button type="button" onclick="confirmDeleteCS(' + jurusanId + ', ' + d.subject.id + ', \'' + d.subject.nama.replace(/'/g, "\\'") + '\')" class="btn btn-outline" style="min-height:28px;min-width:28px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" title="Hapus">' +
        '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      body.insertBefore(item, form);
      const header = group.querySelector('.jurusan-cs-header strong');
      const countSpan = group.querySelector('.jurusan-cs-header span[style*="font-weight:400"]');
      const currentCount = parseInt(countSpan.textContent.match(/\d+/)?.[0] || 0);
      countSpan.textContent = '(' + (currentCount + 1) + ' mapel)';
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 2000 });
    } else {
      Swal.fire('Gagal', d.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
}

function confirmDeleteCS(jurusanId, csId, csName) {
  Swal.fire({
    title: 'Hapus?',
    text: 'Pelajaran "' + csName + '" akan dihapus dari jurusan.',
    icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
    confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      fetch('/admin/jurusans/custom-subjects/' + csId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          const item = document.querySelector('.jurusan-cs-item[data-cs-id="' + csId + '"]');
          if (item) item.remove();
          const group = document.querySelector('.jurusan-cs-group[data-jurusan-id="' + jurusanId + '"]');
          if (group) {
            const countSpan = group.querySelector('.jurusan-cs-header span[style*="font-weight:400"]');
            const currentCount = parseInt(countSpan.textContent.match(/\d+/)?.[0] || 0);
            countSpan.textContent = '(' + Math.max(0, currentCount - 1) + ' mapel)';
          }
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 2000 });
        } else {
          Swal.fire('Gagal', d.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
    }
  });
}
</script>
@endpush
@endsection



