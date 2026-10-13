<div class="tabs">
  <a class="tab-btn {{ $active === 'today' ? 'active' : '' }}" href="{{ route('admin.rfid.index') }}">Absensi Hari Ini</a>
  <a class="tab-btn {{ $active === 'registration' ? 'active' : '' }}" href="{{ route('admin.rfid.index', ['tab'=>'registration']) }}">Registrasi Kartu</a>
  <a class="tab-btn {{ $active === 'notifications' ? 'active' : '' }}" href="{{ route('admin.rfid.notifications') }}">Notifikasi</a>
  <a class="tab-btn {{ $active === 'settings' ? 'active' : '' }}" href="{{ route('admin.rfid.settings') }}">Pengaturan</a>
  <a class="tab-btn" href="{{ route('admin.attendance-recap.index') }}">Rekap Absensi ↗</a>
</div>
