@extends('layouts.admin')

@section('title', 'Kelola Jurusan')

@section('content')
@php
$jurusanIcons = [
    'RPL' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
    'DKV' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/></svg>',
    'AKL' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>',
    'default' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="M8 7h8"/><path d="M8 11h6"/></svg>',
];
@endphp
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen akademik</span>
    <h1>Jurusan</h1>
    <p>Kelola jurusan SMA/SMK — tambah, edit, atau nonaktifkan jurusan.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    Tambah Jurusan
  </button>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:1;min-width:250px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode jurusan...">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search'))
      <a href="{{ route('admin.jurusans.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

@if (session('success'))
<div style="padding:14px 18px;border-radius:12px;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success);font-weight:600;margin-bottom:20px;border:1px solid color-mix(in srgb,var(--success) 20%,transparent)">
  {{ session('success') }}
</div>
@endif

<section class="portal-panel">
  <div class="jurusan-grid">
    @forelse ($jurusans as $jurusan)
    <div class="jurusan-card {{ $jurusan->is_active ? '' : 'inactive' }}" id="card-{{ $jurusan->id }}" onclick="openDetailModal({{ $jurusan->id }})" style="cursor:pointer">
      <div class="jurusan-card-header">
        <div style="display:flex;align-items:center;gap:8px">
          <span class="jurusan-icon">{!! $jurusanIcons[$jurusan->kode] ?? $jurusanIcons['default'] !!}</span>
          <span class="jurusan-kode">{{ $jurusan->kode }}</span>
        </div>
        <div class="jurusan-card-actions" onclick="event.stopPropagation()">
          <button type="button" class="jurusan-action-btn" onclick="openEditModal({{ $jurusan->id }})" title="Edit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
              <path d="m15 5 4 4" />
            </svg>
          </button>
          <button type="button" class="jurusan-action-btn" style="color:#ef4444" onclick="confirmDelete({{ $jurusan->id }}, '{{ addslashes($jurusan->nama) }}')" title="Hapus">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 6h18" />
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
            </svg>
          </button>
        </div>
      </div>
      <h3 class="jurusan-nama">{{ $jurusan->nama }}</h3>
      @if ($jurusan->deskripsi)
      <p class="jurusan-deskripsi">{{ $jurusan->deskripsi }}</p>
      @endif
      <div class="jurusan-stats">
        <span class="jurusan-badge {{ $jurusan->is_active ? 'badge-active' : 'badge-inactive' }}">
          {{ $jurusan->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
        <span style="font-size:.78rem;color:var(--muted)">{{ $jurusan->kelas_count }} kelas</span>
      </div>
    </div>
    @empty
    <div class="jurusan-empty">
      <div style="font-size:2.5rem;margin-bottom:.5rem">🏫</div>
      <h3 style="margin:0 0 .5rem;color:var(--ink)">Belum ada jurusan</h3>
      <p style="color:var(--muted);margin:0">Klik "Tambah Jurusan" untuk membuat jurusan baru.</p>
    </div>
    @endforelse

    <button type="button" class="jurusan-card jurusan-card-add" onclick="openCreateModal()">
      <div class="jurusan-add-icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
      </div>
      <span style="font-weight:700;font-size:.95rem;color:var(--muted)">Tambah Jurusan</span>
    </button>
  </div>

  <div style="margin-top:1.5rem">{{ $jurusans->links('vendor.pagination.admin') }}</div>
</section>

{{-- Modal --}}
<div class="admin-modal-overlay" id="jurusanModal">
  <div class="admin-modal-box" style="max-width:580px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Jurusan</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="jurusanForm" onsubmit="submitForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" id="formJurusanId" value="">

        <div style="display:grid;grid-template-columns:1fr 2fr;gap:14px">
          <div class="field">
            <label for="m_kode">Kode <span style="color:#ef4444">*</span></label>
            <input id="m_kode" name="kode" type="text" required placeholder="RPL" style="text-transform:uppercase">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label for="m_nama">Nama Jurusan <span style="color:#ef4444">*</span></label>
            <input id="m_nama" name="nama" type="text" required placeholder="Rekayasa Perangkat Lunak">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_deskripsi">Deskripsi</label>
          <textarea id="m_deskripsi" name="deskripsi" rows="2" placeholder="Deskripsi singkat tentang jurusan ini" style="width:100%;padding:.7rem .9rem;border-radius:.75rem;font-size:.9rem;outline:none;transition:border-color .2s;background:var(--card);border:1.5px solid var(--line);color:var(--ink);font-family:inherit;resize:vertical"></textarea>
        </div>

        <div style="margin-top:14px;margin-bottom:6px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1" checked id="m_is_active" style="width:18px;height:18px;accent-color:#4338ca">
            <span style="font-weight:600;color:var(--ink)">Jurusan aktif</span>
          </label>
        </div>

        {{-- Sub Kelas --}}
        <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1.5px solid var(--line)">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <h3 style="margin:0;font-size:1rem;font-weight:700;color:var(--ink)">Sub Kelas</h3>
            <button type="button" class="btn btn-outline" onclick="addKelasRow()" style="min-height:34px;padding:0 14px;font-size:.82rem;display:inline-flex;align-items:center;gap:5px">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
              </svg>
              Tambah Kelas
            </button>
          </div>

          <div id="kelasRows" style="display:flex;flex-direction:column;gap:8px">
            {{-- Kelas rows inserted here by JS --}}
          </div>

          <div id="kelasEmpty" style="text-align:center;padding:1.5rem 1rem;background:var(--bg);border-radius:12px;border:1.5px dashed var(--line)">
            <p style="margin:0;font-size:.85rem;color:var(--muted)">Belum ada kelas. Klik "Tambah Kelas" untuk menambahkan.</p>
          </div>
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
  <div class="admin-modal-box" style="max-width:720px">
    <div class="admin-modal-header">
      <h2 id="detailTitle">Detail Jurusan</h2>
      <button class="admin-modal-close" onclick="closeDetailModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
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
@endsection

@push('scripts')
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const ROMAWI = {10: 'X', 11: 'XI', 12: 'XII'};
let kelasRowCounter = 0;

function previewKelasNama(tingkat, jurusanNama, nomor) {
  if (tingkat && jurusanNama && nomor) {
    return ROMAWI[tingkat] + ' ' + jurusanNama + ' ' + nomor;
  }
  return '';
}

function addKelasRow(data) {
  data = data || {tingkat: '', nomor: '', nama: ''};
  const id = ++kelasRowCounter;
  const jurusanNama = document.getElementById('m_nama').value.trim();

  const row = document.createElement('div');
  row.className = 'kelas-row';
  row.id = 'kelasRow-' + id;
  row.dataset.nama = data.nama;

  row.innerHTML = `
    <div class="kelas-row-inner">
      <div class="kelas-row-field" style="flex:1">
        <select class="kelas-input kelas-tingkat" required onchange="updateKelasPreview(this)">
          <option value="">Tingkat</option>
          <option value="10" ${data.tingkat === '10' ? 'selected' : ''}>X</option>
          <option value="11" ${data.tingkat === '11' ? 'selected' : ''}>XI</option>
          <option value="12" ${data.tingkat === '12' ? 'selected' : ''}>XII</option>
        </select>
      </div>
      <div class="kelas-row-field" style="flex:0 0 70px">
        <input type="number" class="kelas-input kelas-nomor" min="1" max="20" required placeholder="No" value="${data.nomor}" oninput="updateKelasPreview(this)">
      </div>
      <div class="kelas-row-preview" id="kelasPreview-${id}">
        ${previewKelasNama(data.tingkat, jurusanNama, data.nomor) || '—'}
      </div>
      <input type="hidden" class="kelas-nama-hidden" value="${data.nama}">
      <input type="hidden" class="kelas-id-hidden" value="${data.id || ''}">
      <button type="button" class="kelas-row-remove" onclick="removeKelasRow(this)" title="Hapus kelas">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
  `;

  document.getElementById('kelasRows').appendChild(row);
  toggleKelasEmpty();
}

function updateKelasPreview(el) {
  const row = el.closest('.kelas-row');
  const tingkat = row.querySelector('.kelas-tingkat').value;
  const nomor = row.querySelector('.kelas-nomor').value;
  const jurusanNama = document.getElementById('m_nama').value.trim();
  const preview = row.querySelector('.kelas-row-preview');
  const hidden = row.querySelector('.kelas-nama-hidden');

  const nama = jurusanNama + ' ' + nomor;
  preview.textContent = previewKelasNama(tingkat, jurusanNama, nomor) || '—';
  hidden.value = nama.trim() ? nama : '';
}

function removeKelasRow(btn) {
  btn.closest('.kelas-row').remove();
  toggleKelasEmpty();
}

function toggleKelasEmpty() {
  const rows = document.getElementById('kelasRows');
  const empty = document.getElementById('kelasEmpty');
  empty.style.display = rows.children.length === 0 ? 'block' : 'none';
}

function openCreateModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Jurusan';
  document.getElementById('modalSubmitBtn').textContent = 'Simpan';
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('formJurusanId').value = '';
  document.getElementById('jurusanForm').action = '{{ route("admin.jurusans.store") }}';
  document.getElementById('m_kode').value = '';
  document.getElementById('m_nama').value = '';
  document.getElementById('m_deskripsi').value = '';
  document.getElementById('m_is_active').checked = true;
  document.getElementById('kelasRows').innerHTML = '';
  toggleKelasEmpty();
  clearErrors();
  document.getElementById('jurusanModal').classList.add('open');
  document.getElementById('m_kode').focus();
}

function openEditModal(jurusanId) {
  clearErrors();
  document.getElementById('modalTitle').textContent = 'Edit Jurusan';
  document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('formJurusanId').value = jurusanId;
  document.getElementById('jurusanForm').action = '/admin/jurusans/' + jurusanId;
  document.getElementById('kelasRows').innerHTML = '';

  fetch('/admin/jurusans/' + jurusanId + '/data', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('m_kode').value = data.kode;
    document.getElementById('m_nama').value = data.nama;
    document.getElementById('m_deskripsi').value = data.deskripsi || '';
    document.getElementById('m_is_active').checked = data.is_active;

    if (data.kelas && data.kelas.length) {
      data.kelas.forEach(k => {
        addKelasRow({
          id: k.id,
          tingkat: String(k.tingkat),
          nomor: k.nama.split(' ').pop() || '',
          nama: k.nama
        });
      });
    }
    toggleKelasEmpty();
    document.getElementById('jurusanModal').classList.add('open');
  });
}

function closeModal() {
  document.getElementById('jurusanModal').classList.remove('open');
  clearErrors();
}

function clearErrors() {
  document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
  document.querySelectorAll('.admin-modal-box .field input, .admin-modal-box .field textarea').forEach(el => el.style.borderColor = '');
}

function showFieldError(fieldName, message) {
  const form = document.getElementById('jurusanForm');
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

  const form = document.getElementById('jurusanForm');
  const formData = new FormData(form);

  if (!document.getElementById('m_is_active').checked) {
    formData.set('is_active', '0');
  }

  const rows = document.querySelectorAll('.kelas-row');
  if (rows.length > 0) {
    rows.forEach((row, i) => {
      const tingkat = row.querySelector('.kelas-tingkat')?.value || '';
      const nomor = row.querySelector('.kelas-nomor')?.value || '';
      const nama = row.querySelector('.kelas-nama-hidden')?.value || '';
      const id = row.querySelector('.kelas-id-hidden')?.value || '';

      formData.append('kelas[' + i + '][tingkat]', tingkat);
      formData.append('kelas[' + i + '][nomor]', nomor);
      formData.append('kelas[' + i + '][nama]', nama);
      formData.append('kelas[' + i + '][id]', id);
      formData.append('kelas[' + i + '][is_active]', '1');
    });
  }

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

function confirmDelete(jurusanId, jurusanName) {
  Swal.fire({
    title: 'Hapus Jurusan?',
    html: '<strong>' + jurusanName + '</strong> akan dihapus permanen. Semua kelas di dalamnya juga akan ikut terhapus.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Hapus',
    cancelButtonText: 'Batal',
    customClass: { container: 'swal2-danger' },
  }).then(result => {
    if (result.isConfirmed) {
      fetch('/admin/jurusans/' + jurusanId, {
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

document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closeDetailModal(); } });
document.getElementById('jurusanModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
document.getElementById('detailModal').addEventListener('click', function(e) { if (e.target === this) closeDetailModal(); });

function openDetailModal(jurusanId) {
  const modal = document.getElementById('detailModal');
  const body = document.getElementById('detailBody');
  const footer = document.getElementById('detailFooter');
  body.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--muted)">Memuat...</div>';
  footer.style.display = 'none';
  modal.classList.add('open');

  fetch('/admin/jurusans/' + jurusanId + '/detail', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    const j = data.jurusan;
    document.getElementById('detailTitle').textContent = j.nama;
    let html = '';

    // info jurusan
    html += '<div style="display:flex;align-items:center;gap:12px;margin-bottom:1.5rem;padding:1.25rem;background:var(--bg);border-radius:12px;border:1px solid var(--line)">';
    html += '<span style="font-family:monospace;font-weight:800;font-size:.85rem;padding:6px 14px;border-radius:8px;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)">' + j.kode + '</span>';
    html += '<div><strong style="font-size:1.1rem">' + j.nama + '</strong>';
    if (j.deskripsi) html += '<p style="margin:4px 0 0;font-size:.85rem;color:var(--muted)">' + j.deskripsi + '</p>';
    html += '</div>';
    html += '<span class="jurusan-badge ' + (j.is_active ? 'badge-active' : 'badge-inactive') + '" style="margin-left:auto">' + (j.is_active ? 'Aktif' : 'Nonaktif') + '</span>';
    html += '</div>';

    // Pelajaran Jurusan (custom)
    html += '<div style="margin-bottom:1.5rem">';
    html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">';
    html += '<h3 style="font-size:.95rem;font-weight:700;margin:0;color:var(--ink)">Pelajaran Jurusan</h3>';
    html += '<button type="button" class="btn btn-outline" onclick="showAddCustomSubject()" style="min-height:32px;padding:0 12px;font-size:.8rem;display:inline-flex;align-items:center;gap:4px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Tambah</button>';
    html += '</div>';
    html += '<div id="customSubjectForm" style="display:none;margin-bottom:.75rem;padding:.75rem;background:var(--bg);border-radius:10px;border:1px solid var(--line)">';
    html += '<div class="field" style="margin-bottom:8px"><input id="csNama" type="text" placeholder="Nama mata pelajaran" class="kelas-input" style="padding:.55rem .65rem"></div>';
    html += '<div class="field" style="margin-bottom:8px"><input id="csDeskripsi" type="text" placeholder="Deskripsi (opsional)" class="kelas-input" style="padding:.55rem .65rem"></div>';
    html += '<button type="button" class="btn btn-primary" onclick="saveCustomSubject(' + jurusanId + ')" style="min-height:34px;padding:0 14px;font-size:.82rem">Simpan</button>';
    html += '</div>';
    html += '<div id="customSubjectList" class="detail-subject-grid" style="max-height:none">';
    if (j.custom_subjects && j.custom_subjects.length) {
      j.custom_subjects.forEach(cs => {
        html += '<div class="custom-subject-item" data-id="' + cs.id + '"><span>' + cs.nama + '</span><button type="button" onclick="deleteCustomSubject(' + jurusanId + ',' + cs.id + ')" class="cs-delete-btn" title="Hapus"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>';
      });
    } else {
      html += '<p style="color:var(--muted);font-size:.85rem;padding:.5rem 0">Belum ada pelajaran jurusan.</p>';
    }
    html += '</div></div>';

    // kelas
    html += '<h3 style="font-size:.95rem;font-weight:700;margin:0 0 .75rem;color:var(--ink)">Sub Kelas <span class="kelas-group-count" style="font-size:.78rem">' + (j.kelas ? j.kelas.length : 0) + ' kelas</span></h3>';
    if (j.kelas && j.kelas.length) {
      html += '<div style="display:flex;flex-direction:column;gap:10px">';
      j.kelas.forEach(k => {
        const namaLengkap = (ROMAWI[k.tingkat] || k.tingkat) + ' ' + k.nama;
        const totalMapel = (k.subjects ? k.subjects.length : 0) + (k.custom_subjects ? k.custom_subjects.length : 0);
        html += '<div class="detail-kelas-item" data-kelas-id="' + k.id + '"><div class="detail-kelas-header" onclick="toggleKelasSubjects(this)"><div style="display:flex;align-items:center;gap:8px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="kelas-chevron"><polyline points="9 18 15 12 9 6"/></svg><strong>' + namaLengkap + '</strong></div><span class="kelas-group-count" style="font-size:.75rem">' + totalMapel + ' mapel</span></div>';
        html += '<div class="detail-kelas-subjects" style="display:none">';

        // Wali Kelas
        const teacherId = k.homeroom_teacher ? k.homeroom_teacher.id : '';
        html += '<div style="margin-bottom:.75rem;display:flex;align-items:center;gap:8px">';
        html += '<label style="font-size:.82rem;font-weight:700;color:var(--muted);white-space:nowrap">Wali Kelas</label>';
        html += '<select class="kelas-wali-select" data-kelas-id="' + k.id + '" style="flex:1;padding:.5rem .65rem;border-radius:8px;font-size:.82rem;outline:none;background:var(--card);border:1.5px solid var(--line);color:var(--ink);font-family:inherit">';
        html += '<option value="">— Pilih —</option>';
        if (data.teachers) {
          data.teachers.forEach(t => {
            const full = t.full_name || t.name;
            html += '<option value="' + t.id + '" ' + (teacherId == t.id ? 'selected' : '') + '>' + full + '</option>';
          });
        }
        html += '</select></div>';

        // Pelajaran Umum
        if (data.allSubjects.length) {
          html += '<p style="font-size:.78rem;font-weight:700;color:var(--muted);margin:0 0 .5rem">Pelajaran Umum</p>';
          html += '<div class="detail-subject-grid" style="margin-bottom:.75rem">';
          data.allSubjects.forEach(s => {
            const checked = k.subjects && k.subjects.some(ks => ks.id === s.id);
            html += '<label class="subject-check-item"><input type="checkbox" class="kelas-subject-cb" data-kelas-id="' + k.id + '" value="' + s.id + '" ' + (checked ? 'checked' : '') + '><span>' + s.code + ' — ' + s.name + '</span></label>';
          });
          html += '</div>';
        }

        // Pelajaran Jurusan (custom) per kelas
        if (j.custom_subjects && j.custom_subjects.length) {
          html += '<p style="font-size:.78rem;font-weight:700;color:var(--muted);margin:0 0 .5rem">Pelajaran Jurusan</p>';
          html += '<div class="detail-subject-grid">';
          j.custom_subjects.forEach(cs => {
            const checked = k.custom_subjects && k.custom_subjects.some(kcs => kcs.id === cs.id);
            html += '<label class="subject-check-item"><input type="checkbox" class="kelas-custom-cb" data-kelas-id="' + k.id + '" value="' + cs.id + '" ' + (checked ? 'checked' : '') + '><span>' + cs.nama + '</span></label>';
          });
          html += '</div>';
        }

        html += '</div></div>';
      });
      html += '</div>';
    } else {
      html += '<p style="color:var(--muted);font-size:.85rem">Belum ada kelas. Tambah kelas melalui edit jurusan.</p>';
    }

    body.innerHTML = html;
    footer.style.display = 'flex';
    window._currentJurusanId = jurusanId;
  })
  .catch(() => {
    body.innerHTML = '<div style="text-align:center;padding:2rem;color:#ef4444">Gagal memuat data.</div>';
  });
}

function showAddCustomSubject() {
  const form = document.getElementById('customSubjectForm');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
  if (form.style.display === 'block') document.getElementById('csNama').focus();
}

function saveCustomSubject(jurusanId) {
  const nama = document.getElementById('csNama').value.trim();
  if (!nama) { Swal.fire('', 'Nama mata pelajaran wajib diisi.', 'warning'); return; }
  const deskripsi = document.getElementById('csDeskripsi').value.trim();

  fetch('/admin/jurusans/' + jurusanId + '/custom-subjects', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' },
    body: JSON.stringify({ nama: nama, deskripsi: deskripsi })
  })
  .then(r => r.json())
  .then(json => {
    if (json.success) {
      document.getElementById('csNama').value = '';
      document.getElementById('csDeskripsi').value = '';
      document.getElementById('customSubjectForm').style.display = 'none';
      const list = document.getElementById('customSubjectList');
      const empty = list.querySelector('p');
      if (empty) empty.remove();
      list.insertAdjacentHTML('beforeend', '<div class="custom-subject-item" data-id="' + json.subject.id + '"><span>' + json.subject.nama + '</span><button type="button" onclick="deleteCustomSubject(' + jurusanId + ',' + json.subject.id + ')" class="cs-delete-btn"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button></div>');

      // inject checkbox ke setiap kelas
      document.querySelectorAll('.detail-kelas-item').forEach(item => {
        const body = item.querySelector('.detail-kelas-subjects');
        if (!body) return;

        let grid = body.querySelectorAll('.detail-subject-grid');
        let csGrid;

        if (grid.length > 1) {
          csGrid = grid[1];
        } else {
          // buat section Pelajaran Jurusan kalo belum ada
          const label = document.createElement('p');
          label.style.cssText = 'font-size:.78rem;font-weight:700;color:var(--muted);margin:1rem 0 .5rem';
          label.textContent = 'Pelajaran Jurusan';
          csGrid = document.createElement('div');
          csGrid.className = 'detail-subject-grid';
          body.appendChild(label);
          body.appendChild(csGrid);
        }

        csGrid.insertAdjacentHTML('beforeend',
          '<label class="subject-check-item">' +
          '<input type="checkbox" class="kelas-custom-cb" data-kelas-id="' + item.dataset.kelasId + '" value="' + json.subject.id + '">' +
          '<span>' + json.subject.nama + '</span></label>'
        );
      });

      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 2000 });
    } else {
      Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
}

function deleteCustomSubject(jurusanId, subjectId) {
  Swal.fire({
    title: 'Hapus?',
    text: 'Pelajaran ini akan dihapus dari jurusan.',
    icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
    confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      fetch('/admin/jurusans/custom-subjects/' + subjectId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
      })
      .then(r => r.json())
      .then(json => {
        if (json.success) {
          document.querySelector('.custom-subject-item[data-id="' + subjectId + '"]')?.remove();
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 2000 });
        } else {
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
    }
  });
}

function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('open');
  window._currentJurusanId = null;
}

function toggleKelasSubjects(header) {
  const content = header.nextElementSibling;
  const chevron = header.querySelector('.kelas-chevron');
  const isOpen = content.style.display !== 'none';
  content.style.display = isOpen ? 'none' : 'block';
  if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
}

function saveDetailSubjects() {
  const jurusanId = window._currentJurusanId;
  if (!jurusanId) return;

  const kelasSubjects = {};
  document.querySelectorAll('.kelas-subject-cb').forEach(cb => {
    const kid = cb.dataset.kelasId;
    if (!kelasSubjects[kid]) kelasSubjects[kid] = [];
    if (cb.checked) kelasSubjects[kid].push(cb.value);
  });

  const kelasCustomSubjects = {};
  document.querySelectorAll('.kelas-custom-cb').forEach(cb => {
    const kid = cb.dataset.kelasId;
    if (!kelasCustomSubjects[kid]) kelasCustomSubjects[kid] = [];
    if (cb.checked) kelasCustomSubjects[kid].push(cb.value);
  });

  const formData = new FormData();
  formData.append('_token', CSRF_TOKEN);
  Object.entries(kelasSubjects).forEach(([kelasId, ids]) => {
    ids.forEach(id => formData.append('kelas_subjects[' + kelasId + '][]', id));
  });
  Object.entries(kelasCustomSubjects).forEach(([kelasId, ids]) => {
    ids.forEach(id => formData.append('kelas_custom_subjects[' + kelasId + '][]', id));
  });

  document.querySelectorAll('.kelas-wali-select').forEach(sel => {
    formData.append('homeroom_teachers[' + sel.dataset.kelasId + ']', sel.value);
  });

  fetch('/admin/jurusans/' + jurusanId + '/subjects', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': CSRF_TOKEN,
    }
  })
  .then(r => {
    if (!r.ok) { console.error('HTTP error:', r.status, r.statusText); return r.text().then(t => { console.error('Response body:', t); throw new Error(t); }); }
    return r.json().then(json => ({ ok: true, json }));
  })
  .then(({ ok, json }) => {
    if (ok && json.success) {
      Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 2000 });
    } else {
      Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
    }
  })
  .catch((err) => { console.error('Save error:', err); Swal.fire('Error', 'Tidak dapat terhubung ke server: ' + (err.message || ''), 'error'); });
}
</script>
@endpush



