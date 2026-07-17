@extends('layouts.admin')

@section('title', 'Penugasan Guru')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen penugasan</span>
    <h1>Penugasan Guru</h1>
    <p>Atur penugasan guru mengajar per mata pelajaran dan kelas.</p>
  </div>
  <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="12" y1="5" x2="12" y2="19" />
      <line x1="5" y1="12" x2="19" y2="12" />
    </svg>
    Tambah Penugasan
  </button>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <div class="field" style="flex:1;min-width:200px">
      <select name="period">
        <option value="">Semua Periode</option>
        @foreach ($periods as $period)
        <option value="{{ $period->id }}" {{ request('period') == $period->id ? 'selected' : '' }}>
          {{ $period->academic_year }} {{ $period->semester }} {{ $period->is_active ? '(Aktif)' : '' }}
        </option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari guru, mata pelajaran, kelas...">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Filter</button>
    @if (request('period') || request('search'))
    <a href="{{ route('admin.teaching.index') }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="teachingTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Periode</th>
          <th>Mata Pelajaran</th>
          <th>Guru</th>
          <th>Kelas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($assignments as $assignment)
        <tr id="row-{{ $assignment->id }}"
          data-id="{{ $assignment->id }}"
          data-period_id="{{ $assignment->period_id }}"
          data-subject_id="{{ $assignment->subject_id }}"
          data-teacher_id="{{ $assignment->teacher_id }}"
          data-class_name="{{ $assignment->class_name }}">
          <td style="text-align:center">{{ $loop->iteration + ($assignments->currentPage() - 1) * $assignments->perPage() }}</td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">
              {{ $assignment->period->academic_year }} {{ $assignment->period->semester }}
              @if ($assignment->period->is_active) <span style="color:var(--success)">&bull; Aktif</span> @endif
            </span>
          </td>
          <td><strong>{{ $assignment->subject->code }}</strong> — {{ $assignment->subject->name }}</td>
          <td style="font-size:.88rem">{{ $assignment->teacher->full_name ?? $assignment->teacher->name }}</td>
          <td><span style="padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem;background:color-mix(in srgb,var(--accent) 10%,var(--card));color:#7a5500">{{ $assignment->class_name }}</span></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit penugasan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $assignment->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus penugasan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $assignment->id }}, '{{ addslashes($assignment->subject->name) }}', '{{ addslashes($assignment->teacher->full_name ?? $assignment->teacher->name) }}')">
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
          <td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Belum ada penugasan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $assignments->links('vendor.pagination.admin') }}</div>
</section>

{{-- Modal --}}
<div class="admin-modal-overlay" id="teachingModal">
  <div class="admin-modal-box" style="max-width:520px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Penugasan</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="teachingForm" onsubmit="submitForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" id="formAssignmentId" value="">

        <div class="field">
          <label for="m_period_id">Periode Akademik <span style="color:#ef4444">*</span></label>
          <select id="m_period_id" name="period_id" required>
            <option value="">-- Pilih Periode --</option>
            @foreach ($periods as $period)
            <option value="{{ $period->id }}">{{ $period->academic_year }} Semester {{ ucfirst($period->semester) }} {{ $period->is_active ? '(Aktif)' : '' }}</option>
            @endforeach
          </select>
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_subject_id">Mata Pelajaran <span style="color:#ef4444">*</span></label>
          <select id="m_subject_id" name="subject_id" required>
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach ($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->code }} — {{ $subject->name }} (KKM: {{ $subject->kkm }})</option>
            @endforeach
          </select>
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_teacher_id">Guru <span style="color:#ef4444">*</span></label>
          <select id="m_teacher_id" name="teacher_id" required>
            <option value="">-- Pilih Guru --</option>
            @foreach ($teachers as $teacher)
            <option value="{{ $teacher->id }}">{{ $teacher->full_name ?: $teacher->name }} ({{ $teacher->role }})</option>
            @endforeach
          </select>
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_class_name">Kelas <span style="color:#ef4444">*</span></label>
          <select id="m_class_name" name="class_name" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach ($classNames as $class)
            <option value="{{ $class }}">{{ $class }}</option>
            @endforeach
          </select>
          <small class="field-error" style="color:#ef4444;display:none"></small>
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
    document.getElementById('modalTitle').textContent = 'Tambah Penugasan';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formAssignmentId').value = '';
    document.getElementById('teachingForm').action = '{{ route("admin.teaching.store") }}';
    document.getElementById('m_period_id').value = '';
    document.getElementById('m_subject_id').value = '';
    document.getElementById('m_teacher_id').value = '';
    document.getElementById('m_class_name').value = '';
    clearErrors();
    document.getElementById('teachingModal').classList.add('open');
  }

  function openEditModal(assignmentId) {
    clearErrors();
    document.getElementById('modalTitle').textContent = 'Edit Penugasan';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formAssignmentId').value = assignmentId;
    document.getElementById('teachingForm').action = '/admin/teaching/' + assignmentId;

    fetch('/admin/teaching/' + assignmentId + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        document.getElementById('m_period_id').value = data.period_id;
        document.getElementById('m_subject_id').value = data.subject_id;
        document.getElementById('m_teacher_id').value = data.teacher_id;
        document.getElementById('m_class_name').value = data.class_name;
        document.getElementById('teachingModal').classList.add('open');
      });
  }

  function closeModal() {
    document.getElementById('teachingModal').classList.remove('open');
    clearErrors();
  }

  function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
    document.querySelectorAll('.admin-modal-box .field select').forEach(el => el.style.borderColor = '');
  }

  function showFieldError(fieldName, message) {
    const form = document.getElementById('teachingForm');
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
    const form = document.getElementById('teachingForm');
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

  function confirmDelete(assignmentId, subjectName, teacherName) {
    Swal.fire({
      title: 'Hapus Penugasan?',
      html: 'Penugasan <strong>' + subjectName + '</strong> oleh <strong>' + teacherName + '</strong> akan dihapus permanen.',
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
        fetch('/admin/teaching/' + assignmentId, {
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
  document.getElementById('teachingModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
</script>
@endpush
@endsection


