@extends('layouts.guru')

@section('title', 'Materi & Lampiran')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Materi</span>
    <h1>Materi & Lampiran</h1>
    <p>Kelola materi belajar untuk kelas yang Anda ajar.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div><h2>Tambah Materi Baru</h2><p>Pilih kelas/mapel, isi judul, dan tempel link materi.</p></div>
    </div>
    <form method="POST" action="{{ route('guru.materi.store') }}">
      @csrf
      <div class="field" style="margin-bottom:14px">
        <label>Kelas & Mata Pelajaran</label>
        <select name="teaching_assignment_id" required>
          <option value="">— Pilih —</option>
          @foreach ($pairs as $pair)
            <option value="{{ $pair['assignment_id'] }}" @selected(old('teaching_assignment_id') == $pair['assignment_id'])>{{ $pair['class_name'] }} — {{ $pair['subject_name'] }}</option>
          @endforeach
        </select>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label for="title">Judul Materi</label>
        <input id="title" name="title" type="text" required placeholder="Contoh: Bab 1 — Pengenalan Algoritma" value="{{ old('title') }}">
      </div>
      <div class="field" style="margin-bottom:14px">
        <label for="description">Deskripsi (opsional)</label>
        <textarea id="description" name="description" placeholder="Deskripsi singkat tentang materi..." style="min-height:70px">{{ old('description') }}</textarea>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label for="url">Link Materi</label>
        <input id="url" name="url" type="url" placeholder="https://drive.google.com/... atau https://youtube.com/..." value="{{ old('url') }}">
        <span style="font-size:.78rem;color:var(--muted)">Tempel link Google Drive, YouTube, PDF online, atau tautan lainnya.</span>
      </div>
      <button class="btn btn-primary" type="submit" style="width:100%">Simpan Materi</button>
    </form>
  </section>

  <section class="portal-panel">
    <div class="portal-panel-header">
      <div><h2>Daftar Materi</h2><p>{{ $materials->count() }} materi sudah diunggah.</p></div>
    </div>
    @if ($materials->count())
      <div style="display:grid;gap:12px">
        @foreach ($materials as $material)
          <div style="padding:16px;border-radius:14px;border:1px solid var(--line);background:var(--card)">
            <div style="display:flex;justify-content:space-between;align-items:start;gap:12px">
              <div style="flex:1;min-width:0">
                <strong style="display:block">{{ $material->title }}</strong>
                <span style="font-size:.82rem;color:var(--muted)">{{ $material->teachingAssignment->class_name }} — {{ $material->teachingAssignment->subject->name ?? '' }}</span>
                @if ($material->description)
                  <p style="margin:8px 0 0;font-size:.88rem;color:var(--muted)">{{ $material->description }}</p>
                @endif
                @if ($material->url)
                  <a href="{{ $material->url }}" target="_blank" rel="noopener"
                     style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;font-size:.85rem;color:var(--primary-2);font-weight:700;text-decoration:underline">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    Buka Link
                  </a>
                @endif
              </div>
              <form method="POST" action="{{ route('guru.materi.destroy', $material) }}" onsubmit="return confirm('Hapus materi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none;border:none;color:var(--danger);cursor:pointer;padding:6px;border-radius:8px" title="Hapus">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </form>
            </div>
            <span style="font-size:.78rem;color:var(--muted)">{{ $material->created_at->format('d M Y') }}</span>
          </div>
        @endforeach
      </div>
    @else
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">Belum ada materi. Tambahkan materi baru menggunakan form di sebelah kiri.</p>
      </div>
    @endif
  </section>
</div>
@endsection



