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
        <div class="school-illustration">
          <div class="school-building">
            <div class="block left"></div>
            <div class="block center">
              <div class="roof"></div>
              <div class="door"></div>
              <div class="window w5"></div>
              <div class="window w6"></div>
            </div>
            <div class="block right"></div>
            <div class="window w1"></div>
            <div class="window w2"></div>
            <div class="window w3"></div>
            <div class="window w4"></div>
          </div>
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
    <div class="about-visual reveal">
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
    <div class="grid grid-3 mt-8">
      <article class="card card-hover program-card reveal">
        <span class="tag tag-is">IS</span>
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v8" />
            <path d="M8 12h8" />
          </svg>
        </div>
        <h3>SMA Sains & Teknologi</h3>
        <p>Penguatan matematika, sains, riset, coding, dan persiapan perguruan tinggi.</p>
        <div class="program-meta"><span>3 tahun</span><span>Proyek riset</span></div>
      </article>
      <article class="card card-hover program-card reveal">
        <span class="tag tag-is">IS</span>
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M2 12h20" />
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
          </svg>
        </div>
        <h3>SMA Sosial & Humaniora</h3>
        <p>Ekonomi, sosiologi, geografi, komunikasi, kewirausahaan, dan kepemimpinan.</p>
        <div class="program-meta"><span>3 tahun</span><span>Debat & proyek sosial</span></div>
      </article>
      <article class="card card-hover program-card reveal">
        <span class="tag tag-is">IS</span>
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="20" height="14" x="2" y="3" rx="2" />
            <line x1="8" y1="21" x2="16" y2="21" />
            <line x1="12" y1="17" x2="12" y2="21" />
          </svg>
        </div>
        <h3>Rekayasa Perangkat Lunak</h3>
        <p>Web, aplikasi, basis data, UI/UX, cloud, serta proyek nyata bersama mitra.</p>
        <div class="program-meta"><span>3 tahun</span><span>Sertifikasi kompetensi</span></div>
      </article>
      <article class="card card-hover program-card reveal">
        <span class="tag tag-is">IS</span>
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
          </svg>
        </div>
        <h3>Desain Komunikasi Visual</h3>
        <p>Branding, ilustrasi, fotografi, video, animasi, dan produksi konten digital.</p>
        <div class="program-meta"><span>3 tahun</span><span>Studio kreatif</span></div>
      </article>
      <article class="card card-hover program-card reveal">
        <span class="tag tag-is">IS</span>
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="20" x2="12" y2="10" />
            <line x1="18" y1="20" x2="18" y2="4" />
            <line x1="6" y1="20" x2="6" y2="16" />
          </svg>
        </div>
        <h3>Akuntansi & Keuangan</h3>
        <p>Akuntansi, perpajakan, aplikasi keuangan, administrasi, dan simulasi bisnis.</p>
        <div class="program-meta"><span>3 tahun</span><span>Teaching factory</span></div>
      </article>
      <article class="card card-hover program-card reveal">
        <span class="tag tag-is">IS</span>
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
        </div>
        <h3>Bisnis Digital</h3>
        <p>E-commerce, pemasaran digital, analitik, penjualan, dan kewirausahaan.</p>
        <div class="program-meta"><span>3 tahun</span><span>Proyek usaha siswa</span></div>
      </article>
    </div>
  </div>
</section>

{{-- PENGALAMAN BELAJAR --}}
<section class="section accent-section">
  <div class="container split">
    <div class="reveal">
      <span class="kicker kicker-white">Pengalaman belajar</span>
      <h2 class="section-title">Belajar tidak berhenti di ruang kelas.</h2>
      <p class="section-desc">Siswa mengembangkan portofolio melalui klub, kompetisi, proyek sosial, kunjungan industri, praktik kerja, dan kegiatan kepemimpinan.</p>
      <a class="btn btn-accent mt-6" href="{{ route('akademik') }}">Lihat Semua Program</a>
    </div>
    <div class="grid grid-2 reveal">
      <div class="card" style="color:var(--ink)"><strong class="text-2xl">15+</strong>
        <p style="color:var(--muted)">Ekstrakurikuler akademik, olahraga, seni, dan teknologi.</p>
      </div>
      <div class="card" style="color:var(--ink)"><strong class="text-2xl">2×</strong>
        <p style="color:var(--muted)">Career day dan pameran karya siswa setiap tahun.</p>
      </div>
      <div class="card" style="color:var(--ink)"><strong class="text-2xl">100%</strong>
        <p style="color:var(--muted)">Siswa SMK mengikuti praktik kerja lapangan.</p>
      </div>
      <div class="card" style="color:var(--ink)"><strong class="text-2xl">1:15</strong>
        <p style="color:var(--muted)">Rasio pendampingan guru terhadap kelompok siswa.</p>
      </div>
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
        <div class="news-thumb"><span class="date">18 JUN 2026</span></div>
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
        <div class="news-thumb" style="background:linear-gradient(135deg,var(--accent-2),var(--accent))"><span class="date">10 JUN 2026</span></div>
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
        <div class="news-thumb" style="background:linear-gradient(135deg,var(--primary-4),var(--primary-3))"><span class="date">02 JUN 2026</span></div>
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
      <div class="g1" data-label="Pembelajaran kolaboratif"></div>
      <div data-label="Laboratorium"></div>
      <div data-label="Kegiatan olahraga"></div>
      <div class="g4" data-label="Pameran karya siswa"></div>
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
@endsection


