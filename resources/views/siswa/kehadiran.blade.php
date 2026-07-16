@extends('layouts.siswa')
@section('title', 'Kehadiran')
@section('page-title', 'Kehadiran')

@section('content')
<div class="grid grid-cols-5 gap-4">
  <div class="p-4 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center mb-2">
      <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase">Hadir</div>
    <div class="text-xl font-bold text-emerald-600 mt-0.5">{{ $breakdown['present'] ?? 0 }}</div>
  </div>
  <div class="p-4 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center mb-2">
      <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase">Sakit</div>
    <div class="text-xl font-bold text-amber-600 mt-0.5">{{ $breakdown['sick'] ?? 0 }}</div>
  </div>
  <div class="p-4 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mb-2">
      <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
    </div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase">Izin</div>
    <div class="text-xl font-bold text-blue-600 mt-0.5">{{ $breakdown['excused'] ?? 0 }}</div>
  </div>
  <div class="p-4 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center mb-2">
      <svg class="w-4 h-4 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase">Terlambat</div>
    <div class="text-xl font-bold text-orange-600 mt-0.5">{{ $breakdown['late'] ?? 0 }}</div>
  </div>
  <div class="p-4 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center mb-2">
      <svg class="w-4 h-4 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    </div>
    <div class="text-[11px] font-semibold text-slate-400 uppercase">Alpa</div>
    <div class="text-xl font-bold text-rose-600 mt-0.5">{{ $breakdown['unexcused'] ?? 0 }}</div>
  </div>
</div>

<div class="grid lg:grid-cols-2 gap-5">
  <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
    <h3 class="text-sm font-bold text-slate-800 mb-3">Tingkat Kehadiran</h3>
    <div class="flex items-center gap-6">
      <div class="relative w-24 h-24">
        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="16" fill="none" stroke="#e2e8f0" stroke-width="3"/>
          <circle cx="18" cy="18" r="16" fill="none" stroke="currentColor" stroke-width="3" stroke-dasharray="{{ $rate * 1.0048 }} {{ 100.48 - $rate * 1.0048 }}" class="text-teal-500" style="stroke-linecap:round"/>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
          <span class="text-lg font-bold text-slate-800">{{ $rate }}%</span>
        </div>
      </div>
      <div>
        <p class="text-sm text-slate-600">{{ $breakdown['present'] ?? 0 }} hadir dari {{ $total }} hari total</p>
        <div class="mt-2 text-xs text-slate-400">
          <span class="inline-flex items-center gap-1.5 mr-3"><span class="w-2 h-2 rounded-full bg-teal-500"></span>Hadir {{ $breakdown['present'] ?? 0 }}</span>
          <span class="inline-flex items-center gap-1.5 mr-3"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Sakit {{ $breakdown['sick'] ?? 0 }}</span>
          <span class="inline-flex items-center gap-1.5 mr-3"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Izin {{ $breakdown['excused'] ?? 0 }}</span>
          <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Alpa {{ $breakdown['unexcused'] ?? 0 }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
    <h3 class="text-sm font-bold text-slate-800 mb-3">Riwayat Terakhir</h3>
    <div class="space-y-1.5">
      @foreach($recentAttendance->take(8) as $a)
      @php
        $colors = ['present' => 'bg-emerald-100 text-emerald-700','sick' => 'bg-amber-100 text-amber-700','excused' => 'bg-blue-100 text-blue-700','late' => 'bg-orange-100 text-orange-700','unexcused' => 'bg-rose-100 text-rose-700'];
        $icons = ['present' => '✓','sick' => 'S','excused' => 'I','late' => 'L','unexcused' => 'A'];
      @endphp
      <div class="flex items-center justify-between py-2 px-3 rounded-xl hover:bg-slate-50 transition-colors">
        <div class="flex items-center gap-3">
          <span class="w-6 h-6 rounded-full {{ $colors[$a->status] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-[11px] font-bold">{{ $icons[$a->status] ?? '?' }}</span>
          <span class="text-sm text-slate-600">{{ $a->attendance_date->translatedFormat('d M Y') }}</span>
        </div>
        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md {{ $colors[$a->status] ?? 'bg-slate-100 text-slate-600' }}">
          {{ ['present'=>'Hadir','sick'=>'Sakit','excused'=>'Izin','late'=>'Terlambat','unexcused'=>'Alpa'][$a->status] ?? $a->status }}
        </span>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection