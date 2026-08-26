@extends('layouts.admin')

@section('title', 'Perpustakaan')

@push('styles')
<style>
  .upload-zone { transition: border-color .2s ease, background .2s ease; }
  .upload-zone:hover {
    border-color: var(--primary-2) !important;
    background: color-mix(in srgb, var(--primary-2) 8%, var(--card)) !important;
  }
</style>
@endpush

@php
$currentStatus = request('status', '');
@endphp

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen perpustakaan</span>
    <h1>Perpustakaan</h1>
    <p>Kelola katalog buku dan ebook digital. Buku terbit tampil di halaman publik, file hanya untuk siswa.</p>
  </div>
</div>

<div class="tabs" style="margin:0 0 20px">
  <a href="{{ route('admin.perpustakaan.index', array_filter(['search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === '' ? 'active' : '' }}">
    Semua <span class="tab-count">{{ $tabCounts['all'] }}</span>
  </a>
  <a href="{{ route('admin.perpustakaan.index', array_filter(['status' => 'active', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'active' ? 'active' : '' }}">
    Terbit <span class="tab-count">{{ $tabCounts['active'] }}</span>
  </a>
  <a href="{{ route('admin.perpustakaan.index', array_filter(['status' => 'inactive', 'search' => request('search')])) }}"
    class="tab-btn {{ $currentStatus === 'inactive' ? 'active' : '' }}">
    Disembunyikan <span class="tab-count">{{ $tabCounts['inactive'] }}</span>
  </a>
  <div class="portal-actions" style="margin-left:auto">
    <button type="button" class="btn btn-outline" onclick="openImportModal()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
        <polyline points="17 8 12 3 7 8" />
        <line x1="12" y1="3" x2="12" y2="15" />
      </svg>
      Import PDF
    </button>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19" />
        <line x1="5" y1="12" x2="19" y2="12" />
      </svg>
      Tambah Buku
    </button>
  </div>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap">
    @if ($currentStatus)
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    @endif
    <div class="field" style="flex:1;min-width:250px">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, atau kategori...">
    </div>
    <button class="btn btn-primary" type="submit" style="min-height:42px">Cari</button>
    @if (request('search'))
    <a href="{{ route('admin.perpustakaan.index', array_filter(['status' => $currentStatus])) }}" class="btn btn-outline" style="min-height:42px">Reset</a>
    @endif
  </form>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" id="booksTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Cover</th>
          <th>Judul</th>
          <th>Penulis</th>
          <th>Kategori</th>
          <th>Stok</th>
          <th>Tipe</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($books as $book)
        <tr id="row-{{ $book->id }}"
          data-cover-url="{{ $book->cover_path ? asset('storage/' . $book->cover_path) : '' }}"
          data-has-file-bool="{{ $book->file_path ? 'true' : 'false' }}">
          <td style="text-align:center">{{ $loop->iteration + ($books->currentPage() - 1) * $books->perPage() }}</td>
          <td>
            <div style="width:44px;height:58px">
              <x-book-cover :book="$book" size="sm" />
            </div>
          </td>
          <td><strong>{{ $book->title }}</strong></td>
          <td style="font-size:.85rem">{{ $book->author ?: '—' }}</td>
          <td>
            @if ($book->category)
            <span style="padding:4px 10px;border-radius:8px;font-size:.75rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)">{{ $book->category }}</span>
            @else
            <span style="color:var(--muted)">—</span>
            @endif
          </td>
          <td style="text-align:center">{{ $book->stock }}</td>
          <td>
            @if ($book->file_path)
            <span style="padding:4px 10px;border-radius:8px;font-size:.75rem;font-weight:700;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)"><i class="fa-solid fa-file-lines"></i> Ebook</span>
            @else
            <span style="padding:4px 10px;border-radius:8px;font-size:.75rem;font-weight:600;background:color-mix(in srgb,var(--muted) 12%,var(--card));color:var(--muted)">Katalog</span>
            @endif
          </td>
          <td>
            @if ($book->is_active)
            <span style="padding:4px 10px;border-radius:8px;font-size:.75rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Terbit</span>
            @else
            <span style="padding:4px 10px;border-radius:8px;font-size:.75rem;font-weight:700;background:color-mix(in srgb,var(--muted) 12%,var(--card));color:var(--muted)">Disembunyikan</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <button type="button" class="btn btn-outline" title="Edit buku" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center" onclick="openEditModal({{ $book->id }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 3a2.85 2.85 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                  <path d="m15 5 4 4" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="{{ $book->is_active ? 'Sembunyikan dari publik' : 'Terbitkan ke publik' }}" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:var(--success)" onclick="confirmToggle({{ $book->id }}, '{{ addslashes($book->title) }}', {{ $book->is_active ? 'true' : 'false' }})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                  <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
              </button>
              <button type="button" class="btn btn-outline" title="Hapus buku" style="min-height:32px;min-width:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;color:#ef4444" onclick="confirmDelete({{ $book->id }}, '{{ addslashes($book->title) }}')">
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
          <td colspan="9" style="text-align:center;padding:30px;color:var(--muted)">Belum ada buku.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $books->links('vendor.pagination.admin') }}</div>
</section>

{{-- Modal --}}
<div class="admin-modal-overlay" id="bookModal">
  <div class="admin-modal-box" style="max-width:640px">
    <div class="admin-modal-header">
      <h2 id="modalTitle">Tambah Buku</h2>
      <button class="admin-modal-close" onclick="closeModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="bookForm" onsubmit="submitForm(event)">
      <div class="admin-modal-body">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" id="formBookId" value="">

        <div class="field">
          <label for="m_title">Judul Buku <span style="color:#ef4444">*</span></label>
          <input id="m_title" name="title" type="text" required placeholder="Judul buku">
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="m_author">Penulis</label>
            <input id="m_author" name="author" type="text" placeholder="Nama penulis">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label for="m_category">Kategori</label>
            <input id="m_category" name="category" type="text" placeholder="mis. Fiksi, Sains, Teknologi">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="m_publisher">Penerbit</label>
            <input id="m_publisher" name="publisher" type="text">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div class="field">
              <label for="m_year">Tahun</label>
              <input id="m_year" name="year" type="text">
              <small class="field-error" style="color:#ef4444;display:none"></small>
            </div>
            <div></div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label for="m_stock">Stok</label>
            <input id="m_stock" name="stock" type="number" min="0" value="1">
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field" style="display:flex;align-items:center;gap:10px;margin-top:26px">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="m_is_active" value="1" checked style="width:18px;height:18px;accent-color:#4338ca">
            <label for="m_is_active" style="font-weight:600;color:var(--ink)">Terbitkan di publik</label>
          </div>
        </div>

        <div class="field" style="margin-top:14px">
          <label for="m_description">Deskripsi</label>
          <textarea id="m_description" name="description" rows="3" placeholder="Sinopsis singkat buku..."></textarea>
          <small class="field-error" style="color:#ef4444;display:none"></small>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
          <div class="field">
            <label>Cover Buku</label>
            <div class="upload-zone" id="coverZone" title="Klik untuk memilih gambar"
              style="border:2px dashed color-mix(in srgb,var(--primary-2) 45%,var(--line));border-radius:14px;padding:14px;cursor:pointer;text-align:center;background:color-mix(in srgb,var(--primary-2) 4%,var(--card))">
              <div id="coverEmpty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:18px 8px">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--primary-2)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                  <circle cx="9" cy="9" r="2" />
                  <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                </svg>
                <span style="font-weight:700;font-size:.9rem;color:var(--ink)">Pilih Cover</span>
                <span style="font-size:.72rem;color:var(--muted)">JPG, PNG, WEBP, SVG &bull; maks 2MB</span>
              </div>
              <div id="coverSelected" style="display:none;position:relative">
                <button type="button" id="coverRemove" title="Hapus cover" onclick="event.stopPropagation(); resetCover()"
                  style="position:absolute;top:6px;right:6px;z-index:2;width:26px;height:26px;border:none;border-radius:50%;background:rgba(15,23,42,.65);color:#fff;display:none;align-items:center;justify-content:center;cursor:pointer">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18M6 6l12 12" />
                  </svg>
                </button>
                <div style="height:150px;border-radius:10px;overflow:hidden;background:color-mix(in srgb,var(--muted) 15%,var(--card));display:flex;align-items:center;justify-content:center">
                  <img id="coverPreview" alt="Preview cover" style="width:100%;height:100%;object-fit:cover">
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:8px">
                  <span id="coverName" style="font-size:.75rem;font-weight:600;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
                  <span style="font-size:.7rem;color:var(--muted);font-weight:600;flex-shrink:0">Klik untuk ganti</span>
                </div>
              </div>
              <input id="m_cover" name="cover" type="file" accept="image/*" style="display:none">
            </div>
            <small class="field-error" style="color:#ef4444;display:none"></small>
          </div>
          <div class="field">
            <label>File Ebook (PDF/dokumen)</label>
            <div class="upload-zone" id="fileZone" title="Klik untuk memilih file"
              style="border:2px dashed color-mix(in srgb,var(--primary-2) 45%,var(--line));border-radius:14px;padding:14px;cursor:pointer;text-align:center;background:color-mix(in srgb,var(--primary-2) 4%,var(--card))">
              <div id="fileEmpty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:18px 8px">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="var(--primary-2)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                  <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                </svg>
                <span style="font-weight:700;font-size:.9rem;color:var(--ink)">Pilih file ebook</span>
                <span style="font-size:.72rem;color:var(--muted)">PDF, EPUB, DOCX &bull; maks 50MB</span>
              </div>
              <div id="fileSelected" style="display:none;align-items:center;gap:10px;border:1px solid var(--line);border-radius:10px;padding:12px;background:var(--card)">
                <div style="width:40px;height:46px;border-radius:8px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:color-mix(in srgb,var(--primary-2) 14%,var(--card))">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary-2)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <path d="M14 2v6h6" />
                  </svg>
                </div>
                <div style="flex:1;min-width:0;text-align:left">
                  <div id="fileSelectedName" style="font-weight:700;font-size:.85rem;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></div>
                  <div id="fileSelectedSize" style="font-size:.72rem;color:var(--muted)"></div>
                </div>
                <button type="button" id="fileRemove" title="Hapus file" onclick="event.stopPropagation(); resetFile()"
                  style="width:26px;height:26px;flex-shrink:0;border:none;border-radius:50%;background:color-mix(in srgb,#ef4444 12%,transparent);color:#ef4444;display:none;align-items:center;justify-content:center;cursor:pointer">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <input id="m_file" name="file" type="file" accept=".pdf,.epub,.mobi,.doc,.docx,.ppt,.pptx" style="display:none">
            </div>
            <span id="fileInfo" style="display:none;font-weight:600;font-size:.78rem;color:var(--muted);margin-top:8px"></span>
            <small class="field-help" style="color:var(--muted);font-size:.75rem">Kosongkan untuk buku katalog tanpa file digital.</small>
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

{{-- Import Modal --}}
<div class="admin-modal-overlay" id="importModal">
  <div class="admin-modal-box" style="max-width:640px">
    <div class="admin-modal-header">
      <h2>Import Banyak PDF</h2>
      <button class="admin-modal-close" onclick="closeImportModal()" type="button">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18" />
          <line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      </button>
    </div>
    <form id="importForm" onsubmit="submitImport(event)">
      <div class="admin-modal-body">
        @csrf
        <p style="font-size:.85rem;color:var(--muted);margin:0 0 14px">Pilih beberapa file PDF sekaligus. Setiap file akan jadi buku baru (judul dari nama file, cover otomatis). Maks 50MB per file.</p>
        <div class="upload-zone" id="importZone" style="border:2px dashed color-mix(in srgb,var(--primary-2) 45%,var(--line));border-radius:14px;padding:18px;cursor:pointer;text-align:center;background:color-mix(in srgb,var(--primary-2) 4%,var(--card))">
          <div id="importEmpty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--primary-2)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="17 8 12 3 7 8" />
              <line x1="12" y1="3" x2="12" y2="15" />
            </svg>
            <span style="font-weight:700;font-size:.95rem;color:var(--ink)">Seret file PDF ke sini</span>
            <span style="font-size:.75rem;color:var(--muted)">atau klik untuk memilih banyak file sekaligus</span>
          </div>
          <div id="importFilesList" style="display:none;margin-top:14px;text-align:left;max-height:220px;overflow:auto;padding:8px;background:var(--card);border-radius:8px;border:1px solid var(--line)"></div>
        </div>
        <input id="m_import_files" name="import_files[]" type="file" accept=".pdf,.epub,.mobi,.doc,.docx,.ppt,.pptx" multiple style="display:none">
        <small class="field-help" style="color:var(--muted);font-size:.75rem;display:block;margin-top:8px">File non-PDF akan dilewati. Judul diambil dari nama file.</small>
        <small class="field-error" style="color:#ef4444;display:none"></small>
      </div>
      <div class="admin-modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeImportModal()">Batal</button>
        <button type="submit" class="btn btn-primary" id="importSubmitBtn" disabled>Import</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
@vite('resources/js/pdf-cover.js')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  let autoCoverBlob = null;
  let coverUploadedManually = false;

  function openCreateModal() {
    clearErrors();
    document.getElementById('modalTitle').textContent = 'Tambah Buku';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formBookId').value = '';
    document.getElementById('bookForm').action = '{{ route("admin.perpustakaan.store") }}';
    document.getElementById('m_title').value = '';
    document.getElementById('m_author').value = '';
    document.getElementById('m_publisher').value = '';
    document.getElementById('m_category').value = '';
    document.getElementById('m_year').value = '';
    document.getElementById('m_description').value = '';
    document.getElementById('m_stock').value = '1';
    document.getElementById('m_is_active').checked = true;
    autoCoverBlob = null;
    coverUploadedManually = false;
    resetCover();
    resetFile();
    document.getElementById('bookModal').classList.add('open');
  }

  function openEditModal(bookId) {
    clearErrors();
    document.getElementById('modalTitle').textContent = 'Edit Buku';
    document.getElementById('modalSubmitBtn').textContent = 'Simpan Perubahan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formBookId').value = bookId;
    document.getElementById('bookForm').action = '/admin/perpustakaan/' + bookId;
    autoCoverBlob = null;
    coverUploadedManually = false;
    resetCover();
    resetFile();

    fetch('/admin/perpustakaan/' + bookId + '/data', {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(data => {
        document.getElementById('m_title').value = data.title;
        document.getElementById('m_author').value = data.author || '';
        document.getElementById('m_publisher').value = data.publisher || '';
        document.getElementById('m_category').value = data.category || '';
        document.getElementById('m_year').value = data.year || '';
        document.getElementById('m_description').value = data.description || '';
        document.getElementById('m_stock').value = data.stock;
        document.getElementById('m_is_active').checked = data.is_active;

        if (data.cover_url) {
          setCoverPreview(data.cover_url, '');
        }

        const fileInfo = document.getElementById('fileInfo');
        if (data.has_file) {
          fileInfo.textContent = 'File saat ini: ' + data.file_name +
            (data.file_size ? ' (' + formatBytes(data.file_size) + ')' : '') +
            '. Pilih file baru untuk mengganti.';
          fileInfo.style.display = 'block';
        } else {
          fileInfo.style.display = 'none';
        }

        document.getElementById('bookModal').classList.add('open');
      })
      .catch(() => Swal.fire('Error', 'Gagal memuat data.', 'error'));
  }

  function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  function resetCover() {
    resetAutoCoverPreview();
    document.getElementById('m_cover').value = '';
    document.getElementById('coverEmpty').style.display = 'flex';
    document.getElementById('coverSelected').style.display = 'none';
    document.getElementById('coverPreview').src = '';
    document.getElementById('coverRemove').style.display = 'none';
    document.getElementById('coverName').textContent = '';
  }

  function setCoverPreview(url, name) {
    document.getElementById('coverEmpty').style.display = 'none';
    document.getElementById('coverSelected').style.display = 'block';
    document.getElementById('coverPreview').src = url;
    document.getElementById('coverRemove').style.display = 'flex';
    document.getElementById('coverName').textContent = name || url.split('/').pop().split('?')[0];
  }

  function resetFile() {
    document.getElementById('m_file').value = '';
    document.getElementById('fileEmpty').style.display = 'flex';
    document.getElementById('fileSelected').style.display = 'none';
    document.getElementById('fileSelectedName').textContent = '';
    document.getElementById('fileSelectedSize').textContent = '';
    document.getElementById('fileRemove').style.display = 'none';
    const fi = document.getElementById('fileInfo');
    fi.textContent = '';
    fi.style.display = 'none';
  }

  function setFileState(file) {
    document.getElementById('fileEmpty').style.display = 'none';
    document.getElementById('fileSelected').style.display = 'flex';
    document.getElementById('fileSelectedName').textContent = file.name;
    document.getElementById('fileSelectedSize').textContent = formatBytes(file.size);
    document.getElementById('fileRemove').style.display = 'flex';
  }

  function generateAutoCover(file) {
    autoCoverBlob = null;
    resetAutoCoverPreview();
    setCoverPreview('', 'Membuat cover dari halaman 1 PDF...');
    window.renderPdfCover(file)
      .then((blob) => {
        autoCoverBlob = blob;
        const url = URL.createObjectURL(blob);
        setCoverPreview(url, 'Cover otomatis dari PDF');
        coverUploadedManually = false;
      })
      .catch(() => {
        autoCoverBlob = null;
        resetAutoCoverPreview();
      });
  }

  function resetAutoCoverPreview() {
    const preview = document.getElementById('coverPreview');
    if (preview && preview.src) {
      URL.revokeObjectURL(preview.src);
    }
  }

  function closeModal() {
    document.getElementById('bookModal').classList.remove('open');
    clearErrors();
  }

  function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
      el.textContent = '';
      el.style.display = 'none';
    });
    document.querySelectorAll('.admin-modal-box .field input, .admin-modal-box .field select, .admin-modal-box .field textarea').forEach(el => el.style.borderColor = '');
  }

  function showFieldError(fieldName, message) {
    const form = document.getElementById('bookForm');
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
    const form = document.getElementById('bookForm');
    const formData = new FormData(form);

    if (autoCoverBlob && !coverUploadedManually) {
      const title = (document.getElementById('m_title').value || 'buku').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
      formData.append('cover', autoCoverBlob, (title || 'buku') + '-cover.png');
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
          Swal.fire('Gagal', 'Periksa kembali isian form.', 'error');
        } else {
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => {
        Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
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
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
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

  function confirmToggle(bookId, title, isActive) {
    const willHide = isActive;
    Swal.fire({
      title: willHide ? 'Sembunyikan Buku?' : 'Terbitkan Buku?',
      html: '<strong>' + title + '</strong><br>' + (willHide ? 'Buku akan disembunyikan dari halaman publik.' : 'Buku akan tampil di halaman publik.'),
      icon: 'info',
      showCancelButton: true,
      confirmButtonColor: willHide ? '#ef4444' : '#1f8f62',
      cancelButtonColor: '#6b7280',
      confirmButtonText: willHide ? 'Sembunyikan' : 'Terbitkan',
      cancelButtonText: 'Batal',
    }).then(result => {
      if (result.isConfirmed) {
        doAction('/admin/perpustakaan/' + bookId + '/toggle', 'PATCH');
      }
    });
  }

  function confirmDelete(bookId, title) {
    Swal.fire({
      title: 'Hapus Buku?',
      html: '<strong>' + title + '</strong> akan dihapus permanen beserta file dan cover-nya.',
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
        doAction('/admin/perpustakaan/' + bookId, 'DELETE');
      }
    });
  }

  document.getElementById('m_cover').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      coverUploadedManually = true;
      autoCoverBlob = null;
      const reader = new FileReader();
      reader.onload = function(e) {
        setCoverPreview(e.target.result, file.name);
      };
      reader.readAsDataURL(file);
    } else {
      resetCover();
    }
  });

  document.getElementById('m_file').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
      setFileState(file);
      coverUploadedManually = false;
      if (window.renderPdfCover && isPdfFile(file)) {
        generateAutoCover(file);
      } else {
        autoCoverBlob = null;
        resetAutoCoverPreview();
      }
    } else {
      resetFile();
    }
  });

  ['coverZone','fileZone'].forEach(function(id) {
    const zone = document.getElementById(id);
    const input = zone.querySelector('input[type="file"]');
    zone.addEventListener('click', function(e) {
      if (e.target === zone || zone.contains(e.target)) {
        input.click();
      }
    });
    zone.addEventListener('dragover', function(e) { e.preventDefault(); zone.style.borderColor = 'var(--primary-2)'; zone.style.background = 'color-mix(in srgb,var(--primary-2) 10%,var(--card))'; });
    zone.addEventListener('dragleave', function() { zone.style.borderColor = ''; zone.style.background = ''; });
    zone.addEventListener('drop', function(e) {
      e.preventDefault();
      zone.style.borderColor = '';
      zone.style.background = '';
      const dt = e.dataTransfer.files[0];
      if (dt) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });
  document.getElementById('bookModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });

  function openImportModal() {
    importCovers = {};
    document.getElementById('importFilesList').innerHTML = '';
    document.getElementById('importEmpty').style.display = 'flex';
    document.getElementById('importFilesList').style.display = 'none';
    document.getElementById('m_import_files').value = '';
    document.getElementById('importSubmitBtn').disabled = true;
    document.getElementById('importModal').classList.add('open');
  }

  function closeImportModal() {
    document.getElementById('importModal').classList.remove('open');
  }

  let importCovers = {};

  function renderImportFiles(files) {
    const list = document.getElementById('importFilesList');
    importCovers = {};
    list.innerHTML = '';
    Array.from(files).forEach(function(f, index) {
      const item = document.createElement('div');
      item.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px 10px;border-bottom:1px solid var(--line)';
      item.innerHTML =
        '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary-2)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>' +
        '<div style="flex:1;min-width:0"><div style="font-size:.82rem;font-weight:700;color:var(--ink);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + f.name + '</div>' +
        '<div style="font-size:.7rem;color:var(--muted)">' + formatBytes(f.size) + '</div></div>' +
        '<span class="importCoverState" style="flex-shrink:0;font-size:.7rem;font-weight:600;color:var(--muted)"></span>';
      list.appendChild(item);
    });
    document.getElementById('importEmpty').style.display = 'none';
    list.style.display = 'block';
    document.getElementById('importSubmitBtn').disabled = files.length === 0;

    Array.from(files).forEach(function(f, index) {
      if (window.renderPdfCover && isPdfFile(f)) {
        const stateEl = list.querySelectorAll('.importCoverState')[index];
        stateEl.textContent = 'Membuat cover...';
        window.renderPdfCover(f)
          .then((blob) => {
            importCovers[index] = blob;
            const state = list.querySelectorAll('.importCoverState')[index];
            if (state) {
              state.textContent = 'Cover OK';
              state.style.color = '#16a34a';
            }
          })
          .catch(() => {
            const state = list.querySelectorAll('.importCoverState')[index];
            if (state) {
              state.textContent = 'Tanpa cover';
              state.style.color = 'var(--muted)';
            }
          });
      } else {
        const stateEl = list.querySelectorAll('.importCoverState')[index];
        if (stateEl) stateEl.textContent = 'Tanpa cover';
      }
    });
  }

  document.getElementById('m_import_files').addEventListener('change', function() {
    renderImportFiles(this.files);
  });

  const importZone = document.getElementById('importZone');
  const importInput = document.getElementById('m_import_files');
  importZone.addEventListener('click', function(e) {
    if (e.target === importZone || importZone.contains(e.target)) importInput.click();
  });
  importZone.addEventListener('dragover', function(e) { e.preventDefault(); importZone.style.borderColor = 'var(--primary-2)'; importZone.style.background = 'color-mix(in srgb,var(--primary-2) 10%,var(--card))'; });
  importZone.addEventListener('dragleave', function() { importZone.style.borderColor = ''; importZone.style.background = ''; });
  importZone.addEventListener('drop', function(e) {
    e.preventDefault();
    importZone.style.borderColor = '';
    importZone.style.background = '';
    importInput.files = e.dataTransfer.files;
    renderImportFiles(importInput.files);
  });
  document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) closeImportModal();
  });

  function submitImport(e) {
    e.preventDefault();
    const input = document.getElementById('m_import_files');
    if (!input.files.length) {
      Swal.fire('Pilih File', 'Pilih minimal satu file PDF terlebih dahulu.', 'warning');
      return;
    }
    const btn = document.getElementById('importSubmitBtn');
    btn.disabled = true;
    const formData = new FormData(document.getElementById('importForm'));

    Object.entries(importCovers).forEach(function([index, blob]) {
      const fileName = (document.getElementById('m_import_files').files[index]?.name || 'buku').toLowerCase().replace(/\.pdf$/i, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
      formData.append('covers[]', blob, (fileName || 'buku') + '-cover.png');
    });

    fetch('{{ route("admin.perpustakaan.import") }}', {
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
          closeImportModal();
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: json.message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
          }).then(() => location.reload());
        } else {
          btn.disabled = false;
          Swal.fire('Gagal', json.message || 'Terjadi kesalahan.', 'error');
        }
      })
      .catch(() => {
        btn.disabled = false;
        Swal.fire('Error', 'Tidak dapat terhubung ke server.', 'error');
      });
  }
</script>
@endpush
@endsection