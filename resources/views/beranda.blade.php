@extends('layouts.public')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/portal.css') }}">
@endpush

@section('title', config('app.name'))
@section('meta_description', 'Website resmi InvestaSchool — sekolah unggul, terampil, dan berkarakter.')

@section('content')
{{-- HERO --}}
<section class="hero">
  <div class="hero-shapes">
    <div class="hero-shape hero-shape-1"></div>
    <div class="hero-shape hero-shape-2"></div>
    <div class="hero-shape hero-shape-3"></div>
  </div>
  <div class="container hero-grid">
    <div class="reveal">
      <span class="hero-badge">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg>
        Terakreditasi A <span class="mx-1.5 opacity-40">&middot;</span> Sekolah Berbasis Digital
      </span>
      <h1>Membentuk Generasi <span>Unggul</span>, Terampil, dan Berkarakter.</h1>
      <p>Pendidikan terpadu untuk siswa SMA dan SMK melalui pembelajaran akademik, vokasi, teknologi, kepemimpinan, dan pengalaman industri.</p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="{{ route('ppdb') }}">
          Mulai Pendaftaran
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:4px">
            <path d="M5 12h14" />
            <path d="m12 5 7 7-7 7" />
          </svg>
        </a>
        <a class="btn btn-outline" href="{{ route('profil') }}">Jelajahi Sekolah</a>
      </div>
      <div class="hero-note">
        <span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          Kurikulum Merdeka
        </span>
        <span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          Mitra Industri
        </span>
        <span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
            <polyline points="20 6 9 17 4 12" />
          </svg>
          Bimbingan Karier
        </span>
      </div>
    </div>
    <div class="visual reveal">
      <div class="visual-card visual-main">
        <div class="visual-label"><small>Selamat datang di</small><strong>InvestaSchool</strong></div>
        <div class="school-illustration reveal" style="background:url('/img/tentang-sekolah.webp') center/cover no-repeat">
         
        </div>
      </div>
      <div class="visual-card float-card float-1">
        <div class="mini-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="6" />
            <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />
          </svg>
        </div>
        <div><strong>72 Prestasi</strong><small>Tingkat kota hingga nasional</small></div>
      </div>
      <div class="visual-card float-card float-2">
        <div class="mini-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
            <path d="M6 12v5c3 3 9 3 12 0v-5" />
          </svg>
        </div>
        <div><strong>94% Lulusan</strong><small>Melanjutkan studi atau bekerja</small></div>
      </div>
    </div>
  </div>
</section>

{{-- STATS --}}
<div class="container stats-strip">
  <div class="stats-card">
    <div class="stat">
      <div class="stat-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
        </svg>
      </div>
      <strong class="counter" data-count="1250" data-suffix="+">0</strong>
      <span>Siswa Aktif</span>
    </div>
    <div class="stat">
      <div class="stat-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <polyline points="16 11 18 13 22 9" />
        </svg>
      </div>
      <strong class="counter" data-count="82" data-suffix="+">0</strong>
      <span>Guru & Tenaga Ahli</span>
    </div>
    <div class="stat">
      <div class="stat-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
          <path d="M6 12v5c3 3 9 3 12 0v-5" />
        </svg>
      </div>
      <strong class="counter" data-count="28" data-suffix="+">0</strong>
      <span>Mitra Industri</span>
    </div>
    <div class="stat">
      <div class="stat-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <path d="M12 8v8" />
          <path d="M8 12h8" />
        </svg>
      </div>
      <strong class="counter" data-count="15" data-suffix="+">0</strong>
      <span>Ekstrakurikuler</span>
    </div>
  </div>
</div>

{{-- MENGAPA MEMILIH KAMI --}}
<section class="section">
  <div class="container">
    <span class="kicker">Mengapa memilih kami</span>
    <h2 class="section-title">Sekolah yang menyiapkan siswa untuk dunia nyata.</h2>
    <p class="section-desc">Kami menghadirkan lingkungan belajar aman, inklusif, modern, dan berorientasi masa depan.</p>
    <div class="grid grid-4 mt-8">
      <article class="card card-hover feature-card reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20" />
          </svg>
        </div>
        <h3>Akademik Kuat</h3>
        <p>Pembelajaran konseptual, proyek, literasi, numerasi, dan persiapan pendidikan tinggi.</p>
      </article>
      <article class="card card-hover feature-card reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="16" height="13" x="4" y="4" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
        </div>
        <h3>Teknologi Digital</h3>
        <p>Laboratorium modern, e-learning, coding, multimedia, dan budaya belajar berbasis data.</p>
      </article>
      <article class="card card-hover feature-card reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <h3>Kolaborasi Industri</h3>
        <p>Guru tamu, kunjungan industri, praktik kerja lapangan, dan sertifikasi kompetensi.</p>
      </article>
      <article class="card card-hover feature-card reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
        </div>
        <h3>Karakter & Kepemimpinan</h3>
        <p>Pendampingan wali kelas, kegiatan sosial, organisasi siswa, dan penguatan profil pelajar.</p>
      </article>
    </div>
  </div>
</section>

{{-- TENTANG SEKOLAH --}}
<section class="section" style="background:var(--card)">
  <div class="container split">
    <div class="about-visual reveal" style="background:url('/img/tentang-sekolah.webp') center/cover no-repeat">
      <div class="quote">“Setiap siswa memiliki potensi besar. Tugas sekolah adalah membantu mereka menemukan arah dan keberanian untuk bertumbuh.”</div>
    </div>
    <div class="reveal">
      <span class="kicker">Tentang sekolah</span>
      <h2 class="section-title">Pendidikan terpadu SMA dan SMK dalam satu ekosistem.</h2>
      <p class="section-desc">InvestaSchool menghubungkan penguasaan ilmu, keterampilan praktis, nilai karakter, dan pengalaman kolaboratif.</p>
      <div class="check-list">
        <div class="check-item">
          <span class="check">✓</span>
          <div><strong>Pembelajaran personal</strong><br><span style="color:var(--muted)">Pendampingan berdasarkan minat, bakat, dan tujuan siswa.</span></div>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <div><strong>Lingkungan aman dan suportif</strong><br><span style="color:var(--muted)">Budaya sekolah yang menghargai disiplin, empati, dan integritas.</span></div>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <div><strong>Kesiapan kuliah dan karier</strong><br><span style="color:var(--muted)">Konseling studi lanjut, portofolio, magang, dan pelatihan wawancara.</span></div>
        </div>
      </div>
      <a class="btn btn-primary" href="{{ route('profil') }}">Baca Profil Sekolah</a>
    </div>
  </div>
</section>

{{-- PROGRAM PENDIDIKAN --}}
<section class="section">
  <div class="container">
    <span class="kicker">Program pendidikan</span>
    <h2 class="section-title">Pilih jalur sesuai potensi dan cita-cita.</h2>
    <div class="grid-jurusan mt-8">
      <article class="jurusan-card reveal" data-jurusan="rpl">
        <div class="jurusan-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="16 18 22 12 16 6" />
            <polyline points="8 6 2 12 8 18" />
          </svg>
        </div>
        <h3>RPL</h3>
        <p>Rekayasa Perangkat Lunak — Web, aplikasi, basis data, UI/UX, cloud, serta proyek nyata bersama mitra.</p>
        <div class="jurusan-info">
          <span class="jurusan-pill">3 Tahun</span>
          <span class="jurusan-pill">Sertifikasi</span>
        </div>
      </article>
      <article class="jurusan-card reveal" data-jurusan="bdp">
        <div class="jurusan-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1" />
            <circle cx="20" cy="21" r="1" />
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
          </svg>
        </div>
        <h3>BDP</h3>
        <p>Bisnis Digital — E-commerce, pemasaran digital, analitik, penjualan, dan kewirausahaan berbasis teknologi.</p>
        <div class="jurusan-info">
          <span class="jurusan-pill">3 Tahun</span>
          <span class="jurusan-pill">Proyek Usaha</span>
        </div>
      </article>
      <article class="jurusan-card reveal" data-jurusan="otkp">
        <div class="jurusan-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="2" width="16" height="20" rx="2" ry="2" />
            <line x1="9" y1="6" x2="15" y2="6" />
            <line x1="9" y1="10" x2="15" y2="10" />
            <line x1="9" y1="14" x2="15" y2="14" />
            <line x1="9" y1="18" x2="13" y2="18" />
          </svg>
        </div>
        <h3>OTKP</h3>
        <p>Otomatisasi Tata Kelola Perkantoran — Administrasi, korespondensi, kehumasan, arsip digital, dan layanan bisnis.</p>
        <div class="jurusan-info">
          <span class="jurusan-pill">3 Tahun</span>
          <span class="jurusan-pill">Teaching Factory</span>
        </div>
      </article>
      <article class="jurusan-card reveal" data-jurusan="akl">
        <div class="jurusan-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="2" width="16" height="20" rx="2" />
            <line x1="8" y1="6" x2="16" y2="6" />
            <line x1="8" y1="10" x2="16" y2="10" />
            <line x1="8" y1="14" x2="16" y2="14" />
            <line x1="8" y1="18" x2="16" y2="18" />
          </svg>
        </div>
        <h3>AKL</h3>
        <p>Akuntansi Keuangan Lembaga — Akuntansi, perpajakan, aplikasi keuangan, administrasi, dan simulasi bisnis.</p>
        <div class="jurusan-info">
          <span class="jurusan-pill">3 Tahun</span>
          <span class="jurusan-pill">Kompetensi</span>
        </div>
      </article>
      <article class="jurusan-card reveal" data-jurusan="dkv">
        <div class="jurusan-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
            <path d="M15 5l4 4" />
          </svg>
        </div>
        <h3>DKV</h3>
        <p>Desain Komunikasi Visual — Branding, ilustrasi, fotografi, video, animasi, dan produksi konten digital.</p>
        <div class="jurusan-info">
          <span class="jurusan-pill">3 Tahun</span>
          <span class="jurusan-pill">Portofolio</span>
        </div>
      </article>
    </div>
  </div>
</section>

{{-- PENGALAMAN BELAJAR --}}
<section class="section">
  <div class="container">
    <div class="reveal" style="text-align:center">
      <span class="kicker" style="justify-content:center">Pengalaman belajar</span>
      <h2 class="section-title">Belajar tidak berhenti di ruang kelas.</h2>
      <p class="section-desc" style="margin-left:auto;margin-right:auto">Siswa mengembangkan portofolio melalui klub, kompetisi, proyek sosial, kunjungan industri, praktik kerja, dan kegiatan kepemimpinan.</p>
    </div>
    <div class="grid grid-3 mt-10">
      <article class="card experience-card reveal">
        <div class="experience-icon" style="background:#e8f4f8;color:var(--primary)">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
            <line x1="12" y1="12" x2="12" y2="17" />
            <polyline points="8 15 12 12 16 15" />
          </svg>
        </div>
        <h3>Praktik Kerja Lapangan</h3>
        <p>"Rivan, siswa RPL, magang di PT Infosys dan membangun sistem inventaris yang masih dipakai perusahaan hingga sekarang."</p>
        <div class="experience-meta">
          <span class="experience-tag">SMK</span>
          <span class="experience-tag">RPL</span>
          <span class="experience-tag">Mitra Industri</span>
        </div>
      </article>
      <article class="card experience-card reveal">
        <div class="experience-icon" style="background:#fef3e2;color:#c9760d">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="6" />
            <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />
          </svg>
        </div>
        <h3>Kompetisi & Prestasi</h3>
        <p>"Tim DKV meraih Juara 1 lomba desain logo tingkat provinsi—proyek nyata yang jadi portofolio mereka ke perguruan tinggi."</p>
        <div class="experience-meta">
          <span class="experience-tag">DKV</span>
          <span class="experience-tag">Prestasi</span>
          <span class="experience-tag">Portofolio</span>
        </div>
      </article>
      <article class="card experience-card reveal">
        <div class="experience-icon" style="background:#e6f7ee;color:var(--success)">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <h3>Proyek Sosial</h3>
        <p>"Bakti lingkungan bersama siswa OTKP: program daur ulang sampah plastik menjadi produk bernilai jual untuk masyarakat sekitar."</p>
        <div class="experience-meta">
          <span class="experience-tag">OTKP</span>
          <span class="experience-tag">Sosial</span>
          <span class="experience-tag">Kewirausahaan</span>
        </div>
      </article>
    </div>
    <div style="text-align:center;margin-top:48px">
      <a class="btn btn-primary" href="{{ route('akademik') }}">Lihat Semua Program</a>
    </div>
  </div>
</section>

{{-- BERITA & AGENDA --}}
<section class="section">
  <div class="container">
    <span class="kicker">Berita & agenda</span>
    <h2 class="section-title">Aktivitas terbaru di sekolah.</h2>
    <div class="grid grid-3 mt-8">
      <article class="card news-card reveal">
        <div class="news-thumb" style="background-image:url('/img/si-menang.webp')"><span class="date">18 JUN 2026</span></div>
        <div class="news-body">
          <h3>Tim siswa meraih juara inovasi teknologi tingkat kota</h3>
          <p>Proyek sistem monitoring energi sekolah berhasil meraih penghargaan kategori solusi berkelanjutan.</p>
          <a class="text-link" href="#">
            Baca berita
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:3px">
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" />
            </svg>
          </a>
        </div>
      </article>
      <article class="card news-card reveal">
        <div class="news-thumb" style="background-image:url('/img/si-menang.webp')"><span class="date">10 JUN 2026</span></div>
        <div class="news-body">
          <h3>Career Day mempertemukan siswa dengan profesional</h3>
          <p>Siswa memperoleh wawasan kuliah, karier, portofolio, dan kompetensi masa depan.</p>
          <a class="text-link" href="#">
            Baca berita
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:3px">
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" />
            </svg>
          </a>
        </div>
      </article>
      <article class="card news-card reveal">
        <div class="news-thumb" style="background-image:url('/img/si-menang.webp')"><span class="date">02 JUN 2026</span></div>
        <div class="news-body">
          <h3>Pameran karya kreatif dan produk kewirausahaan</h3>
          <p>Karya desain, aplikasi, produk bisnis, dan penelitian siswa dipamerkan kepada publik.</p>
          <a class="text-link" href="#">
            Baca berita
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:3px">
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" />
            </svg>
          </a>
        </div>
      </article>
    </div>
  </div>
</section>

{{-- GALERI --}}
<section class="section" style="background:var(--card)">
  <div class="container">
    <span class="kicker">Galeri</span>
    <h2 class="section-title">Suasana belajar yang aktif dan inspiratif.</h2>
    <div class="gallery mt-8">
      <div class="g1" data-label="Pembelajaran kolaboratif" style="background:url('/img/si-menang.webp') center/cover no-repeat"></div>
      <div data-label="Laboratorium" style="background:url('/img/si-menang.webp') center/cover no-repeat"></div>
      <div data-label="Kegiatan olahraga" style="background:url('/img/si-menang.webp') center/cover no-repeat"></div>
      <div class="g4" data-label="Pameran karya siswa" style="background:url('/img/si-menang.webp') center/cover no-repeat"></div>
    </div>
  </div>
</section>

{{-- PORTAL ORANG TUA --}}
<section class="section">
  <div class="container split">
    <div class="reveal">
      <span class="kicker">Portal orang tua</span>
      <h2 class="section-title">Perkembangan belajar anak dalam satu dashboard.</h2>
      <p class="section-desc">Orang tua dapat memantau skor kuis, PR/tugas, proyek atau praktik, UTS, UAS, nilai akhir, ketuntasan, kehadiran, karakter, kegiatan, dan catatan guru.</p>
      <div class="check-list">
        <div class="check-item">
          <span class="check">✓</span>
          <div><strong>Nilai transparan dan terperinci</strong><br><span style="color:var(--muted)">Setiap komponen penilaian dan bobotnya dapat dilihat.</span></div>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <div><strong>Tren perkembangan antarsemester</strong><br><span style="color:var(--muted)">Membantu orang tua memahami kemajuan dan area pendampingan.</span></div>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <div><strong>Catatan dan tindak lanjut guru</strong><br><span style="color:var(--muted)">Umpan balik akademik dan karakter tersaji dalam satu laporan.</span></div>
        </div>
      </div>
      <a class="btn btn-primary" href="{{ route('login') }}">
        Masuk Portal Orang Tua
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:4px">
          <path d="M5 12h14" />
          <path d="m12 5 7 7-7 7" />
        </svg>
      </a>
    </div>
    <div class="card reveal p-8">
      <div class="portal-kpis" style="grid-template-columns:1fr 1fr">
        <div class="portal-kpi">
          <div class="portal-kpi-label"><span>Rata-rata</span><span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle">
              <line x1="12" y1="20" x2="12" y2="10" />
              <line x1="18" y1="20" x2="18" y2="4" />
              <line x1="6" y1="20" x2="6" y2="16" />
            </svg>
          </span></div><strong class="portal-kpi-value">86,4</strong><span class="portal-kpi-note good">Di atas KKM</span>
        </div>
        <div class="portal-kpi">
          <div class="portal-kpi-label"><span>Kehadiran</span><span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle">
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
              <line x1="16" y1="2" x2="16" y2="6" />
              <line x1="8" y1="2" x2="8" y2="6" />
              <line x1="3" y1="10" x2="21" y2="10" />
            </svg>
          </span></div><strong class="portal-kpi-value">96,4%</strong><span class="portal-kpi-note good">Sangat baik</span>
        </div>
      </div>
      <div class="mt-6">
        <div class="subject-progress-head"><strong>Pemrograman</strong><strong>93,2</strong></div>
        <div class="progress-track">
          <div class="progress-fill" style="--score:93.2%"></div>
        </div>
        <div class="subject-progress-head mt-4"><strong>Bahasa Indonesia</strong><strong>90,2</strong></div>
        <div class="progress-track">
          <div class="progress-fill" style="--score:90.2%"></div>
        </div>
        <div class="subject-progress-head mt-4"><strong>Matematika</strong><strong>84,0</strong></div>
        <div class="progress-track">
          <div class="progress-fill" style="--score:84%"></div>
        </div>
      </div>
      <div class="portal-note mt-6"><strong>Catatan wali kelas</strong>
        <p>Perkembangan proyek sangat baik. Perlu meningkatkan konsistensi latihan Matematika.</p>
      </div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="section">
  <div class="container">
    <div class="cta reveal">
      <div>
        <h2>Siap menjadi bagian dari InvestaSchool?</h2>
        <p>Temukan program terbaik, jadwal penerimaan, persyaratan, serta alur pendaftaran peserta didik baru.</p>
      </div>
      <a class="btn btn-accent" href="{{ route('ppdb') }}">
        Lihat Informasi PPDB
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-left:4px">
          <path d="M5 12h14" />
          <path d="m12 5 7 7-7 7" />
        </svg>
      </a>
    </div>
  </div>
</section>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.news-thumb, .about-visual, .gallery > div').forEach(function(el) {
      var bg = el.style.backgroundImage || getComputedStyle(el).backgroundImage;
      if (!bg || bg === 'none') return;

      el.style.position = 'relative';

      var overlay = document.createElement('div');
      overlay.className = 'skeleton-overlay';
      el.appendChild(overlay);

      var url = bg.replace(/^url\(['"]?/, '').replace(/['"]?\)$/, '');
      var img = new Image();
      img.onload = function() { overlay.remove(); };
      img.onerror = function() { overlay.remove(); };
      img.src = url;
    });
  });
</script>
@endpush
@endsection


