<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Portal Alumni InvestaSchool">
  <title>@yield('title') | Portal Alumni</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
  <style>
    .alumni-navbar {
      background: var(--card);
      color: var(--ink);
      padding: 0 clamp(18px, 3vw, 40px);
      min-height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--line);
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .alumni-navbar .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--ink);
      text-decoration: none;
      font-weight: 700;
    }
    .alumni-navbar .brand-text {
      display: flex;
      flex-direction: column;
      line-height: 1.1;
    }
    .alumni-navbar .brand-text small {
      font-size: 0.65rem;
      color: var(--muted);
      font-weight: normal;
      letter-spacing: 0.05em;
    }
    .alumni-nav-links {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .alumni-nav-link {
      color: var(--muted);
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      padding: 8px 16px;
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    .alumni-nav-link:hover {
      background: var(--bg);
      color: var(--ink);
    }
    .alumni-nav-link.active {
      background: color-mix(in srgb, var(--primary) 12%, var(--card));
      color: var(--primary);
    }
    .alumni-navbar-right {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .alumni-user-info {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85rem;
      color: var(--ink);
    }
    .alumni-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--primary-2);
      color: white;
      display: grid;
      place-items: center;
      font-weight: 700;
      font-size: 0.85rem;
    }
    .alumni-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 30px 18px 60px;
    }
    .btn-logout-alumni {
      border: 1px solid var(--line);
      color: var(--ink);
      background: transparent;
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.82rem;
      transition: all 0.2s ease;
    }
    .btn-logout-alumni:hover {
      background: rgba(239, 68, 68, 0.1);
      color: #f87171;
      border-color: rgba(239, 68, 68, 0.3);
    }
    @media (max-width: 768px) {
      .alumni-navbar {
        flex-direction: column;
        padding: 12px;
        gap: 12px;
      }
      .alumni-nav-links {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
      }
      .alumni-navbar-right {
        width: 100%;
        justify-content: space-between;
      }
    }
  </style>
  @stack('styles')
</head>

<body class="portal-body">
  <nav class="alumni-navbar">
    <a class="brand" href="{{ route('alumni.dashboard') }}">
      <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="" width="28" height="28"></span>
      <div class="brand-text">
        <span>Portal Alumni</span>
        <small>InvestaSchool</small>
      </div>
    </a>

    <div class="alumni-nav-links">
      <a href="{{ route('alumni.dashboard') }}" class="alumni-nav-link {{ request()->routeIs('alumni.dashboard') ? 'active' : '' }}">Dashboard</a>
      <a href="{{ route('alumni.profil') }}" class="alumni-nav-link {{ request()->routeIs('alumni.profil') ? 'active' : '' }}">Profil & Nilai</a>
      <a href="{{ route('alumni.sertifikat') }}" class="alumni-nav-link {{ request()->routeIs('alumni.sertifikat') ? 'active' : '' }}">Sertifikat</a>
      <a href="{{ route('alumni.proyek') }}" class="alumni-nav-link {{ request()->routeIs('alumni.proyek') ? 'active' : '' }}">Proyek</a>
    </div>

    <div class="alumni-navbar-right">
      <div class="alumni-user-info">
        <span class="alumni-avatar">{{ substr(auth()->user()->name, 0, 1) }}</span>
        <span style="font-weight:600">{{ auth()->user()->name }}</span>
      </div>
      <form id="logoutForm" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn-logout-alumni" type="button" onclick="confirmLogout()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          <span>Keluar</span>
        </button>
      </form>
    </div>
  </nav>

  <div class="alumni-container">
    @if(session('success'))
      <div class="portal-success" style="margin-bottom: 20px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="portal-error" style="margin-bottom: 20px;">{{ session('error') }}</div>
    @endif
    @yield('content')
  </div>

  <script src="{{ asset('js/script.js') }}"></script>
  <script src="{{ asset('js/portal.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  function confirmLogout() {
    Swal.fire({
      title: 'Keluar dari portal?',
      text: 'Anda akan keluar dari sistem dan perlu login kembali.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, keluar',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#ef4444',
      width: 420
    }).then(r => {
      if (r.isConfirmed) document.getElementById('logoutForm').submit();
    });
  }
  </script>
  @stack('scripts')
</body>

</html>