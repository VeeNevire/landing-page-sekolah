@extends('layouts.admin')

@section('title', 'Kelola Siswa')

@php
$currentStatus = request('status', '');
$statusColors = ['active' => 'var(--success)', 'graduated' => 'var(--primary-2)', 'inactive' => '#ef4444'];
$statusLabels = ['active' => 'Aktif', 'graduated' => 'Lulus', 'inactive' => 'Nonaktif'];
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen siswa</span>
    <h1>Kelola Siswa</h1>
    <p>Kelola data siswa, hubungkan ke akun orang tua, dan import data.</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('admin.students.import') }}" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="17 8 12 3 7 8" />
        <line x1="12" y1="3" x2="12" y2="15" />
      </svg>
      Import CSV
    </a>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      Tambah Siswa
    </button>
  </div>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.students.index', array_filter(['search' => request('search'), 'class' => request('class')])) }}"
    class="tab-btn {{ $currentStatus === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.students.index', array_filter(['status' => 'active', 'search' => request('search'), 'class' => request('class')])) }}"
    class="tab-btn {{ $currentStatus === 'active' ? 'active' : '' }}">
    Aktif <span class="tab-count">{{ $tabCounts['active'] }}</span>
  </a>
  <a href="{{ route('admin.students.index', array_filter(['status' => 'graduated', 'search' => request('search'), 'class' => request('class')])) }}"
    class="tab-btn {{ $currentStatus === 'graduated' ? 'active' : '' }}">
    Lulus <span class="tab-count">{{ $tabCounts['graduated'] }}</span>
  </a>
  <a href="{{ route('admin.students.index', array_filter(['status' => 'inactive', 'search' => request('search'), 'class' => request('class')])) }}"
    class="tab-btn {{ $currentStatus === 'inactive' ? 'active' : '' }}">
    Nonaktif <span class="tab-count">{{ $tabCounts['inactive'] }}</span>
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    @if ($currentStatus)
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    @endif
    <div class="field" style="flex:2;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NISN...">
    </div>
    <div class="field" style="flex:1;min-width:150px">
      <select name="class">
        <option value="">Semua Kelas</option>
        @foreach ($classNames as $class)
        <option value="{{ $class }}" {{ request('class') === $class ? 'selected' : '' }}>{{ $class }}</option>
        @endforeach
      </select>
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search') || request('class'))
    <a href="{{ route('admin.students.index', array_filter(['status' => $currentStatus])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="studentsTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Siswa</th>
          <th>NISN</th>
          <th>Kelas</th>
          <th>Program</th>
          <th>Wali Kelas</th>
          <th>Orang Tua</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($students as $student)
        @php
        $parentNames = $student->parents->map(fn($p) => $p->full_name ?: $p->name)->implode(', ');
        @endphp
        <tr id="row-{{ $student->id }}"
          data-id="{{ $student->id }}"
          data-nisn="{{ $student->nisn }}"
          data-full_name="{{ $student->full_name }}"
          data-birth_date="{{ $student->birth_date?->format('Y-m-d') }}"
          data-class_name="{{ $student->class_name }}"
          data-program_name="{{ $student->program_name }}"
          data-homeroom_teacher_id="{{ $student->homeroom_teacher_id }}"
          data-status="{{ $student->status }}">
          <td style="text-align:center">{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <span style="width:36px;height:36px;border-radius:10px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.82rem;flex-shrink:0">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
              <div>
                <strong style="display:block;font-size:.92rem">{{ $student->full_name }}</strong>
                <span style="font-size:.8rem;color:var(--muted)">{{ $student->birth_date?->format('d M Y') ?? '-' }}</span>
              </div>
            </div>
          </td>
          <td style="font-family:monospace;font-size:.85rem">{{ $student->nisn }}</td>
          <td><span style="padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $student->class_name }}</span></td>
          <td style="font-size:.88rem">{{ $student->program_name }}</td>
          <td style="font-size:.88rem">{{ $student->homeroomTeacher?->full_name ?? '-' }}</td>
          <td style="font-size:.85rem;color:var(--muted)">{{ $parentNames ?: '-' }}</td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $statusColors[$student->status] ?? '#666' }} 12%,var(--card));color:{{ $statusColors[$student->status] ?? '#666' }}">{{ $statusLabels[$student->status] ?? $student->status }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit siswa" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $student->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus siswa" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}')">
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
          <td colspan="8" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada siswa ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $students->links() }}</div>
</section>

{{-- Wizard Modal --}}
<div class="admin-modal-overlay" id="studentModal">
  <div class="admin-modal-box" style="max-width:580px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Siswa</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>

    <div class="wizard-steps">
      <div class="wizard-step active" id="step1Indicator">
        <span class="wizard-step-dot">1</span>
        <span class="wizard-step-label">Data Siswa</span>
      </div>
      <div class="wizard-step" id="step2Indicator">
        <span class="wizard-step-dot">2</span>
        <span class="wizard-step-label">Orang Tua</span>
      </div>
    </div>

    <form id="studentForm" onsubmit="submitForm(event)">
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" id="formStudentId" value="">

      {{-- Step 1: Data Siswa --}}
      <div class="wizard-section active" id="step1">
        <div class="admin-modal-body" style="padding-top:0">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div class="field">
              <label for="m_nisn">NISN <span style="color:#ef4444">*</span></label>
              <input id="m_nisn" name="nisn" type="text" required placeholder="Nomor induk siswa">
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
            <div class="field">
              <label for="m_full_name">Nama Lengkap <span style="color:#ef4444">*</span></label>
              <input id="m_full_name" name="full_name" type="text" required placeholder="Nama lengkap siswa">
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
          </div>
          <div class="field" style="margin-top:14px">
            <label for="m_birth_date">Tanggal Lahir</label>
            <input id="m_birth_date" name="birth_date" type="date">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <div class="field">
              <label for="m_class_name">Kelas <span style="color:#ef4444">*</span></label>
              <input id="m_class_name" name="class_name" type="text" required placeholder="Contoh: XI RPL 1">
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
            <div class="field">
              <label for="m_program_name">Program <span style="color:#ef4444">*</span></label>
              <input id="m_program_name" name="program_name" type="text" required placeholder="Contoh: RPL">
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <div class="field">
              <label for="m_homeroom_teacher_id">Wali Kelas</label>
              <select id="m_homeroom_teacher_id" name="homeroom_teacher_id">
                <option value="">-- Pilih --</option>
                @foreach ($teachers as $t)
                <option value="{{ $t->id }}">{{ $t->full_name ?: $t->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label for="m_status">Status <span style="color:#ef4444">*</span></label>
              <select id="m_status" name="status" required>
                <option value="active">Aktif</option>
                <option value="graduated">Lulus</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button>
          <button type="button" class="btn btn-primary" onclick="goToStep2()">Selanjutnya</button>
        </div>
      </div>

      {{-- Step 2: Orang Tua --}}
      <div class="wizard-section" id="step2">
        <div class="admin-modal-body" style="padding-top:0">
          <div style="display:flex;gap:8px;margin-bottom:16px">
            <button type="button" class="parent-action-btn active" data-action="none" onclick="setParentAction('none')">Tidak Ada</button>
            <button type="button" class="parent-action-btn" data-action="existing" onclick="setParentAction('existing')">Pilih Orang Tua</button>
            <button type="button" class="parent-action-btn" data-action="new" onclick="setParentAction('new')">Buat Baru</button>
          </div>
          <input type="hidden" name="parent_action" id="parentAction" value="none">

          {{-- Current parents (edit mode) --}}
          <div id="currentParentsSection" style="display:none;margin-bottom:16px">
            <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:6px">Orang Tua Terhubung</label>
            <div id="currentParentsList"></div>
          </div>

          {{-- Existing parent --}}
          <div id="existingParentSection" style="display:none">
            <div class="field">
              <label for="m_parent_id">Pilih Orang Tua</label>
              <select id="m_parent_id" name="parent_id">
                <option value="">-- Pilih Orang Tua --</option>
              </select>
            </div>
            <div class="field" style="margin-top:10px">
              <label for="m_parent_relationship">Hubungan</label>
              <input id="m_parent_relationship" name="parent_relationship" type="text" value="Ayah" placeholder="Contoh: Ayah, Ibu, Wali">
            </div>
          </div>

          {{-- New parent --}}
          <div id="newParentSection" style="display:none">
            <div class="field">
              <label for="m_parent_name">Nama Orang Tua <span style="color:#ef4444">*</span></label>
              <input id="m_parent_name" name="parent_name" type="text" placeholder="Nama lengkap">
            </div>
            <div class="field" style="margin-top:10px">
              <label for="m_parent_email">Email <span style="color:#ef4444">*</span></label>
              <input id="m_parent_email" name="parent_email" type="email" placeholder="email@contoh.com">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:10px">
              <div class="field">
                <label for="m_parent_password">Password <span style="color:#ef4444">*</span></label>
                <input id="m_parent_password" name="parent_password" type="password" placeholder="Minimal 6 karakter">
              </div>
              <div class="field">
                <label for="m_parent_relationship_new">Hubungan</label>
                <input id="m_parent_relationship_new" name="parent_relationship" type="text" value="Ayah" placeholder="Ayah, Ibu, Wali">
              </div>
            </div>
          </div>
        </div>
        <div class="admin-modal-footer">
          <button type="button" class="btn btn-outline" onclick="goToStep1()">Kembali</button>
          <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
  .parent-action-btn {
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid var(--line);
    background: var(--card);
    color: var(--muted);
    font-weight: 700;
    font-size: .82rem;
    cursor: pointer;
    transition: .15s ease
  }

  .parent-action-btn.active {
    border-color: #4338ca;
    background: color-mix(in srgb, #4338ca 10%, var(--card));
    color: #4338ca
  }

  .parent-action-btn:hover:not(.active) {
    border-color: var(--muted)
  }

  .parent-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 8px;
    background: color-mix(in srgb, var(--primary-2) 10%, var(--card));
    font-size: .82rem;
    margin: 0 6px 6px 0
  }

  .parent-tag button {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 0;
    font-size: .9rem;
    line-height: 1
  }
</style>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const CURRENT_STATUS = '{{ $currentStatus }}';
  let currentStep = 1;
  let isEditMode = false;
  let existingParents = [];

  function getDefaultStatus() {
    return CURRENT_STATUS || 'active';
  }

  function openCreateModal() {
    isEditMode = false;
    document.getElementById('modalTitle').textContent = 'Tambah Siswa';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formStudentId').value = '';
    document.getElementById('studentForm').action = '{{ route("admin.students.store") }}';
    document.getElementById('m_nisn').value = '';
    document.getElementById('m_full_name').value = '';
    document.getElementById('m_birth_date').value = '';
    document.getElementById('m_class_name').value = '';
    document.getElementById('m_program_name').value = '';
    document.getElementById('m_homeroom_teacher_id').value = '';
    document.getElementById('m_status').value = getDefaultStatus();
    setParentAction('none');
    document.getElementById('currentParentsSection').style.display = 'none';
    existingParents = [];
    resetWizard();
    clearErrors();
    document.getElementById('studentModal').classList.add('open');
  }

  function openEditModal(studentId) {
    isEditMode = true;
    clearErrors();
    resetWizard();

    fetch('/admin/students/' + studentId + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalTitle').textContent = 'Edit Siswa';
        document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('formStudentId').value = data.id;
        document.getElementById('studentForm').action = '/admin/students/' + data.id;
        document.getElementById('m_nisn').value = data.nisn;
        document.getElementById('m_full_name').value = data.full_name;
        document.getElementById('m_birth_date').value = data.birth_date || '';
        document.getElementById('m_class_name').value = data.class_name;
        document.getElementById('m_program_name').value = data.program_name;
        document.getElementById('m_homeroom_teacher_id').value = data.homeroom_teacher_id || '';
        document.getElementById('m_status').value = data.status;

        existingParents = data.parents || [];
        if (existingParents.length > 0) {
          document.getElementById('currentParentsSection').style.display = 'block';
          const list = document.getElementById('currentParentsList');
          list.innerHTML = existingParents.map(p =>
            '<div class="parent-tag">' + escHtml(p.name) + ' (' + escHtml(p.pivot.relationship) + ')' +
            ' <button type="button" onclick="disconnectParent(' + p.id + ', this)" title="Putuskan">&times;</button></div>'
          ).join('');
        } else {
          document.getElementById('currentParentsSection').style.display = 'none';
        }

        document.getElementById('studentModal').classList.add('open');
      });
  }

  function disconnectParent(parentId, btn) {
    Swal.fire({
      title: 'Putuskan Hubungan?',
      html: 'Orang tua ini tidak lagi terhubung ke siswa.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Putuskan',
      cancelButtonText: 'Batal',
    }).then(result => {
      if (result.isConfirmed) {
        let hidden = document.getElementById('disconnect_parent_ids');
        if (!hidden) {
          hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = 'disconnect_parent_id';
          hidden.id = 'disconnect_parent_ids';
          document.getElementById('studentForm').appendChild(hidden);
        }
        hidden.value = parentId;
        btn.closest('.parent-tag').remove();
        existingParents = existingParents.filter(p => p.id !== parentId);
        if (existingParents.length === 0) {
          document.getElementById('currentParentsSection').style.display = 'none';
        }
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: 'Hubungan diputuskan.',
          showConfirmButton: false,
          timer: 2000
        });
      }
    });
  }

  function closeModal() {
    document.getElementById('studentModal').classList.remove('open');
    clearErrors();
  }

  function resetWizard() {
    currentStep = 1;
    document.getElementById('step1').classList.add('active');
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1Indicator').classList.add('active');
    document.getElementById('step1Indicator').classList.remove('done');
    document.getElementById('step2Indicator').classList.remove('active');
    document.getElementById('step2Indicator').classList.remove('done');
  }

  function goToStep2() {
    if (!document.getElementById('m_nisn').value || !document.getElementById('m_full_name').value || !document.getElementById('m_class_name').value || !document.getElementById('m_program_name').value) {
      Swal.fire('Lengkapi Data', 'Mohon isi semua field yang wajib diisi pada data siswa.', 'warning');
      return;
    }
    currentStep = 2;
    document.getElementById('step1').classList.remove('active');
    document.getElementById('step2').classList.add('active');
    document.getElementById('step1Indicator').classList.remove('active');
    document.getElementById('step1Indicator').classList.add('done');
    document.getElementById('step2Indicator').classList.add('active');

    if (!isEditMode) {
      loadParentsList();
    }
  }

  function goToStep1() {
    currentStep = 1;
    document.getElementById('step1').classList.add('active');
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1Indicator').classList.add('active');
    document.getElementById('step1Indicator').classList.remove('done');
    document.getElementById('step2Indicator').classList.remove('active');
  }

  function setParentAction(action) {
    document.getElementById('parentAction').value = action;
    document.querySelectorAll('.parent-action-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.parent-action-btn[data-action="' + action + '"]').classList.add('active');
    document.getElementById('existingParentSection').style.display = action === 'existing' ? 'block' : 'none';
    document.getElementById('newParentSection').style.display = action === 'new' ? 'block' : 'none';
  }

  function loadParentsList() {
    fetch('/admin/parents/list', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(parents => {
        const select = document.getElementById('m_parent_id');
        select.innerHTML = '<option value="">-- Pilih Orang Tua --</option>';
        parents.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name + ' (' + p.email + ') — ' + p.students_count + ' siswa';
          select.appendChild(opt);
        });
      });
  }

  function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
    document.querySelectorAll('.admin-modal-box .field input, .admin-modal-box .field select').forEach(el => el.style.borderColor = '');
  }

  function showFieldError(fieldName, message) {
    const form = document.getElementById('studentForm');
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
    const form = document.getElementById('studentForm');
    const formData = new FormData(form);
    const isEdit = document.getElementById('formStudentId').value !== '';

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
          if (currentStep === 1) {
            ['nisn', 'full_name', 'class_name', 'program_name', 'status', 'birth_date', 'homeroom_teacher_id'].forEach(f => {
              if (json.errors[f]) showFieldError(f, json.errors[f][0]);
            });
          } else {
            ['parent_id', 'parent_name', 'parent_email', 'parent_password'].forEach(f => {
              if (json.errors[f]) showFieldError(f, json.errors[f][0]);
            });
          }
        } else {
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => {
        Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
      });
  }

  function confirmDelete(studentId, studentName) {
    Swal.fire({
      title: 'Hapus Siswa?',
      html: 'Data <strong>' + studentName + '</strong> beserta semua nilai, kehadiran, dan catatan akan dihapus permanen.',
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
        fetch('/admin/students/' + studentId, {
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

  function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
  document.getElementById('studentModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
</script>
@endpush
@endsection