@extends('layouts.public')

@section('title', 'Kontak | InvestaSchool')

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
      <span>Kontak</span>
    </div>
    <h1>Hubungi Kami</h1>
    <p class="lead">
      <span class="lead-bar"></span>
      <span>Tim sekolah siap membantu pertanyaan mengenai program pendidikan, pendaftaran, kemitraan, dan kunjungan sekolah.</span>
    </p>
  </div>
</section>

{{-- INFORMASI KONTAK & FORM --}}
<section class="section">
  <div class="container split">
    <div>
      <span class="kicker">Informasi kontak</span>
      <h2 class="section-title">Mari terhubung.</h2>
      <div class="contact-list">
        <div class="contact-item card-hover" style="cursor:default">
          <div class="icon-box">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
              <circle cx="12" cy="10" r="3" />
            </svg>
          </div>
          <div><strong>Alamat</strong>
            <div style="color:var(--muted)">Jl. Pendidikan No. 88, Jakarta</div>
          </div>
        </div>
        <div class="contact-item card-hover" style="cursor:default">
          <div class="icon-box">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
          </div>
          <div><strong>Telepon & WhatsApp</strong>
            <div style="color:var(--muted)">(021) 555-0198 • 0812-3456-7890</div>
          </div>
        </div>
        <div class="contact-item card-hover" style="cursor:default">
          <div class="icon-box">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect width="20" height="16" x="2" y="4" rx="2" />
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
            </svg>
          </div>
          <div><strong>Email</strong>
            <div style="color:var(--muted)">info@investaschool.sch.id</div>
          </div>
        </div>
        <div class="contact-item card-hover" style="cursor:default">
          <div class="icon-box">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
          </div>
          <div><strong>Jam Layanan</strong>
            <div style="color:var(--muted)">Senin–Jumat, 07.00–16.00 WIB</div>
          </div>
        </div>
      </div>
    </div>
    <form class="card form-card reveal" data-demo-form>
      <h3 style="margin-top:0">Kirim Pesan</h3>
      <div class="form-grid">
        <div class="field"><label>Nama</label><input required placeholder="Nama lengkap"></div>
        <div class="field"><label>Email</label><input required type="email" placeholder="nama@email.com"></div>
        <div class="field full"><label>Subjek</label><input required placeholder="Subjek pesan"></div>
        <div class="field full"><label>Pesan</label><textarea required placeholder="Tuliskan pesan Anda"></textarea></div>
        <div class="field full">
          <button class="btn btn-primary" type="submit">Kirim Pesan</button>
          <div class="notice"></div>
        </div>
      </div>
    </form>
  </div>
</section>

{{-- LOKASI --}}
<section class="section" style="background:var(--card)">
  <div class="container">
    <span class="kicker">Lokasi</span>
    <h2 class="section-title">Kunjungi kampus kami.</h2>
    <div class="map-placeholder mt-8"></div>
  </div>
</section>

{{-- FAQ --}}
<section class="section">
  <div class="container">
    <span class="kicker">Pertanyaan umum</span>
    <h2 class="section-title">Informasi singkat untuk calon siswa dan orang tua.</h2>
    <div class="grid grid-2 mt-8">
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
            <path d="M12 17h.01" />
          </svg>
        </div>
        <h3>Apakah tersedia beasiswa?</h3>
        <p style="color:var(--muted)">Tersedia program bantuan berdasarkan prestasi dan kondisi ekonomi sesuai ketentuan sekolah.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
        </div>
        <h3>Bisakah melakukan kunjungan sekolah?</h3>
        <p style="color:var(--muted)">Bisa. Jadwal kunjungan dapat dikoordinasikan melalui telepon atau formulir kontak.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
            <path d="M6 12v5c3 3 9 3 12 0v-5" />
          </svg>
        </div>
        <h3>Apakah SMK memiliki program magang?</h3>
        <p style="color:var(--muted)">Ya. Setiap siswa SMK mengikuti praktik kerja lapangan bersama mitra industri.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="16" height="13" x="4" y="4" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
        </div>
        <h3>Apakah sekolah memiliki LMS?</h3>
        <p style="color:var(--muted)">Ya. Materi, tugas, asesmen, dan komunikasi pembelajaran tersedia secara digital.</p>
      </div>
    </div>
  </div>
</section>
@endsection


