<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') | Portal Siswa</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  @vite('resources/css/app.css')
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @stack('styles')
</head>
<body class="min-h-screen" style="background:#f1f5f9">
  <div class="flex min-h-screen">

    <aside class="w-60 shrink-0 fixed inset-y-0 left-0 z-40 flex flex-col max-lg:hidden overflow-hidden" style="background:linear-gradient(195deg,#0d9488,#0f766e)" id="sidebar">
      <div class="p-5">
        <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
            <img src="{{ asset('img/logo.svg') }}" alt="" class="w-5 h-5 brightness-0 invert">
          </div>
          <div>
            <div class="text-sm font-bold text-white/90 leading-tight">Portal Siswa</div>
            <div class="text-[11px] text-white/40 leading-tight">CAKRAWALA</div>
          </div>
        </a>
      </div>

      <div class="px-4 pb-4 border-b border-white/10">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-white/10 backdrop-blur-sm">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-300 to-emerald-400 text-teal-900 flex items-center justify-center text-sm font-bold shadow-lg shadow-teal-900/20">{{ $initials }}</div>
          <div class="min-w-0">
            <div class="text-sm font-semibold text-white truncate">{{ $student->full_name }}</div>
            <div class="text-[11px] text-white/50 truncate">{{ $student->class_name }}</div>
          </div>
        </div>
      </div>

      <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
        @php
          $navItems = [
            'siswa.dashboard' => ['Dashboard', '<svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
            'siswa.nilai' => ['Nilai', '<svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>'],
            'siswa.kehadiran' => ['Kehadiran', '<svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
            'siswa.jadwal' => ['Jadwal', '<svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'],
            'siswa.materi' => ['Materi', '<svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg>'],
            'siswa.profil' => ['Profil', '<svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'],
          ];
        @endphp
        @foreach($navItems as $route => [$label, $svg])
          <a href="{{ route($route) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
            {{ request()->routeIs($route) ? 'bg-white/20 text-white shadow-lg shadow-black/5' : 'text-white/60 hover:text-white/90 hover:bg-white/5' }}">
            {!! $svg !!}
            {{ $label }}
          </a>
        @endforeach
      </nav>

      <div class="p-3 border-t border-white/10 space-y-1">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-emerald-50 hover:text-white hover:bg-white/10 transition-all duration-200">
          <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
          Website
        </a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex items-center justify-start gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-emerald-50 hover:text-rose-300 bg-white/10 hover:bg-white/20 transition-all duration-200">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
          </button>
        </form>
      </div>
    </aside>

    <div class="flex-1 flex flex-col min-h-screen lg:ml-60">
      <header class="sticky top-0 z-30 bg-white/70 backdrop-blur-xl border-b border-slate-200/50 h-14 flex items-center justify-between px-5 shrink-0">
        <div class="flex items-center gap-3">
          <button id="mobileMenuBtn" class="lg:hidden p-1.5 rounded-xl hover:bg-slate-100 text-slate-600">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
          </button>
          <div>
            <h1 class="text-sm font-bold text-slate-800">@yield('page-title')</h1>
            <p class="text-[11px] text-slate-400">{{ now()->translatedFormat('l, d M Y') }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-teal-50 text-sm">
            <span class="text-teal-700 font-semibold">{{ $student->class_name }}</span>
          </div>
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-lg shadow-teal-500/20">{{ $initials }}</div>
        </div>
      </header>

      <main class="flex-1 p-5 space-y-6">
        @yield('content')
      </main>
    </div>
  </div>

  <script>
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');
    mobileBtn?.addEventListener('click', () => {
      sidebar.classList.toggle('max-lg:hidden');
      sidebar.classList.toggle('max-lg:flex');
      sidebar.classList.toggle('max-lg:fixed');
      sidebar.classList.toggle('max-lg:inset-0');
      sidebar.classList.toggle('max-lg:z-50');
    });
  </script>
</body>
</html>