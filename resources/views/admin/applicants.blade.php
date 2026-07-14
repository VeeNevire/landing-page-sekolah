@extends('layouts.admin')

@section('title', 'Pendaftar PPDB')
@php
$statusLabels = ['draft' => 'Draft', 'submitted' => 'Terkirim', 'verified' => 'Terverifikasi', 'paid' => 'Lunas', 'rejected' => 'Ditolak'];
$stepLabels = ['not_started' => 'Belum Mulai', 'student_data' => 'Data Siswa', 'parent_data' => 'Data Ortu', 'documents' => 'Upload Berkas', 'completed' => 'Selesai'];
$stepPercent = ['not_started' => 0, 'student_data' => 33, 'parent_data' => 66, 'documents' => 90, 'completed' => 100];
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen PPDB</span>
    <h1>Pendaftar PPDB</h1>
    <p>Kelola data pendaftar, verifikasi dokumen, dan proses penerimaan siswa baru.</p>
  </div>
</div>

<div class="admin-filter-toolbar">
  <div class="filter-tabs">
    <a href="{{ route('admin.applicants.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">
      Semua
      <span class="filter-tab-count">{{ $statusCounts['all'] }}</span>
    </a>
    @foreach (['draft', 'submitted', 'verified', 'paid', 'rejected'] as $s)
    <a href="{{ route('admin.applicants.index', ['status' => $s]) }}" class="filter-tab {{ request('status') === $s ? 'active' : '' }}">
      {{ $statusLabels[$s] }}
      <span class="filter-tab-count">{{ $statusCounts[$s] }}</span>
    </a>
    @endforeach
  </div>
  <form method="GET" action="{{ route('admin.applicants.index') }}" class="search-box">
    <input type="text" name="search" class="search-input" placeholder="Cari nama atau asal sekolah..." value="{{ request('search') }}">
    <button class="btn btn-outline btn-sm" type="submit">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"/>
        <path d="m21 21-4.35-4.35"/>
      </svg>
    </button>
  </form>
</div>

<div class="admin-table-container">
  <table class="admin-table">
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
                   style="width: {{ $stepPercent[$a->completion_step] }}%"></div>
            </div>
            <span class="progress-label">{{ $stepLabels[$a->completion_step] }}</span>
          </div>
        </td>
        <td>
          <div class="action-buttons">
            @if($a->status === 'submitted')
              <button class="action-btn action-btn-primary" 
                      onclick="verifyApplicant({{ $a->id }}, '{{ $a->full_name }}')" 
                      title="Validasi Data">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
              </button>
              <button class="action-btn action-btn-danger" 
                      onclick="rejectApplicant({{ $a->id }}, '{{ $a->full_name }}')" 
                      title="Tolak">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </button>
            @endif

            @if($a->status === 'verified')
              <button class="action-btn action-btn-danger" 
                      onclick="cancelApplicant({{ $a->id }}, '{{ $a->full_name }}')" 
                      title="Batalkan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="4" y1="12" x2="20" y2="12" />
                </svg>
              </button>
            @endif

            <button class="action-btn" 
                    onclick="showDetail({{ $a->id }})" 
                    title="Lihat Detail">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
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

<div style="margin-top:1rem">{{ $applicants->links() }}</div>

<div id="detailModal" class="applicant-detail-modal" onclick="if(event.target===this)closeDetail()">
  <div class="modal-content">
    <div class="modal-header">
      <div class="modal-header-content">
        <h3 id="modalName">Detail Pendaftar</h3>
        <div class="modal-status-bar" id="modalStatusBar">
          <!-- Status badge & progress will be inserted here -->
        </div>
      </div>
      <button class="btn btn-outline btn-sm" onclick="closeDetail()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"/>
          <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
      </button>
    </div>
    <div class="modal-body" id="modalBody">
      <!-- Dynamic content -->
    </div>
    <div class="modal-footer" id="modalFooter" style="display:none;">
      <!-- Action buttons will be inserted here -->
    </div>
  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const STATUS_LABELS = @json($statusLabels);
  const STEP_LABELS = @json($stepLabels);
  const STEP_PERCENT = @json($stepPercent);
  let currentApplicantId = null;
  let currentApplicantStatus = null;

  function showDetail(id) {
    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');
    const modalStatusBar = document.getElementById('modalStatusBar');
    
    modalBody.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--muted)">Memuat data...</div>';
    modalStatusBar.innerHTML = '';
    modalFooter.style.display = 'none';
    document.getElementById('detailModal').classList.add('open');
    
    fetch('{{ url("admin/applicants") }}/' + id + '/data')
      .then(r => r.json())
      .then(d => {
        currentApplicantId = id;
        currentApplicantStatus = d.status;
        
        // Build status bar
        const statusBadgeClass = d.status;
        const statusLabel = STATUS_LABELS[d.status];
        const completionPercent = STEP_PERCENT[d.completion_step];
        const completionLabel = STEP_LABELS[d.completion_step];
        
        let statusBarHtml = '<span class="status-badge ' + statusBadgeClass + '">' + statusLabel + '</span>';
        statusBarHtml += '<div class="applicant-progress" style="flex:1;max-width:300px;">';
        statusBarHtml += '<div class="progress-bar-wrap">';
        statusBarHtml += '<div class="progress-bar-fill ' + (d.completion_step === 'completed' ? 'completed' : '') + '" style="width:' + completionPercent + '%"></div>';
        statusBarHtml += '</div>';
        statusBarHtml += '<span class="progress-label">' + completionLabel + '</span>';
        statusBarHtml += '</div>';
        
        modalStatusBar.innerHTML = statusBarHtml;
        
        // Build sections with icons
        const sections = [
          {
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
        sections.forEach(section => {
          html += '<div class="detail-section">';
          html += '<h4 class="detail-section-title" data-icon="' + section.icon + '">' + section.title + '</h4>';
          section.fields.forEach(([key, label]) => {
            const value = d[key];
            html += '<div class="detail-row">';
            html += '<div class="detail-label">' + label + '</div>';
            html += '<div class="detail-value' + (!value ? ' empty' : '') + '">' + (value || 'Tidak diisi') + '</div>';
            html += '</div>';
          });
          html += '</div>';
        });

        // Dokumen section
        html += '<div class="detail-section">';
        html += '<h4 class="detail-section-title" data-icon="📁">Dokumen</h4>';
        html += '<div class="doc-grid">';

        const docTypes = [
          { key: 'ijazah', label: 'Ijazah / STTB' },
          { key: 'rapor', label: 'Rapor Semester 1-5' },
          { key: 'kk', label: 'Kartu Keluarga' },
          { key: 'akta', label: 'Akta Kelahiran' },
          { key: 'foto', label: 'Pas Foto 3x4' }
        ];

        docTypes.forEach(dt => {
          const doc = d.documents?.find(doc => doc.document_type === dt.key);
          const isUploaded = !!doc;

          html += '<div class="doc-card-item ' + (isUploaded ? 'uploaded' : 'missing') + '">';
          html += '<div class="doc-card-icon">' + getDocIcon(dt.key) + '</div>';
          html += '<div class="doc-card-info">';
          html += '<strong>' + dt.label + '</strong>';
          if (isUploaded) {
            html += '<span class="doc-card-name">' + doc.file_name + '</span>';
            html += '<span class="doc-card-size">' + formatBytes(doc.file_size) + '</span>';
          } else {
            html += '<span class="doc-card-status missing">Belum diupload</span>';
          }
          html += '</div>';
          if (isUploaded) {
            html += '<a href="/storage/' + doc.file_path + '" target="_blank" class="doc-card-action" title="Lihat dokumen">';
            html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            html += '</a>';
          }
          html += '</div>';
        });

        // Sertifikat optional - hanya tampil jika diupload
        const sertifikat = d.documents?.find(doc => doc.document_type === 'sertifikat');
        if (sertifikat) {
          html += '<div class="doc-card-item uploaded">';
          html += '<div class="doc-card-icon">' + getDocIcon('sertifikat') + '</div>';
          html += '<div class="doc-card-info"><strong>Sertifikat Prestasi</strong><span class="doc-card-name">' + sertifikat.file_name + '</span><span class="doc-card-size">' + formatBytes(sertifikat.file_size) + '</span></div>';
          html += '<a href="/storage/' + sertifikat.file_path + '" target="_blank" class="doc-card-action" title="Lihat dokumen">';
          html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';
          html += '</div>';
        }

        html += '</div></div>'; // close doc-grid + detail-section
        html += '</div>'; // close detail-grid

        document.getElementById('modalName').textContent = d.full_name;
        modalBody.innerHTML = html;
        
        // Show action buttons based on status
        if (d.status === 'submitted') {
          modalFooter.innerHTML = `
            <div style="display:flex;align-items:center;gap:8px;margin-right:auto">
              <button class="btn btn-sm" style="background:#ef4444;color:white" onclick="deleteApplicantFromModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px">
                  <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                </svg>
                Hapus
              </button>
            </div>
            <button class="btn btn-outline" onclick="closeDetail()">Tutup</button>
            <button class="btn" style="background:#ef4444;color:white" onclick="rejectApplicantFromModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
              Tolak
            </button>
            <button class="btn btn-primary" onclick="verifyApplicantFromModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              Validasi
            </button>
          `;
          modalFooter.style.display = 'flex';
        } else if (d.status === 'verified') {
          modalFooter.innerHTML = `
            <button class="btn btn-outline" onclick="closeDetail()">Tutup</button>
            <button class="btn" style="background:#ef4444;color:white" onclick="cancelApplicantFromModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px">
                <circle cx="12" cy="12" r="10" />
                <line x1="4" y1="12" x2="20" y2="12" />
              </svg>
              Batalkan
            </button>
          `;
          modalFooter.style.display = 'flex';
        } else if (d.status === 'draft' || d.status === 'rejected') {
          modalFooter.innerHTML = `
            <div style="display:flex;align-items:center;gap:8px;margin-right:auto">
              <button class="btn btn-sm" style="background:#ef4444;color:white" onclick="deleteApplicantFromModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px">
                  <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                </svg>
                Hapus
              </button>
            </div>
            <button class="btn btn-outline" onclick="closeDetail()">Tutup</button>
          `;
          modalFooter.style.display = 'flex';
        } else {
          modalFooter.style.display = 'none';
        }
      })
      .catch(err => {
        modalBody.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--danger)">Gagal memuat data. Silakan coba lagi.</div>';
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
    currentApplicantStatus = null;
  }

  function verifyApplicantFromModal() {
    if (!currentApplicantId) return;
    closeDetail();
    const name = document.getElementById('modalName').textContent;
    verifyApplicant(currentApplicantId, name);
  }

  function rejectApplicantFromModal() {
    if (!currentApplicantId) return;
    closeDetail();
    const name = document.getElementById('modalName').textContent;
    rejectApplicant(currentApplicantId, name);
  }

  function cancelApplicantFromModal() {
    if (!currentApplicantId) return;
    closeDetail();
    const name = document.getElementById('modalName').textContent;
    cancelApplicant(currentApplicantId, name);
  }

  function deleteApplicantFromModal() {
    if (!currentApplicantId) return;
    closeDetail();
    const name = document.getElementById('modalName').textContent;
    deleteApplicant(currentApplicantId, name);
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

        fetch('{{ url("admin/applicants") }}/' + id, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(j => {
          if (j.success) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: j.message, showConfirmButton: false, timer: 3000 })
            .then(() => location.reload());
          } else {
            Swal.fire('Gagal', j.message || 'Terjadi kesalahan.', 'error');
          }
        })
        .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
      }
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById('detailModal').classList.contains('open')) {
      closeDetail();
    }
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

  function updateApplicantStatus(id, status, note) {
    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('_method', 'PATCH');
    formData.append('status', status);
    if (note) formData.append('admin_note', note);

    fetch('{{ url("admin/applicants") }}/' + id + '/status', {
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
</script>
@endsection
