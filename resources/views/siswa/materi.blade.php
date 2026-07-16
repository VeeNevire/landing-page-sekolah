@extends('layouts.siswa')
@section('title', 'Materi')
@section('page-title', 'Materi Pelajaran')

@section('content')
@if($materials->count() > 0)
<div class="grid md:grid-cols-2 gap-4">
  @foreach($materials as $m)
  <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
    <div class="flex items-start gap-4">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center shadow-lg shadow-teal-500/20 shrink-0">
        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
      </div>
      <div class="min-w-0 flex-1">
        <div class="flex items-start justify-between gap-2">
          <div>
            <h3 class="text-sm font-bold text-slate-800">{{ $m->title }}</h3>
            <p class="text-[11px] font-medium text-teal-600 mt-0.5">{{ $m->teachingAssignment->subject->name ?? '-' }}</p>
          </div>
          <span class="text-[11px] text-slate-400 shrink-0">{{ $m->created_at->translatedFormat('d M') }}</span>
        </div>
        @if($m->description)
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ Str::limit($m->description, 120) }}</p>
        @endif
        @if($m->url)
        <a href="{{ $m->url }}" target="_blank" class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold text-teal-600 hover:text-teal-700 transition-colors">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Buka Materi
        </a>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-12 text-center">
  <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
    <svg class="w-8 h-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
  </div>
  <h3 class="text-sm font-bold text-slate-600 mb-1">Belum ada materi</h3>
  <p class="text-sm text-slate-400">Materi akan muncul ketika guru menguploadnya.</p>
</div>
@endif
@endsection