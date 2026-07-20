<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') | Portal Siswa</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/siswa.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @stack('styles')
</head>
<body class="siswa-body">
  <div class="siswa-shell">

    {{-- Sidebar --}}
    <aside class="siswa-sidebar" id="siswaSidebar">
      <a class="siswa-brand" href="{{ route('siswa.dashboard') }}">
        <span class="siswa-brand-mark"><img src="{{ asset('img/logo.svg') }}" alt=""></span>
        <span class="siswa-brand-text">Portal Siswa<small>InvestaSchool</small></span>
      </a>

      <div class="siswa-user-card">
        <div class="siswa-user-avatar">{{ $initials }}</div>
        <div>
          <span class="siswa-user-name">{{ $student->full_name }}</span>
          <span class="siswa-user-class">{{ $student->class_name }}</span>
        </div>
      </div>

      <nav class="siswa-nav">
        <div class="siswa-nav-group">Pembelajaran</div>
        <a href="{{ route('siswa.dashboard') }}" class="siswa-nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
          <span class="siswa-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
          Dashboard
        </a>
        <a href="{{ route('siswa.nilai') }}" class="siswa-nav-item {{ request()->routeIs('siswa.nilai') ? 'active' : '' }}">
          <span class="siswa-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
          Nilai
        </a>
        <a href="{{ route('siswa.kehadiran') }}" class="siswa-nav-item {{ request()->routeIs('siswa.kehadiran') ? 'active' : '' }}">
          <span class="siswa-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
          Kehadiran
        </a>
        <a href="{{ route('siswa.jadwal') }}" class="siswa-nav-item {{ request()->routeIs('siswa.jadwal') ? 'active' : '' }}">
          <span class="siswa-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
          Jadwal
        </a>
        <a href="{{ route('siswa.materi') }}" class="siswa-nav-item {{ request()->routeIs('siswa.materi') ? 'active' : '' }}">
          <span class="siswa-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg></span>
          Materi
        </a>
        <div class="siswa-nav-divider"></div>
        <a href="{{ route('siswa.profil') }}" class="siswa-nav-item {{ request()->routeIs('siswa.profil') ? 'active' : '' }}">
          <span class="siswa-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
          Profil
        </a>
      </nav>

      <div class="siswa-sidebar-footer">
        <a href="{{ route('beranda') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
          Website
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
          </button>
        </form>
      </div>
    </aside>

    {{-- Main --}}
    <div class="siswa-main">
      <header class="siswa-topbar">
        <div class="siswa-topbar-left">
          <button class="siswa-mobile-menu" id="siswaMenuBtn" aria-label="Buka menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
          </button>
          <div>
            <div class="siswa-topbar-title">@yield('title')</div>
            <div class="siswa-topbar-date">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
          </div>
        </div>
        <div class="siswa-topbar-right">
          @if(isset($period) && $period)
          <span class="siswa-topbar-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $period->academic_year }} {{ ucfirst($period->semester) }}
          </span>
          @endif
          <div class="siswa-topbar-avatar">{{ $initials }}</div>
        </div>
      </header>

      <main class="siswa-content">
        @if (session('success'))
        <div style="padding:14px 18px;border-radius:14px;background:rgba(52,199,89,0.1);color:var(--s-success);font-weight:600;margin-bottom:18px;border:1px solid rgba(52,199,89,0.15)">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div style="padding:14px 18px;border-radius:14px;background:rgba(255,59,48,0.1);color:var(--s-danger);font-weight:600;margin-bottom:18px;border:1px solid rgba(255,59,48,0.15)">{{ session('error') }}</div>
        @endif
        @yield('content')
      </main>
    </div>

  </div>

  <script>
    document.getElementById('siswaMenuBtn')?.addEventListener('click', function() {
      const sidebar = document.getElementById('siswaSidebar');
      const isHidden = sidebar.style.display === 'none' || getComputedStyle(sidebar).display === 'none';
      if (isHidden) {
        sidebar.style.display = 'flex';
        sidebar.style.position = 'fixed';
        sidebar.style.inset = '0';
        sidebar.style.zIndex = '50';
        sidebar.style.width = '260px';
        sidebar.style.overflow = 'auto';
      } else {
        if (window.innerWidth <= 768) {
          sidebar.style.display = 'none';
        }
      }
    });

    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('siswaSidebar');
      if (window.innerWidth > 768) {
        sidebar.style.display = '';
        sidebar.style.position = '';
        sidebar.style.inset = '';
        sidebar.style.zIndex = '';
        sidebar.style.width = '';
        sidebar.style.overflow = '';
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
