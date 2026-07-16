@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@php
$currentRole = request('role', '');
$roleColors = ['admin' => '#4338ca', 'teacher' => '#0369a1', 'homeroom' => '#0d9488', 'parent' => '#b45309', 'principal' => '#7c3aed'];
$roleLabels = ['admin' => 'Admin', 'teacher' => 'Guru', 'homeroom' => 'Wali Kelas', 'parent' => 'Orang Tua', 'principal' => 'Kepsek'];
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen pengguna</span>
    <h1>Kelola Pengguna</h1>
    <p>Kelola akun guru, orang tua, administrator, dan wali kelas.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    Tambah Pengguna
  </button>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.users.index', array_filter(['search' => request('search')])) }}"
    class="tab-btn {{ $currentRole === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.users.index', array_filter(['role' => 'parent', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentRole === 'parent' ? 'active' : '' }}">
    Orang Tua <span class="tab-count">{{ $tabCounts['parent'] }}</span>
  </a>
  <a href="{{ route('admin.users.index', array_filter(['role' => 'teacher,homeroom,principal', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentRole === 'teacher,homeroom,principal' ? 'active' : '' }}">
    Guru <span class="tab-count">{{ $tabCounts['guru'] }}</span>
  </a>
  <a href="{{ route('admin.users.index', array_filter(['role' => 'admin', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentRole === 'admin' ? 'active' : '' }}">
    Admin <span class="tab-count">{{ $tabCounts['admin'] }}</span>
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    @if ($currentRole)
    <input type="hidden" name="role" value="{{ $currentRole }}">
    @endif
    <div class="field" style="flex:1;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email...">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search'))
    <a href="{{ route('admin.users.index', array_filter(['role' => $currentRole])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="usersTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Pengguna</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Login Terakhir</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
        <tr id="row-{{ $user->id }}"
          data-id="{{ $user->id }}"
          data-name="{{ $user->name }}"
          data-full_name="{{ $user->full_name }}"
          data-email="{{ $user->email }}"
          data-role="{{ $user->role }}"
          data-is_active="{{ $user->is_active }}">
          <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <span style="width:36px;height:36px;border-radius:10px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.82rem;flex-shrink:0">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
              <div>
                <strong style="display:block;font-size:.92rem">{{ $user->full_name ?: $user->name }}</strong>
                <span style="font-size:.8rem;color:var(--muted)">{{ $user->name }}</span>
              </div>
            </div>
          </td>
          <td>{{ $user->email }}</td>
          <td>
            <span class="role-badge" style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $roleColors[$user->role] ?? '#666' }} 12%,var(--card));color:{{ $roleColors[$user->role] ?? '#666' }}">{{ $roleLabels[$user->role] ?? $user->role }}</span>
          </td>
          <td class="status-cell">
            @if ($user->is_active)
            <span class="status-badge" style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
            @else
            <span class="status-badge" style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444">Nonaktif</span>
            @endif
          </td>
          <td style="font-size:.85rem;color:var(--muted)">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }}</td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit pengguna" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $user->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="{{ $user->is_active ? 'Nonaktifkan akun' : 'Aktifkan akun' }}" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:{{ $user->is_active ? '#ef4444' : 'var(--success)' }}" onclick="confirmToggle({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->is_active ? 'true' : 'false' }})">
                @if($user->is_active)
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <line x1="17" y1="8" x2="22" y2="13" />
                  <line x1="22" y1="8" x2="17" y2="13" />
                </svg>
                @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <polyline points="16 11 18 13 22 9" />
                </svg>
                @endif
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada pengguna ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $users->links() }}</div>
</section>

{{-- Modal Overlay --}}
<div class="admin-modal-overlay" id="userModal">
  <div class="admin-modal-box">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Pengguna</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="userForm" onsubmit="submitForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" id="formUserId" value="">

        <div class="field">
          <label for="modal_name">Nama Panggilan <span style="color:#ef4444">*</span></label>
          <input id="modal_name" name="name" type="text" required placeholder="Nama tampilan">
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="modal_full_name">Nama Lengkap</label>
          <input id="modal_full_name" name="full_name" type="text" placeholder="Nama lengkap (opsional)">
        </div>

        <div class="field" style="margin-top:14px">
          <label for="modal_email">Email <span style="color:#ef4444">*</span></label>
          <input id="modal_email" name="email" type="email" required placeholder="email@contoh.com">
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="modal_role">Role <span style="color:#ef4444">*</span></label>
          <select id="modal_role" name="role" required>
            <option value="parent">Orang Tua</option>
            <option value="teacher">Guru</option>
            <option value="homeroom">Wali Kelas</option>
            <option value="admin">Admin</option>
            <option value="principal">Kepala Sekolah</option>
          </select>
        </div>

        <div id="passwordFields">
          <div class="field" style="margin-top:14px">
            <label for="modal_password">Password <span style="color:#ef4444" id="pwdRequiredMark">*</span></label>
            <input id="modal_password" name="password" type="password" placeholder="Minimal 6 karakter">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>

          <div class="field" style="margin-top:14px">
            <label for="modal_password_confirmation">Konfirmasi Password <span style="color:#ef4444" id="pwdConfirmRequiredMark">*</span></label>
            <input id="modal_password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password">
          </div>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Buat Pengguna</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const ROLE_COLORS = @json($roleColors);
  const ROLE_LABELS = @json($roleLabels);
  const CURRENT_ROLE = '{{ $currentRole }}';

  function getDefaultRole() {
    if (CURRENT_ROLE === 'parent') return 'parent';
    if (CURRENT_ROLE === 'admin') return 'admin';
    if (CURRENT_ROLE === 'teacher,homeroom,principal') return 'teacher';
    return 'parent';
  }

  function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
    document.getElementById('modalSubmitBtn').textContent = 'Buat Pengguna';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formUserId').value = '';
    document.getElementById('userForm').action = '{{ route("admin.users.store") }}';
    document.getElementById('passwordFields').style.display = 'block';
    document.getElementById('pwdRequiredMark').style.display = 'inline';
    document.getElementById('pwdConfirmRequiredMark').style.display = 'inline';
    document.getElementById('modal_password').placeholder = 'Minimal 6 karakter';
    document.getElementById('modal_password').setAttribute('required', '');
    document.getElementById('modal_password_confirmation').setAttribute('required', '');
    document.getElementById('modal_name').value = '';
    document.getElementById('modal_full_name').value = '';
    document.getElementById('modal_email').value = '';
    document.getElementById('modal_role').value = getDefaultRole();
    document.getElementById('modal_password').value = '';
    document.getElementById('modal_password_confirmation').value = '';
    clearErrors();
    document.getElementById('userModal').classList.add('open');
  }

  function openEditModal(userId) {
    clearErrors();
    document.getElementById('modalTitle').textContent = 'Edit Pengguna';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formUserId').value = userId;
    document.getElementById('userForm').action = '/admin/users/' + userId;
    document.getElementById('passwordFields').style.display = 'block';
    document.getElementById('pwdRequiredMark').style.display = 'none';
    document.getElementById('pwdConfirmRequiredMark').style.display = 'none';
    document.getElementById('modal_password').placeholder = 'Kosongkan jika tidak diganti';
    document.getElementById('modal_password').removeAttribute('required');
    document.getElementById('modal_password_confirmation').removeAttribute('required');
    document.getElementById('modal_password').value = '';
    document.getElementById('modal_password_confirmation').value = '';

    fetch('/admin/users/' + userId + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modal_name').value = data.name;
        document.getElementById('modal_full_name').value = data.full_name || '';
        document.getElementById('modal_email').value = data.email;
        document.getElementById('modal_role').value = data.role;
        document.getElementById('userModal').classList.add('open');
      });
  }

  function closeModal() {
    document.getElementById('userModal').classList.remove('open');
    clearErrors();
  }

  function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
    document.querySelectorAll('.field input, .field select').forEach(el => el.style.borderColor = '');
  }

  function showFieldError(fieldName, message) {
    const form = document.getElementById('userForm');
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
    const form = document.getElementById('userForm');
    const formData = new FormData(form);
    const isEdit = document.getElementById('formUserId').value !== '';

    const pwd = document.getElementById('modal_password').value;
    if (isEdit && !pwd) {
      formData.delete('password');
      formData.delete('password_confirmation');
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

  function confirmToggle(userId, userName, isActive) {
    const action = isActive ? 'Menonaktifkan' : 'Mengaktifkan';
    const confirmText = isActive ? 'Nonaktifkan' : 'Aktifkan';

    Swal.fire({
      title: action + ' Akun?',
      html: 'Akun <strong>' + userName + '</strong> akan ' + (isActive ? 'dinonaktifkan' : 'diaktifkan') + '.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: isActive ? '#ef4444' : '#1f8f62',
      cancelButtonColor: '#6b7280',
      confirmButtonText: confirmText,
      cancelButtonText: 'Batal',
      customClass: {
        container: isActive ? 'swal2-danger' : ''
      },
    }).then(result => {
      if (result.isConfirmed) {
        doToggle(userId);
      }
    });
  }

  function doToggle(userId) {
    fetch('/admin/users/' + userId + '/toggle', {
        method: 'PATCH',
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
            timer: 3000,
            timerProgressBar: true,
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire('Gagal', json.message, 'error');
        }
      })
      .catch(() => {
        Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
      });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
  });

  document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
</script>
@endpush
@endsection