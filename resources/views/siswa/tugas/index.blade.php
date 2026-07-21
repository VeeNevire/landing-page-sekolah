@extends('layouts.siswa')
@section('title', 'Tugas')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Tugas</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Tugas dari guru</p>
</div>

@if (session('success'))
  <div style="padding:10px 14px;border-radius:10px;background:#d1fae5;color:#065f46;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div style="padding:10px 14px;border-radius:10px;background:#fce4ec;color:#b71c1c;font-weight:600;font-size:.82rem;margin-bottom:14px">{{ session('error') }}</div>
@endif

@if ($assignments->count())
  <div class="bento" style="display:grid;gap:10px">
    @foreach ($assignments as $assignment)
      @php
        $submitted = isset($submittedIds[$assignment->id]);
      @endphp
      <a href="{{ route('siswa.tugas.show', $assignment) }}" style="text-decoration:none">
        <div class="b-card" style="display:flex;align-items:center;gap:14px;padding:14px 16px;transition:all .15s" onmouseover="this.style.borderColor='var(--s-primary)'" onmouseout="this.style.borderColor=''">
          <div style="width:40px;height:40px;border-radius:10px;background:color-mix(in srgb,var(--s-primary) 12%,transparent);display:grid;place-items:center;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:8px">
              <h3 style="font-size:.85rem;font-weight:600;color:var(--s-ink);margin:0">{{ $assignment->title }}</h3>
              @if ($submitted)
                <span style="display:inline-flex;align-items:center;gap:3px;font-size:.65rem;padding:2px 8px;border-radius:20px;background:#d1fae5;color:#065f46;font-weight:600">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  Selesai
                </span>
              @elseif ($assignment->due_date && now()->gt($assignment->due_date))
                <span style="display:inline-flex;align-items:center;gap:3px;font-size:.65rem;padding:2px 8px;border-radius:20px;background:#fce4ec;color:#b71c1c;font-weight:600">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  Terlambat
                </span>
              @endif
            </div>
            <p style="font-size:.72rem;color:var(--s-muted);margin:4px 0 0">
              {{ $assignment->teachingAssignment->subject->name ?? $assignment->teachingAssignment->customSubject->nama ?? '-' }}
              @if ($assignment->due_date)
                 · Due: {{ $assignment->due_date->format('d M H:i') }}
              @endif
            </p>
          </div>
          <span style="font-size:.78rem;color:var(--s-muted);flex-shrink:0">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </span>
        </div>
      </a>
    @endforeach
  </div>
@else
  <div class="b-card" style="text-align:center;padding:48px">
    <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
    </div>
    <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">Belum ada tugas</h3>
    <p style="font-size:.82rem;color:var(--s-muted);margin:0">Tugas akan muncul ketika guru mempublikasikannya.</p>
  </div>
@endif
@endsection
