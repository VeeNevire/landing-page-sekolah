@extends('layouts.guru')

@section('title', 'Materi & Modul')

@push('styles')
<style>
.lms-tabs{display:flex;gap:4px;margin-bottom:20px;border-bottom:1.5px solid var(--line);padding-bottom:2px}
.lms-tab{padding:8px 18px;border-radius:8px 8px 0 0;font-size:.84rem;font-weight:600;cursor:pointer;color:var(--muted);border:none;background:none;transition:all .15s}
.lms-tab:hover{color:var(--ink);background:color-mix(in srgb,var(--primary-4) 6%,transparent)}
.lms-tab.active{color:var(--primary);background:color-mix(in srgb,var(--primary-4) 12%,transparent);box-shadow:inset 0 -2px 0 var(--primary)}
.lms-tab-content{display:none}
.lms-tab-content.active{display:block}

.module-card{border:1.5px solid var(--line);border-radius:14px;overflow:hidden;margin-bottom:12px;transition:border-color .15s}
.module-card:hover{border-color:var(--primary-3)}
.module-header{display:flex;align-items:center;gap:10px;padding:12px 14px;cursor:pointer;background:var(--card)}
.module-header .drag-handle{color:var(--muted);cursor:grab;opacity:.4;display:flex}
.module-header:hover .drag-handle{opacity:1}
.module-header-title{flex:1;min-width:0}
.module-header-title strong{display:block;font-size:.9rem;color:var(--ink)}
.module-header-title span{font-size:.75rem;color:var(--muted)}
.module-body{padding:8px 14px 14px 40px;display:none;border-top:1px solid var(--line);background:color-mix(in srgb,var(--primary-4) 4%,transparent)}
.module-body.open{display:block}
.module-body .material-item{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;margin-bottom:4px;transition:background .15s}
.module-body .material-item:hover{background:color-mix(in srgb,var(--primary-4) 8%,transparent)}
.module-body .material-item .mat-icon{flex-shrink:0;width:28px;height:28px;border-radius:8px;display:grid;place-items:center;font-size:.8rem}
.module-body .material-item .mat-info{flex:1;min-width:0}
.module-body .material-item .mat-info strong{font-size:.82rem;color:var(--ink);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.module-body .material-item .mat-info small{font-size:.72rem;color:var(--muted)}
.module-body .material-item .mat-actions{display:flex;gap:4px}
.module-body .material-item .mat-actions button,
.module-body .material-item .mat-actions a{width:28px;height:28px;border-radius:7px;border:none;background:none;cursor:pointer;display:grid;place-items:center;color:var(--muted);font-size:.75rem;transition:all .15s;text-decoration:none}
.module-body .material-item .mat-actions button:hover,
.module-body .material-item .mat-actions a:hover{background:color-mix(in srgb,var(--primary-4) 10%,transparent);color:var(--primary)}

.module-add-form{display:flex;gap:8px;margin-bottom:16px}
.module-add-form input{flex:1;padding:8px 12px;border-radius:8px;border:1.5px solid var(--line);font-size:.84rem;background:var(--card);color:var(--ink)}
.module-add-form input:focus{outline:none;border-color:var(--primary-3)}

.file-upload-zone{border:2px dashed var(--line);border-radius:14px;padding:30px;text-align:center;cursor:pointer;transition:all .2s;margin-bottom:14px}
.file-upload-zone:hover{border-color:var(--primary-3);background:color-mix(in srgb,var(--primary-4) 6%,transparent)}
.file-upload-zone.has-file{border-color:#22c55e;background:color-mix(in srgb,#22c55e 6%,transparent)}
.file-upload-zone .upload-icon{margin-bottom:8px;display:grid;place-items:center}
.file-upload-zone p{margin:0;font-size:.85rem;color:var(--muted)}
.file-upload-zone .file-name{font-weight:600;color:var(--ink);margin-top:4px}

.type-toggle{display:flex;gap:6px;margin-bottom:14px}
.type-toggle button{flex:1;padding:8px;border-radius:8px;border:1.5px solid var(--line);background:var(--card);cursor:pointer;font-size:.8rem;font-weight:600;color:var(--muted);transition:all .15s}
.type-toggle button.active{border-color:var(--primary);color:var(--primary);background:color-mix(in srgb,var(--primary-4) 10%,transparent)}

.ta-selector-bar{display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap}
.ta-selector-bar select{min-width:280px;padding:8px 12px;border-radius:10px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.85rem}
.ta-selector-bar select:focus{outline:none;border-color:var(--primary-3)}
.ta-selector-bar .ta-info{font-size:.82rem;color:var(--muted)}

.empty-module{padding:30px;text-align:center;color:var(--muted)}
.empty-module p{margin:4px 0}
</style>
@endpush

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>Materi & Modul</h1>
    <p>Kelola modul dan materi pembelajaran untuk setiap kelas.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

{{-- TA Selector --}}
<div class="ta-selector-bar">
  <label style="font-weight:600;font-size:.85rem;color:var(--ink)">Kelas & Mapel</label>
  <select id="ta-select" onchange="window.location='{{ route('guru.materi') }}?ta_id='+this.value">
    <option value="">— Pilih Kelas & Mapel —</option>
    @foreach ($pairs as $pair)
      <option value="{{ $pair['assignment_id'] }}" @selected($selectedTa && $selectedTa->id == $pair['assignment_id'])>
        {{ $pair['class_name'] }} — {{ $pair['subject_name'] }}
      </option>
    @endforeach
  </select>
  @if ($selectedTa)
    <span class="ta-info">Materi: {{ $materials->count() }} | Modul: {{ $modules->count() }}</span>
  @endif
</div>

@if ($selectedTa)
{{-- Tabs --}}
<div class="lms-tabs">
  <button class="lms-tab active" data-tab="modules">Modul</button>
  <button class="lms-tab" data-tab="add">Tambah Materi</button>
  <button class="lms-tab" data-tab="all">Semua Materi</button>
</div>

{{-- Tab 1: Module Builder --}}
<div class="lms-tab-content active" id="tab-modules">
  <div class="portal-dashboard-grid" style="grid-template-columns:1.2fr .8fr">
    {{-- Left: Module list --}}
    <section class="portal-panel">
      <div class="portal-panel-header">
        <div>
          <h2>Modul Pembelajaran</h2>
          <p>BAB / Topik / Pertemuan</p>
        </div>
      </div>

      <div class="module-add-form">
        <input type="text" id="new-module-title" placeholder="Nama modul baru..." maxlength="255">
        <button class="btn btn-primary" onclick="createModule()" style="white-space:nowrap">+ Modul</button>
      </div>

      <div id="module-list">
        @forelse ($modules as $module)
          <div class="module-card" data-module-id="{{ $module->id }}">
            <div class="module-header" onclick="toggleModule(this)">
              <span class="drag-handle" title="Drag to reorder">⠿</span>
              <div class="module-header-title">
                <strong>{{ $module->title }}</strong>
                <span>{{ $module->materials->count() }} materi</span>
              </div>
              <button onclick="event.stopPropagation();editModule({{ $module->id }},'{{ $module->title }}')" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;border-radius:6px" title="Edit">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </button>
              <button onclick="event.stopPropagation();deleteModule({{ $module->id }})" style="background:none;border:none;cursor:pointer;color:var(--danger);padding:4px;border-radius:6px" title="Hapus">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
              </button>
            </div>
            <div class="module-body" id="module-body-{{ $module->id }}">
              @forelse ($module->materials as $m)
                <div class="material-item" data-material-id="{{ $m->id }}">
                    <span class="mat-icon" style="background:color-mix(in srgb,var(--primary-4) 15%,transparent);color:var(--primary)">
                    @if ($m->type === 'file')
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    @elseif ($m->type === 'embed')
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                    @else
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    @endif
                  </span>
                  <div class="mat-info">
                    <strong>{{ $m->title }}</strong>
                    <small>
                      @if ($m->file_name)
                        {{ $m->file_name }} ({{ round($m->file_size / 1024) }}KB)
                      @elseif ($m->url)
                        {{ \Illuminate\Support\Str::limit($m->url, 40) }}
                      @endif
                    </small>
                  </div>
                  <div class="mat-actions">
                    @if ($m->file_path)
                      <a href="{{ route('download.materi', $m) }}" title="Download" target="_blank">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                      </a>
                      <a href="{{ route('download.materi.preview', $m) }}" title="Preview" target="_blank">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      </a>
                    @elseif ($m->url)
                      <a href="{{ $m->url }}" title="Buka Link" target="_blank">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                      </a>
                    @endif
                    <form method="POST" action="{{ route('guru.materi.destroy', $m) }}" onsubmit="return confirm('Hapus materi ini?')" style="display:inline">
                      @csrf @method('DELETE')
                      <button type="submit" title="Hapus">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                      </button>
                    </form>
                  </div>
                </div>
              @empty
                <div class="empty-module">
                  <p>Belum ada materi di modul ini.</p>
                  <button class="btn btn-primary" style="margin-top:10px;padding:6px 16px;font-size:.8rem" onclick="goToAddMaterial({{ $module->id }})">+ Tambah Materi</button>
                </div>
              @endforelse
            </div>
          </div>
        @empty
          <div class="empty-module">
            <p style="font-weight:600;color:var(--ink)">Belum ada modul</p>
            <p>Buat modul baru menggunakan form di atas.</p>
          </div>
        @endforelse
      </div>
    </section>

    {{-- Right: Quick stats / instructions --}}
    <section class="portal-panel">
      <div class="portal-panel-header">
        <div><h2>Informasi</h2></div>
      </div>
      <div style="font-size:.85rem;color:var(--muted);line-height:1.6">
        <p><strong style="color:var(--ink)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:text-bottom;margin-right:4px"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg> Panduan:</strong></p>
        <ul style="padding-left:16px;margin:8px 0">
          <li>Buat modul untuk setiap BAB atau Topik</li>
          <li>Klik modul untuk melihat daftar materinya</li>
          <li>Gunakan tab "Tambah Materi" untuk upload file atau link</li>
          <li>Drag handle (⠿) untuk mengurutkan modul</li>
        </ul>
        <hr style="border:none;border-top:1px solid var(--line);margin:16px 0">
        <p><strong style="color:var(--ink)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:text-bottom;margin-right:4px"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg> Statistik:</strong></p>
        <p>Total modul: <strong>{{ $modules->count() }}</strong></p>
        <p>Total materi: <strong>{{ $materials->count() }}</strong></p>
      </div>
    </section>
  </div>
</div>

{{-- Tab 2: Add Material --}}
<div class="lms-tab-content" id="tab-add">
  <section class="portal-panel" style="max-width:680px">
    <div class="portal-panel-header">
      <div><h2>Tambah Materi Baru</h2><p>Upload file atau tempel link materi pembelajaran.</p></div>
    </div>
    <form method="POST" action="{{ route('guru.materi.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="teaching_assignment_id" value="{{ $selectedTa->id }}">

      <div class="field" style="margin-bottom:14px">
        <label>Modul (opsional)</label>
        <select name="module_id">
          <option value="">— Tanpa Modul —</option>
          @foreach ($modules as $module)
            <option value="{{ $module->id }}">{{ $module->title }}</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="margin-bottom:14px">
        <label for="title">Judul Materi</label>
        <input id="title" name="title" type="text" required placeholder="Contoh: Bab 1 — Pengenalan Algoritma" value="{{ old('title') }}">
      </div>

      <div class="field" style="margin-bottom:14px">
        <label>Tipe Materi</label>
        <div class="type-toggle">
          <button type="button" class="active" data-type="file" onclick="switchType(this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:text-bottom;margin-right:4px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Upload File
          </button>
          <button type="button" data-type="link" onclick="switchType(this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:text-bottom;margin-right:4px"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            Link URL
          </button>
          <button type="button" data-type="embed" onclick="switchType(this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:text-bottom;margin-right:4px"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
            Embed
          </button>
        </div>
        <input type="hidden" name="type" id="material-type" value="file">
      </div>

      <div id="file-upload-section">
        <div class="file-upload-zone" id="upload-zone" onclick="document.getElementById('file-input').click()">
          <div class="upload-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <p>Klik atau drag & drop file di sini</p>
          <p style="font-size:.75rem;margin-top:4px">PDF, DOC, PPT, gambar, video — maks 50MB</p>
          <div class="file-name" id="file-name-display"></div>
        </div>
        <input type="file" id="file-input" name="file" style="display:none" onchange="handleFileSelect(this)" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.svg,.mp4,.webm,.mp3,.zip,.rar">
        <button type="button" class="btn btn-text" onclick="clearFile()" id="clear-file-btn" style="display:none;font-size:.8rem">Hapus file</button>
      </div>

      <div id="link-section" style="display:none">
        <div class="field" style="margin-bottom:14px">
          <label for="url">Link URL</label>
          <input id="url" name="url" type="url" placeholder="https://drive.google.com/... atau https://youtube.com/..." value="{{ old('url') }}">
          <span style="font-size:.78rem;color:var(--muted)">Google Drive, YouTube, atau tautan online lainnya.</span>
        </div>
      </div>

      <div class="field" style="margin-bottom:18px">
        <label for="description">Deskripsi (opsional)</label>
        <textarea id="description" name="description" placeholder="Deskripsi singkat tentang materi..." style="min-height:70px">{{ old('description') }}</textarea>
      </div>

      <button class="btn btn-primary" type="submit" style="width:100%">Simpan Materi</button>
    </form>
  </section>
</div>

{{-- Tab 3: All Materials --}}
<div class="lms-tab-content" id="tab-all">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div><h2>Semua Materi</h2><p>{{ $materials->count() }} materi di kelas ini.</p></div>
    </div>
    @if ($materials->count())
      <div style="display:grid;gap:10px">
        @foreach ($materials as $material)
          <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;border:1px solid var(--line);background:var(--card)">
            <span style="flex-shrink:0;width:32px;height:32px;border-radius:8px;background:color-mix(in srgb,var(--primary-4) 12%,transparent);display:grid;place-items:center;color:var(--primary)">
              @if ($material->type === 'file')
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              @elseif ($material->type === 'embed')
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
              @else
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
              @endif
            </span>
            <div style="flex:1;min-width:0">
              <strong style="display:block;font-size:.85rem">{{ $material->title }}</strong>
              <span style="font-size:.75rem;color:var(--muted)">
                @if ($material->module)
                  {{ $material->module->title }} —
                @endif
                @if ($material->file_name)
                  {{ $material->file_name }}
                @elseif ($material->url)
                  {{ \Illuminate\Support\Str::limit($material->url, 50) }}
                @endif
              </span>
            </div>
            <span style="font-size:.72rem;color:var(--muted)">{{ $material->created_at->format('d M Y') }}</span>
            <div style="display:flex;gap:4px">
              @if ($material->file_path)
                <a href="{{ route('download.materi', $material) }}" target="_blank" class="btn btn-text" style="padding:4px 10px;font-size:.75rem">Download</a>
              @elseif ($material->url)
                <a href="{{ $material->url }}" target="_blank" class="btn btn-text" style="padding:4px 10px;font-size:.75rem">Buka</a>
              @endif
              <form method="POST" action="{{ route('guru.materi.destroy', $material) }}" onsubmit="return confirm('Hapus materi ini?')" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-text" style="padding:4px 10px;font-size:.75rem;color:var(--danger)">Hapus</button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="portal-empty">
        <p style="color:var(--muted)">Belum ada materi. Tambahkan materi baru menggunakan tab "Tambah Materi".</p>
      </div>
    @endif
  </section>
</div>

@else
  <div class="portal-panel" style="text-align:center;padding:60px 20px">
    <p style="color:var(--muted);font-size:1rem">Pilih kelas dan mata pelajaran terlebih dahulu.</p>
  </div>
@endif
@endsection

@push('scripts')
<script>
// ── Tab Switching ──
document.querySelectorAll('.lms-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.lms-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.lms-tab-content').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
    document.getElementById('tab-' + this.dataset.tab).classList.add('active');
  });
});

// ── Module Toggle ──
function toggleModule(header) {
  const body = header.nextElementSibling;
  body.classList.toggle('open');
}

// ── Material Type Switch ──
function switchType(btn) {
  document.querySelectorAll('.type-toggle button').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const type = btn.dataset.type;
  document.getElementById('material-type').value = type;
  document.getElementById('file-upload-section').style.display = type === 'file' ? 'block' : 'none';
  document.getElementById('link-section').style.display = type !== 'file' ? 'block' : 'none';

  if (type !== 'file') {
    document.getElementById('file-input').value = '';
    document.getElementById('file-name-display').textContent = '';
    document.getElementById('upload-zone').classList.remove('has-file');
    document.getElementById('clear-file-btn').style.display = 'none';
  }
}

// ── File Handling ──
function handleFileSelect(input) {
  const file = input.files[0];
  if (!file) return;
  document.getElementById('file-name-display').textContent = file.name + ' (' + Math.round(file.size/1024) + 'KB)';
  document.getElementById('upload-zone').classList.add('has-file');
  document.getElementById('clear-file-btn').style.display = 'inline-block';

  document.querySelectorAll('.type-toggle button').forEach(b => b.classList.remove('active'));
  document.querySelector('.type-toggle button[data-type="file"]').classList.add('active');
  document.getElementById('material-type').value = 'file';
  document.getElementById('link-section').style.display = 'none';
  document.getElementById('file-upload-section').style.display = 'block';
}

function clearFile() {
  document.getElementById('file-input').value = '';
  document.getElementById('file-name-display').textContent = '';
  document.getElementById('upload-zone').classList.remove('has-file');
  document.getElementById('clear-file-btn').style.display = 'none';
}

// ── Drag and drop upload zone ──
const zone = document.getElementById('upload-zone');
if (zone) {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = 'var(--primary)'; });
  zone.addEventListener('dragleave', () => { zone.style.borderColor = ''; });
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.style.borderColor = '';
    const file = e.dataTransfer.files[0];
    if (file) {
      document.getElementById('file-input').files = e.dataTransfer.files;
      handleFileSelect(document.getElementById('file-input'));
    }
  });
}

// ── Create Module ──
function createModule() {
  const input = document.getElementById('new-module-title');
  const title = input.value.trim();
  if (!title) return alert('Nama modul harus diisi.');

  const taId = {{ $selectedTa ? $selectedTa->id : 'null' }};
  if (!taId) return;

  fetch('{{ route('guru.module.store') }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ teaching_assignment_id: taId, title })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      input.value = '';
      location.reload();
    } else {
      alert(d.message || 'Gagal membuat modul.');
    }
  })
  .catch(() => alert('Terjadi kesalahan.'));
}

// ── Edit Module ──
function editModule(id, currentTitle) {
  const newTitle = prompt('Edit nama modul:', currentTitle);
  if (!newTitle || newTitle.trim() === currentTitle) return;

  fetch('{{ url('guru/module') }}/' + id, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ title: newTitle.trim() })
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); else alert(d.message); })
  .catch(() => alert('Terjadi kesalahan.'));
}

// ── Delete Module ──
function deleteModule(id) {
  if (!confirm('Hapus modul ini? Materi di dalamnya akan tetap ada (tanpa modul).')) return;

  fetch('{{ url('guru/module') }}/' + id, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(r => r.json())
  .then(d => { if (d.success) location.reload(); else alert(d.message); })
  .catch(() => alert('Terjadi kesalahan.'));
}

function goToAddMaterial(moduleId) {
  document.querySelectorAll('.lms-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.lms-tab-content').forEach(c => c.classList.remove('active'));
  document.querySelector('.lms-tab[data-tab="add"]').classList.add('active');
  document.getElementById('tab-add').classList.add('active');

  const select = document.querySelector('select[name="module_id"]');
  if (select) select.value = moduleId;
}
</script>
@endpush
