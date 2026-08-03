<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex">
  <meta name="description" content="Halaman tidak ditemukan — InvestaSchool">
  <title>404 — Halaman Tidak Ditemukan | InvestaSchool</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Calistoga&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #1a5f7a;
      --primary-2: #3d8baf;
      --primary-3: #6bb5ce;
      --primary-4: #95ccdd;
      --primary-5: #b5dce8;
      --primary-6: #d4eaf2;
      --accent: #4a90d9;
      --accent-2: #7ab8e0;
      --accent-3: #aed9e6;
      --ink: #132238;
      --muted: #5f6f82;
      --bg: #f3f2ed;
      --card: #ffffff;
      --line: #e4e1da;
      --success: #1f8f62;
      --danger: #d94a4a;
      --shadow: 0 12px 40px rgba(26, 95, 122, 0.09);
      --shadow-lg: 0 24px 60px rgba(26, 95, 122, 0.12);
    }
    body.dark {
      --ink: #edf4ff;
      --muted: #aebcd0;
      --bg: #0a1a2e;
      --card: #0f2035;
      --line: #1c3450;
      --primary: #6bb5ce;
      --primary-2: #95ccdd;
      --primary-3: #b5dce8;
      --primary-4: #d4eaf2;
      --primary-5: #e3f2f8;
      --primary-6: #0d2740;
      --shadow: 0 18px 50px rgba(0, 0, 0, 0.35);
      --shadow-lg: 0 24px 60px rgba(0, 0, 0, 0.35);
    }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      color: var(--ink);
      background: var(--bg);
      line-height: 1.65;
      overflow-x: hidden;
      transition: background .3s ease, color .3s ease;
    }
    .page {
      position: relative;
      min-height: 100svh;
      display: grid;
      align-items: center;
      justify-items: center;
      padding: 56px 24px;
      background:
        radial-gradient(ellipse 80% 60% at 80% 10%, rgba(174, 217, 230, 0.22), transparent),
        radial-gradient(ellipse 60% 50% at 12% 90%, rgba(149, 204, 221, 0.20), transparent),
        linear-gradient(180deg, var(--card), var(--bg));
      overflow: hidden;
    }
    .shapes { position: absolute; inset: 0; pointer-events: none; z-index: 0; }
    .shape {
      position: absolute;
      border-radius: 50%;
      opacity: .16;
      background: radial-gradient(circle, var(--primary-4), transparent 70%);
    }
    .shape-1 { width: 420px; height: 420px; top: -110px; right: -80px; animation: floatShape 8s ease-in-out infinite; }
    .shape-2 { width: 300px; height: 300px; bottom: -40px; left: -90px; background: radial-gradient(circle, var(--accent-3), transparent 70%); animation: floatShape 10s ease-in-out infinite reverse; }
    .shape-3 { width: 170px; height: 170px; top: 44%; left: 18%; background: radial-gradient(circle, var(--primary-5), transparent 70%); animation: floatShape 7s ease-in-out infinite 1s; }
    .shape-4 { width: 90px; height: 90px; top: 14%; right: 16%; background: radial-gradient(circle, var(--primary-3), transparent 70%); animation: floatShape 6s ease-in-out infinite .5s; }
    @keyframes floatShape {
      0%, 100% { transform: translate(0, 0) scale(1); }
      33% { transform: translate(20px, -30px) scale(1.05); }
      66% { transform: translate(-12px, 18px) scale(.95); }
    }
    .wrap {
      position: relative;
      z-index: 1;
      width: min(100%, 780px);
      text-align: center;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
      animation: rise .7s ease both;
    }
    .brand-mark {
      width: 44px; height: 44px;
      border-radius: 13px;
      display: grid; place-items: center;
      background: linear-gradient(145deg, var(--primary), var(--primary-2));
      box-shadow: 0 6px 16px color-mix(in srgb, var(--primary) 30%, transparent);
    }
    .brand-mark img { width: 25px; height: 25px; }
    .brand-text { text-align: left; line-height: 1.05; font-size: 1.05rem; }
    .brand-text strong { display: block; color: var(--primary-2); font-weight: 800; letter-spacing: .01em; }
    .brand-text small { display: block; color: var(--muted); font-size: .66rem; letter-spacing: .11em; margin-top: 4px; }
    .hero-404 {
      position: relative;
      display: grid;
      place-items: center;
      animation: rise .7s ease .08s both;
    }
    .num {
      font-family: Calistoga, Georgia, serif;
      font-size: clamp(7rem, 24vw, 13rem);
      line-height: .95;
      letter-spacing: -0.02em;
      background: linear-gradient(135deg, var(--primary), var(--primary-2), var(--accent));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      filter: drop-shadow(0 22px 40px color-mix(in srgb, var(--primary) 30%, transparent));
      user-select: none;
    }
    .cap {
      position: absolute;
      top: -14%;
      right: 6%;
      width: clamp(74px, 12vw, 108px);
      animation: bob 5s ease-in-out infinite;
      transform-origin: center;
    }
    .cap svg { width: 100%; height: auto; filter: drop-shadow(0 12px 18px rgba(26, 95, 122, .28)); }
    .school {
      position: absolute;
      bottom: -16%;
      left: 4%;
      width: clamp(150px, 22vw, 220px);
      animation: bob 6.5s ease-in-out infinite 1s;
    }
    .school svg { width: 100%; height: auto; filter: drop-shadow(0 14px 24px rgba(26, 95, 122, .22)); }
    .cloud {
      position: absolute;
      top: 4%;
      left: 12%;
      width: 74px; height: 26px;
      border-radius: 99px;
      background: color-mix(in srgb, var(--primary-5) 80%, transparent);
      opacity: .7;
      animation: drift 14s linear infinite;
    }
    .cloud::before, .cloud::after {
      content: "";
      position: absolute;
      background: inherit;
      border-radius: 50%;
    }
    .cloud::before { width: 34px; height: 34px; top: -16px; left: 8px; }
    .cloud::after { width: 24px; height: 24px; top: -10px; right: 8px; }
    @keyframes bob {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-12px) rotate(1.5deg); }
    }
    @keyframes drift {
      0% { transform: translateX(-40px); }
      100% { transform: translateX(calc(100vw + 80px)); }
    }
    .content { margin-top: clamp(46px, 9vw, 72px); animation: rise .7s ease .16s both; }
    .kicker {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--primary-2);
      font-weight: 800;
      letter-spacing: .12em;
      text-transform: uppercase;
      font-size: .76rem;
    }
    .kicker::before { content: ""; width: 32px; height: 2px; border-radius: 99px; background: linear-gradient(90deg, var(--accent), var(--accent-2)); }
    .kicker::after { content: ""; width: 32px; height: 2px; border-radius: 99px; background: linear-gradient(90deg, var(--accent-2), var(--accent)); }
    h1 {
      font-family: Calistoga, Georgia, serif;
      font-weight: 400;
      font-size: clamp(1.9rem, 4.6vw, 3rem);
      line-height: 1.15;
      letter-spacing: -0.03em;
      margin: 16px 0 12px;
    }
    h1 span {
      background: linear-gradient(135deg, var(--primary), var(--primary-2));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .lead {
      max-width: 520px;
      margin: 0 auto;
      color: var(--muted);
      font-size: 1rem;
    }
    .actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 30px;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      min-height: 48px;
      padding: 0 22px;
      border-radius: 13px;
      border: 1px solid transparent;
      font-weight: 800;
      text-decoration: none;
      transition: .2s ease;
    }
    .btn:hover { transform: translateY(-2px); }
    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--primary-2), var(--primary-3));
      color: #fff;
      box-shadow: 0 10px 24px color-mix(in srgb, var(--primary) 40%, transparent);
    }
    .btn-primary:hover { box-shadow: 0 14px 30px color-mix(in srgb, var(--primary) 50%, transparent); }
    .btn-outline { border-color: var(--line); background: var(--card); color: var(--ink); }
    .btn-outline:hover { border-color: var(--primary-2); color: var(--primary-2); }
    .hint {
      margin-top: 22px;
      color: var(--muted);
      font-size: .82rem;
    }
    .hint code {
      font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
      font-size: .78rem;
      padding: 2px 8px;
      border-radius: 7px;
      background: color-mix(in srgb, var(--muted) 10%, transparent);
      border: 1px solid var(--line);
    }
    .theme-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 5;
      width: 44px; height: 44px;
      border-radius: 12px;
      border: 1px solid var(--line);
      background: var(--card);
      color: var(--ink);
      display: grid; place-items: center;
      cursor: pointer;
      box-shadow: var(--shadow);
      transition: .2s ease;
    }
    .theme-btn:hover { transform: translateY(-2px); border-color: var(--primary-2); color: var(--primary-2); }
    .theme-btn svg { width: 22px; height: 22px; }
    .theme-btn .icon-moon { display: none; }
    body.dark .theme-btn .icon-sun { display: none; }
    body.dark .theme-btn .icon-moon { display: block; }
    @keyframes rise {
      from { opacity: 0; transform: translateY(24px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 560px) {
      .page { padding: 44px 18px; }
      .cap { right: 2%; }
      .school { left: -2%; }
      .actions { flex-direction: column; align-items: stretch; }
    }
    @media (prefers-reduced-motion: reduce) {
      .shape, .cap, .school, .cloud { animation: none; }
    }
  </style>
</head>

<body>
  <button class="theme-btn" id="themeBtn" type="button" aria-label="Ganti tema">
    <svg class="icon-sun" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
    <svg class="icon-moon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
    </svg>
  </button>

  <div class="page">
    <div class="shapes" aria-hidden="true">
      <div class="shape shape-1"></div>
      <div class="shape shape-2"></div>
      <div class="shape shape-3"></div>
      <div class="shape shape-4"></div>
    </div>

    <div class="wrap">
      <div class="brand">
        <span class="brand-mark"><img src="{{ asset('img/logo.svg') }}" alt="Logo InvestaSchool"></span>
        <span class="brand-text"><strong>InvestaSchool</strong><small>UNGGUL &middot; TERAMPIL &middot; BERKARAKTER</small></span>
      </div>

      <div class="hero-404">
        <div class="num" aria-hidden="true">404</div>

        <div class="cap" aria-hidden="true">
          <svg viewBox="0 0 120 90" fill="none">
            <path d="M10 34 60 10l50 24-50 24L10 34Z" fill="#1a5f7a" />
            <path d="M22 44v26c18 12 58 12 76 0V44l-38 18-38-18Z" fill="#3d8baf" />
            <path d="M90 20v14c-2 6-8 8-14 6-5-2-7-7-5-12l-11-5 40-19Z" fill="#6bb5ce" />
            <path d="M110 40h10v8h-10z" fill="#f2b632" />
            <circle cx="110" cy="36" r="3.4" fill="#f2b632" />
          </svg>
        </div>

        <div class="school" aria-hidden="true">
          <svg viewBox="0 0 220 170" fill="none">
            <ellipse cx="110" cy="150" rx="104" ry="10" fill="color-mix(in srgb, #1a5f7a 20%, transparent)" />
            <rect x="30" y="96" width="160" height="12" rx="6" fill="#1a5f7a" />
            <rect x="46" y="46" width="128" height="64" fill="#3d8baf" />
            <path d="M46 96V60l64-34 64 34v36" fill="none" stroke="#1a5f7a" stroke-width="8" stroke-linejoin="round" />
            <path d="M110 26v14M80 36l30-10 30 10" stroke="#95ccdd" stroke-width="6" stroke-linecap="round" fill="none" />
            <rect x="84" y="70" width="18" height="26" rx="3" fill="#f2b632" />
            <rect x="120" y="70" width="18" height="26" rx="3" fill="#f2b632" />
            <path d="M40 96h28v20H40zM152 96h28v20h-28z" fill="#b5dce8" />
            <circle cx="56" cy="86" r="4" fill="#0f2035" />
            <circle cx="168" cy="86" r="4" fill="#0f2035" />
            <path d="M28 104c-10 0-18-6-18-14" stroke="#6bb5ce" stroke-width="7" stroke-linecap="round" fill="none" />
            <path d="M192 104c10 0 18-6 18-14" stroke="#6bb5ce" stroke-width="7" stroke-linecap="round" fill="none" />
            <path d="M158 28h52M190 12v32" stroke="#7ab8e0" stroke-width="7" stroke-linecap="round" />
          </svg>
        </div>

        <div class="cloud" aria-hidden="true"></div>
      </div>

      <div class="content">
        <span class="kicker">Halaman Tidak Ditemukan</span>
        <h1>Ups, halaman ini <span>tersesat</span>.</h1>
        <p class="lead">Alamat yang kamu buka mungkin sudah dipindah, dihapus, atau memang tidak pernah ada. Tenang, sekolah masih buka — ayo kembali ke halaman utama.</p>

        <div class="actions">
          <a class="btn btn-primary" href="{{ route('beranda') }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
              <path d="M9 22V12h6v10" />
            </svg>
            Kembali ke Beranda
          </a>
          <a class="btn btn-outline" href="{{ route('kontak') }}">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
            </svg>
            Laporkan Masalah
          </a>
        </div>

        <p class="hint">Salah ketik? Coba cek lagi alamatnya &mdash; <code>/beranda</code>, <code>/ppdb</code>, <code>/kontak</code>.</p>
      </div>
    </div>
  </div>

  <script>
    const stored = localStorage.getItem('school-theme');
    if (stored === 'dark') document.body.classList.add('dark');
    document.getElementById('themeBtn').addEventListener('click', () => {
      document.body.classList.toggle('dark');
      localStorage.setItem('school-theme', document.body.classList.contains('dark') ? 'dark' : 'light');
    });
  </script>
</body>

</html>
