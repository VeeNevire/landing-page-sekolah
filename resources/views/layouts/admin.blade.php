<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Portal Admin SMA/SMK Cakrawala Nusantara">
  <title>@yield('title') | Portal Admin</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  @stack('styles')
</head>
<body class="portal-body admin-body">
  <div class="portal-shell">
    <aside class="portal-sidebar admin-sidebar" id="adminSidebar">
      <a class="brand" href="{{ route('admin.dashboard') }}">
        <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="" width="28" height="28"></span>
        <span class="brand-text">Portal Admin<small>CAKRAWALA NUSANTARA</small></span>
      </a>
      <div class="portal-user-card">
        <div class="portal-user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div>
          <strong>{{ auth()->user()->full_name ?? auth()->user()->name }}</strong>
          <span>{{ auth()->user()->email }}</span>
        </div>
      </div>
      <nav class="portal-menu" aria-label="Menu Admin">
        <a href="{{ route('admin.dashboard') }}"
           @class(['active' => request()->routeIs('admin.dashboard')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span class="portal-menu-label">Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           @class(['active' => request()->routeIs('admin.users.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </span>
          <span class="portal-menu-label">Pengguna</span>
        </a>
        <a href="{{ route('admin.students.index') }}"
           @class(['active' => request()->routeIs('admin.students.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </span>
          <span class="portal-menu-label">Siswa</span>
        </a>
        <a href="{{ route('admin.subjects.index') }}"
           @class(['active' => request()->routeIs('admin.subjects.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
          </span>
          <span class="portal-menu-label">Mapel</span>
        </a>
        <a href="{{ route('admin.periods.index') }}"
           @class(['active' => request()->routeIs('admin.periods.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <span class="portal-menu-label">Periode</span>
        </a>
        <a href="{{ route('admin.teaching.index') }}"
           @class(['active' => request()->routeIs('admin.teaching.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </span>
          <span class="portal-menu-label">Penugasan</span>
        </a>
        <a href="{{ route('admin.parent-student.index') }}"
           @class(['active' => request()->routeIs('admin.parent-student.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
          </span>
          <span class="portal-menu-label">Orang Tua–Siswa</span>
        </a>
        <a href="{{ route('admin.audit.index') }}"
           @class(['active' => request()->routeIs('admin.audit.*')])>
          <span class="portal-menu-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </span>
          <span class="portal-menu-label">Audit Log</span>
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
          <button class="icon-btn portal-mobile-menu" id="adminMenuButton" aria-label="Buka menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
          </button>
          <div>
            <h1 class="portal-topbar-title">@yield('title')</h1>
            <span class="portal-topbar-date">
              <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
              <span class="portal-clock-sep">&bull;</span>
              <span id="portalClock">--:--</span>
            </span>
          </div>
        </div>
        <div class="portal-actions no-print">
          <button class="icon-btn" id="themeBtn" aria-label="Ubah tema">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
          </button>
          <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button class="btn btn-outline" type="submit">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              <span class="btn-text">Keluar</span>
            </button>
          </form>
        </div>
      </header>

      <div class="portal-content">
        @if (session('success'))
          <div style="padding:14px 18px;border-radius:12px;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success);font-weight:600;margin-bottom:18px;border:1px solid color-mix(in srgb,var(--success) 20%,transparent)">
            {{ session('success') }}
          </div>
        @endif
        @if (session('error'))
          <div style="padding:14px 18px;border-radius:12px;background:color-mix(in srgb,#ef4444 12%,var(--card));color:#ef4444;font-weight:600;margin-bottom:18px;border:1px solid color-mix(in srgb,#ef4444 20%,transparent)">
            {{ session('error') }}
          </div>
        @endif
        @yield('content')
      </div>
    </main>
  </div>

  <script src="{{ asset('js/script.js') }}"></script>
  <script src="{{ asset('js/portal.js') }}"></script>
  @stack('scripts')
</body>
</html>
