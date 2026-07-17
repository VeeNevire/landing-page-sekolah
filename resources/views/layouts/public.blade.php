<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="@yield('meta_description', 'Website resmi InvestaSchool ')">
  <title>@yield('title', config('app.name'))</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Calistoga&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @stack('styles')
</head>

<body>
  @if(!request()->routeIs('ppdb.form') && !request()->routeIs('ppdb.upload') && !request()->routeIs('ppdb.status') && !request()->routeIs('ppdb.payment') && !request()->routeIs('ppdb.success'))
  <header class="navbar">
    <div class="container nav-inner">
      <a class="brand" href="{{ route('beranda') }}">
        <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="Logo"></span>
        <span class="brand-text">InvestaSchool</span>
      </a>
      <nav class="nav-links" id="navLinks">
        <a href="{{ route('beranda') }}" @class(['active'=> request()->routeIs('beranda')])>Beranda</a>
        <a href="{{ route('profil') }}" @class(['active'=> request()->routeIs('profil')])>Profil</a>
        <a href="{{ route('akademik') }}" @class(['active'=> request()->routeIs('akademik')])>Akademik</a>
        <a href="{{ route('ppdb') }}" @class(['active'=> request()->routeIs('ppdb')])>PPDB</a>
        <div class="nav-dropdown">
          <button class="nav-dropdown-trigger" type="button" aria-haspopup="true" aria-expanded="false">
            Portal
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </button>
          <div class="nav-dropdown-menu">
            <a href="{{ route('login') }}?role=student">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20" />
              </svg>
              Portal Siswa
            </a>
            <a href="{{ route('login') }}">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
              Orang Tua / Wali
            </a>
            <a href="{{ route('login') }}?role=guru">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <polyline points="16 11 18 13 22 9" />
              </svg>
              Guru / Wali Kelas
            </a>
            <a href="{{ route('login') }}?role=admin">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
              </svg>
              Administrator
            </a>
          </div>
        </div>
        <a href="{{ route('kontak') }}" @class(['active'=> request()->routeIs('kontak')])>Kontak</a>
      </nav>
      <div class="nav-actions">
        <button class="icon-btn" id="themeBtn">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5" />
            <line x1="12" y1="1" x2="12" y2="3" />
            <line x1="12" y1="21" x2="12" y2="23" />
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
            <line x1="1" y1="12" x2="3" y2="12" />
            <line x1="21" y1="12" x2="23" y2="12" />
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
          </svg>
        </button>
        <a class="btn-cta" href="{{ route('ppdb.start') }}">Daftar Sekarang</a>
        <button class="icon-btn menu-btn" id="menuBtn">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="6" x2="20" y2="6" />
            <line x1="4" y1="12" x2="20" y2="12" />
            <line x1="4" y1="18" x2="20" y2="18" />
          </svg>
        </button>
      </div>
    </div>
  </header>
  @endif

  <main class="@if(request()->routeIs('ppdb.form') || request()->routeIs('ppdb.upload') || request()->routeIs('ppdb.status') || request()->routeIs('ppdb.payment') || request()->routeIs('ppdb.success')) no-nav @endif">
    @yield('content')
  </main>

  <footer>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <div class="brand">
            <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="Logo" width="32" height="32"></span>
            <span class="brand-text">InvestaSchool<small>UNGGUL • TERAMPIL • BERKARAKTER</small></span>
          </div>
          <p style="margin-top:1rem;color:var(--muted);line-height:1.7">Mencetak generasi unggul, terampil, dan berkarakter sejak 2008 — menuju Indonesia Emas 2045.</p>
        </div>
        <div class="footer-col">
          <h4>Tautan</h4>
          <ul>
            <li><a href="{{ route('profil') }}">Profil Sekolah</a></li>
            <li><a href="{{ route('akademik') }}">Program Akademik</a></li>
            <li><a href="{{ route('ppdb') }}">PPDB</a></li>
            <li><a href="{{ route('kontak') }}">Kontak</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Layanan</h4>
          <ul>
            <li><a href="#">E-Learning</a></li>
            <li><a href="#">Portal Siswa</a></li>
            <li><a href="{{ route('login') }}">Portal</a></li>
            <li><a href="#">Perpustakaan</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Kontak</h4>
          <address>
            Jl. Raya Leuwinanggung No.111, Leuwinanggung, Kec. Tapos, Kota Depok, Jawa Barat 16456<br>
            <a href="tel:+62215550198">(021) 555-0198</a><br>
            <a href="mailto:info@investaschool.sch.id">info@investaschool.sch.id</a><br>
            WhatsApp: <a href="https://wa.me/6281234567890">+62 812-3456-7890</a>
          </address>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} InvestaSchool .</span>
      </div>
    </div>
  </footer>

  <button class="back-top" id="backTop">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="m18 15-6-6-6 6" />
    </svg>
  </button>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/script.js') }}"></script>
  @stack('scripts')
</body>

</html>


