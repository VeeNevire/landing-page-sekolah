@extends('layouts.admin')

@section('title', 'Periode Akademik')

@php
$currentStatus = request('status', '');
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen akademik</span>
    <h1>Periode Akademik</h1>
    <p>Kelola tahun ajaran dan semester. Aktifkan periode yang berlaku.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    Tambah Periode
  </button>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.periods.index', array_filter(['search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.periods.index', array_filter(['status' => 'active', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'active' ? 'active' : '' }}">
    Aktif <span class="tab-count">{{ $tabCounts['active'] }}</span>
  </a>
  <a href="{{ route('admin.periods.index', array_filter(['status' => 'inactive', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'inactive' ? 'active' : '' }}">
    Nonaktif <span class="tab-count">{{ $tabCounts['inactive'] }}</span>
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    @if ($currentStatus)
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    @endif
    <div class="field" style="flex:1;min-width:250px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tahun ajaran atau semester...">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search'))
    <a href="{{ route('admin.periods.index', array_filter(['status' => $currentStatus])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="periodsTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Tahun Ajaran</th>
          <th>Semester</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Penugasan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($periods as $period)
        <tr id="row-{{ $period->id }}"
          data-id="{{ $period->id }}"
          data-academic_year="{{ $period->academic_year }}"
          data-semester="{{ $period->semester }}"
          data-start_date="{{ $period->start_date->format('Y-m-d') }}"
          data-end_date="{{ $period->end_date->format('Y-m-d') }}"
          data-is_active="{{ $period->is_active }}">
          <td style="text-align:center">{{ $loop->iteration + ($periods->currentPage() - 1) * $periods->perPage() }}</td>
          <td><strong>{{ $period->academic_year }}</strong></td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2);text-transform:capitalize">{{ $period->semester }}</span>
          </td>
          <td style="font-size:.85rem">{{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}</td>
          <td>
            @if ($period->is_active)
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
            @else
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--muted) 12%,var(--card));color:var(--muted)">Nonaktif</span>
            @endif
          </td>
          <td style="font-size:.85rem">{{ $period->teaching_assignments_count }} penugasan</td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit periode" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $period->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              @if (!$period->is_active)
              <button type="button" class="btn btn-outline" title="Aktifkan periode" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:var(--success)" onclick="confirmActivate({{ $period->id }}, '{{ addslashes($period->academic_year) }}', '{{ $period->semester }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
              </button>
              @endif
              <button type="button" class="btn btn-outline" title="Hapus periode" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $period->id }}, '{{ addslashes($period->academic_year) }}', '{{ $period->semester }}')">
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
          <td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada periode akademik.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $periods->links('vendor.pagination.admin') }}</div>
</section>

{{-- Modal --}}
<div class="admin-modal-overlay" id="periodModal">
  <div class="admin-modal-box" style="max-width:500px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Periode Akademik</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="periodForm" onsubmit="submitForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" id="formPeriodId" value="">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="field">
            <label for="m_academic_year">Tahun Ajaran <span style="color:#ef4444">*</span></label>
            <input id="m_academic_year" name="academic_year" type="text" required placeholder="2026/2027">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label for="m_semester">Semester <span style="color:#ef4444">*</span></label>
            <select id="m_semester" name="semester" required>
              <option value="ganjil">Ganjil</option>
              <option value="genap">Genap</option>
            </select>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="m_start_date">Tanggal Mulai <span style="color:#ef4444">*</span></label>
            <input id="m_start_date" name="start_date" type="date" required>
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label for="m_end_date">Tanggal Selesai <span style="color:#ef4444">*</span></label>
            <input id="m_end_date" name="end_date" type="date" required>
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
    document.getElementById('modalTitle').textContent = 'Tambah Periode Akademik';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formPeriodId').value = '';
    document.getElementById('periodForm').action = '{{ route("admin.periods.store") }}';
    document.getElementById('m_academic_year').value = '';
    document.getElementById('m_semester').value = 'ganjil';
    document.getElementById('m_start_date').value = '';
    document.getElementById('m_end_date').value = '';
    clearErrors();
    document.getElementById('periodModal').classList.add('open');
  }

  function openEditModal(periodId) {
    clearErrors();
    document.getElementById('modalTitle').textContent = 'Edit Periode Akademik';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formPeriodId').value = periodId;
    document.getElementById('periodForm').action = '/admin/periods/' + periodId;

    fetch('/admin/periods/' + periodId + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('m_academic_year').value = data.academic_year;
        document.getElementById('m_semester').value = data.semester;
        document.getElementById('m_start_date').value = data.start_date;
        document.getElementById('m_end_date').value = data.end_date;
        document.getElementById('periodModal').classList.add('open');
      });
  }

  function closeModal() {
    document.getElementById('periodModal').classList.remove('open');
    clearErrors();
  }

  function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
    document.querySelectorAll('.admin-modal-box .field input, .admin-modal-box .field select').forEach(el => el.style.borderColor = '');
  }

  function showFieldError(fieldName, message) {
    const form = document.getElementById('periodForm');
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
    const form = document.getElementById('periodForm');
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

  function confirmActivate(periodId, year, semester) {
    Swal.fire({
      title: 'Aktifkan Periode?',
      html: 'Periode <strong>' + year + ' ' + semester.charAt(0).toUpperCase() + semester.slice(1) + '</strong> akan diaktifkan. Periode aktif sebelumnya akan dinonaktifkan.',
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: '#1f8f62',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Aktifkan',
      cancelButtonText: 'Batal',
    }).then(result => {
      if (result.isConfirmed) {
        doAction('/admin/periods/' + periodId + '/activate', 'PATCH');
      }
    });
  }

  function confirmDelete(periodId, year, semester) {
    Swal.fire({
      title: 'Hapus Periode?',
      html: 'Periode <strong>' + year + ' ' + semester.charAt(0).toUpperCase() + semester.slice(1) + '</strong> akan dihapus permanen.',
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
        doAction('/admin/periods/' + periodId, 'DELETE');
      }
    });
  }

  function doAction(url, method) {
    fetch(url, {
        method: method,
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

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
  document.getElementById('periodModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
</script>
@endpush
@endsection


