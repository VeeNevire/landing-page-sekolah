@extends('layouts.admin')

@section('title', 'Jadwal Mengajar')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Jadwal</span>
    <h1>Jadwal Mengajar</h1>
    <p>Lihat dan atur jadwal mengajar per mapel di setiap hari.</p>
  </div>
</div>

<div class="admin-toolbar">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;align-items:end">
    <div class="field" style="flex:1;min-width:200px;margin:0">
      <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Semester</label>
      <select name="semester_id" onchange="this.form.submit()" style="min-height:42px">
        @foreach ($periods as $period)
          <option value="{{ $period->id }}" {{ $semesterId == $period->id ? 'selected' : '' }}>
            {{ $period->academic_year }} {{ ucfirst($period->semester) }} {{ $period->is_active ? '(Aktif)' : '' }}
          </option>
        @endforeach
      </select>
    </div>
  </form>
</div>

{{-- Schedule Grid --}}
<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table" style="min-width:800px">
      <thead>
        <tr>
          <th style="min-width:110px">Jam</th>
          @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
            <th style="text-transform:capitalize;text-align:center">{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php $timeSlots = [1=>'07:00–08:30', 2=>'08:30–10:00', 3=>'10:15–11:45', 4=>'12:30–14:00', 5=>'14:00–15:30']; @endphp
        @foreach ($timeSlots as $slot => $time)
        <tr>
          <td style="font-size:.82rem;font-weight:700;color:var(--muted);white-space:nowrap">{{ $time }}</td>
          @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
            @php $cell = $grid[$day][$slot] ?? null; @endphp
            <td style="text-align:center;min-width:120px">
              @if ($cell)
                <div style="padding:10px 8px;border-radius:10px;background:color-mix(in srgb,var(--primary-2) 8%,var(--card));border:1px solid color-mix(in srgb,var(--primary-2) 15%,var(--line))">
                  <div style="font-weight:700;font-size:.88rem;color:var(--primary-2)">{{ $cell['code'] }}</div>
                  <div style="font-size:.78rem;font-weight:600;margin-top:2px">{{ $cell['subject'] }}</div>
                  <div style="font-size:.72rem;color:var(--muted);margin-top:2px">{{ $cell['teacher'] }}</div>
                </div>
              @else
                <span style="color:var(--line);font-size:.75rem">—</span>
              @endif
            </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>

{{-- Form Tambah Jadwal --}}
<section class="portal-panel" style="margin-top:20px;max-width:700px">
  <div style="padding:24px">
    <h3 style="margin:0 0 16px">Tambah Jadwal</h3>
    <form method="POST" action="{{ route('admin.jadwal.store') }}" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">
      @csrf

      <div class="field" style="flex:2;min-width:200px;margin:0">
        <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Mapel & Guru</label>
        <select name="guru_mapel_id" required style="min-height:42px">
          <option value="">-- Pilih Mapel --</option>
          @foreach ($subjects as $subject)
            <option value="{{ $subject['guru_mapel_id'] }}">{{ $subject['label'] }}</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="flex:1;min-width:130px;margin:0">
        <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Hari</label>
        <select name="day" required style="min-height:42px">
          <option value="">-- Pilih Hari --</option>
          @foreach (['senin','selasa','rabu','kamis','jumat'] as $day)
            <option value="{{ $day }}">{{ ucfirst($day) }}</option>
          @endforeach
        </select>
      </div>

      <div class="field" style="flex:1;min-width:130px;margin:0">
        <label style="font-size:.82rem;font-weight:700;color:var(--muted);display:block;margin-bottom:4px">Jam ke-</label>
        <select name="time_slot" required style="min-height:42px">
          <option value="">-- Pilih Slot --</option>
          @foreach ([1=>'07:00–08:30', 2=>'08:30–10:00', 3=>'10:15–11:45', 4=>'12:30–14:00', 5=>'14:00–15:30'] as $slot => $time)
            <option value="{{ $slot }}">Slot {{ $slot }} ({{ $time }})</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-primary" style="min-height:42px">Tambah</button>
    </form>
  </div>
</section>
@endsection
