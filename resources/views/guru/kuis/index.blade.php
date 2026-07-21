@extends('layouts.guru')
@section('title', 'Kuis')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">LMS</span>
    <h1>Kuis</h1>
    <p>Kelola kuis online untuk setiap kelas.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
  <select onchange="window.location='{{ route('guru.kuis.index') }}?ta_id='+this.value" style="min-width:260px;padding:10px 14px;border-radius:13px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.85rem">
    <option value="">— Semua Kelas —</option>
    @foreach ($pairs as $pair)
      <option value="{{ $pair['assignment_id'] }}" @selected($selectedTa && $selectedTa->id == $pair['assignment_id'])>
        {{ $pair['class_name'] }} — {{ $pair['subject_name'] }}
      </option>
    @endforeach
  </select>
  @if ($selectedTa)
    <a href="{{ route('guru.kuis.create', ['ta_id' => $selectedTa->id]) }}" class="btn btn-primary" style="padding:8px 20px;font-size:.82rem">+ Kuis Baru</a>
  @endif
</div>

@if ($quizzes->count())
  <div style="display:grid;gap:12px">
    @foreach ($quizzes as $quiz)
      <div class="card card-hover" style="display:flex;align-items:center;gap:16px;padding:16px 20px">
        <div style="width:44px;height:44px;border-radius:12px;background:color-mix(in srgb,var(--primary-4) 15%,transparent);display:grid;place-items:center;flex-shrink:0;color:var(--primary)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:10px">
            <strong style="font-size:.9rem;color:var(--ink)">{{ $quiz->title }}</strong>
            @if ($quiz->published_at)
              <span style="font-size:.68rem;padding:2px 10px;border-radius:20px;background:#d1fae5;color:#065f46;font-weight:600">Published</span>
            @else
              <span style="font-size:.68rem;padding:2px 10px;border-radius:20px;background:#fef3c7;color:#92400e;font-weight:600">Draft</span>
            @endif
          </div>
          <div style="display:flex;gap:16px;margin-top:5px;font-size:.78rem;color:var(--muted);flex-wrap:wrap">
            <span>{{ $quiz->teachingAssignment->class_name }} — {{ $quiz->teachingAssignment->subject->name ?? $quiz->teachingAssignment->customSubject->nama ?? '-' }}</span>
            @if ($quiz->module)
              <span>{{ $quiz->module->title }}</span>
            @endif
            @if ($quiz->time_limit)
              <span>{{ $quiz->time_limit }} menit</span>
            @endif
            <span>{{ $quiz->questions->count() }} soal</span>
            <span>{{ $quiz->max_attempts }}x attempts</span>
          </div>
        </div>
        <div style="display:flex;gap:6px">
          <a href="{{ route('guru.kuis.hasil', $quiz) }}" class="btn btn-primary" style="padding:7px 14px;font-size:.78rem">Hasil</a>
          <a href="{{ route('guru.kuis.edit', $quiz) }}" style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;border:1.5px solid var(--line);color:var(--muted);text-decoration:none" title="Edit">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </a>
          <form method="POST" action="{{ route('guru.kuis.publish', $quiz) }}" style="display:inline" class="form-publish">
            @csrf @method('PATCH')
            <button type="button" onclick="confirmPublish(this)" style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;border:1.5px solid {{ $quiz->published_at ? 'var(--danger)' : '#22c55e' }};color:{{ $quiz->published_at ? 'var(--danger)' : '#22c55e' }};background:none;cursor:pointer" title="{{ $quiz->published_at ? 'Tarik' : 'Publikasikan' }}">
              @if ($quiz->published_at)
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a18.4 18.4 0 0 1-2.88 3.85"/><path d="M6.61 6.61A17.53 17.53 0 0 0 1 13s4 8 11 8a10.9 10.9 0 0 0 4.15-.73"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              @else
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              @endif
            </button>
          </form>
          <form method="POST" action="{{ route('guru.kuis.destroy', $quiz) }}" style="display:inline" class="form-delete">
            @csrf @method('DELETE')
            <button type="button" onclick="confirmDelete(this, '{{ addslashes($quiz->title) }}')" style="width:34px;height:34px;border-radius:9px;display:grid;place-items:center;border:1.5px solid var(--danger);color:var(--danger);background:none;cursor:pointer" title="Hapus">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="portal-panel" style="text-align:center;padding:60px 20px">
    <p style="color:var(--muted);font-size:.95rem;margin-bottom:16px">Belum ada kuis.</p>
    @if ($selectedTa)
      <a href="{{ route('guru.kuis.create', ['ta_id' => $selectedTa->id]) }}" class="btn btn-primary">+ Buat Kuis</a>
    @endif
  </div>
@endif

@push('scripts')
<script>
function confirmPublish(btn) {
  const form = btn.closest('.form-publish');
  const isPublish = !btn.title.includes('Tarik');
  Swal.fire({
    title: isPublish ? 'Publikasikan kuis ini?' : 'Tarik publikasi kuis?',
    text: isPublish ? 'Kuis akan tersedia untuk siswa.' : 'Kuis tidak akan terlihat oleh siswa.',
    icon: 'question', showCancelButton: true,
    confirmButtonColor: isPublish ? '#22c55e' : '#ef4444',
    confirmButtonText: isPublish ? 'Ya, Publikasikan' : 'Ya, Tarik',
    cancelButtonText: 'Batal',
  }).then(r => { if (r.isConfirmed) form.submit(); });
}
function confirmDelete(btn, title) {
  const form = btn.closest('.form-delete');
  Swal.fire({
    title: 'Hapus kuis?',
    html: `Hapus <strong>"${title}"</strong>? Semua data siswa akan ikut terhapus.`,
    icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444',
    confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
  }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush
@endsection
