@extends('layouts.admin')
@section('title', 'Pengaturan RFID')
@section('content')
<div class="portal-heading"><div><span class="kicker">Kehadiran siswa</span><h1>Pengaturan RFID</h1><p>Atur aturan pencatatan absensi dari reader RFID.</p></div></div>
@include('admin.partials.rfid-tabs', ['active' => 'settings'])
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
@if($errors->any())<p role="alert" style="color:var(--danger)">{{ $errors->first() }}</p>@endif
<section class="portal-panel">
  <div class="portal-panel-header"><div><h2>Status Terlambat</h2><p>Scan yang masuk setelah batas jam dicatat sebagai <strong>Terlambat</strong>, sebelum atau tepat pada batas dicatat <strong>Hadir</strong>.</p></div></div>
  <form method="POST" action="{{ route('admin.rfid.settings.save') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:end">
    @csrf
    <input type="hidden" name="late_enabled" value="0">
    <label for="late-enabled" style="display:flex;gap:8px;align-items:center;min-height:42px">
      <input type="checkbox" id="late-enabled" name="late_enabled" value="1" @checked(old('late_enabled', $settings->late_enabled))>
      Aktifkan status Terlambat
    </label>
    <div class="field">
      <label for="late-after">Batas jam masuk (WIB)</label>
      <input type="time" id="late-after" name="late_after" value="{{ old('late_after', $settings->late_after ? substr($settings->late_after, 0, 5) : '') }}">
    </div>
    <button class="btn btn-primary">Simpan Pengaturan</button>
  </form>
  <p style="color:var(--muted);margin-top:12px">Perubahan berlaku untuk scan berikutnya; absensi yang sudah tercatat tidak diubah. Jam mengikuti waktu server ({{ config('app.timezone') }}).</p>
</section>
@endsection
