@extends('layouts.siswa')
@section('title', $assignment->title)
@section('content')
<div style="margin-bottom:16px">
  <a href="{{ route('siswa.tugas.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;color:var(--s-primary);text-decoration:none;margin-bottom:10px;font-weight:600">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    Kembali ke daftar tugas
  </a>
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">{{ $assignment->title }}</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:4px 0 0">{{ $assignment->teachingAssignment->subject->name ?? $assignment->teachingAssignment->customSubject->nama ?? '-' }}</p>
</div>

@if (session('success'))
  <div style="padding:10px 14px;border-radius:10px;background:#d1fae5;color:#065f46;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div style="padding:10px 14px;border-radius:10px;background:#fce4ec;color:#b71c1c;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('error') }}</div>
@endif

<div style="display:grid;gap:16px">
  {{-- Assignment Info --}}
  <div class="b-card" style="padding:18px">
    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:14px">
      <div style="font-size:.78rem;color:var(--s-muted)">
        <span style="font-weight:600;color:var(--s-ink);display:block">Skor Maksimal</span>
        {{ $assignment->max_score }}
      </div>
      @if ($assignment->due_date)
      <div style="font-size:.78rem;color:var(--s-muted)">
        <span style="font-weight:600;color:var(--s-ink);display:block">Batas Waktu</span>
        {{ $assignment->due_date->format('d M Y H:i') }}
        @if (now()->gt($assignment->due_date))
          <span style="color:#b71c1c">({{ $assignment->allow_late_submission ? 'Terlambat (diizinkan)' : 'Terlambat' }})</span>
        @endif
      </div>
      @endif
      <div style="font-size:.78rem;color:var(--s-muted)">
        <span style="font-weight:600;color:var(--s-ink);display:block">Pengumpulan Terlambat</span>
        {{ $assignment->allow_late_submission ? 'Diizinkan' : 'Tidak' }}
      </div>
    </div>

    <div style="margin-bottom:14px">
      <h4 style="font-size:.82rem;font-weight:700;color:var(--s-ink);margin:0 0 6px">Instruksi</h4>
      <p style="font-size:.82rem;color:var(--s-muted);line-height:1.6;white-space:pre-wrap;margin:0">{{ $assignment->instructions }}</p>
    </div>

    @if ($assignment->attachment_path)
    <a href="{{ route('download.assignment', $assignment) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:.78rem;font-weight:600;color:var(--s-primary);background:color-mix(in srgb,var(--s-primary) 8%,transparent);text-decoration:none">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      Download Lampiran: {{ $assignment->attachment_name }}
    </a>
    @endif
  </div>

  {{-- Submission Status --}}
  <div class="b-card" style="padding:18px">
    <h3 style="font-size:.88rem;font-weight:700;color:var(--s-ink);margin:0 0 12px">Pengumpulan Tugas</h3>

    @if ($submission && $submission->grade)
      {{-- Graded --}}
      <div style="padding:16px;border-radius:12px;background:color-mix(in srgb,var(--s-primary) 6%,transparent);margin-bottom:14px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:48px;height:48px;border-radius:50%;background:var(--s-primary);display:grid;place-items:center">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div>
            <strong style="font-size:1.1rem;color:var(--s-primary)">{{ $submission->grade->score }} / {{ $assignment->max_score }}</strong>
            <span style="display:block;font-size:.78rem;color:var(--s-muted)">Nilai — {{ $submission->grade->graded_at->format('d M Y') }}</span>
          </div>
        </div>
        @if ($submission->grade->feedback)
          <div style="margin-top:12px;padding:12px;border-radius:8px;background:var(--s-bg);font-size:.82rem;color:var(--s-ink);line-height:1.5">
            <strong style="font-size:.75rem;color:var(--s-muted);display:block;margin-bottom:4px">Feedback Guru:</strong>
            {{ $submission->grade->feedback }}
          </div>
        @endif
      </div>
    @elseif ($submission)
      {{-- Submitted but not graded --}}
      <div style="padding:14px;border-radius:10px;background:#d1fae5;color:#065f46;margin-bottom:14px">
        <strong style="display:block;font-size:.88rem">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:text-bottom;margin-right:4px"><polyline points="20 6 9 17 4 12"/></svg>
          Tugas sudah dikumpulkan
        </strong>
        <span style="font-size:.78rem">{{ $submission->submitted_at->format('d M Y H:i') }} {{ $submission->is_late ? '(Terlambat)' : '' }}</span>
      </div>
      <p style="font-size:.78rem;color:var(--s-muted);margin:0 0 10px">File: {{ $submission->file_name }} ({{ round($submission->file_size / 1024) }}KB)</p>
      <div style="display:flex;gap:8px;margin-bottom:14px">
        <a href="{{ route('download.submission', $submission) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:.78rem;font-weight:600;color:var(--s-primary);background:color-mix(in srgb,var(--s-primary) 8%,transparent);text-decoration:none">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download File
        </a>
        <button onclick="document.getElementById('reupload-form').style.display='block'" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:.78rem;font-weight:600;border:1.5px solid var(--s-line);background:var(--s-card);cursor:pointer;color:var(--s-ink)">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
          Ganti File
        </button>
      </div>
    @else
      {{-- Not submitted --}}
      <div style="padding:14px;border-radius:10px;background:#fef3c7;color:#92400e;margin-bottom:14px">
        <strong style="display:block;font-size:.88rem">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="display:inline;vertical-align:text-bottom;margin-right:4px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Belum dikumpulkan
        </strong>
        @if ($assignment->due_date && now()->gt($assignment->due_date) && !$assignment->allow_late_submission)
          <span style="font-size:.78rem">Batas waktu sudah lewat. Pengumpulan tidak diizinkan.</span>
        @else
          <span style="font-size:.78rem">Segera kumpulkan tugasmu.</span>
        @endif
      </div>
    @endif

    {{-- Upload Form --}}
    @php
      $canSubmit = !$submission || !$submission->grade;
      $isPastDue = $assignment->due_date && now()->gt($assignment->due_date);
      $canUpload = $canSubmit && ($assignment->allow_late_submission || !$isPastDue);
    @endphp

    @if ($canUpload)
      <form method="POST" action="{{ route('siswa.tugas.submit', $assignment) }}" enctype="multipart/form-data" id="submit-form" style="{{ $submission && !$submission->grade ? 'display:none' : '' }}">
        @csrf
        <div style="margin-bottom:14px">
          <div style="border:2px dashed var(--s-line);border-radius:12px;padding:24px;text-align:center;cursor:pointer;transition:all .2s" onclick="document.getElementById('file-input-tugas').click()" onmouseover="this.style.borderColor='var(--s-primary)'" onmouseout="this.style.borderColor=''" id="upload-zone-tugas">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5" style="display:block;margin:0 auto 6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <p style="margin:0;font-size:.82rem;color:var(--s-muted)">Klik untuk upload file tugas</p>
            <p style="margin:4px 0 0;font-size:.72rem;color:var(--s-muted)">PDF, DOC, PPT, gambar, ZIP — maks 50MB</p>
            <p id="file-name-tugas" style="margin:6px 0 0;font-size:.82rem;font-weight:600;color:var(--s-ink);display:none"></p>
          </div>
          <input id="file-input-tugas" name="file" type="file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar" style="display:none" onchange="document.getElementById('file-name-tugas').textContent=this.files[0]?.name||'';document.getElementById('file-name-tugas').style.display=this.files[0]?'block':'none'">
        </div>
        <div class="field" style="margin-bottom:14px">
          <label for="notes">Catatan (opsional)</label>
          <textarea id="notes" name="notes" placeholder="Tambahkan catatan untuk guru..." style="min-height:60px"></textarea>
        </div>
        <button class="btn btn-primary" type="submit" style="width:100%;padding:10px">
          {{ $submission && !$submission->grade ? 'Ganti File Tugas' : 'Kumpulkan Tugas' }}
        </button>
      </form>
    @elseif ($submission && $submission->grade)
      <p style="font-size:.78rem;color:var(--s-muted);margin:0">Tugas sudah dinilai. Tidak bisa mengganti file.</p>
    @elseif (!$submission && $isPastDue && !$assignment->allow_late_submission)
      <p style="font-size:.78rem;color:#b71c1c;margin:0">Batas pengumpulan sudah lewat.</p>
    @endif
  </div>
</div>

@if ($submission && !$submission->grade)
<script>
document.querySelector('#submit-form button')?.addEventListener('click', function() {
  document.getElementById('submit-form').scrollIntoView({behavior:'smooth'});
});
</script>
@endif
@endsection
