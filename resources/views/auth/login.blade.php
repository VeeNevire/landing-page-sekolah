@php
  $isGuru = request()->query('role') === 'guru';
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Portal Orang Tua SMA/SMK Cakrawala Nusantara">
  <title>Masuk | {{ $isGuru ? 'Portal Guru' : 'Portal Orang Tua' }}</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>
<body>
<div class="portal-login-page">
  <section class="portal-login-brand">
    <a class="brand" href="{{ route('beranda') }}">
      <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="" width="28" height="28"></span>
      <span class="brand-text">SMA/SMK Cakrawala<small>UNGGUL • TERAMPIL • BERKARAKTER</small></span>
    </a>
    <div class="portal-login-copy">
      <span class="hero-badge">{{ $isGuru ? 'Portal Guru & Wali Kelas' : 'Portal Perkembangan Siswa' }}</span>
      <h1>{{ $isGuru ? 'Kelola kelas, input nilai, dan pantau perkembangan siswa.' : 'Pantau proses belajar anak dengan lebih dekat.' }}</h1>
      <p>{{ $isGuru ? 'Akses aman bagi guru untuk mengelola penilaian, input nilai, mencatat kehadiran, memberikan catatan perkembangan, dan mempublikasikan rapor.' : 'Akses aman bagi orang tua untuk melihat nilai, kehadiran, ketuntasan tugas, catatan guru, karakter, dan kegiatan siswa.' }}</p>
      <div class="portal-feature-list">
        @if ($isGuru)
          <div class="portal-feature"><strong>Input nilai</strong><span>Kuis, PR, proyek, UTS, dan UAS.</span></div>
          <div class="portal-feature"><strong>Kelola kelas</strong><span>Jadwal, kehadiran, dan daftar siswa.</span></div>
          <div class="portal-feature"><strong>Catatan siswa</strong><span>Umpan balik dan rekomendasi belajar.</span></div>
          <div class="portal-feature"><strong>Publikasi rapor</strong><span>Validasi dan publikasi ke orang tua.</span></div>
        @else
          <div class="portal-feature"><strong>Nilai terperinci</strong><span>Kuis, PR, proyek, UTS, dan UAS.</span></div>
          <div class="portal-feature"><strong>Tren perkembangan</strong><span>Perbandingan hasil antarsemester.</span></div>
          <div class="portal-feature"><strong>Kehadiran</strong><span>Hadir, sakit, izin, dan tanpa keterangan.</span></div>
          <div class="portal-feature"><strong>Catatan guru</strong><span>Umpan balik dan tindak lanjut belajar.</span></div>
        @endif
      </div>
    </div>
    <small style="position:relative;z-index:2;color:#9fb7d5">&copy; {{ date('Y') }} SMA/SMK Cakrawala Nusantara</small>
  </section>

  <section class="portal-login-form-wrap">
    <form class="portal-login-form" method="POST" action="{{ route('login') }}" autocomplete="on">
      @csrf
      @if ($isGuru)
        <input type="hidden" name="role" value="guru">
      @endif

      <span class="kicker">{{ $isGuru ? 'Akses guru' : 'Akses orang tua' }}</span>
      <h2>Masuk ke {{ $isGuru ? 'portal guru' : 'portal' }}</h2>
      <p class="section-desc">{{ $isGuru ? 'Gunakan akun guru yang terdaftar pada administrasi sekolah.' : 'Gunakan akun orang tua yang terdaftar pada administrasi sekolah.' }}</p>

      @if ($isGuru)
      <div class="portal-demo-box">
        <strong>Akun demonstrasi</strong>
        <code>Email: guru@demo.sch.id</code>
        <code>Kata sandi: Demo123!</code>
      </div>
      @else
      <div class="portal-demo-box">
        <strong>Akun demonstrasi</strong>
        <code>Email: orangtua@demo.sch.id</code>
        <code>Kata sandi: Demo123!</code>
      </div>
      @endif

      @if ($errors->any())
        <div class="portal-error">{{ $errors->first() }}</div>
      @endif

      @if (session('status'))
        <div class="portal-success">{{ session('status') }}</div>
      @endif

      <div class="field" style="margin-top:17px">
        <label for="email">{{ $isGuru ? 'Email guru' : 'Email orang tua' }}</label>
        <input id="email" name="email" type="email" required value="{{ old('email', $isGuru ? 'guru@demo.sch.id' : 'orangtua@demo.sch.id') }}" placeholder="nama@email.com" autofocus autocomplete="username">
      </div>

      <div class="field" style="margin-top:15px">
        <label for="password">Kata sandi</label>
        <div style="display:flex;gap:8px">
          <input id="password" name="password" type="password" required value="Demo123!" placeholder="Kata sandi" autocomplete="current-password">
          <button class="btn btn-outline" type="button" data-toggle-password="password">Tampilkan</button>
        </div>
      </div>

      <button class="btn btn-primary" type="submit" style="width:100%;margin-top:20px">Masuk ke {{ $isGuru ? 'Portal Guru' : 'Dashboard' }} <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:4px"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></button>

      <p class="security-note">Versi ini adalah prototipe demonstrasi. Pada implementasi produksi, akun harus terhubung ke database sekolah, verifikasi identitas, HTTPS, dan pengamanan akses berbasis peran.</p>
      <a class="text-link" href="{{ route('beranda') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg> Kembali ke website sekolah</a>
      <p style="margin-top:14px;font-size:.85rem;color:var(--muted)">
        @if ($isGuru)
          <a class="text-link" href="{{ route('login') }}">Masuk sebagai Orang Tua</a>
        @else
          <a class="text-link" href="{{ route('login') }}?role=guru">Masuk sebagai Guru</a>
        @endif
      </p>
    </form>
  </section>
</div>
<script src="{{ asset('js/portal.js') }}"></script>
</body>
</html>
