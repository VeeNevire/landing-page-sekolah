@extends('layouts.guru')
@section('title', $assignment ? 'Edit Tugas' : 'Buat Tugas')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>{{ $assignment ? 'Edit Tugas' : 'Buat Tugas Baru' }}</h1>
    <p>{{ $selectedTa->class_name }} — {{ $selectedTa->subject->name ?? $selectedTa->customSubject->nama ?? '-' }}</p>
  </div>
</div>

<section class="portal-panel">
  <div class="portal-panel-header">
    <div>
      <h2>{{ $assignment ? 'Edit Tugas' : 'Tugas Baru' }}</h2>
      <p>Isi detail tugas untuk siswa.</p>
    </div>
  </div>

  <form method="POST" action="{{ $assignment ? route('guru.tugas.update', $assignment) : route('guru.tugas.store') }}" enctype="multipart/form-data">
    @csrf
    @if ($assignment) @method('PUT') @endif

    <input type="hidden" name="teaching_assignment_id" value="{{ $selectedTa->id }}">

    <div class="field" style="margin-bottom:14px">
      <label for="title">Judul Tugas</label>
      <input id="title" name="title" type="text" required placeholder="Contoh: Makalah Bab 1" value="{{ old('title', $assignment->title ?? '') }}">
    </div>

    <div class="field" style="margin-bottom:14px">
      <label for="module_id">Modul (opsional)</label>
      <select name="module_id">
        <option value="">— Tanpa Modul —</option>
        @foreach ($modules as $module)
          <option value="{{ $module->id }}" @selected(old('module_id', $assignment->module_id ?? '') == $module->id)>{{ $module->title }}</option>
        @endforeach
      </select>
    </div>

    <div class="field" style="margin-bottom:14px">
      <label for="instructions">Instruksi Tugas</label>
      <textarea id="instructions" name="instructions" required placeholder="Jelaskan apa yang harus dikerjakan siswa..." style="min-height:160px">{{ old('instructions', $assignment->instructions ?? '') }}</textarea>
    </div>

    <div class="field" style="margin-bottom:14px">
      <label for="max_score">Skor Maksimal</label>
      <input id="max_score" name="max_score" type="number" min="1" max="999" required value="{{ old('max_score', $assignment->max_score ?? 100) }}">
    </div>

    <div class="field" style="margin-bottom:14px">
      <label for="due_date">Batas Waktu (opsional)</label>
      <input id="due_date" name="due_date" type="datetime-local" value="{{ old('due_date', $assignment?->due_date?->format('Y-m-d\TH:i') ?? '') }}">
    </div>

    <div class="field" style="margin-bottom:14px">
      <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600;font-size:.85rem">
        <input type="checkbox" name="allow_late_submission" value="1" @checked(old('allow_late_submission', $assignment->allow_late_submission ?? false)) style="width:auto">
        Izinkan pengumpulan terlambat
      </label>
    </div>

    <div class="field" style="margin-bottom:18px">
      <label for="attachment">Lampiran (opsional)</label>
      <div style="border:2px dashed var(--line);border-radius:14px;padding:20px;text-align:center;cursor:pointer;transition:all .2s;margin-top:4px" onclick="document.getElementById('file-input').click()" onmouseover="this.style.borderColor='var(--primary-3)'" onmouseout="this.style.borderColor=''">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" style="display:block;margin:0 auto 6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <p style="margin:0;font-size:.82rem;color:var(--muted)">Klik untuk upload file lampiran</p>
        <p style="margin:4px 0 0;font-size:.72rem;color:var(--muted)">PDF, DOC, PPT, gambar, ZIP — maks 50MB</p>
        <p id="file-name-display" style="margin:6px 0 0;font-size:.82rem;font-weight:600;color:var(--ink);display:none"></p>
      </div>
      <input id="file-input" name="attachment" type="file" style="display:none" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar" onchange="document.getElementById('file-name-display').textContent=this.files[0]?.name||'';document.getElementById('file-name-display').style.display=this.files[0]?'block':'none'">
      @if ($assignment && $assignment->attachment_path)
        <div style="margin-top:8px;padding:10px 14px;border-radius:10px;background:color-mix(in srgb,var(--primary-4) 8%,transparent);display:flex;align-items:center;gap:10px">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <span style="flex:1;font-size:.82rem;color:var(--ink)">{{ $assignment->attachment_name }}</span>
          <a href="{{ route('download.assignment', $assignment) }}" target="_blank" style="font-size:.78rem;color:var(--primary);font-weight:600;text-decoration:none">Download</a>
        </div>
      @endif
    </div>

    <button class="btn btn-primary" type="submit" style="width:100%">
      {{ $assignment ? 'Simpan Perubahan' : 'Buat Tugas' }}
    </button>
    <div style="text-align:center;margin-top:10px">
      <a href="{{ route('guru.tugas.index', ['ta_id' => $selectedTa->id]) }}" style="font-size:.82rem;color:var(--muted);text-decoration:none">Batal</a>
    </div>
  </form>
</section>
@endsection
