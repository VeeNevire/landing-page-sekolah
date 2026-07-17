@extends('layouts.public')

@section('title', 'PPDB | InvestaSchool')

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
      <span>PPDB</span>
    </div>
    <h1>Penerimaan Peserta Didik Baru</h1>
    <p class="lead">
      <span class="lead-bar"></span>
      <span>Informasi jalur pendaftaran, persyaratan, jadwal, biaya, dan formulir minat calon siswa tahun ajaran 2026/2027.</span>
    </p>
    <div class="hero-actions" style="margin-top:32px">
      <a class="btn btn-primary" href="{{ route('ppdb.start') }}">Daftar Sekarang</a>
      <a class="btn btn-outline" href="#jadwal">Lihat Jadwal</a>
    </div>
  </div>
</section>

{{-- ALUR PENDAFTARAN --}}
<section class="section">
  <div class="container">
    <span class="kicker">Alur pendaftaran</span>
    <h2 class="section-title">Daftar dalam empat langkah mudah.</h2>
    <div class="grid grid-2 mt-8 steps">
      <div class="card step card-hover reveal">
        <h3>Isi formulir pendaftaran</h3>
        <p style="color:var(--muted)">Lengkapi identitas calon siswa, orang tua, dan pilihan program.</p>
      </div>
      <div class="card step card-hover reveal">
        <h3>Unggah dokumen</h3>
        <p style="color:var(--muted)">Siapkan rapor, kartu keluarga, akta lahir, dan pas foto.</p>
      </div>
      <div class="card step card-hover reveal">
        <h3>Seleksi dan wawancara</h3>
        <p style="color:var(--muted)">Ikuti tes pemetaan akademik dan sesi wawancara.</p>
      </div>
      <div class="card step card-hover reveal">
        <h3>Daftar ulang</h3>
        <p style="color:var(--muted)">Konfirmasi kelulusan dan selesaikan administrasi.</p>
      </div>
    </div>
  </div>
</section>

{{-- JADWAL PPDB --}}
<section class="section" id="jadwal" style="background:var(--card)">
  <div class="container">
    <div style="max-width:720px">
      <span class="kicker">Jadwal PPDB</span>
      <h2 class="section-title">Gelombang penerimaan 2026/2027.</h2>
      <p class="section-desc">Pilih gelombang yang sesuai. Semakin awal mendaftar, semakin besar kesempatan mendapatkan program pilihan.</p>
    </div>
    <div class="timeline-modern">
      <div class="tm-item reveal">
        <div class="tm-side">
          <span class="tm-tag tm-tag-prem">PREMIUM</span>
          <div class="tm-date-box">
            <span class="tm-date-range">1 Juli – 31 Agustus</span>
            <span class="tm-status tm-status-active">Aktif</span>
          </div>
        </div>
        <div class="tm-body">
          <div class="tm-dot"></div>
          <div class="tm-card">
            <h3>Gelombang Prestasi</h3>
            <p>Seleksi nilai rapor, prestasi akademik, dan nonakademik tanpa tes masuk.</p>
            <div class="tm-meta">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <span>Prioritas pemilihan program</span>
            </div>
            <a class="btn btn-primary" href="{{ route('ppdb.start') }}">Daftar Sekarang</a>
          </div>
        </div>
      </div>
      <div class="tm-item reveal">
        <div class="tm-side">
          <span class="tm-tag tm-tag-gel1">GEL I</span>
          <div class="tm-date-box">
            <span class="tm-date-range">1 September – 30 November</span>
            <span class="tm-status tm-status-upcoming">Akan datang</span>
          </div>
        </div>
        <div class="tm-body">
          <div class="tm-dot"></div>
          <div class="tm-card">
            <h3>Gelombang I</h3>
            <p>Pendaftaran umum dengan kuota prioritas dan jadwal seleksi reguler.</p>
            <div class="tm-meta">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              <span>Estimasi: 90 hari lagi</span>
            </div>
            <a class="btn btn-outline" href="{{ route('ppdb.start') }}">Beri Pengingat</a>
          </div>
        </div>
      </div>
      <div class="tm-item reveal">
        <div class="tm-side">
          <span class="tm-tag tm-tag-gel2">GEL II</span>
          <div class="tm-date-box">
            <span class="tm-date-range">1 Desember – 28 Februari</span>
            <span class="tm-status tm-status-upcoming">Akan datang</span>
          </div>
        </div>
        <div class="tm-body">
          <div class="tm-dot"></div>
          <div class="tm-card">
            <h3>Gelombang II</h3>
            <p>Pendaftaran lanjutan sesuai ketersediaan kursi pada setiap program.</p>
            <div class="tm-meta">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              <span>Estimasi: 150 hari lagi</span>
            </div>
            <a class="btn btn-outline" href="{{ route('ppdb.start') }}">Beri Pengingat</a>
          </div>
        </div>
      </div>
      <div class="tm-item reveal">
        <div class="tm-side">
          <span class="tm-tag tm-tag-akhir">AKHIR</span>
          <div class="tm-date-box">
            <span class="tm-date-range">Maret – Juni</span>
            <span class="tm-status tm-status-closed">Menunggu</span>
          </div>
        </div>
        <div class="tm-body">
          <div class="tm-dot"></div>
          <div class="tm-card">
            <h3>Gelombang Akhir</h3>
            <p>Dibuka apabila kuota program masih tersedia setelah gelombang sebelumnya.</p>
            <div class="tm-meta">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v4" />
                <path d="M12 16h.01" />
              </svg>
              <span>Jadwal menyusul</span>
            </div>
            <a class="btn btn-outline" href="{{ route('ppdb.start') }}">Notifikasi Saya</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- PERSYARATAN --}}
<section class="section">
  <div class="container">
    <span class="kicker">Persyaratan</span>
    <h2 class="section-title">Dokumen yang perlu disiapkan.</h2>
    <div class="grid grid-3 mt-8">
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20" />
          </svg>
        </div>
        <h3>Dokumen Siswa</h3>
        <p style="color:var(--muted)">Akta lahir, kartu keluarga, NISN, pas foto, dan identitas.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v8" />
            <path d="M8 12h8" />
          </svg>
        </div>
        <h3>Dokumen Akademik</h3>
        <p style="color:var(--muted)">Rapor semester 1—5 dan sertifikat prestasi apabila ada.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
            <path d="M6 12v5c3 3 9 3 12 0v-5" />
          </svg>
        </div>
        <h3>Dokumen Pendukung</h3>
        <p style="color:var(--muted)">Surat keterangan lulus serta dokumen program beasiswa.</p>
      </div>
    </div>
  </div>
</section>

{{-- BIAYA PENDIDIKAN --}}
<section class="section" style="background:var(--card)">
  <div class="container">
    <span class="kicker">Biaya pendidikan</span>
    <h2 class="section-title">Transparan dan dapat disesuaikan dengan kebijakan sekolah.</h2>
    <div class="table-wrap mt-8">
      <table>
        <thead>
          <tr>
            <th>Komponen</th>
            <th>SMA</th>
            <th>SMK</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Formulir</td><td>Rp250.000</td><td>Rp250.000</td><td>Dibayar saat pendaftaran</td></tr>
          <tr><td>Uang Pangkal</td><td>Mulai Rp7.500.000</td><td>Mulai Rp8.500.000</td><td>Skema cicilan tersedia</td></tr>
          <tr><td>SPP Bulanan</td><td>Mulai Rp950.000</td><td>Mulai Rp1.050.000</td><td>Termasuk akses LMS</td></tr>
          <tr><td>Praktikum</td><td>Sesuai program</td><td>Sesuai jurusan</td><td>Dirinci pada saat daftar ulang</td></tr>
        </tbody>
      </table>
    </div>
    <p style="color:var(--muted);font-size:.88rem;margin-top:12px">* Data biaya di atas hanya contoh untuk template.</p>
  </div>
</section>

{{-- FORMULIR MINAT --}}
<section class="section" id="form-ppdb">
  <div class="container split">
    <div>
      <span class="kicker">Formulir minat</span>
      <h2 class="section-title">Mulai langkah pertama bersama kami.</h2>
      <p class="section-desc">Tim PPDB akan menghubungi calon siswa untuk memberikan informasi program dan jadwal kunjungan sekolah.</p>
    </div>
    <div class="card form-card reveal">
      <div class="form-card-icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="20" height="14" x="2" y="3" rx="2" />
          <line x1="8" y1="21" x2="16" y2="21" />
          <line x1="12" y1="17" x2="12" y2="21" />
        </svg>
      </div>
      <h3 style="margin-bottom:0.8rem">Daftar Online</h3>
      <p style="color:var(--muted);margin-bottom:1.5rem;max-width:360px;margin-left:auto;margin-right:auto">Mulai pendaftaran dengan mengisi formulir online. Isi data siswa dan orang tua secara lengkap.</p>
      <a href="{{ route('ppdb.start') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        Daftar Sekarang
      </a>
    </div>
  </div>
</section>
@endsection


