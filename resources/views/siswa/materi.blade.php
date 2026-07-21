@extends('layouts.siswa')
@section('title', 'Materi')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">Materi Pelajaran</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Materi ajar dari guru</p>
</div>

@if($grouped->isEmpty() && $ungrouped->isEmpty())
<div class="b-card" style="text-align:center;padding:48px">
  <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
  </div>
  <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">Belum ada materi</h3>
  <p style="font-size:.82rem;color:var(--s-muted);margin:0">Materi akan muncul ketika guru mengunggahnya.</p>
</div>
@endif

{{-- Grouped by Module --}}
@foreach($grouped as $group)
<div class="b-card" style="padding:0;overflow:hidden;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px;padding:14px 18px;background:color-mix(in srgb,var(--s-primary) 8%,var(--s-card));border-bottom:1px solid color-mix(in srgb,var(--s-line) 50%,transparent)">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5" style="flex-shrink:0"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
    <div style="flex:1;min-width:0">
      <h3 style="font-size:.88rem;font-weight:700;color:var(--s-ink);margin:0">{{ $group['module']->title }}</h3>
    </div>
    <span style="font-size:.72rem;font-weight:600;color:var(--s-muted);white-space:nowrap;background:var(--s-bg);padding:3px 10px;border-radius:20px">{{ $group['materials']->count() }} materi</span>
  </div>
  <div style="padding:6px 0">
    @forelse($group['materials'] as $m)
    <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;transition:background .15s;border-bottom:1px solid color-mix(in srgb,var(--s-line) 30%,transparent)" onmouseover="this.style.background='color-mix(in srgb,var(--s-primary) 4%,var(--s-card))'" onmouseout="this.style.background=''">
      <div style="width:32px;height:32px;border-radius:8px;background:color-mix(in srgb,var(--s-primary) 10%,transparent);display:grid;place-items:center;flex-shrink:0">
        @if ($m->type === 'file')
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        @elseif ($m->type === 'embed')
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
        @else
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        @endif
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:8px">
          <strong style="font-size:.82rem;color:var(--s-ink);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->title }}</strong>
          @if($m->file_name)
          <span style="font-size:.68rem;color:var(--s-muted);white-space:nowrap">({{ round($m->file_size / 1024) }}KB)</span>
          @endif
        </div>
        <p style="font-size:.72rem;font-weight:500;color:var(--s-primary);margin:2px 0 0">
          {{ $m->teachingAssignment->subject->name ?? $m->teachingAssignment->customSubject->nama ?? '-' }}
        </p>
      </div>
      <div style="display:flex;gap:4px;flex-shrink:0">
        @if($m->file_path)
        <a href="{{ route('download.materi', $m) }}" target="_blank" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;color:var(--s-primary);background:color-mix(in srgb,var(--s-primary) 8%,transparent);text-decoration:none;transition:all .15s" title="Download">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
        <a href="{{ route('download.materi.preview', $m) }}" target="_blank" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;color:var(--s-ink);background:var(--s-bg);text-decoration:none;transition:all .15s" title="Preview">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
        @endif
        @if($m->url)
        <a href="{{ $m->url }}" target="_blank" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;color:var(--s-primary);background:color-mix(in srgb,var(--s-primary) 8%,transparent);text-decoration:none;transition:all .15s" title="Buka Link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </a>
        @endif
      </div>
    </div>
    @empty
    <div style="padding:24px;text-align:center;color:var(--s-muted);font-size:.82rem">Belum ada materi di modul ini.</div>
    @endforelse
  </div>
</div>
@endforeach

{{-- Ungrouped materials --}}
@if($ungrouped->isNotEmpty())
<div class="b-card" style="padding:0;overflow:hidden;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px;padding:14px 18px;background:color-mix(in srgb,var(--s-muted) 6%,var(--s-card));border-bottom:1px solid color-mix(in srgb,var(--s-line) 50%,transparent)">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5" style="flex-shrink:0"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
    <div style="flex:1;min-width:0">
      <h3 style="font-size:.88rem;font-weight:700;color:var(--s-ink);margin:0">Materi Lainnya</h3>
    </div>
    <span style="font-size:.72rem;font-weight:600;color:var(--s-muted);white-space:nowrap;background:var(--s-bg);padding:3px 10px;border-radius:20px">{{ $ungrouped->count() }} materi</span>
  </div>
  <div style="padding:6px 0">
    @foreach($ungrouped as $m)
    <div style="display:flex;align-items:center;gap:12px;padding:10px 18px;transition:background .15s;border-bottom:1px solid color-mix(in srgb,var(--s-line) 30%,transparent)" onmouseover="this.style.background='color-mix(in srgb,var(--s-primary) 4%,var(--s-card))'" onmouseout="this.style.background=''">
      <div style="width:32px;height:32px;border-radius:8px;background:color-mix(in srgb,var(--s-primary) 10%,transparent);display:grid;place-items:center;flex-shrink:0">
        @if ($m->type === 'file')
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        @else
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--s-primary)" stroke-width="1.5"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        @endif
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:8px">
          <strong style="font-size:.82rem;color:var(--s-ink);display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->title }}</strong>
          @if($m->file_name)
          <span style="font-size:.68rem;color:var(--s-muted);white-space:nowrap">({{ round($m->file_size / 1024) }}KB)</span>
          @endif
        </div>
        <p style="font-size:.72rem;font-weight:500;color:var(--s-primary);margin:2px 0 0">
          {{ $m->teachingAssignment->subject->name ?? $m->teachingAssignment->customSubject->nama ?? '-' }}
        </p>
      </div>
      <div style="display:flex;gap:4px;flex-shrink:0">
        @if($m->file_path)
        <a href="{{ route('download.materi', $m) }}" target="_blank" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;color:var(--s-primary);background:color-mix(in srgb,var(--s-primary) 8%,transparent);text-decoration:none;transition:all .15s" title="Download">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </a>
        <a href="{{ route('download.materi.preview', $m) }}" target="_blank" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;color:var(--s-ink);background:var(--s-bg);text-decoration:none;transition:all .15s" title="Preview">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
        @endif
        @if($m->url)
        <a href="{{ $m->url }}" target="_blank" style="width:30px;height:30px;border-radius:7px;display:grid;place-items:center;color:var(--s-primary);background:color-mix(in srgb,var(--s-primary) 8%,transparent);text-decoration:none;transition:all .15s" title="Buka Link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </a>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif
@endsection
