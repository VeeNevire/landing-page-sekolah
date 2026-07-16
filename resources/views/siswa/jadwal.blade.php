@extends('layouts.siswa')
@section('title', 'Jadwal')
@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="rounded-2xl bg-white border border-slate-200/50 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="text-left py-3 px-4 text-[11px] font-semibold text-slate-400 uppercase w-16 bg-slate-50/50"></th>
          @foreach($days as $day)
          <th class="text-center py-3 px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wide bg-slate-50/50 border-l border-slate-100">{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php $subjectColors = ['#0d9488','#6366f1','#f43f5e','#a855f7','#f59e0b','#0891b2','#8b5cf6','#ec4899'] @endphp
        @for($slot = 1; $slot <= 3; $slot++)
        <tr class="border-t border-slate-100">
          <td class="py-3 px-4 text-center">
            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-[11px] font-bold text-slate-500">{{ $slot }}</span>
          </td>
          @foreach($days as $day)
          <td class="py-3 px-2 text-center border-l border-slate-50">
            @php $match = $jadwal[$day]->firstWhere('slot', $slot) @endphp
            @if($match)
            @php $idx = crc32($match['subject']) % 8; $bg = $subjectColors[abs($idx)] @endphp
            <div class="inline-flex flex-col items-center px-3 py-2 rounded-xl w-full" style="background:color-mix(in srgb, {{ $bg }} 12%, transparent);border:1px solid color-mix(in srgb, {{ $bg }} 20%, transparent)">
              <span class="text-xs font-bold" style="color:{{ $bg }}">{{ $match['subject'] }}</span>
            </div>
            @else
            <span class="text-slate-200">-</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endfor
      </tbody>
    </table>
  </div>
</div>
@endsection