@extends('layouts.guru')
@section('title', 'Tugas')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>Tugas</h1>
    <p>Kelola tugas untuk setiap kelas.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div style="padding:12px 16px;border-radius:12px;background:#fce4ec;color:#b71c1c;font-weight:700;margin-bottom:16px">{{ session('error') }}</div>
@endif

<div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
  <select onchange="window.location='{{ route('guru.tugas.index') }}?ta_id='+this.value" style="min-width:260px;padding:10px 14px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.85rem">
    <option value="">— Semua Kelas —</option>
    @foreach ($pairs as $pair)
      <option value="{{ $pair['assignment_id'] }}" @selected($selectedTa && $selectedTa->id == $pair['assignment_id'])>
        {{ $pair['class_name'] }} — {{ $pair['subject_name'] }}
      </option>
    @endforeach
  </select>
  @if ($selectedTa)
    <a href="{{ route('guru.tugas.create', ['ta_id' => $selectedTa->id]) }}" class="btn btn-primary" style="padding:8px 20px;font-size:.82rem">+ Tugas Baru</a>
  @endif
</div>

@if ($assignments->count())
  <div style="display:grid;gap:12px">
    @foreach ($assignments as $assignment)
      <div class="card card-hover" style="display:flex;align-items:center;gap:16px;padding:16px 20px">
        <div style="width:44px;height:44px;border-radius:12px;background:color-mix(in srgb,var(--primary-4) 15%,transparent);display:grid;place-items:center;flex-shrink:0;color:var(--primary)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:10px">
            <strong style="font-size:.9rem;color:var(--ink)">{{ $assignment->title }}</strong>
            @if ($assignment->published_at)
              <span style="font-size:.68rem;padding:2px 10px;border-radius:20px;background:#d1fae5;color:#065f46;font-weight:600">Published</span>
            @else
              <span style="font-size:.68rem;padding:2px 10px;border-radius:20px;background:#fef3c7;color:#92400e;font-weight:600">Draft</span>
            @endif
          </div>
          <div style="display:flex;gap:16px;margin-top:5px;font-size:.78rem;color:var(--muted);flex-wrap:wrap">
            <span>{{ $assignment->teachingAssignment->class_name }} — {{ $assignment->teachingAssignment->subject->name ?? $assignment->teachingAssignment->customSubject->nama ?? '-' }}</span>
            @if ($assignment->module)
              <span>{{ $assignment->module->title }}</span>
            @endif
            @if ($assignment->due_date)
              <span>Due: {{ $assignment->due_date->format('d M Y H:i') }}</span>
            @endif
            <span>Skor: {{ $assignment->max_score }}</span>
            <span>{{ $assignment->submissions->count() }} pengumpulan</span>
          </div>
        </div>
        <div style="display:flex;gap:6px">
          <a href="{{ route('guru.tugas.submissions', $assignment) }}" style="height:34px;padding:0 12px;border-radius:9px;display:inline-flex;align-items:center;gap:5px;background:var(--primary);color:#fff;text-decoration:none;font-size:.75rem;font-weight:700">Nilai</a>
          <a href="{{ route('guru.tugas.edit', $assignment) }}" style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;border:1.5px solid var(--line);color:var(--muted);text-decoration:none" title="Edit">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </a>
          <form method="POST" action="{{ route('guru.tugas.publish', $assignment) }}" style="display:inline" class="form-publish">
            @csrf @method('PATCH')
            <button type="button" onclick="confirmPublish(this)" style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;border:1.5px solid {{ $assignment->published_at ? 'var(--danger)' : '#22c55e' }};color:{{ $assignment->published_at ? 'var(--danger)' : '#22c55e' }};background:none;cursor:pointer" title="{{ $assignment->published_at ? 'Tarik publikasi' : 'Publikasikan' }}">
              @if ($assignment->published_at)
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a18.4 18.4 0 0 1-2.88 3.85"/><path d="M6.61 6.61A17.53 17.53 0 0 0 1 13s4 8 11 8a10.9 10.9 0 0 0 4.15-.73"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              @endif
            </button>
          </form>
          <form method="POST" action="{{ route('guru.tugas.destroy', $assignment) }}" style="display:inline" class="form-delete">
            @csrf @method('DELETE')
            <button type="button" onclick="confirmDelete(this)" style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;border:1.5px solid var(--danger);color:var(--danger);background:none;cursor:pointer" title="Hapus">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="portal-panel" style="text-align:center;padding:60px 20px">
    <p style="color:var(--muted);font-size:.95rem;margin-bottom:16px">Belum ada tugas.</p>
    @if ($selectedTa)
      <a href="{{ route('guru.tugas.create', ['ta_id' => $selectedTa->id]) }}" class="btn btn-primary">+ Buat Tugas</a>
    @else
      <p style="font-size:.82rem;color:var(--muted)">Pilih kelas & mapel terlebih dahulu untuk membuat tugas.</p>
    @endif
  </div>
@endif

@push('scripts')
<script>
function confirmPublish(btn) {
  const form = btn.closest('.form-publish');
  const isPublish = !form.querySelector('button[type="button"]').title.includes('Tarik');

  Swal.fire({
    title: isPublish ? 'Publikasikan tugas ini?' : 'Tarik publikasi tugas?',
    text: isPublish ? 'Tugas akan terlihat oleh siswa.' : 'Tugas tidak akan terlihat oleh siswa.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: isPublish ? '#22c55e' : '#ef4444',
    confirmButtonText: isPublish ? 'Ya, Publikasikan' : 'Ya, Tarik',
    cancelButtonText: 'Batal',
  }).then(result => {
    if (result.isConfirmed) form.submit();
  });
}

function confirmDelete(btn) {
  const form = btn.closest('.form-delete');
  const title = form.closest('.card')?.querySelector('strong')?.textContent?.trim() || 'tugas ini';

  Swal.fire({
    title: 'Hapus tugas?',
    html: `Apakah kamu yakin ingin menghapus <strong>"${title}"</strong>?<br><span style="font-size:.85rem;color:var(--muted)">Semua pengumpulan siswa akan ikut terhapus.</span>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
  }).then(result => {
    if (result.isConfirmed) form.submit();
  });
}
</script>
@endpush

@endsection
