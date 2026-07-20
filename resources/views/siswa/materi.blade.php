@extends('layouts.siswa')
@section('title', 'Materi')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Materi Pelajaran</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Materi ajar dari guru</p>
</div>

@if($materials->count() > 0)
<div class="bento bento-2">
  @foreach($materials as $m)
  <div class="b-card">
    <div style="display:flex;align-items:flex-start;gap:14px">
      <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--s-primary),var(--s-primary-dark));display:grid;place-items:center;flex-shrink:0;box-shadow:0 4px 12px rgba(107,163,199,0.2)">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
      </div>
      <div style="min-width:0;flex:1">
        <div class="b-flex-between" style="align-items:flex-start">
          <div>
            <h3 style="font-size:.88rem;font-weight:600;color:var(--s-ink);margin:0">{{ $m->title }}</h3>
            <p style="font-size:.72rem;font-weight:500;color:var(--s-primary);margin:4px 0 0">{{ $m->teachingAssignment->subject->name ?? $m->teachingAssignment->customSubject->nama ?? '-' }}</p>
          </div>
          <span style="font-size:.68rem;color:var(--s-muted);white-space:nowrap">{{ $m->created_at->translatedFormat('d M') }}</span>
        </div>
        @if($m->description)
        <p style="font-size:.8rem;color:var(--s-muted);margin:8px 0 0;line-height:1.5">{{ Str::limit($m->description, 120) }}</p>
        @endif
        @if($m->url)
        <a href="{{ $m->url }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;font-size:.78rem;font-weight:600;color:var(--s-primary);text-decoration:none">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Buka Materi
        </a>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="b-card" style="text-align:center;padding:48px">
  <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
  </div>
  <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">Belum ada materi</h3>
  <p style="font-size:.82rem;color:var(--s-muted);margin:0">Materi akan muncul ketika guru mengunggahnya.</p>
</div>
@endif
@endsection
