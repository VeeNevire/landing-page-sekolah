<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Portal Orang Tua SMA/SMK Cakrawala Nusantara">
  <title>@yield('title') | Portal Orang Tua</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
  @stack('styles')
</head>
<body class="portal-body">
  <div class="portal-shell">
    <aside class="portal-sidebar" id="portalSidebar">
      <a class="brand" href="{{ route('portal.dashboard') }}">
        <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="" width="28" height="28"></span>
        <span class="brand-text">Portal Orang Tua<small>CAKRAWALA NUSANTARA</small></span>
      </a>
      <div class="portal-user-card">
        <strong>{{ auth()->user()->full_name ?? auth()->user()->name }}</strong>
        <span>{{ auth()->user()->email }}</span>
      </div>
      <nav class="portal-menu" aria-label="Menu Portal">
        <a href="{{ route('portal.dashboard', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.dashboard')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span class="portal-menu-label">Ringkasan</span>
        </a>
        <a href="{{ route('portal.laporan', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.laporan')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </span>
          <span class="portal-menu-label">Laporan Nilai</span>
        </a>
        <a href="{{ route('portal.kehadiran', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.kehadiran')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <span class="portal-menu-label">Kehadiran</span>
        </a>
        <a href="{{ route('portal.jadwal', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.jadwal')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </span>
          <span class="portal-menu-label">Jadwal</span>
        </a>
        <a href="{{ route('portal.tagihan', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.tagihan')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          </span>
          <span class="portal-menu-label">Tagihan</span>
        </a>
        <a href="{{ route('portal.profil', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.profil')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </span>
          <span class="portal-menu-label">Profil</span>
        </a>
        <a href="{{ route('portal.notifikasi', ['student_id' => $selectedStudentId ?? '']) }}"
           @class(['active' => request()->routeIs('portal.notifikasi')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
          </span>
          <span class="portal-menu-label">Pesan</span>
        </a>
      </nav>
      <div class="portal-menu-divider"></div>
      <div class="portal-sidebar-footer">
        <a href="{{ route('beranda') }}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          <span>Kembali ke website</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;padding:0;font:inherit;display:flex;align-items:center;gap:10px;padding:10px 11px;border-radius:10px;transition:all .2s ease;width:100%;font-size:.85rem" onmouseover="this.style.background='rgba(255,255,255,.06)'" onmouseout="this.style.background='transparent'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Keluar dari portal</span>
          </button>
        </form>
      </div>
    </aside>

    <main class="portal-main">
      <header class="portal-topbar">
        <div class="portal-topbar-left">
          <button class="icon-btn portal-mobile-menu" id="portalMenuButton" aria-label="Buka menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
          </button>
          <div>
            <h1 class="portal-topbar-title">@yield('title')</h1>
            <span class="portal-topbar-date">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
          </div>
        </div>
        @isset($students)
        <div class="student-switcher">
          <span class="student-avatar">{{ $selectedStudentInitials ?? 'S' }}</span>
          <select id="studentSwitcher" aria-label="Pilih siswa" onchange="window.location.href=this.value">
            @foreach ($students as $student)
              <option value="{{ route(request()->routeIs('portal.dashboard') ? 'portal.dashboard' : 'portal.laporan', ['student_id' => $student->id]) }}"
                      @selected(($selectedStudentId ?? '') == $student->id)>
                {{ $student->full_name }} — {{ $student->class_name }}
              </option>
            @endforeach
          </select>
        </div>
        @endisset
        <div class="portal-actions no-print">
          <button class="icon-btn" id="themeBtn" aria-label="Ubah tema">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
          </button>
          <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button class="btn btn-outline" type="submit">Keluar</button>
          </form>
        </div>
      </header>

      <div class="portal-content">
        @yield('content')
      </div>
    </main>
  </div>

  <script src="{{ asset('js/script.js') }}"></script>
  <script src="{{ asset('js/portal.js') }}"></script>
  @stack('scripts')
</body>
</html>
