@extends('layouts.admin')

@section('title', 'Kelola Siswa')
@php
$tab = request('tab', 'students');
$studentStatus = request('status', '');
$studentStatusColors = ['active' => 'var(--success)', 'graduated' => 'var(--primary-2)', 'inactive' => '#ef4444'];
$studentStatusLabels = ['active' => 'Aktif', 'graduated' => 'Lulus', 'inactive' => 'Nonaktif'];

$applicantStatusLabels = ['draft' => 'Draft', 'submitted' => 'Terkirim', 'verified' => 'Terverifikasi', 'paid' => 'Lunas', 'rejected' => 'Ditolak'];
$applicantStepLabels = ['not_started' => 'Belum Mulai', 'student_data' => 'Data Siswa', 'parent_data' => 'Data Ortu', 'documents' => 'Upload Berkas', 'completed' => 'Selesai'];
$applicantStepPercent = ['not_started' => 0, 'student_data' => 33, 'parent_data' => 66, 'documents' => 90, 'completed' => 100];
@endphp



@section('content')
<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.students.index', ['tab' => 'students']) }}"
    class="tab-btn {{ $tab === 'students' ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:8px">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
      <circle cx="9" cy="7" r="4" />
      <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
      <path d="M16 3.13a4 4 0 0 1 0 7.75" />
    </svg>
    Siswa Aktif
    <span class="tab-count">{{ $tabCounts['all'] ?? 0 }}</span>
  </a>
  <a href="{{ route('admin.students.index', ['tab' => 'applicants']) }}"
    class="tab-btn {{ $tab === 'applicants' ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:8px">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
      <polyline points="14 2 14 8 20 8" />
      <line x1="16" y1="13" x2="8" y2="13" />
      <line x1="16" y1="17" x2="8" y2="17" />
    </svg>
    Pendaftar PPDB
    <span class="tab-count">{{ $applicantStatusCounts['all'] ?? 0 }}</span>
  </a>
</div>

@if ($tab === 'students')
{{-- ================= TAB SISWA AKTIF ================= --}}
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen siswa</span>
    <h1>Siswa Aktif</h1>
    <p>Kelola data siswa aktif, hubungkan ke akun orang tua, dan import data.</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('admin.students.import') }}" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="17 8 12 3 7 8" />
        <line x1="12" y1="3" x2="12" y2="15" />
      </svg>
      Import CSV
    </a>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:6px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      Tambah Siswa
    </button>
  </div>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.students.index', array_merge(['tab' => 'students'], array_filter(['search' => request('search'), 'class' => request('class')]))) }}"
    class="tab-btn {{ $studentStatus === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.students.index', array_merge(['tab' => 'students', 'status' => 'active'], array_filter(['search' => request('search'), 'class' => request('class')]))) }}"
    class="tab-btn {{ $studentStatus === 'active' ? 'active' : '' }}">
    Aktif <span class="tab-count">{{ $tabCounts['active'] }}</span>
  </a>
  <a href="{{ route('admin.students.index', array_merge(['tab' => 'students', 'status' => 'graduated'], array_filter(['search' => request('search'), 'class' => request('class')]))) }}"
    class="tab-btn {{ $studentStatus === 'graduated' ? 'active' : '' }}">
    Lulus <span class="tab-count">{{ $tabCounts['graduated'] }}</span>
  </a>
  <a href="{{ route('admin.students.index', array_merge(['tab' => 'students', 'status' => 'inactive'], array_filter(['search' => request('search'), 'class' => request('class')]))) }}"
    class="tab-btn {{ $studentStatus === 'inactive' ? 'active' : '' }}">
    Nonaktif <span class="tab-count">{{ $tabCounts['inactive'] }}</span>
  </a>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <input type="hidden" name="tab" value="students">
    @if ($studentStatus)
    <input type="hidden" name="status" value="{{ $studentStatus }}">
    @endif
    <div class="field" style="flex:2;min-width:200px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NISN...">
    </div>
    <div class="field" style="flex:1;min-width:140px">
      <select name="jurusan_id" id="filterJurusan">
        <option value="">Semua Jurusan</option>
        @foreach ($jurusans as $j)
        <option value="{{ $j->id }}" {{ (int) request('jurusan_id') === $j->id ? 'selected' : '' }}>{{ $j->kode }} — {{ $j->nama }}</option>
        @endforeach
      </select>
    </div>
    <div class="field" style="flex:1;min-width:140px">
      <select name="kelas_id" id="filterKelas">
        <option value="">Semua Kelas</option>
        @foreach ($kelasList as $k)
        @php $romawi = [10 => 'X', 11 => 'XI', 12 => 'XII']; @endphp
        <option value="{{ $k->id }}" {{ (int) request('kelas_id') === $k->id ? 'selected' : '' }} data-jurusan="{{ $k->jurusan_id }}">{{ $romawi[$k->tingkat] ?? $k->tingkat }} {{ $k->nama }}</option>
        @endforeach
      </select>
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search') || request('jurusan_id') || request('kelas_id'))
    <a href="{{ route('admin.students.index', array_merge(['tab' => 'students'], array_filter(['status' => $studentStatus]))) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
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
        <tr id="row-{{ $student->id }}" data-id="{{ $student->id }}" data-nisn="{{ $student->nisn }}" data-full_name="{{ $student->full_name }}" data-birth_date="{{ $student->birth_date?->format('Y-m-d') }}" data-jurusan_id="{{ $student->jurusan_id }}" data-kelas_id="{{ $student->kelas_id }}" data-class_name="{{ $student->class_name }}" data-program_name="{{ $student->program_name }}" data-homeroom_teacher_id="{{ $student->homeroom_teacher_id }}" data-status="{{ $student->status }}">
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
          <td><span style="padding:4px 10px;border-radius:8px;font-weight:700;font-size:.82rem;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $student->kelas?->nama_lengkap ?? $student->class_name }}</span></td>
          <td style="font-size:.88rem">{{ $student->jurusan?->nama ?? $student->program_name }}</td>
          <td style="font-size:.88rem">{{ $student->homeroomTeacher?->full_name ?? '-' }}</td>
          <td style="font-size:.85rem;color:var(--muted)">{{ $parentNames ?: '-' }}</td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,{{ $studentStatusColors[$student->status] ?? '#666' }} 12%,var(--card));color:{{ $studentStatusColors[$student->status] ?? '#666' }}">{{ $studentStatusLabels[$student->status] ?? $student->status }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit siswa" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $student->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus siswa" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
          <td colspan="9" style="text-align:center;padding:30px;color:var(--muted)">Tidak ada siswa ditemukan.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $students->links('vendor.pagination.admin') }}</div>
</section>

{{-- Wizard Modal --}}
<div class="admin-modal-overlay" id="studentModal">
  <div class="admin-modal-box" style="max-width:580px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Siswa</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <div class="wizard-steps">
      <div class="wizard-step active" id="step1Indicator"><span class="wizard-step-dot">1</span><span class="wizard-step-label">Data Siswa</span></div>
      <div class="wizard-step" id="step2Indicator"><span class="wizard-step-dot">2</span><span class="wizard-step-label">Orang Tua</span></div>
    </div>
    <form id="studentForm" onsubmit="submitForm(event)" novalidate>
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" id="formStudentId" value="">
      <div class="wizard-section active" id="step1">
        <div class="admin-modal-body" style="padding-top:0">
          @csrf
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div class="field"><label for="m_nisn">NISN <span style="color:#ef4444">*</span></label><input id="m_nisn" name="nisn" type="text" required placeholder="Nomor induk siswa"><small class="field-error" style="color:#ef4444;display:none"></small></div>
            <div class="field"><label for="m_full_name">Nama Lengkap <span style="color:#ef4444">*</span></label><input id="m_full_name" name="full_name" type="text" required placeholder="Nama lengkap siswa"><small class="field-error" style="color:#ef4444;display:none"></small></div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <div class="field"><label for="m_student_email">Email Akun <span style="color:#ef4444">*</span></label>
              <input id="m_student_email" name="student_email" type="email" required placeholder="email@contoh.com" oninput="checkEmailAvailability(this.value)">
              <small class="field-error" style="color:#ef4444;display:none"></small>
              <small id="emailStatus" style="display:none;font-size:.78rem;font-weight:600;margin-top:.3rem"></small>
            </div>
            <div class="field"><label for="m_birth_date">Tanggal Lahir</label><input id="m_birth_date" name="birth_date" type="date"></div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <div class="field"><label for="m_jurusan_id">Jurusan <span style="color:#ef4444">*</span></label>
              <select id="m_jurusan_id" name="jurusan_id" required onchange="loadKelasByJurusan(this.value)">
                <option value="">Pilih Jurusan</option>
                @foreach ($jurusans as $j)
                <option value="{{ $j->id }}">{{ $j->kode }} — {{ $j->nama }}</option>
                @endforeach
              </select>
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
            <div class="field"><label for="m_kelas_id">Kelas <span style="color:#ef4444">*</span></label>
              <select id="m_kelas_id" name="kelas_id" required onchange="updateWaliKelas(this)">
                <option value="">Pilih Jurusan dulu</option>
              </select>
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <input type="hidden" name="homeroom_teacher_id" id="m_homeroom_teacher_id" value="">
            <div class="field"><label for="m_homeroom_teacher_text">Wali Kelas</label>
              <input id="m_homeroom_teacher_text" type="text" readonly placeholder="Otomatis dari kelas" style="width:100%;padding:.7rem .9rem;border-radius:.75rem;font-size:.9rem;outline:none;background:color-mix(in srgb,var(--muted) 6%,var(--card));border:1.5px solid var(--line);color:var(--muted);font-family:inherit;cursor:default">
            </div>
            <div class="field"><label for="m_status">Status <span style="color:#ef4444">*</span></label><select id="m_status" name="status" required>
                <option value="active">Aktif</option>
                <option value="graduated">Lulus</option>
                <option value="inactive">Nonaktif</option>
              </select></div>
          </div>
          <div id="resetPasswordSection" style="display:none;margin-top:14px;padding-top:14px;border-top:1px solid var(--line)">
            <div style="display:flex;align-items:center;justify-content:space-between">
              <div>
                <strong style="font-size:.9rem;color:var(--ink)">Akun Siswa</strong>
                <p id="studentAccountEmail" style="margin:4px 0 0;font-size:.82rem;color:var(--muted)"></p>
              </div>
              <button type="button" class="btn btn-outline" onclick="confirmResetPassword()" style="min-height:36px;padding:0 14px;font-size:.82rem;color:#d97706;border-color:#d97706;display:inline-flex;align-items:center;gap:6px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                Reset Password
              </button>
            </div>
          </div>
        </div>
        <div class="admin-modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal()">Batal</button><button type="button" class="btn btn-primary" onclick="goToStep2()">Selanjutnya</button></div>
      </div>
      <div class="wizard-section" id="step2">
        <div class="admin-modal-body" style="padding-top:0">
          <div style="display:flex;gap:8px;margin-bottom:16px"><button type="button" class="parent-action-btn active" data-action="none" onclick="setParentAction('none')">Tidak Ada</button><button type="button" class="parent-action-btn" data-action="existing" onclick="setParentAction('existing')">Pilih Orang Tua</button><button type="button" class="parent-action-btn" data-action="new" onclick="setParentAction('new')">Buat Baru</button></div>
          <input type="hidden" name="parent_action" id="parentAction" value="none">
          <div id="currentParentsSection" style="display:none;margin-bottom:16px"><label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:6px">Orang Tua Terhubung</label>
            <div id="currentParentsList"></div>
          </div>
          <div id="existingParentSection" style="display:none">
            <div class="field"><label for="m_parent_id">Pilih Orang Tua</label><select id="m_parent_id" name="parent_id">
                <option value="">-- Pilih Orang Tua --</option>
              </select></div>
            <div class="field" style="margin-top:10px"><label for="m_parent_relationship">Hubungan</label><input id="m_parent_relationship" name="parent_relationship" type="text" value="Ayah" placeholder="Contoh: Ayah, Ibu, Wali"></div>
          </div>
          <div id="newParentSection" style="display:none">
            <div class="field"><label for="m_parent_name">Nama Orang Tua <span style="color:#ef4444">*</span></label><input id="m_parent_name" name="parent_name" type="text" placeholder="Nama lengkap"></div>
            <div class="field" style="margin-top:10px"><label for="m_parent_email">Email <span style="color:#ef4444">*</span></label><input id="m_parent_email" name="parent_email" type="email" placeholder="email@contoh.com"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:10px">
              <div class="field"><label for="m_parent_password">Password <span style="color:#ef4444">*</span></label><input id="m_parent_password" name="parent_password" type="password" placeholder="Minimal 6 karakter"></div>
              <div class="field"><label for="m_parent_relationship_new">Hubungan</label><input id="m_parent_relationship_new" name="parent_relationship" type="text" value="Ayah" placeholder="Ayah, Ibu, Wali"></div>
            </div>
          </div>
        </div>
        <div class="admin-modal-footer"><button type="button" class="btn btn-outline" onclick="goToStep1()">Kembali</button><button type="submit" class="btn btn-primary" id="modalSubmitBtn">Simpan</button></div>
      </div>
    </form>
  </div>
</div>

@elseif ($tab === 'applicants')
{{-- ================= TAB PENDAFTAR PPDB ================= --}}

<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen PPDB</span>
    <h1>Pendaftar PPDB</h1>
    <p>Kelola data pendaftar, verifikasi dokumen, dan proses penerimaan siswa baru.</p>
  </div>
</div>

<div class="admin-filter-toolbar">
  <div class="filter-tabs">
    <a href="{{ route('admin.students.index', ['tab' => 'applicants']) }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">
      Semua
      <span class="filter-tab-count">{{ $applicantStatusCounts['all'] }}</span>
    </a>
    @foreach (['draft', 'submitted', 'verified', 'paid', 'rejected'] as $s)
    <a href="{{ route('admin.students.index', ['tab' => 'applicants', 'status' => $s]) }}" class="filter-tab {{ request('status') === $s ? 'active' : '' }}">
      {{ $applicantStatusLabels[$s] }}
      <span class="filter-tab-count">{{ $applicantStatusCounts[$s] }}</span>
    </a>
    @endforeach
  </div>
  <form method="GET" class="search-box">
    <input type="hidden" name="tab" value="applicants">
    <input type="text" name="search" class="search-input" placeholder="Cari nama atau asal sekolah..." value="{{ request('search') }}">
    <button class="btn btn-outline btn-sm" type="submit">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8" />
        <path d="m21 21-4.35-4.35" />
      </svg>
    </button>
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="applicantsTable">
      <thead>
        <tr>
          <th class="table-cell-index">No</th>
          <th>Nama</th>
          <th>Asal Sekolah</th>
          <th>Program</th>
          <th style="min-width: 200px;">Progress</th>
          <th style="width: 140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($applicants as $a)
        <tr data-applicant-id="{{ $a->id }}">
          <td class="table-cell-index">{{ $loop->iteration + ($applicants->currentPage() - 1) * $applicants->perPage() }}</td>
          <td class="table-cell-name">
            <strong>{{ $a->full_name }}</strong>
            @if($a->user)
            <span class="email">{{ $a->user->email }}</span>
            @endif
          </td>
          <td>{{ $a->asal_sekolah ?: '-' }}</td>
          <td class="table-cell-program">{{ $a->program_diminati ?: '-' }}</td>
          <td>
            <div class="applicant-progress">
              <div class="progress-bar-wrap">
                <div class="progress-bar-fill {{ $a->completion_step === 'completed' ? 'completed' : '' }}"
                  style="width: {{ $applicantStepPercent[$a->completion_step] }}%"></div>
              </div>
              <span class="progress-label">{{ $applicantStepLabels[$a->completion_step] }}</span>
            </div>
          </td>
          <td>
            <div class="action-buttons">
              @if($a->status === 'submitted')
              <button class="action-btn action-btn-primary" onclick="verifyApplicant({{ $a->id }}, '{{ $a->full_name }}')" title="Validasi Data">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
              </button>
              <button class="action-btn action-btn-danger" onclick="rejectApplicant({{ $a->id }}, '{{ $a->full_name }}')" title="Tolak">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
              @endif

              @if($a->status === 'verified')
              <button class="action-btn action-btn-danger" onclick="cancelApplicant({{ $a->id }}, '{{ $a->full_name }}')" title="Batalkan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="4" y1="12" x2="20" y2="12" />
                </svg>
              </button>
              @endif

              <button class="action-btn" onclick="showDetail({{ $a->id }})" title="Lihat Detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" style="padding:0;">
            <div class="empty-state">
              <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
              </div>
              <h3>Belum Ada Pendaftar</h3>
              <p>Belum ada pendaftar PPDB yang sesuai dengan filter ini.</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $applicants->links('vendor.pagination.admin') }}</div>
</section>

<div id="detailModal" class="applicant-detail-modal" onclick="if(event.target===this)closeDetail()">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-header-content">
        <h3 id="modalName">Detail Pendaftar</h3>
        <div class="modal-status-bar" id="modalStatusBar"></div>
      </div>
      <button class="btn btn-outline btn-sm" onclick="closeDetail()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-footer" id="modalFooter" style="display:none;"></div>
  </div>
</div>

@endif
@endsection

@push('styles')
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

  .bulk-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
  }

  .bulk-actions-count {
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
  }

  .bulk-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .bulk-btn-accept {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
  }

  .bulk-btn-accept:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
  }

  .bulk-btn-verify {
    background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);
    color: #fff;
  }

  .bulk-btn-verify:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(168, 85, 247, 0.4);
  }

  .bulk-btn-reject {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff;
  }

  .bulk-btn-reject:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
  }
</style>
@endpush

@push('scripts')
<script>
  @if($tab === 'students')
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const CURRENT_STATUS = '{{ $studentStatus }}';
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
    document.getElementById('studentForm').action = '{{ route(name: "admin.students.store") }}';
    document.getElementById('m_nisn').value = '';
    document.getElementById('m_full_name').value = '';
    document.getElementById('m_student_email').value = '';
    document.getElementById('m_birth_date').value = '';
    document.getElementById('m_jurusan_id').value = '';
    document.getElementById('m_kelas_id').innerHTML = '<option value="">Pilih Jurusan dulu</option>';
    document.getElementById('m_homeroom_teacher_id').value = '';
    document.getElementById('m_homeroom_teacher_text').value = '';
    document.getElementById('m_status').value = getDefaultStatus();
    document.getElementById('resetPasswordSection').style.display = 'none';
    setParentAction('none');
    document.getElementById('currentParentsSection').style.display = 'none';
    existingParents = [];
    resetWizard();
    clearErrors();
    document.getElementById('studentModal').classList.add('open');
  }

  function openEditModal(id) {
    isEditMode = true;
    clearErrors();
    resetWizard();
    fetch('/admin/students/' + id + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json()).then(d => {
        document.getElementById('modalTitle').textContent = 'Edit Siswa';
        document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('formStudentId').value = d.id;
        document.getElementById('studentForm').action = '/admin/students/' + d.id;
        document.getElementById('m_nisn').value = d.nisn;
        document.getElementById('m_full_name').value = d.full_name;
        document.getElementById('m_student_email').value = d.student_email || '';
        document.getElementById('m_birth_date').value = d.birth_date || '';
        document.getElementById('m_jurusan_id').value = d.jurusan_id || '';
        loadKelasByJurusan(d.jurusan_id || '', d.kelas_id || '');
        var wali = d.homeroom_teacher_id || '';
        document.getElementById('m_homeroom_teacher_id').value = wali;
        document.getElementById('m_status').value = d.status;

        // Reset password section
        var rpSection = document.getElementById('resetPasswordSection');
        var rpEmail = document.getElementById('studentAccountEmail');
        if (d.user_id && d.student_email) {
          rpSection.style.display = 'block';
          rpEmail.textContent = 'Email: ' + d.student_email;
          rpSection.dataset.studentId = d.id;
        } else {
          rpSection.style.display = 'none';
        }
        existingParents = d.parents || [];
        if (existingParents.length > 0) {
          document.getElementById('currentParentsSection').style.display = 'block';
          document.getElementById('currentParentsList').innerHTML = existingParents.map(p =>
            '<div class="parent-tag">' + escHtml(p.name) + ' (' + escHtml(p.pivot.relationship) + ')' +
            ' <button type="button" onclick="disconnectParent(' + p.id + ', this)">&times;</button></div>'
          ).join('');
        } else {
          document.getElementById('currentParentsSection').style.display = 'none';
        }
        document.getElementById('studentModal').classList.add('open');
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
    if (!document.getElementById('m_nisn').value || !document.getElementById('m_full_name').value || !document.getElementById('m_student_email').value || !document.getElementById('m_kelas_id').value) {
      Swal.fire('Lengkapi Data', 'Mohon isi semua field yang wajib diisi pada data siswa.', 'warning');
      return;
    }
    const emailStatus = document.getElementById('emailStatus');
    if (emailStatus.style.display === 'block' && emailStatus.style.color === 'rgb(220, 38, 38)') {
      Swal.fire('Email Tidak Tersedia', 'Gunakan email lain yang belum terdaftar.', 'warning');
      return;
    }
    currentStep = 2;
    document.getElementById('step1').classList.remove('active');
    document.getElementById('step2').classList.add('active');
    document.getElementById('step1Indicator').classList.remove('active');
    document.getElementById('step1Indicator').classList.add('done');
    document.getElementById('step2Indicator').classList.add('active');
    if (!isEditMode) loadParentsList();
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
      .then(r => r.json()).then(parents => {
        const sel = document.getElementById('m_parent_id');
        sel.innerHTML = '<option value="">-- Pilih Orang Tua --</option>';
        parents.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name + ' (' + p.email + ') — ' + p.students_count + ' siswa';
          sel.appendChild(opt);
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

  function showFieldError(name, msg) {
    const form = document.getElementById('studentForm');
    const input = form.querySelector('[name="' + name + '"]');
    if (input) {
      input.style.borderColor = '#ef4444';
      const err = input.closest('.field').querySelector('.field-error');
      if (err) {
        err.textContent = msg;
        err.style.display = 'block';
      }
    }
  }

  function submitForm(e) {
    e.preventDefault();
    clearErrors();
    const btn = document.getElementById('modalSubmitBtn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
    const form = document.getElementById('studentForm');
    const fd = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN
        }
      })
      .then(r => {
        const ct = r.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
          return r.text().then(text => {
            const title = text.match(/<title>([^<]+)<\/title>/)?.[1] || 'Server returned HTML instead of JSON';
            throw new Error(title + ' — Coba reload page dan ulangi');
          });
        }
        return r.json().then(j => ({ ok: r.ok, j }));
      })
      .then(({
        ok,
        j
      }) => {
        btn.disabled = false;
        btn.textContent = originalText;
        if (ok && j.success) {
          closeModal();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: j.message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          }).then(() => location.reload());
        } else if (j.errors) {
          const fields = currentStep === 1 ? ['nisn', 'full_name', 'student_email', 'jurusan_id', 'kelas_id', 'status', 'birth_date', 'homeroom_teacher_id'] : ['parent_id', 'parent_name', 'parent_email', 'parent_password'];
          fields.forEach(f => {
            if (j.errors[f]) showFieldError(f, j.errors[f][0]);
          });
        } else {
          Swal.fire('Gagal', j.message || 'Terjadi kesalahan.', 'error');
        }
      }).catch((err) => {
        btn.disabled = false;
        btn.textContent = originalText;
        console.error('Fetch error:', err);
        Swal.fire('Error', err.message || 'Tidak dapat terhubung ke server.', 'error');
      });
  }

  function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Siswa?',
        html: 'Data <strong>' + name + '</strong> beserta semua nilai, kehadiran, dan catatan akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      })
      .then(result => {
        if (result.isConfirmed) {
          fetch('/admin/students/' + id, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
              }
            })
            .then(r => r.json()).then(j => {
              if (j.success) Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: j.message,
                showConfirmButton: false,
                timer: 3000
              }).then(() => location.reload());
              else Swal.fire('Gagal', j.message, 'error');
            })
            .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
        }
      });
  }

  function disconnectParent(pid, btn) {
    Swal.fire({
        title: 'Putuskan Hubungan?',
        html: 'Orang tua ini tidak lagi terhubung ke siswa.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Putuskan',
        cancelButtonText: 'Batal'
      })
      .then(r => {
        if (r.isConfirmed) {
          let h = document.getElementById('disconnect_parent_ids');
          if (!h) {
            h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'disconnect_parent_id';
            h.id = 'disconnect_parent_ids';
            document.getElementById('studentForm').appendChild(h);
          }
          h.value = pid;
          btn.closest('.parent-tag').remove();
          existingParents = existingParents.filter(p => p.id !== pid);
          if (existingParents.length === 0) document.getElementById('currentParentsSection').style.display = 'none';
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

  function loadKelasByJurusan(jurusanId, selectedKelasId) {
    const sel = document.getElementById('m_kelas_id');
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled = true;
    document.getElementById('m_homeroom_teacher_id').value = '';
    document.getElementById('m_homeroom_teacher_text').value = '';

    if (!jurusanId) {
      sel.innerHTML = '<option value="">Pilih Jurusan dulu</option>';
      sel.disabled = false;
      return;
    }

    fetch('/admin/jurusans/' + jurusanId + '/kelas', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(data => {
        sel.innerHTML = '<option value="">Pilih Kelas</option>';
        data.forEach(k => {
          const s = document.createElement('option');
          s.value = k.id;
          s.textContent = k.nama_lengkap;
          s.dataset.wali = k.homeroom_teacher_id || '';
          s.dataset.waliNama = k.wali_nama || '';
          if (selectedKelasId && String(k.id) === String(selectedKelasId)) s.selected = true;
          sel.appendChild(s);
        });
        sel.disabled = false;
        if (selectedKelasId) updateWaliKelas(sel);
      })
      .catch(() => {
        sel.innerHTML = '<option value="">Gagal memuat</option>';
        sel.disabled = false;
      });
  }

  function updateWaliKelas(sel) {
    if (typeof sel === 'string') sel = document.getElementById('m_kelas_id');
    const selected = sel.options[sel.selectedIndex];
    const wali = selected ? selected.dataset.wali : '';
    const waliNama = selected ? selected.dataset.waliNama : '';
    document.getElementById('m_homeroom_teacher_id').value = wali || '';
    document.getElementById('m_homeroom_teacher_text').value = waliNama || 'Walas belum ditentukan';
  }

  let emailCheckTimer;

  function checkEmailAvailability(email) {
    const status = document.getElementById('emailStatus');
    const field = document.getElementById('m_student_email');

    if (!email) {
      status.style.display = 'none';
      field.style.borderColor = '';
      return;
    }

    if (!email.includes('@') || !email.includes('.')) {
      status.style.display = 'none';
      field.style.borderColor = '';
      return;
    }

    clearTimeout(emailCheckTimer);
    emailCheckTimer = setTimeout(() => {
      fetch('/admin/check-email?email=' + encodeURIComponent(email), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
      .then(r => r.json())
      .then(d => {
        if (d.available) {
          status.style.display = 'block';
          status.style.color = '#059669';
          status.textContent = '✓ Email tersedia';
          field.style.borderColor = '#059669';
        } else {
          status.style.display = 'block';
          status.style.color = '#dc2626';
          status.textContent = '✗ ' + d.message;
          field.style.borderColor = '#dc2626';
        }
      })
      .catch(() => {});
    }, 500);
  }

  function confirmResetPassword() {
    const section = document.getElementById('resetPasswordSection');
    const studentId = section.dataset.studentId;
    if (!studentId) return;

    Swal.fire({
      title: 'Reset Password?',
      text: 'Password baru akan dikirim ke email siswa.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d97706',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Reset',
      cancelButtonText: 'Batal',
    }).then(result => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Memproses...',
          html: 'Mengirim password baru ke email',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
        fetch('/admin/students/' + studentId + '/reset-password', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': CSRF_TOKEN
            }
          })
          .then(r => r.json())
          .then(json => {
            if (json.success) {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: json.message,
                timer: 3000
              });
            } else {
              Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
      }
    });
  }

  function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
  document.getElementById('studentModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  @elseif($tab === 'applicants')
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const STATUS_LABELS = @json($applicantStatusLabels);
  const STEP_LABELS = @json($applicantStepLabels);
  const STEP_PERCENT = @json($applicantStepPercent);
  let currentApplicantId = null;

  function showDetail(id) {
    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');
    const modalStatusBar = document.getElementById('modalStatusBar');

    modalBody.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--muted)">Memuat data...</div>';
    modalStatusBar.innerHTML = '';
    modalFooter.style.display = 'none';
    document.getElementById('detailModal').classList.add('open');

    fetch('/admin/applicants/' + id + '/data').then(r => r.json()).then(d => {
      currentApplicantId = id;

      const completionPercent = STEP_PERCENT[d.completion_step];
      const completionLabel = STEP_LABELS[d.completion_step];

      let statusBarHtml = '<span class="status-badge ' + d.status + '">' + STATUS_LABELS[d.status] + '</span>';
      statusBarHtml += '<div class="applicant-progress" style="flex:1;max-width:300px;">';
      statusBarHtml += '<div class="progress-bar-wrap"><div class="progress-bar-fill ' + (d.completion_step === 'completed' ? 'completed' : '') + '" style="width:' + completionPercent + '%"></div></div>';
      statusBarHtml += '<span class="progress-label">' + completionLabel + '</span></div>';
      modalStatusBar.innerHTML = statusBarHtml;

      const sections = [{
          title: 'Data Pribadi',
          icon: '📋',
          fields: [
            ['full_name', 'Nama Lengkap'],
            ['nickname', 'Nama Panggilan'],
            ['birth_place', 'Tempat Lahir'],
            ['birth_date', 'Tanggal Lahir'],
            ['gender', 'Jenis Kelamin'],
            ['religion', 'Agama'],
            ['address', 'Alamat'],
            ['phone', 'No. HP']
          ]
        },
        {
          title: 'Data Akademik',
          icon: '🎓',
          fields: [
            ['asal_sekolah', 'Asal Sekolah'],
            ['nisn', 'NISN'],
            ['jenjang', 'Jenjang'],
            ['program_diminati', 'Program Diminati']
          ]
        },
        {
          title: 'Data Orang Tua',
          icon: '👨‍👩‍👧',
          fields: [
            ['ayah_name', 'Nama Ayah'],
            ['ayah_occupation', 'Pekerjaan Ayah'],
            ['ayah_phone', 'HP Ayah'],
            ['ayah_email', 'Email Ayah'],
            ['ibu_name', 'Nama Ibu'],
            ['ibu_occupation', 'Pekerjaan Ibu'],
            ['ibu_phone', 'HP Ibu'],
            ['ibu_email', 'Email Ibu']
          ]
        }
      ];
      if (d.wali_name || d.wali_occupation || d.wali_phone || d.wali_email) {
        sections.push({
          title: 'Data Wali',
          icon: '👤',
          fields: [
            ['wali_name', 'Nama Wali'],
            ['wali_occupation', 'Pekerjaan Wali'],
            ['wali_phone', 'HP Wali'],
            ['wali_email', 'Email Wali']
          ]
        });
      }

      let html = '<div class="detail-grid">';
      sections.forEach(s => {
        html += '<div class="detail-section"><h4 class="detail-section-title" data-icon="' + s.icon + '">' + s.title + '</h4>';
        s.fields.forEach(([k, l]) => {
          const v = d[k];
          html += '<div class="detail-row"><div class="detail-label">' + l + '</div><div class="detail-value' + (!v ? ' empty' : '') + '">' + (v || 'Tidak diisi') + '</div></div>';
        });
        html += '</div>';
      });

      // Dokumen section
      html += '<div class="detail-section">';
      html += '<h4 class="detail-section-title" data-icon="📁">Dokumen</h4>';
      html += '<div class="doc-grid">';
      const docTypes = [{
        key: 'ijazah',
        label: 'Ijazah / STTB'
      }, {
        key: 'rapor',
        label: 'Rapor Semester 1-5'
      }, {
        key: 'kk',
        label: 'Kartu Keluarga'
      }, {
        key: 'akta',
        label: 'Akta Kelahiran'
      }, {
        key: 'foto',
        label: 'Pas Foto 3x4'
      }];
      docTypes.forEach(dt => {
        const doc = d.documents?.find(doc => doc.document_type === dt.key);
        const isUploaded = !!doc;
        html += '<div class="doc-card-item ' + (isUploaded ? 'uploaded' : 'missing') + '">';
        html += '<div class="doc-card-icon">' + getDocIcon(dt.key) + '</div>';
        html += '<div class="doc-card-info"><strong>' + dt.label + '</strong>';
        if (isUploaded) {
          html += '<span class="doc-card-name">' + doc.file_name + '</span><span class="doc-card-size">' + formatBytes(doc.file_size) + '</span>';
        } else {
          html += '<span class="doc-card-status missing">Belum diupload</span>';
        }
        html += '</div>';
        if (isUploaded) {
          html += '<a href="/ppdb/document/' + doc.id + '/preview" target="_blank" class="doc-card-action" title="Lihat dokumen">';
          html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';
        }
        html += '</div>';
      });
      const sertifikat = d.documents?.find(doc => doc.document_type === 'sertifikat');
      if (sertifikat) {
        html += '<div class="doc-card-item uploaded">';
        html += '<div class="doc-card-icon">' + getDocIcon('sertifikat') + '</div>';
        html += '<div class="doc-card-info"><strong>Sertifikat Prestasi</strong><span class="doc-card-name">' + sertifikat.file_name + '</span><span class="doc-card-size">' + formatBytes(sertifikat.file_size) + '</span></div>';
        html += '<a href="/ppdb/document/' + sertifikat.id + '/preview" target="_blank" class="doc-card-action" title="Lihat dokumen"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';
        html += '</div>';
      }
      html += '</div></div>';
      html += '</div>';

      document.getElementById('modalName').textContent = d.full_name;
      modalBody.innerHTML = html;

      if (d.status === 'submitted') {
        modalFooter.innerHTML = '<div style="display:flex;align-items:center;gap:8px;margin-right:auto"><button class="btn btn-sm" style="background:#ef4444;color:white" onclick="closeDetail();deleteApplicant(' + d.id + ',\'' + d.full_name + '\')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>Hapus</button></div><button class="btn btn-outline" onclick="closeDetail()">Tutup</button><button class="btn" style="background:#ef4444;color:white" onclick="closeDetail();rejectApplicant(' + d.id + ',\'' + d.full_name + '\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Tolak</button><button class="btn btn-primary" onclick="closeDetail();verifyApplicant(' + d.id + ',\'' + d.full_name + '\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Validasi</button>';
        modalFooter.style.display = 'flex';
      } else if (d.status === 'verified') {
        modalFooter.innerHTML = '<button class="btn btn-outline" onclick="closeDetail()">Tutup</button><button class="btn" style="background:#ef4444;color:white" onclick="closeDetail();cancelApplicant(' + d.id + ',\'' + d.full_name + '\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px"><circle cx="12" cy="12" r="10"/><line x1="4" y1="12" x2="20" y2="12"/></svg>Batalkan</button>';
        modalFooter.style.display = 'flex';
      } else if (d.status === 'draft' || d.status === 'rejected') {
        modalFooter.innerHTML = '<div style="display:flex;align-items:center;gap:8px;margin-right:auto"><button class="btn btn-sm" style="background:#ef4444;color:white" onclick="closeDetail();deleteApplicant(' + d.id + ',\'' + d.full_name + '\')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>Hapus</button></div><button class="btn btn-outline" onclick="closeDetail()">Tutup</button>';
        modalFooter.style.display = 'flex';
      } else {
        modalFooter.style.display = 'none';
      }
    });
  }

  function getDocIcon(type) {
    const icons = {
      ijazah: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="M8 7h8"/><path d="M8 11h6"/></svg>',
      rapor: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
      kk: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
      akta: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v8"/><path d="M5 10l7 6 7-6"/></svg>',
      foto: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
      sertifikat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>'
    };
    return icons[type] || '';
  }

  function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(i > 0 ? 1 : 0) + ' ' + sizes[i];
  }

  function closeDetail() {
    document.getElementById('detailModal').classList.remove('open');
    currentApplicantId = null;
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById('detailModal').classList.contains('open')) closeDetail();
  });

  function verifyApplicant(id, name) {
    Swal.fire({
      title: 'Validasi Data Pendaftar?',
      html: '<div style="text-align:center"><div style="width:80px;height:80px;margin:0 auto 1rem;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><p>Anda akan memvalidasi data pendaftar:</p><p><strong>' + name + '</strong></p><p style="font-size:0.875rem;color:#6b7280;margin-top:0.5rem">Pastikan semua data dan dokumen telah diperiksa dengan lengkap.</p></div>',
      showCancelButton: true,
      confirmButtonColor: '#2563eb',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Validasi',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then(result => {
      if (result.isConfirmed) {
        updateApplicantStatus(id, 'verified');
      }
    });
  }

  function rejectApplicant(id, name) {
    Swal.fire({
      title: 'Tolak Pendaftar?',
      html: '<div style="text-align:center"><div style="width:80px;height:80px;margin:0 auto 1rem;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div><p>Anda akan menolak pendaftar:</p><p><strong>' + name + '</strong></p><textarea id="rejectNote" class="swal2-textarea" placeholder="Alasan penolakan (opsional)" style="width:100%;margin-top:1rem;padding:0.5rem;border:1px solid #d1d5db;border-radius:8px"></textarea></div>',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Tolak',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      preConfirm: () => {
        return document.getElementById('rejectNote').value;
      }
    }).then(result => {
      if (result.isConfirmed) {
        updateApplicantStatus(id, 'rejected', result.value);
      }
    });
  }

  function cancelApplicant(id, name) {
    Swal.fire({
      title: 'Batalkan Pendaftaran?',
      html: '<div style="text-align:center"><div style="width:80px;height:80px;margin:0 auto 1rem;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4" y1="12" x2="20" y2="12"/></svg></div><p>Batalkan pendaftaran <strong>' + name + '</strong>?</p><textarea id="cancelNote" class="swal2-textarea" placeholder="Alasan pembatalan (opsional)" style="width:100%;margin-top:1rem;padding:0.5rem;border:1px solid #d1d5db;border-radius:8px"></textarea></div>',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Batalkan',
      cancelButtonText: 'Kembali',
      reverseButtons: true,
      preConfirm: () => {
        return document.getElementById('cancelNote').value;
      }
    }).then(result => {
      if (result.isConfirmed) {
        updateApplicantStatus(id, 'rejected', result.value);
      }
    });
  }

  function deleteApplicant(id, name) {
    Swal.fire({
      title: 'Hapus Data Pendaftar?',
      html: '<div style="text-align:center"><div style="width:80px;height:80px;margin:0 auto 1rem;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></div><p>Anda akan menghapus data pendaftar:</p><p><strong>' + name + '</strong></p><p style="font-size:0.875rem;color:#6b7280;margin-top:0.5rem">Data yang dihapus tidak dapat dikembalikan. Dokumen terkait juga akan ikut terhapus.</p></div>',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then(result => {
      if (result.isConfirmed) {
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('_method', 'DELETE');

        fetch('/admin/applicants/' + id, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          })
          .then(r => r.json())
          .then(j => {
            if (j.success) {
              Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: j.message,
                  showConfirmButton: false,
                  timer: 3000
                })
                .then(() => location.reload());
            } else {
              Swal.fire('Gagal', j.message || 'Terjadi kesalahan.', 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
      }
    });
  }

  function updateApplicantStatus(id, status, note = '') {
    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('_method', 'PATCH');
    formData.append('status', status);
    if (note) formData.append('admin_note', note);

    fetch('/admin/applicants/' + id + '/status', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => r.json())
      .then(j => {
        if (j.success) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: j.message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
          }).then(() => location.reload());
        } else {
          Swal.fire('Gagal', j.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
  }

  @endif
</script>
@endpush