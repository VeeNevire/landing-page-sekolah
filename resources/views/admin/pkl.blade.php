@extends('layouts.admin')

@section('title', 'PKL / Prakerin')

@php
$currentTab = request('tab', 'companies');
$currentStatus = request('status', '');
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen karir &amp; dunia kerja</span>
    <h1>PKL / Prakerin</h1>
    <p>Kelola perusahaan mitra, penempatan siswa, dan guru pembimbing untuk Praktik Kerja Lapangan.</p>
  </div>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.pkl.index', array_filter(['tab' => 'companies', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentTab === 'companies' ? 'active' : '' }}">
    Perusahaan <span class="tab-count">{{ $companyTotal ?? 0 }}</span>
  </a>
  <a href="{{ route('admin.pkl.index', array_filter(['tab' => 'placements', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentTab === 'placements' ? 'active' : '' }}">
    Penempatan Siswa <span class="tab-count">{{ $placementTotal ?? 0 }}</span>
  </a>
  <div class="portal-actions" style="margin-left:auto">
    @if ($currentTab === 'companies')
    <button type="button" class="btn btn-primary" onclick="openCompanyCreate()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      Tambah Perusahaan
    </button>
    @else
    <button type="button" class="btn btn-primary" onclick="openPlacementCreate()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      Tambah Penempatan
    </button>
    @endif
  </div>
</div>

@if ($currentTab === 'placements')
<div class="tabs" style="margin:0 0 16px">
  <a href="{{ route('admin.pkl.index', array_filter(['tab' => 'placements', 'status' => '', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === '' ? 'active' : '' }}">Semua</a>
  <a href="{{ route('admin.pkl.index', array_filter(['tab' => 'placements', 'status' => 'active', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'active' ? 'active' : '' }}">Aktif</a>
  <a href="{{ route('admin.pkl.index', array_filter(['tab' => 'placements', 'status' => 'selesai', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'selesai' ? 'active' : '' }}">Selesai</a>
  <a href="{{ route('admin.pkl.index', array_filter(['tab' => 'placements', 'status' => 'batal', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'batal' ? 'active' : '' }}">Batal</a>
</div>
@endif

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    <input type="hidden" name="tab" value="{{ $currentTab }}">
    @if ($currentStatus)
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    @endif
    <div class="field" style="flex:1;min-width:250px">
      <input type="text" name="search" value="{{ request('search') }}"
        placeholder="{{ $currentTab === 'companies' ? 'Cari nama perusahaan, bidang, kota...' : 'Cari nama siswa, NISN, atau perusahaan...' }}">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search'))
    <a href="{{ route('admin.pkl.index', array_filter(['tab' => $currentTab, 'status' => $currentStatus])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

{{-- ============ TAB PERUSAHAAN ============ --}}
@if ($currentTab === 'companies')
<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="companiesTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Perusahaan</th>
          <th>Bidang</th>
          <th>Lokasi</th>
          <th>Kontak</th>
          <th>Penempatan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($companies as $company)
        <tr id="crow-{{ $company->id }}"
          data-id="{{ $company->id }}"
          data-nama="{{ $company->nama }}"
          data-bidang="{{ $company->bidang }}"
          data-alamat="{{ $company->alamat }}"
          data-kota="{{ $company->kota }}"
          data-kontak_person="{{ $company->kontak_person }}"
          data-kontak_telepon="{{ $company->kontak_telepon }}"
          data-catatan="{{ $company->catatan }}">
          <td style="text-align:center">{{ $loop->iteration + ($companies->currentPage() - 1) * $companies->perPage() }}</td>
          <td><strong>{{ $company->nama }}</strong>
            @if ($company->catatan)
            <div style="font-size:.74rem;color:var(--muted);max-width:240px">{{ $company->catatan }}</div>
            @endif
          </td>
          <td>
            @if ($company->bidang)
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $company->bidang }}</span>
            @else
            <span style="color:var(--muted)">-</span>
            @endif
          </td>
          <td style="font-size:.85rem">
            @if ($company->alamat)
            <div>{{ $company->alamat }}</div>
            @endif
            <span style="color:var(--muted)">{{ $company->kota ?? '' }}</span>
          </td>
          <td style="font-size:.85rem">
            @if ($company->kontak_person)
            <div>{{ $company->kontak_person }}</div>
            @endif
            @if ($company->kontak_telepon)
            <span style="color:var(--muted)">{{ $company->kontak_telepon }}</span>
            @endif
          </td>
          <td style="text-align:center">
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $company->placements_count }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit perusahaan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openCompanyEdit({{ $company->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus perusahaan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmCompanyDelete({{ $company->id }}, '{{ addslashes($company->nama) }}', {{ $company->placements_count }})">
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
          <td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Belum ada perusahaan PKL terdaftar.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $companies->links('vendor.pagination.admin') }}</div>
</section>
@endif

{{-- ============ TAB PENEMPATAN ============ --}}
@if ($currentTab === 'placements')
<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="placementsTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Siswa</th>
          <th>Perusahaan</th>
          <th>Pembimbing</th>
          <th>Rentang Tanggal</th>
          <th>Status</th>
          <th>Aktivitas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($placements as $p)
        @php
          $statusColor = ['active' => '#16a34a', 'selesai' => '#2563eb', 'batal' => '#dc2626'][$p->status];
        @endphp
        <tr id="prow-{{ $p->id }}"
          data-id="{{ $p->id }}"
          data-student_id="{{ $p->student_id }}"
          data-company_id="{{ $p->company_id }}"
          data-guru_pembimbing_id="{{ $p->guru_pembimbing_id }}"
          data-start_date="{{ $p->start_date->format('Y-m-d') }}"
          data-end_date="{{ $p->end_date->format('Y-m-d') }}"
          data-status="{{ $p->status }}"
          data-catatan="{{ $p->catatan }}"
          data-activities="{{ $p->activities_count }}"
          style="{{ $p->status === 'active' ? 'background:color-mix(in srgb,var(--success) 5%,var(--card))' : '' }}">
          <td style="text-align:center">{{ $loop->iteration + ($placements->currentPage() - 1) * $placements->perPage() }}</td>
          <td>
            <strong>{{ $p->student?->full_name ?? '-' }}</strong>
            <div style="font-size:.75rem;color:var(--muted)">NISN {{ $p->student?->nisn ?? '-' }} &bull; {{ $p->student?->class_name ?? '-' }}</div>
          </td>
          <td>
            <strong>{{ $p->company?->nama ?? '-' }}</strong>
            @if ($p->company?->kota)
            <div style="font-size:.75rem;color:var(--muted)">{{ $p->company->kota }}</div>
            @endif
          </td>
          <td style="font-size:.85rem">{{ $p->guruPembimbing?->full_name ?? $p->guruPembimbing?->name ?? '<span style="color:var(--muted)">Belum ditentukan</span>' }}</td>
          <td style="font-size:.85rem">{{ $p->start_date->format('d M Y') }} &mdash; {{ $p->end_date->format('d M Y') }}</td>
          <td>
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $p->status_label }}</span>
          </td>
          <td style="text-align:center">
            <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $p->activities_count }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit penempatan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openPlacementEdit({{ $p->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus penempatan" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmPlacementDelete({{ $p->id }}, '{{ addslashes($p->student?->full_name ?? 'Siswa') }}', {{ $p->activities_count }})">
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
          <td colspan="8" style="text-align:center;padding:30px;color:var(--muted)">Belum ada penempatan PKL.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $placements->links('vendor.pagination.admin') }}</div>
</section>
@endif

{{-- ============ MODAL PERUSAHAAN ============ --}}
<div class="admin-modal-overlay" id="companyModal">
  <div class="admin-modal-box" style="max-width:520px">
    <div class="admin-modal-header">
      <h2 id="companyModalTitle">Tambah Perusahaan</h2>
      <button class="admin-modal-close" onclick="closeCompanyModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="companyForm" onsubmit="submitCompanyForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="companyFormMethod" value="POST">
        <input type="hidden" id="formCompanyId" value="">

        <div class="field">
          <label for="m_nama">Nama Perusahaan <span style="color:#ef4444">*</span></label>
          <input id="m_nama" name="nama" type="text" required placeholder="PT Maju Bersama Teknologi">
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="m_bidang">Bidang / Industri</label>
            <input id="m_bidang" name="bidang" type="text" placeholder="Teknologi Informasi">
          </div>
          <div class="field">
            <label for="m_kota">Kota</label>
            <input id="m_kota" name="kota" type="text" placeholder="Bandung">
          </div>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_alamat">Alamat</label>
          <input id="m_alamat" name="alamat" type="text" placeholder="Jl. Contoh No. 12">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="m_kontak_person">Nama Kontak</label>
            <input id="m_kontak_person" name="kontak_person" type="text" placeholder="Bapak/Ibu HRD">
          </div>
          <div class="field">
            <label for="m_kontak_telepon">No. Telepon</label>
            <input id="m_kontak_telepon" name="kontak_telepon" type="text" placeholder="0812-3456-7890">
          </div>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_catatan">Catatan</label>
          <textarea id="m_catatan" name="catatan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeCompanyModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="companySubmitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- ============ MODAL PENEMPATAN ============ --}}
<div class="admin-modal-overlay" id="placementModal">
  <div class="admin-modal-box" style="max-width:540px">
    <div class="admin-modal-header">
      <h2 id="placementModalTitle">Tambah Penempatan</h2>
      <button class="admin-modal-close" onclick="closePlacementModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="placementForm" onsubmit="submitPlacementForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="placementFormMethod" value="POST">
        <input type="hidden" id="formPlacementId" value="">

        <div class="field">
          <label for="p_student_ids">Siswa <span style="color:#ef4444">*</span> <span style="font-weight:500;color:var(--muted)">(pilih banyak)</span></label>

          <div style="border:1.5px solid var(--line);border-radius:10px;overflow:hidden;background:var(--card)">
            <div style="display:flex;gap:8px;flex-wrap:wrap;padding:10px;border-bottom:1px solid var(--line);background:color-mix(in srgb,var(--muted) 4%,var(--card))">
              <select id="p_filter_program" style="flex:1;min-width:140px;padding:8px 10px;border-radius:8px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.82rem;font-family:inherit">
                <option value="">Semua Jurusan</option>
              </select>
              <select id="p_filter_class" style="flex:1;min-width:140px;padding:8px 10px;border-radius:8px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.82rem;font-family:inherit">
                <option value="">Semua Kelas</option>
              </select>
              <input id="p_filter_search" type="text" placeholder="Cari nama / NISN..."
                style="flex:2;min-width:180px;padding:8px 10px;border-radius:8px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.82rem;font-family:inherit">
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;padding:8px 10px;border-bottom:1px solid var(--line)">
              <label style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--muted);cursor:pointer">
                <input type="checkbox" id="p_show_placed" style="accent-color:var(--primary-2)">
                Tampilkan siswa yang sudah ber-PKL aktif
              </label>
              <span id="p_selected_count" style="font-size:.78rem;font-weight:700;color:var(--primary-2)">0 dipilih</span>
            </div>

            <div id="p_student_list" style="max-height:260px;overflow-y:auto">
              <div style="padding:18px;text-align:center;color:var(--muted);font-size:.82rem">Memuat daftar siswa...</div>
            </div>
          </div>

          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="p_company_id">Perusahaan <span style="color:#ef4444">*</span></label>
          <select id="p_company_id" name="company_id" required>
            <option value="">-- Pilih Perusahaan --</option>
            @foreach ($allCompanies as $c)
            <option value="{{ $c->id }}">{{ $c->nama }}@if($c->kota) ({{ $c->kota }})@endif</option>
            @endforeach
          </select>
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="p_guru_pembimbing_id">Guru Pembimbing</label>
          <select id="p_guru_pembimbing_id" name="guru_pembimbing_id">
            <option value="">-- Pilih Guru Pembimbing --</option>
            @foreach ($teachers as $t)
            <option value="{{ $t->id }}">{{ $t->full_name ?? $t->name }}</option>
            @endforeach
          </select>
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="p_start_date">Tanggal Mulai <span style="color:#ef4444">*</span></label>
            <input id="p_start_date" name="start_date" type="date" required>
          </div>
          <div class="field">
            <label for="p_end_date">Tanggal Selesai <span style="color:#ef4444">*</span></label>
            <input id="p_end_date" name="end_date" type="date" required>
          </div>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="p_status">Status</label>
          <select id="p_status" name="status">
            <option value="active">Aktif</option>
            <option value="selesai">Selesai</option>
            <option value="batal">Batal</option>
          </select>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="p_catatan">Catatan</label>
          <textarea id="p_catatan" name="catatan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
        </div>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closePlacementModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="placementSubmitBtn">Simpan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const currentTab = '{{ $currentTab }}';

  {{-- ---------- Perusahaan ---------- --}}
  function openCompanyCreate() {
    document.getElementById('companyModalTitle').textContent = 'Tambah Perusahaan';
    document.getElementById('companySubmitBtn').textContent = 'Simpan';
    document.getElementById('companyFormMethod').value = 'POST';
    document.getElementById('formCompanyId').value = '';
    document.getElementById('companyForm').action = '{{ route("admin.pkl.companies.store") }}';
    ['m_nama','m_bidang','m_kota','m_alamat','m_kontak_person','m_kontak_telepon','m_catatan'].forEach(id => document.getElementById(id).value = '');
    clearCompanyErrors();
    document.getElementById('companyModal').classList.add('open');
  }

  function openCompanyEdit(companyId) {
    clearCompanyErrors();
    document.getElementById('companyModalTitle').textContent = 'Edit Perusahaan';
    document.getElementById('companySubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('companyFormMethod').value = 'PUT';
    document.getElementById('formCompanyId').value = companyId;
    document.getElementById('companyForm').action = '/admin/pkl/companies/' + companyId;

    fetch('/admin/pkl/companies/' + companyId + '/data', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(data => {
        document.getElementById('m_nama').value = data.nama || '';
        document.getElementById('m_bidang').value = data.bidang || '';
        document.getElementById('m_kota').value = data.kota || '';
        document.getElementById('m_alamat').value = data.alamat || '';
        document.getElementById('m_kontak_person').value = data.kontak_person || '';
        document.getElementById('m_kontak_telepon').value = data.kontak_telepon || '';
        document.getElementById('m_catatan').value = data.catatan || '';
        document.getElementById('companyModal').classList.add('open');
      })
      .catch(() => Swal.fire('Error', 'Gagal memuat data.', 'error'));
  }

  function closeCompanyModal() {
    document.getElementById('companyModal').classList.remove('open');
    clearCompanyErrors();
  }

  function clearCompanyErrors() {
    document.querySelectorAll('#companyForm .field-error').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
    document.querySelectorAll('#companyForm .field input, #companyForm .field textarea').forEach(el => el.style.borderColor = '');
  }

  function showCompanyFieldError(fieldName, message) {
    const input = document.getElementById('companyForm').querySelector('[name="' + fieldName + '"]');
    if (input) {
      input.style.borderColor = '#ef4444';
      const errorEl = input.closest('.field').querySelector('.field-error');
      if (errorEl) { errorEl.textContent = message; errorEl.style.display = 'block'; }
    }
  }

  function submitCompanyForm(e) {
    e.preventDefault();
    clearCompanyErrors();
    const form = document.getElementById('companyForm');
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
      })
      .then(r => r.json().then(json => ({ ok: r.ok, json })))
      .then(({ ok, json }) => {
        if (ok && json.success) {
          closeCompanyModal();
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 })
            .then(() => location.reload());
        } else if (json.errors) {
          Object.entries(json.errors).forEach(([field, messages]) => showCompanyFieldError(field, messages[0]));
        } else {
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
  }

  function confirmCompanyDelete(companyId, nama, placementsCount) {
    let related = '';
    if (placementsCount > 0) {
      related = '<p style="color:#b45309;margin-top:8px;font-size:.8rem;text-align:left">Perusahaan ini memiliki <strong>' + placementsCount + ' penempatan</strong>.<br>Perusahaan berdata tidak dapat dihapus untuk melindungi data historis.</p>';
    }
    Swal.fire({
      title: 'Hapus Perusahaan?',
      html: 'Perusahaan <strong>' + nama + '</strong> akan dihapus permanen.' + related,
      icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
      confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        fetch('/admin/pkl/companies/' + companyId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
          })
          .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
          .then(json => {
            if (json.success) {
              Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 }).then(() => location.reload());
            } else {
              Swal.fire('Gagal', json.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
      }
    });
  }

  {{-- ---------- Penempatan ---------- --}}
  let placementMode = 'create';
  let placementCurrentStudentId = null;
  let placementStudentsData = [];
  let placementSelected = new Set();

  function openPlacementCreate() {
    placementMode = 'create';
    placementCurrentStudentId = null;
    document.getElementById('placementModalTitle').textContent = 'Tambah Penempatan';
    document.getElementById('placementSubmitBtn').textContent = 'Simpan';
    document.getElementById('placementFormMethod').value = 'POST';
    document.getElementById('formPlacementId').value = '';
    document.getElementById('placementForm').action = '{{ route("admin.pkl.placements.store") }}';
    document.getElementById('p_company_id').value = '';
    document.getElementById('p_guru_pembimbing_id').value = '';
    document.getElementById('p_start_date').value = '';
    document.getElementById('p_end_date').value = '';
    document.getElementById('p_status').value = 'active';
    document.getElementById('p_catatan').value = '';
    document.getElementById('p_show_placed').checked = false;
    document.getElementById('p_filter_program').value = '';
    document.getElementById('p_filter_class').value = '';
    document.getElementById('p_filter_search').value = '';
    placementSelected.clear();
    clearPlacementErrors();
    loadPlacementStudents().then(() => {
      renderPlacementFilterOptions();
      renderPlacementStudentList();
    });
    document.getElementById('placementModal').classList.add('open');
  }

  function openPlacementEdit(placementId) {
    clearPlacementErrors();
    document.getElementById('placementModalTitle').textContent = 'Edit Penempatan';
    document.getElementById('placementSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('placementFormMethod').value = 'PUT';
    document.getElementById('formPlacementId').value = placementId;
    document.getElementById('placementForm').action = '/admin/pkl/placements/' + placementId;

    fetch('/admin/pkl/placements/' + placementId + '/data', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(data => {
        placementMode = 'edit';
        placementCurrentStudentId = data.student_id;
        placementSelected.clear();
        if (data.student_id) placementSelected.add(String(data.student_id));
        document.getElementById('p_company_id').value = data.company_id || '';
        document.getElementById('p_guru_pembimbing_id').value = data.guru_pembimbing_id || '';
        document.getElementById('p_start_date').value = data.start_date;
        document.getElementById('p_end_date').value = data.end_date;
        document.getElementById('p_status').value = data.status;
        document.getElementById('p_catatan').value = data.catatan || '';
        document.getElementById('p_show_placed').checked = true;
        document.getElementById('p_filter_program').value = '';
        document.getElementById('p_filter_class').value = '';
        document.getElementById('p_filter_search').value = '';
        loadPlacementStudents().then(() => {
          renderPlacementFilterOptions();
          renderPlacementStudentList();
        });
        document.getElementById('placementModal').classList.add('open');
      })
      .catch(() => Swal.fire('Error', 'Gagal memuat data.', 'error'));
  }

  function loadPlacementStudents() {
    return fetch('{{ route("admin.pkl.students.data") }}', {
        headers: { 'Accept': 'application/json' }
      })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(data => { placementStudentsData = data.students || []; })
      .catch(() => Swal.fire('Error', 'Gagal memuat daftar siswa.', 'error'));
  }

  function renderPlacementFilterOptions() {
    const programEl = document.getElementById('p_filter_program');
    const classEl = document.getElementById('p_filter_class');
    const selectedProgram = programEl.value;
    const selectedClass = classEl.value;

    const programs = [...new Set(placementStudentsData.map(s => s.program_name).filter(Boolean))].sort();
    const classes = [...new Set(
      placementStudentsData
        .filter(s => !selectedProgram || s.program_name === selectedProgram)
        .map(s => s.class_name)
        .filter(Boolean)
    )].sort();

    programEl.innerHTML = '<option value="">Semua Jurusan</option>' + programs.map(p => '<option value="' + p.replace(/"/g, '&quot;') + '">' + p + '</option>').join('');
    classEl.innerHTML = '<option value="">Semua Kelas</option>' + classes.map(c => '<option value="' + c.replace(/"/g, '&quot;') + '">' + c + '</option>').join('');

    if (classes.includes(selectedClass)) classEl.value = selectedClass;
  }

  function filteredPlacementStudents() {
    const program = document.getElementById('p_filter_program').value;
    const className = document.getElementById('p_filter_class').value;
    const search = document.getElementById('p_filter_search').value.toLowerCase().trim();
    const showPlaced = document.getElementById('p_show_placed').checked;

    return placementStudentsData.filter(s => {
      if (program && s.program_name !== program) return false;
      if (className && s.class_name !== className) return false;
      if (search && !(s.full_name.toLowerCase().includes(search) || (s.nisn || '').includes(search))) return false;
      if (!showPlaced && s.has_active_placement && s.id !== placementCurrentStudentId) return false;
      return true;
    });
  }

  function renderPlacementStudentList() {
    const listEl = document.getElementById('p_student_list');
    const visible = filteredPlacementStudents();

    if (visible.length === 0) {
      listEl.innerHTML = '<div style="padding:18px;text-align:center;color:var(--muted);font-size:.82rem">Tidak ada siswa yang cocok.</div>';
      updatePlacementSelectedCount();
      return;
    }

    let html = '';
    visible.forEach(s => {
      const selected = placementSelected.has(String(s.id));
      const disabled = s.has_active_placement && (placementMode === 'create' || s.id !== placementCurrentStudentId);
      const checked = selected ? 'checked' : '';
      const dis = disabled ? 'disabled' : '';
      const opacity = disabled ? 'opacity:.55;' : '';
      html += '<label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-bottom:1px solid color-mix(in srgb,var(--line) 60%,transparent);cursor:pointer;' + opacity + '">';
      html += '<input type="checkbox" name="student_ids[]" value="' + s.id + '" ' + checked + ' ' + dis + ' style="width:16px;height:16px;accent-color:var(--primary-2);flex-shrink:0" onchange="onPlacementStudentToggle(this)">';
      html += '<span style="flex:1;min-width:0">';
      html += '<span style="display:block;font-size:.84rem;font-weight:600;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + s.full_name + '</span>';
      html += '<span style="display:block;font-size:.74rem;color:var(--muted)">NISN ' + (s.nisn || '-');
      if (s.class_name) html += ' &bull; ' + s.class_name;
      if (s.program_name) html += ' &bull; ' + s.program_name;
      html += '</span></span>';
      if (s.has_active_placement) {
        html += '<span style="flex-shrink:0;padding:2px 8px;border-radius:99px;font-size:.68rem;font-weight:700;background:#d9770618;color:#d97706">Sudah ber-PKL</span>';
      }
      html += '</label>';
    });

    listEl.innerHTML = html;
    updatePlacementSelectedCount();
  }

  function onPlacementStudentToggle(cb) {
    const id = String(cb.value);
    if (cb.checked) {
      if (placementMode === 'edit') {
        placementSelected.clear();
        document.querySelectorAll('#p_student_list input[name="student_ids[]"]').forEach(other => {
          if (other !== cb) other.checked = false;
        });
      }
      placementSelected.add(id);
    } else {
      placementSelected.delete(id);
    }
    updatePlacementSelectedCount();
  }

  function updatePlacementSelectedCount() {
    document.getElementById('p_selected_count').textContent = placementSelected.size + ' dipilih';
  }

  function closePlacementModal() {
    document.getElementById('placementModal').classList.remove('open');
    clearPlacementErrors();
  }

  function clearPlacementErrors() {
    document.querySelectorAll('#placementForm .field-error').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
    document.querySelectorAll('#placementForm .field input, #placementForm .field select, #placementForm .field textarea').forEach(el => el.style.borderColor = '');
  }

  function showPlacementFieldError(fieldName, message) {
    let input;
    if (fieldName === 'student_ids') {
      input = document.getElementById('p_student_list');
      input.closest('.field').querySelector('.field-error').textContent = message;
      input.closest('.field').querySelector('.field-error').style.display = 'block';
      input.style.borderColor = '#ef4444';
      return;
    }
    input = document.getElementById('placementForm').querySelector('[name="' + fieldName + '"]');
    if (input) {
      input.style.borderColor = '#ef4444';
      const errorEl = input.closest('.field').querySelector('.field-error');
      if (errorEl) { errorEl.textContent = message; errorEl.style.display = 'block'; }
    }
  }

  function submitPlacementForm(e) {
    e.preventDefault();
    clearPlacementErrors();

    if (placementSelected.size === 0) {
      showPlacementFieldError('student_ids', 'Pilih minimal satu siswa.');
      return;
    }

    const form = document.getElementById('placementForm');
    const formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
      })
      .then(r => r.json().then(json => ({ ok: r.ok, json })))
      .then(({ ok, json }) => {
        if (ok && json.success) {
          closePlacementModal();
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 4000 })
            .then(() => location.reload());
        } else if (json.errors) {
          Object.entries(json.errors).forEach(([field, messages]) => showPlacementFieldError(field, messages[0]));
        } else {
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
  }

  function confirmPlacementDelete(placementId, name, activitiesCount) {
    let related = '';
    if (activitiesCount > 0) {
      related = '<p style="color:#b45309;margin-top:8px;font-size:.8rem;text-align:left">Penempatan ini memiliki <strong>' + activitiesCount + ' aktivitas</strong>.<br>Penempatan berdata tidak dapat dihapus untuk melindungi data historis.</p>';
    }
    Swal.fire({
      title: 'Hapus Penempatan?',
      html: 'Penempatan <strong>' + name + '</strong> akan dihapus permanen.' + related,
      icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
      confirmButtonText: 'Hapus', cancelButtonText: 'Batal'
    }).then(result => {
      if (result.isConfirmed) {
        fetch('/admin/pkl/placements/' + placementId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
          })
          .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
          .then(json => {
            if (json.success) {
              Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 }).then(() => location.reload());
            } else {
              Swal.fire('Gagal', json.message, 'error');
            }
          })
          .catch(() => Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error'));
      }
    });
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeCompanyModal(); closePlacementModal(); }
  });
  document.getElementById('companyModal').addEventListener('click', function(e) { if (e.target === this) closeCompanyModal(); });
  document.getElementById('placementModal').addEventListener('click', function(e) { if (e.target === this) closePlacementModal(); });

  document.getElementById('p_filter_program').addEventListener('change', function () {
    renderPlacementFilterOptions();
    renderPlacementStudentList();
  });
  document.getElementById('p_filter_class').addEventListener('change', renderPlacementStudentList);
  document.getElementById('p_show_placed').addEventListener('change', renderPlacementStudentList);
  document.getElementById('p_filter_search').addEventListener('input', renderPlacementStudentList);
</script>
@endpush
@endsection
