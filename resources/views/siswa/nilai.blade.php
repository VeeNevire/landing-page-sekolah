@extends('layouts.siswa')
@section('title', 'Nilai')
@section('page-title', 'Laporan Nilai')

@section('content')
<div class="grid grid-cols-3 gap-4">
  <div class="p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Rata-rata</div>
    <div class="flex items-baseline gap-2">
      <span class="text-3xl font-bold bg-gradient-to-r from-teal-600 to-emerald-500 bg-clip-text text-transparent">{{ $avgScore }}</span>
      <span class="text-sm font-bold px-1.5 py-0.5 rounded-md {{ $avgScore >= 85 ? 'bg-emerald-50 text-emerald-600' : ($avgScore >= 75 ? 'bg-blue-50 text-blue-600' : ($avgScore >= 65 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')) }}">{{ $avgLetter }}</span>
    </div>
  </div>
  <div class="p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Lulus</div>
    <div class="text-3xl font-bold text-emerald-600">{{ count(array_filter($grades, fn($g) => $g['passed'])) }}</div>
    <div class="text-xs text-slate-400 mt-1">dari {{ count($grades) }} mapel</div>
  </div>
  <div class="p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm">
    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Tidak Lulus</div>
    <div class="text-3xl font-bold {{ count(array_filter($grades, fn($g) => !$g['passed'])) > 0 ? 'text-rose-600' : 'text-slate-800' }}">{{ count(array_filter($grades, fn($g) => !$g['passed'])) }}</div>
    <div class="text-xs text-slate-400 mt-1">di bawah KKM</div>
  </div>
</div>

<div class="grid md:grid-cols-2 gap-4">
  @foreach($grades as $g)
  <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5 hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">{{ substr($g['subject'], 0, 2) }}</div>
        <div>
          <div class="text-sm font-bold text-slate-800">{{ $g['subject'] }}</div>
          <div class="text-[11px] text-slate-400">KKM {{ $g['kkm'] }}</div>
        </div>
      </div>
      <div class="text-right">
        <div class="text-lg font-bold text-slate-800">{{ $g['final_score'] }}</div>
        <span class="inline-block px-2 py-0.5 rounded-md text-xs font-bold {{ $g['passed'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ $g['letter'] }}</span>
      </div>
    </div>
    <div class="space-y-2.5">
      @foreach(['quiz' => 'Kuis', 'homework' => 'PR', 'project' => 'Proyek', 'uts' => 'UTS', 'uas' => 'UAS'] as $key => $label)
      <div class="flex items-center justify-between text-sm">
        <span class="text-slate-500">{{ $label }}</span>
        <span class="font-semibold {{ $g['components'][$key] > 0 ? 'text-slate-800' : 'text-slate-300' }}">{{ $g['components'][$key] > 0 ? number_format($g['components'][$key], 1) : '-' }}</span>
      </div>
      @endforeach
    </div>
    <div class="mt-3 pt-3 border-t border-slate-100">
      <div class="flex items-center justify-between text-sm">
        <span class="text-slate-500 font-semibold">Nilai Akhir</span>
        <span class="font-bold text-slate-800">{{ $g['final_score'] }}</span>
      </div>
      <div class="mt-1.5 h-1.5 bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full rounded-full {{ $g['passed'] ? 'bg-gradient-to-r from-teal-400 to-emerald-500' : 'bg-gradient-to-r from-rose-400 to-pink-500' }}" style="width:{{ min($g['final_score'], 100) }}%"></div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection


