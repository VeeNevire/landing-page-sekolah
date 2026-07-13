@extends('layouts.admin')

@section('title', 'Kelola Mata Pelajaran')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen akademik</span>
    <h1>Mata Pelajaran</h1>
    <p>Kelola daftar mata pelajaran dan nilai KKM.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
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
          data-kkm="{{ $subject->kkm }}">
          <td style="text-align:center">{{ $loop->iteration + ($subjects->currentPage() - 1) * $subjects->perPage() }}</td>
          <td style="font-family:monospace;font-weight:700">{{ $subject->code }}</td>
          <td>{{ $subject->name }}</td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.82rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $subject->kkm }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit mata pelajaran" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $subject->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus mata pelajaran" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $subject->id }}, '{{ addslashes($subject->name) }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 6h18" />
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                </svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" style="text-align:center;padding:30px;color:var(--muted)">Belum ada mata pelajaran.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $subjects->links() }}</div>
</section>

{{-- Modal --}}
<div class="admin-modal-overlay" id="subjectModal">
  <div class="admin-modal-box" style="max-width:460px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Mata Pelajaran</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
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
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Simpan</button>
      </div>
    </form>
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
    clearErrors();
    document.getElementById('subjectModal').classList.add('open');
  }

  function openEditModal(subjectId) {
    clearErrors();
    document.getElementById('modalTitle').textContent = 'Edit Mata Pelajaran';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formSubjectId').value = subjectId;
    document.getElementById('subjectForm').action = '/admin/subjects/' + subjectId;

    fetch('/admin/subjects/' + subjectId + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('m_code').value = data.code;
        document.getElementById('m_name').value = data.name;
        document.getElementById('m_kkm').value = data.kkm;
        document.getElementById('subjectModal').classList.add('open');
      });
  }

  function closeModal() {
    document.getElementById('subjectModal').classList.remove('open');
    clearErrors();
  }

  function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
    document.querySelectorAll('.admin-modal-box .field input').forEach(el => el.style.borderColor = '');
  }

  function showFieldError(fieldName, message) {
    const form = document.getElementById('subjectForm');
    const input = form.querySelector('[name="' + fieldName + '"]');
    if (input) {
      input.style.borderColor = '#ef4444';
      const errorEl = input.closest('.field').querySelector('.field-error');
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.style.display = 'block';
      }
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
      .then(r => r.json().then(json => ({
        ok: r.ok,
        json
      })))
      .then(({
        ok,
        json
      }) => {
        if (ok && json.success) {
          closeModal();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: json.message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
          }).then(() => {
            location.reload();
          });
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
      customClass: {
        container: 'swal2-danger'
      },
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
              Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: json.message,
                  showConfirmButton: false,
                  timer: 3000
                })
                .then(() => {
                  location.reload();
                });
            } else {
              Swal.fire('Gagal', json.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
      }
    });
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
  document.getElementById('subjectModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
</script>
@endpush
@endsection