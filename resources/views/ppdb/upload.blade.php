@extends('layouts.public')

@section('title', 'Upload Berkas PPDB | SMK MADYA DEPOK')

@push('styles')
<style>
.tabs {
  display: flex;
  gap: 0;
  margin-bottom: 2rem;
  border-bottom: 2px solid var(--border);
}
.tab {
  flex: 1;
  padding: 1rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: .95rem;
  font-weight: 600;
  color: var(--muted);
  transition: all .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
  text-decoration: none;
}
.tab:hover {
  color: var(--text);
  background: var(--card);
}
.tab.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}
.tab .step-num {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--border);
  color: var(--muted);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .85rem;
}
.tab.active .step-num, .tab.completed .step-num {
  background: var(--primary);
  color: #fff;
}
.tab.completed {
  color: var(--primary);
}

.doc-card {
  border: 2px dashed var(--border);
  border-radius: 16px;
  padding: 2rem;
  margin-bottom: 1.25rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: all .3s;
  cursor: pointer;
  position: relative;
  background: var(--card);
  min-height: 180px;
}
.doc-card:hover {
  border-color: var(--primary);
  background: color-mix(in srgb, var(--primary) 3%, var(--card));
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(37,99,235,.12);
}
.doc-card.uploaded {
  border-style: solid;
  border-color: #10b981;
  background: linear-gradient(135deg, color-mix(in srgb, #10b981 8%, var(--card)) 0%, color-mix(in srgb, #10b981 3%, var(--card)) 100%);
}
.doc-card.uploaded:hover {
  border-color: #059669;
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(16, 185, 129, .2);
}
.doc-icon {
  width: 72px;
  height: 72px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
  border: none;
  margin-bottom: 1rem;
  box-shadow: 0 8px 20px rgba(37,99,235,.25);
}
.doc-card.uploaded .doc-icon {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 8px 20px rgba(16, 185, 129, .25);
}
.doc-info { 
  text-align: center;
  flex: 1;
  width: 100%;
}
.doc-info h4 { 
  margin: 0 0 .5rem; 
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--text);
}
.doc-info p { 
  margin: 0; 
  font-size: .9rem; 
  color: var(--muted);
  line-height: 1.6;
}
.doc-info .file-meta {
  display: inline-block;
  padding: .35rem .75rem;
  background: var(--border);
  border-radius: 20px;
  font-size: .8rem;
  color: var(--muted);
  margin-top: .75rem;
}
.doc-card.uploaded .file-meta {
  background: color-mix(in srgb, #10b981 15%, var(--card));
  color: #059669;
}
.doc-actions { 
  display: flex; 
  gap: .75rem; 
  margin-top: 1.5rem;
  justify-content: center;
}
.upload-input {
  display: none;
}
.doc-card.uploading {
  pointer-events: none;
  opacity: .6;
}
.doc-card .badge-wajib {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: #fff;
  padding: .25rem .75rem;
  border-radius: 20px;
  font-size: .75rem;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(239, 68, 68, .3);
}
.doc-card .badge-opsional {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  color: #fff;
  padding: .25rem .75rem;
  border-radius: 20px;
  font-size: .75rem;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(139, 92, 246, .3);
}
</style>
@endpush

@section('content')
<section class="page-hero">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <div class="breadcrumb">Beranda / PPDB / Upload Berkas</div>
      <h1>Upload Berkas Pendaftaran</h1>
      <p class="lead">Klik pada kartu dokumen untuk mengupload file.</p>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;white-space:nowrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar
      </button>
    </form>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:920px">
    <div class="tabs">
      <a href="{{ route('ppdb.form', ['step' => 1]) }}" class="tab completed">
        <span class="step-num">✓</span>
        <span>Data Siswa</span>
      </a>
      <a href="{{ route('ppdb.form', ['step' => 2]) }}" class="tab completed">
        <span class="step-num">✓</span>
        <span>Data Orang Tua</span>
      </a>
      <a class="tab active">
        <span class="step-num">3</span>
        <span>Upload Berkas</span>
      </a>
    </div>

    @if (session('success'))
    <div style="background:linear-gradient(135deg, color-mix(in srgb, #10b981 12%, var(--card)) 0%, color-mix(in srgb, #10b981 5%, var(--card)) 100%);border:1.5px solid #10b981;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;color:#059669;font-weight:600;display:flex;align-items:center;gap:1rem">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div style="background:linear-gradient(135deg, color-mix(in srgb, #ef4444 12%, var(--card)) 0%, color-mix(in srgb, #ef4444 5%, var(--card)) 100%);border:1.5px solid #ef4444;border-radius:12px;padding:1.25rem;margin-bottom:1.5rem;color:#dc2626;font-weight:600;display:flex;align-items:center;gap:1rem">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      {{ session('error') }}
    </div>
    @endif

    <p style="color:var(--muted);margin-bottom:2rem;text-align:center;font-size:.95rem">
      Format file: <strong>JPG, JPEG, PNG, PDF</strong> &middot; Maksimal <strong>2MB</strong> per file
    </p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem">
      @foreach ($requiredDocs as $type => $label)
      <div class="doc-card {{ $documents->has($type) ? 'uploaded' : '' }}" onclick="document.getElementById('file_{{ $type }}').click()">
        <span class="badge-wajib">WAJIB</span>
        <div class="doc-icon">{{ $documents->has($type) ? '✅' : '📄' }}</div>
        <div class="doc-info">
          <h4>{{ $label }}</h4>
          @if ($documents->has($type))
            <p style="font-weight:600;color:#059669">✓ Sudah diupload</p>
            <span class="file-meta">{{ $documents[$type]->file_name }}</span>
            <div class="doc-actions" onclick="event.stopPropagation()">
              <a href="{{ Storage::disk('public')->url($documents[$type]->file_path) }}" target="_blank" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Lihat
              </a>
              <form method="POST" action="{{ route('ppdb.upload.destroy', $documents[$type]) }}" onsubmit="return confirm('Hapus dokumen ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#dc2626" type="submit">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  Hapus
                </button>
              </form>
            </div>
          @else
            <p>Klik untuk upload {{ strtolower($label) }}</p>
            <p style="font-size:.85rem;margin-top:.5rem">atau drag & drop file di sini</p>
          @endif
        </div>
        <form method="POST" action="{{ route('ppdb.upload.store') }}" enctype="multipart/form-data" id="form_{{ $type }}" style="display:none">
          @csrf
          <input type="hidden" name="document_type" value="{{ $type }}">
          <input type="file" id="file_{{ $type }}" name="file" accept=".jpg,.jpeg,.png,.pdf" class="upload-input" onchange="document.getElementById('form_{{ $type }}').submit()">
        </form>
      </div>
      @endforeach

      {{-- Sertifikat (Opsional) --}}
      <div class="doc-card {{ isset($documents['sertifikat']) ? 'uploaded' : '' }}" onclick="document.getElementById('file_sertifikat').click()">
        <span class="badge-opsional">OPSIONAL</span>
        <div class="doc-icon">{{ isset($documents['sertifikat']) ? '🏆' : '📜' }}</div>
        <div class="doc-info">
          <h4>Sertifikat Prestasi</h4>
          @if (isset($documents['sertifikat']))
            <p style="font-weight:600;color:#059669">✓ Sudah diupload</p>
            <span class="file-meta">{{ $documents['sertifikat']->file_name }}</span>
            <div class="doc-actions" onclick="event.stopPropagation()">
              <a href="{{ Storage::disk('public')->url($documents['sertifikat']->file_path) }}" target="_blank" class="btn btn-outline btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Lihat
              </a>
              <form method="POST" action="{{ route('ppdb.upload.destroy', $documents['sertifikat']) }}" onsubmit="return confirm('Hapus dokumen ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#dc2626" type="submit">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  Hapus
                </button>
              </form>
            </div>
          @else
            <p>Upload sertifikat prestasi jika ada</p>
            <p style="font-size:.85rem;margin-top:.5rem">Akademik, olahraga, seni, dll</p>
          @endif
        </div>
        <form method="POST" action="{{ route('ppdb.upload.store') }}" enctype="multipart/form-data" id="form_sertifikat" style="display:none">
          @csrf
          <input type="hidden" name="document_type" value="sertifikat">
          <input type="file" id="file_sertifikat" name="file" accept=".jpg,.jpeg,.png,.pdf" class="upload-input" onchange="document.getElementById('form_sertifikat').submit()">
        </form>
      </div>
    </div>

    <form method="POST" action="{{ route('ppdb.submit') }}" style="margin-top:3rem">
      @csrf
      <button class="btn btn-primary" style="width:100%;padding:1.25rem;font-size:1.05rem;font-weight:700;background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);box-shadow:0 8px 24px rgba(37,99,235,.3)" type="submit">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:.5rem"><polyline points="20 6 9 17 4 12"/></svg>
        Kirim Pendaftaran
      </button>
      <p style="color:var(--muted);font-size:.88rem;text-align:center;margin-top:1rem">Pastikan semua dokumen wajib sudah terupload sebelum mengirim</p>
    </form>
  </div>
</section>

@push('scripts')
<script>
// Drag and drop support
document.querySelectorAll('.doc-card').forEach(card => {
  card.addEventListener('dragover', (e) => {
    e.preventDefault();
    card.style.borderColor = 'var(--primary)';
    card.style.background = 'color-mix(in srgb, var(--primary) 8%, var(--card))';
  });
  
  card.addEventListener('dragleave', (e) => {
    card.style.borderColor = '';
    card.style.background = '';
  });
  
  card.addEventListener('drop', (e) => {
    e.preventDefault();
    e.stopPropagation();
    card.style.borderColor = '';
    card.style.background = '';
    
    const file = e.dataTransfer.files[0];
    if (file) {
      const input = card.querySelector('input[type="file"]');
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      input.files = dataTransfer.files;
      input.form.submit();
    }
  });
});
</script>
@endpush
@endsection
