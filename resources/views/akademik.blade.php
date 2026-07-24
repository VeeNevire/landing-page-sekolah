@extends('layouts.public')

@section('title', 'Akademik | InvestaSchool')

@section('content')
{{-- PAGE HERO --}}
<section class="page-hero">
  <div class="page-hero-shapes">
    <div class="page-hero-shape page-hero-shape-1"></div>
    <div class="page-hero-shape page-hero-shape-2"></div>
  </div>
  <div class="container">
    <div class="breadcrumb">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
        <polyline points="9 22 9 12 15 12 15 22" />
      </svg>
      <span>Beranda</span>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m9 18 6-6-6-6" />
      </svg>
      <span>Akademik</span>
    </div>
    <h1>Program Akademik & Vokasi</h1>
    <p class="lead">
      <span class="lead-bar"></span>
      <span>Kurikulum yang fleksibel, berbasis proyek, terhubung dengan teknologi, perguruan tinggi, dan kebutuhan industri.</span>
    </p>
  </div>
</section>

{{-- PROGRAM UNGGULAN --}}
<section class="section">
  <div class="container">
    <span class="kicker">Program unggulan</span>
    <h2 class="section-title">Jalur belajar yang relevan untuk masa depan.</h2>
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
    </div>
  </div>
</section>

{{-- MODEL PEMBELAJARAN --}}
<section class="section" style="background:var(--card)">
  <div class="container split">
    <div>
      <span class="kicker">Model pembelajaran</span>
      <h2 class="section-title">Belajar melalui pengalaman, proyek, dan refleksi.</h2>
      <p class="section-desc">Guru berperan sebagai fasilitator yang membantu siswa memahami konsep, memecahkan masalah, dan menghasilkan karya.</p>
    </div>
    <div class="grid grid-2">
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
          </svg>
        </div>
        <h3>Project-Based Learning</h3>
        <p style="color:var(--muted)">Proyek lintas mata pelajaran yang menghasilkan solusi nyata.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="16" height="13" x="4" y="4" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
        </div>
        <h3>Blended Learning</h3>
        <p style="color:var(--muted)">Perpaduan tatap muka, LMS, materi digital, dan asesmen daring.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
            <path d="M6 12v5c3 3 9 3 12 0v-5" />
          </svg>
        </div>
        <h3>Industry Exposure</h3>
        <p style="color:var(--muted)">Kunjungan, guru tamu, magang, dan studi kasus dari dunia kerja.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <h3>Student Mentoring</h3>
        <p style="color:var(--muted)">Pendampingan akademik, karakter, portofolio, dan rencana karier.</p>
      </div>
    </div>
  </div>
</section>

{{-- EKSTRAKURIKULER --}}
<section class="section">
  <div class="container">
    <span class="kicker">Ekstrakurikuler</span>
    <h2 class="section-title">Ruang untuk bertumbuh di luar kelas.</h2>
    <div class="table-wrap mt-8">
      <table>
        <thead>
          <tr>
            <th>Bidang</th>
            <th>Pilihan Kegiatan</th>
            <th>Fokus Pengembangan</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Akademik</td><td>Klub Sains, Bahasa Inggris, Debat, Karya Ilmiah</td><td>Riset, komunikasi, kompetisi</td></tr>
          <tr><td>Teknologi</td><td>Robotics, Coding Club, E-Sports Edukatif</td><td>Logika, kolaborasi, inovasi</td></tr>
          <tr><td>Seni</td><td>Band, Tari, Teater, Fotografi, Film</td><td>Kreativitas dan ekspresi</td></tr>
          <tr><td>Olahraga</td><td>Basket, Futsal, Badminton, Pencak Silat</td><td>Kebugaran dan sportivitas</td></tr>
          <tr><td>Kepemimpinan</td><td>OSIS, Pramuka, PMR, Paskibra</td><td>Disiplin, pelayanan, organisasi</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

{{-- KESIAPAN LULUSAN --}}
<section class="section">
  <div class="container">
    <div style="text-align:center">
      <span class="kicker" style="justify-content:center">Kesiapan lulusan</span>
      <h2 class="section-title">Satu sekolah, beragam jalur sukses.</h2>
      <p class="section-desc" style="margin-left:auto;margin-right:auto">Lulusan dipersiapkan untuk melanjutkan kuliah, memasuki dunia kerja, atau merintis usaha dengan bekal portofolio dan kompetensi.</p>
    </div>
    <div class="grid grid-3 mt-10">
      <article class="card experience-card reveal">
        <div class="experience-icon" style="background:#e8eefb;color:#3b6fc0">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
            <path d="M6 12v5c3 3 9 3 12 0v-5" />
          </svg>
        </div>
        <h3>Kuliah</h3>
        <p>"Aulia, lulusan IPA 2025, lolos SNBP ke ITB dan sekarang aktif di riset energi terbarukan — semua berkat portofolio proyek sains."</p>
        <div class="experience-meta">
          <span class="experience-tag">PTN</span>
          <span class="experience-tag">SNBP</span>
          <span class="experience-tag">Riset</span>
        </div>
      </article>
      <article class="card experience-card reveal">
        <div class="experience-icon" style="background:#e8f4f8;color:var(--primary)">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <h3>Kerja</h3>
        <p>"Doni, lulusan RPL 2024, langsung direkrut sebagai junior developer di startup edtech setelah sertifikasi kompetensi dan magang."</p>
        <div class="experience-meta">
          <span class="experience-tag">SMK</span>
          <span class="experience-tag">Sertifikasi</span>
          <span class="experience-tag">Mitra Industri</span>
        </div>
      </article>
      <article class="card experience-card reveal">
        <div class="experience-icon" style="background:#e6f7ee;color:var(--success)">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 8v8" />
            <path d="M8 12h8" />
            <circle cx="12" cy="12" r="10" />
          </svg>
        </div>
        <h3>Usaha</h3>
        <p>"Sari dan tim BDP memulai bisnis katering sehat — berawal dari proyek kewirausahaan, kini omzetnya mencapai 8 juta per bulan."</p>
        <div class="experience-meta">
          <span class="experience-tag">BDP</span>
          <span class="experience-tag">Proyek Usaha</span>
          <span class="experience-tag">Inkubasi</span>
        </div>
      </article>
    </div>
  </div>
</section>
@endsection


