@extends('layouts.siswa')
@section('title', 'Profil')
@section('page-title', 'Profil Siswa')

@section('content')
<div class="grid lg:grid-cols-3 gap-5">
  <div class="lg:col-span-1 space-y-5">
    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-6 text-center">
      <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-500 text-white flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-lg shadow-teal-500/20">{{ $initials }}</div>
      <h2 class="text-lg font-bold text-slate-800">{{ $student->full_name }}</h2>
      <p class="text-xs text-slate-400 mt-0.5">{{ $student->class_name }} &middot; {{ $student->program_name }}</p>
      <div class="mt-4 pt-4 border-t border-slate-100 text-left text-sm space-y-3">
        <div class="flex justify-between"><span class="text-slate-400">NISN</span><span class="font-semibold text-slate-700">{{ $student->nisn }}</span></div>
        <div class="flex justify-between"><span class="text-slate-400">Status</span><span class="inline-flex px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-600">Aktif</span></div>
        <div class="flex justify-between"><span class="text-slate-400">Semester</span><span class="font-semibold text-slate-700">{{ $period?->academic_year }} {{ ucfirst($period?->semester ?? '-') }}</span></div>
        @if($student->birth_date)
        <div class="flex justify-between"><span class="text-slate-400">Tanggal Lahir</span><span class="font-semibold text-slate-700">{{ $student->birth_date->translatedFormat('d M Y') }}</span></div>
        @endif
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
      <h3 class="text-sm font-bold text-slate-800 mb-3">Wali Kelas</h3>
      @if($student->homeroomTeacher)
      <div class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-teal-50 to-emerald-50">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white text-sm font-bold">{{ strtoupper(mb_substr($student->homeroomTeacher->full_name ?? $student->homeroomTeacher->name, 0, 1)) }}</div>
        <div>
          <div class="text-sm font-semibold text-slate-700">{{ $student->homeroomTeacher->full_name ?? $student->homeroomTeacher->name }}</div>
          <div class="text-xs text-slate-400">{{ $student->homeroomTeacher->email }}</div>
        </div>
      </div>
      @else
      <p class="text-xs text-slate-400">Belum ditentukan</p>
      @endif
    </div>
  </div>

  <div class="lg:col-span-2 space-y-5">
    @if($behavior->count() > 0)
    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
      <h3 class="text-sm font-bold text-slate-800 mb-3">Nilai Sikap</h3>
      <div class="grid grid-cols-2 gap-3">
        @php $aspectLabels = ['discipline' => 'Disiplin', 'responsibility' => 'Tanggung Jawab', 'collaboration' => 'Kolaborasi', 'independence' => 'Kemandirian'] @endphp
        @foreach($behavior as $b)
        <div class="p-4 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100/50 border border-slate-100/50">
          <div class="text-[11px] font-semibold text-slate-400 uppercase">{{ $aspectLabels[$b->aspect] ?? $b->aspect }}</div>
          <div class="text-2xl font-bold mt-1 {{ $b->grade >= 'A' ? 'text-teal-600' : ($b->grade >= 'B' ? 'text-amber-600' : 'text-slate-600') }}">{{ $b->grade }}</div>
          @if($b->note)
          <div class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $b->note }}</div>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($extracurriculars->count() > 0)
    <div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm p-5">
      <h3 class="text-sm font-bold text-slate-800 mb-3">Ekstrakurikuler</h3>
      <div class="grid sm:grid-cols-2 gap-3">
        @foreach($extracurriculars as $e)
        <div class="flex items-center gap-3 p-4 rounded-xl bg-slate-50/50 border border-slate-100/50 hover:border-teal-100 hover:bg-teal-50/30 transition-all duration-200">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold shadow-sm">{{ strtoupper(mb_substr($e->name, 0, 1)) }}</div>
          <div>
            <div class="text-sm font-semibold text-slate-700">{{ $e->name }}</div>
            <div class="text-xs text-slate-400">Nilai: <span class="font-semibold text-slate-600">{{ $e->score }}</span></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>
@endsection


