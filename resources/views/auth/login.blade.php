@php
$roleParam = request()->query('role');
$isAdmin = $roleParam === 'admin';
$isGuru = $roleParam === 'guru';
$isStudent = $roleParam === 'student';
@endphp
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Portal Siswa InvestaSchool ">
  <title>Masuk | {{ $isAdmin ? 'Portal Admin' : ($isGuru ? 'Portal Guru' : ($isStudent ? 'Portal Siswa' : 'Portal Orang Tua')) }}</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
</head>

<body>
  <div class="portal-login-page">
    <section class="portal-login-brand">
      <div class="portal-login-shapes">
        <div class="portal-login-shape portal-login-shape-1"></div>
        <div class="portal-login-shape portal-login-shape-2"></div>
      </div>
      <a class="brand" href="{{ route('beranda') }}">
        <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="" width="28" height="28"></span>
        <span class="brand-text">InvestaSchool<small>UNGGUL • TERAMPIL • BERKARAKTER</small></span>
      </a>
      <div class="portal-login-copy">
        <span class="hero-badge">{{ $isAdmin ? 'Portal Administrator' : ($isGuru ? 'Portal Guru & Wali Kelas' : ($isStudent ? 'Portal Siswa' : 'Portal Perkembangan Siswa')) }}</span>
        <h1>{{ $isAdmin ? 'Kelola data sekolah, pengguna, dan konfigurasi sistem.' : ($isGuru ? 'Kelola kelas, input nilai, dan pantau perkembangan siswa.' : ($isStudent ? 'Akses nilai, jadwal, kehadiran, dan materi pelajaran.' : 'Pantau proses belajar anak dengan lebih dekat.')) }}</h1>
        <p>{{ $isAdmin ? 'Akses aman bagi administrator untuk mengelola siswa, guru, mata pelajaran, periode akademik, dan penugasan.' : ($isGuru ? 'Akses aman bagi guru untuk mengelola penilaian, input nilai, mencatat kehadiran, memberikan catatan perkembangan, dan mempublikasikan rapor.' : ($isStudent ? 'Akses aman bagi siswa untuk melihat nilai, jadwal pelajaran, materi, dan profil akademik.' : 'Akses aman bagi orang tua untuk melihat nilai, kehadiran, ketuntasan tugas, catatan guru, karakter, dan kegiatan siswa.')) }}</p>
        <div class="portal-feature-list">
          @if ($isAdmin)
          <div class="portal-feature"><strong>Kelola pengguna</strong><span>CRUD akun guru, orang tua, admin.</span></div>
          <div class="portal-feature"><strong>Data siswa</strong><span>Import CSV, assign wali kelas.</span></div>
          <div class="portal-feature"><strong>Konfigurasi</strong><span>Periode akademik, mata pelajaran, penugasan.</span></div>
          <div class="portal-feature"><strong>Audit log</strong><span>Pantau semua aktivitas sistem.</span></div>
          @elseif ($isGuru)
          <div class="portal-feature"><strong>Input nilai</strong><span>Kuis, PR, proyek, UTS, dan UAS.</span></div>
          <div class="portal-feature"><strong>Kelola kelas</strong><span>Jadwal, kehadiran, dan daftar siswa.</span></div>
          <div class="portal-feature"><strong>Catatan siswa</strong><span>Umpan balik dan rekomendasi belajar.</span></div>
          <div class="portal-feature"><strong>Publikasi rapor</strong><span>Validasi dan publikasi ke orang tua.</span></div>
          @elseif ($isStudent)
          <div class="portal-feature"><strong>Dashboard</strong><span>Ringkasan nilai, kehadiran, dan jadwal.</span></div>
          <div class="portal-feature"><strong>Nilai detail</strong><span>Skor per komponen: kuis, PR, proyek, UTS, UAS.</span></div>
          <div class="portal-feature"><strong>Jadwal & materi</strong><span>Jadwal pelajaran dan materi dari guru.</span></div>
          <div class="portal-feature"><strong>Profil siswa</strong><span>Data diri, sikap, dan ekstrakurikuler.</span></div>
          @else
          <div class="portal-feature"><strong>Nilai terperinci</strong><span>Kuis, PR, proyek, UTS, dan UAS.</span></div>
          <div class="portal-feature"><strong>Tren perkembangan</strong><span>Perbandingan hasil antarsemester.</span></div>
          <div class="portal-feature"><strong>Kehadiran</strong><span>Hadir, sakit, izin, dan tanpa keterangan.</span></div>
          <div class="portal-feature"><strong>Catatan guru</strong><span>Umpan balik dan tindak lanjut belajar.</span></div>
          @endif
        </div>
      </div>
      <small style="position:relative;z-index:2;color:#9fb7d5">&copy; {{ date('Y') }} InvestaSchool </small>
    </section>

    <section class="portal-login-form-wrap">
      <form class="portal-login-form" method="POST" action="{{ route('login') }}" autocomplete="on">
        @csrf
        @if ($isAdmin)
        <input type="hidden" name="role" value="admin">
        @elseif ($isGuru)
        <input type="hidden" name="role" value="guru">
        @elseif ($isStudent)
        <input type="hidden" name="role" value="student">
        @endif

        <span class="kicker">{{ $isAdmin ? 'Akses admin' : ($isGuru ? 'Akses guru' : ($isStudent ? 'Akses siswa' : 'Akses orang tua')) }}</span>
        <h2>Masuk ke {{ $isAdmin ? 'portal admin' : ($isGuru ? 'portal guru' : ($isStudent ? 'portal siswa' : 'portal')) }}</h2>
        <p class="section-desc">{{ $isAdmin ? 'Gunakan akun administrator yang terdaftar.' : ($isGuru ? 'Gunakan akun guru yang terdaftar pada administrasi sekolah.' : ($isStudent ? 'Gunakan email dan password saat mendaftar PPDB.' : 'Gunakan akun orang tua yang terdaftar pada administrasi sekolah.')) }}</p>

        @if ($errors->any())
        <div class="portal-error">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
        <div class="portal-success">{{ session('status') }}</div>
        @endif

        <div class="field" style="margin-top:17px">
          <label for="email">{{ $isAdmin ? 'Email admin' : ($isGuru ? 'Email guru' : ($isStudent ? 'NIS / Email' : 'Email orang tua')) }}</label>
          <input id="email" name="email" type="text" required value="{{ old('email') }}" placeholder="{{ $isStudent ? 'NIS atau email' : 'nama@email.com' }}" autofocus autocomplete="username">
        </div>

        <div class="field" style="margin-top:15px">
          <label for="password">Kata sandi</label>
          <div style="display:flex;gap:8px">
            <input id="password" name="password" type="password" required placeholder="Kata sandi" autocomplete="current-password">
            <button class="btn btn-outline" type="button" data-toggle-password="password">Tampilkan</button>
          </div>
        </div>

        <button class="btn btn-primary" type="submit">Masuk ke {{ $isAdmin ? 'Portal Admin' : ($isGuru ? 'Portal Guru' : ($isStudent ? 'Portal Siswa' : 'Dashboard')) }} <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:4px">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
          </svg></button>

        <p class="security-note"></p>
        <a class="text-link" href="{{ route('beranda') }}"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px">
            <path d="M19 12H5" />
            <path d="m12 19-7-7 7-7" />
          </svg> Kembali ke website sekolah</a>
        <p style="margin-top:14px;font-size:.85rem;color:var(--muted)">
          @if ($isAdmin)
          <a class="text-link" href="{{ route('login') }}">Masuk sebagai Orang Tua</a> &middot;
          <a class="text-link" href="{{ route('login') }}?role=guru">Masuk sebagai Guru</a>
          @elseif ($isGuru)
          <a class="text-link" href="{{ route('login') }}">Masuk sebagai Orang Tua</a> &middot;
          <a class="text-link" href="{{ route('login') }}?role=admin">Masuk sebagai Admin</a>
          @elseif ($isStudent)
          <a class="text-link" href="{{ route('login') }}">Masuk sebagai Orang Tua</a> &middot;
          <a class="text-link" href="{{ route('login') }}?role=guru">Masuk sebagai Guru</a>
          @else
          <a class="text-link" href="{{ route('login') }}?role=student">Masuk sebagai Siswa</a> &middot;
          <a class="text-link" href="{{ route('login') }}?role=guru">Masuk sebagai Guru</a> &middot;
          <a class="text-link" href="{{ route('login') }}?role=admin">Masuk sebagai Admin</a>
          @endif
        </p>
      </form>
    </section>
  </div>
  <script src="{{ asset('js/portal.js') }}"></script>
</body>

</html>


