@extends('layouts.siswa')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="flex items-center justify-between animate-fade-in">
  <div>
    <h2 class="text-xl font-bold text-slate-800">Selamat datang, {{ $student->full_name }}! 👋</h2>
    <p class="text-sm text-slate-400 mt-0.5">Semangat belajar hari ini</p>
  </div>
  <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-200/60 shadow-sm">
    <svg class="w-4 h-4 text-teal-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    <span class="text-xs font-semibold text-slate-600">{{ $period?->academic_year }} {{ ucfirst($period?->semester ?? '-') }}</span>
  </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-slide-up">
  <div class="relative p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-500 to-emerald-400"></div>
    <div class="flex items-start justify-between">
      <div>
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Nilai</div>
        <div class="mt-1 flex items-baseline gap-1.5">
          <span class="text-3xl font-bold bg-gradient-to-r from-teal-600 to-emerald-500 bg-clip-text text-transparent">{{ $avgScore }}</span>
          <span class="text-sm font-bold px-1.5 py-0.5 rounded-md {{ $avgScore >= 85 ? 'bg-emerald-50 text-emerald-600' : ($avgScore >= 75 ? 'bg-blue-50 text-blue-600' : ($avgScore >= 65 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')) }}">{{ $avgLetter }}</span>
        </div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center shadow-lg shadow-teal-500/20 shrink-0">
        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
      </div>
    </div>
  </div>

  <div class="relative p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-violet-500 to-purple-400"></div>
    <div class="flex items-start justify-between">
      <div>
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Kehadiran</div>
        <div class="mt-1 flex items-baseline gap-1.5">
          <span class="text-3xl font-bold bg-gradient-to-r from-violet-600 to-purple-500 bg-clip-text text-transparent">{{ $attendanceRate }}%</span>
        </div>
        <div class="mt-2 h-1.5 w-full bg-slate-100 rounded-full overflow-hidden max-w-[120px]">
          <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-purple-400 transition-all duration-1000" style="width:{{ $attendanceRate }}%"></div>
        </div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center shadow-lg shadow-violet-500/20 shrink-0">
        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
    </div>
  </div>

  <div class="relative p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-400 to-orange-400"></div>
    <div class="flex items-start justify-between">
      <div>
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Mata Pelajaran</div>
        <div class="mt-1 flex items-baseline gap-1.5">
          <span class="text-3xl font-bold bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">{{ $subjectCount }}</span>
          <span class="text-xs font-semibold text-slate-400">mapel</span>
        </div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>
      </div>
    </div>
  </div>

  <div class="relative p-5 rounded-2xl bg-white border border-slate-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 to-pink-400"></div>
    <div class="flex items-start justify-between">
      <div>
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Hadir</div>
        <div class="mt-1 flex items-baseline gap-1.5">
          <span class="text-3xl font-bold bg-gradient-to-r from-rose-600 to-pink-500 bg-clip-text text-transparent">{{ $attendanceBreakdown['present'] ?? 0 }}</span>
          <span class="text-xs font-semibold text-slate-400">hari</span>
        </div>
      </div>
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center shadow-lg shadow-rose-500/20 shrink-0">
        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
    </div>
  </div>
</div>

<div class="grid lg:grid-cols-3 gap-5">
  <div class="lg:col-span-2 space-y-5">
    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-slate-800">Nilai per Mata Pelajaran</h3>
        <a href="{{ route('siswa.nilai') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 transition-colors">Lihat semua →</a>
      </div>
      @if(count($grades) > 0)
        <div class="space-y-2.5">
          @foreach($grades as $g)
          <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/50 border border-slate-100/50 hover:border-teal-100 hover:bg-teal-50/30 transition-all duration-200">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">{{ substr($g['subject'], 0, 1) }}</div>
              <div>
                <div class="text-sm font-semibold text-slate-700">{{ $g['subject'] }}</div>
                <div class="text-[11px] text-slate-400">KKM {{ $g['kkm'] }}</div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="text-right">
                <div class="text-sm font-bold text-slate-800">{{ $g['final_score'] }}</div>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $g['passed'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">{{ $g['letter'] }}</span>
              </div>
              <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $g['passed'] ? 'bg-gradient-to-r from-teal-400 to-emerald-500' : 'bg-gradient-to-r from-rose-400 to-pink-500' }}" style="width:{{ min($g['final_score'], 100) }}%"></div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-slate-400 text-center py-8">Belum ada nilai dipublikasikan.</p>
      @endif
    </div>
  </div>

  <div class="space-y-5">
    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
      <h3 class="text-sm font-bold text-slate-800 mb-3">Jadwal Hari Ini</h3>
      @if($todaySchedule->count() > 0)
        <div class="space-y-2">
          @foreach($todaySchedule as $s)
          <div class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-100/50">
            <div class="w-9 h-9 rounded-lg bg-white shadow-sm flex items-center justify-center text-[11px] font-bold text-teal-700">{{ $s['slot'] }}</div>
            <div class="text-sm font-semibold text-slate-700">{{ $s['subject'] }}</div>
          </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-slate-400 text-center py-6">Tidak ada jadwal hari ini.</p>
      @endif
    </div>

    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
      <h3 class="text-sm font-bold text-slate-800 mb-3">Notifikasi</h3>
      @if($notifications->count() > 0)
        <div class="space-y-2">
          @foreach($notifications as $n)
          <div class="p-3 rounded-xl bg-slate-50/50 border border-slate-100/50">
            <div class="flex items-start gap-2.5">
              <div class="w-6 h-6 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <div>
                <div class="text-sm font-semibold text-slate-700">{{ $n->title }}</div>
                <div class="text-[11px] text-slate-400 mt-0.5">{{ Str::limit($n->body, 60) }}</div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p class="text-sm text-slate-400 text-center py-6">Tidak ada notifikasi.</p>
      @endif
    </div>
  </div>
</div>
@endsection


