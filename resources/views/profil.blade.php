@extends('layouts.public')

@section('title', 'Profil Sekolah | InvestaSchool')

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
      <span>Profil</span>
    </div>
    <h1>Profil Sekolah</h1>
    <p class="lead">
      <span class="lead-bar"></span>
      <span>Mengenal visi, nilai, sejarah, kepemimpinan, fasilitas, dan budaya InvestaSchool.</span>
    </p>
  </div>
</section>

{{-- KEPALA SEKOLAH --}}
<section class="section">
  <div class="container profile-highlight">
    <div class="principal-card reveal">
      <div class="principal-photo">
        <img src="{{ asset('img/kepsek.png') }}" alt="Kepala Sekolah" loading="lazy">
      </div>
      <div class="mt-4">
        <h3 style="margin-bottom:2px">Dr. Rehan Maulana, S.KOM.M.PD.</h3>
        <span style="color:var(--muted);font-weight:700;font-size:0.9rem">Kepala Sekolah</span>
      </div>
    </div>
    <div class="reveal">
      <span class="kicker">Sambutan kepala sekolah</span>
      <h2 class="section-title">"Mendampingi setiap siswa menemukan versi terbaik dirinya."</h2>
      <p style="font-size:1.05rem;line-height:1.75;color:var(--muted)">Selamat datang di InvestaSchool. Sekolah kami dibangun atas keyakinan bahwa pendidikan yang baik harus relevan, manusiawi, dan memberi ruang kepada siswa untuk bertumbuh.</p>
      <p style="font-size:1.05rem;line-height:1.75;color:var(--muted)" class="mt-4">Kami memadukan pencapaian akademik, keterampilan vokasi, pembentukan karakter, kemampuan digital, dan pengalaman nyata agar lulusan siap melanjutkan pendidikan, bekerja, maupun berwirausaha.</p>
      <div class="flex items-center gap-3 mt-6" style="display:flex;align-items:center;gap:12px">
        <div style="width:40px;height:2px;border-radius:99px;background:linear-gradient(90deg,var(--primary-4),var(--primary-2))"></div>
        <span style="font-weight:800;color:var(--primary-2);font-size:0.88rem">InvestaSchool, sejak 2008</span>
      </div>
    </div>
  </div>
</section>

{{-- VISI & MISI --}}
<section class="section" style="background:var(--card)">
  <div class="container split">
    <div class="reveal">
      <span class="kicker">Visi</span>
      <h2 class="section-title" style="font-size:clamp(1.8rem,3.5vw,2.8rem)">"Menjadi sekolah unggul yang melahirkan pembelajar berkarakter dan siap berkarya."</h2>
      <div style="width:60px;height:4px;border-radius:99px;background:linear-gradient(90deg,var(--primary-4),var(--primary-2));margin-top:20px"></div>
    </div>
    <div class="card reveal" style="padding:30px">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
        <div style="width:40px;height:40px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,var(--primary-4),var(--primary-3));color:white">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" />
          </svg>
        </div>
        <h3 style="margin:0">Misi Sekolah</h3>
      </div>
      <div class="check-list" style="gap:16px">
        <div class="check-item">
          <span class="check">✓</span>
          <span style="font-size:0.95rem">Menyelenggarakan pembelajaran aktif, adaptif, dan berpusat pada siswa.</span>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <span style="font-size:0.95rem">Mengembangkan kompetensi akademik, vokasi, digital, dan kewirausahaan.</span>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <span style="font-size:0.95rem">Membangun budaya integritas, disiplin, empati, dan tanggung jawab.</span>
        </div>
        <div class="check-item">
          <span class="check">✓</span>
          <span style="font-size:0.95rem">Memperluas kolaborasi dengan perguruan tinggi, industri, dan masyarakat.</span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- NILAI UTAMA --}}
<section class="section">
  <div class="container">
    <span class="kicker">Nilai utama</span>
    <h2 class="section-title">Budaya yang kami hidupkan setiap hari.</h2>
    <div class="values mt-8">
      <div class="value reveal">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
        </div>
        <h3>Integritas</h3>
        <p>Jujur, konsisten, dan dapat dipercaya dalam setiap tindakan.</p>
      </div>
      <div class="value reveal">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
          </svg>
        </div>
        <h3>Keunggulan</h3>
        <p>Selalu belajar dan meningkatkan kualitas secara berkelanjutan.</p>
      </div>
      <div class="value reveal">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
          </svg>
        </div>
        <h3>Kolaborasi</h3>
        <p>Tumbuh bersama melalui kerja tim dan kemitraan strategis.</p>
      </div>
      <div class="value reveal">
        <div class="value-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
        </div>
        <h3>Kepedulian</h3>
        <p>Menghargai sesama manusia dan lingkungan sekitar.</p>
      </div>
    </div>
  </div>
</section>

{{-- PERJALANAN KAMI --}}
<section class="section" style="background:var(--card)">
  <div class="container split">
    <div>
      <span class="kicker">Perjalanan kami</span>
      <h2 class="section-title">Bertumbuh bersama perubahan zaman.</h2>
      <p class="section-desc">Sejak 2008, InvestaSchool terus berkembang dan beradaptasi untuk menghadirkan pendidikan terbaik.</p>
    </div>
    <div class="timeline">
      <div class="timeline-item reveal">
        <div class="timeline-date">2008</div>
        <div>
          <strong>Sekolah didirikan</strong>
          <span>Memulai layanan pendidikan menengah berbasis karakter dan akademik.</span>
        </div>
      </div>
      <div class="timeline-item reveal">
        <div class="timeline-date">2014</div>
        <div>
          <strong>Pengembangan SMK</strong>
          <span>Membuka program vokasi sesuai kebutuhan dunia industri.</span>
        </div>
      </div>
      <div class="timeline-item reveal">
        <div class="timeline-date">2021</div>
        <div>
          <strong>Transformasi digital</strong>
          <span>Menerapkan LMS, laboratorium digital, dan pembelajaran hibrida.</span>
        </div>
      </div>
      <div class="timeline-item reveal">
        <div class="timeline-date">2026</div>
        <div>
          <strong>Ekosistem pendidikan terpadu</strong>
          <span>Penguatan kemitraan, sertifikasi kompetensi, dan pusat karier siswa.</span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- FASILITAS --}}
<section class="section">
  <div class="container">
    <span class="kicker">Fasilitas</span>
    <h2 class="section-title">Ruang belajar yang mendukung eksplorasi.</h2>
    <div class="grid grid-3 mt-8">
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v8" />
            <path d="M8 12h8" />
          </svg>
        </div>
        <h3>Laboratorium Sains</h3>
        <p style="color:var(--muted)">Fisika, kimia, biologi, dan ruang praktikum terintegrasi.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="16" height="13" x="4" y="4" rx="2" />
            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
          </svg>
        </div>
        <h3>Laboratorium Komputer</h3>
        <p style="color:var(--muted)">Perangkat terkini untuk coding, desain, dan simulasi bisnis.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20" />
          </svg>
        </div>
        <h3>Perpustakaan Digital</h3>
        <p style="color:var(--muted)">Koleksi cetak, e-book, ruang baca, dan ruang diskusi.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
          </svg>
        </div>
        <h3>Studio Kreatif</h3>
        <p style="color:var(--muted)">Fotografi, video, desain, animasi, dan podcast.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 8v8" />
            <path d="M8 12h8" />
          </svg>
        </div>
        <h3>Area Olahraga</h3>
        <p style="color:var(--muted)">Lapangan multifungsi dan fasilitas kebugaran siswa.</p>
      </div>
      <div class="card card-hover reveal">
        <div class="icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
        </div>
        <h3>UKS & Konseling</h3>
        <p style="color:var(--muted)">Dukungan kesehatan fisik, psikologis, dan konseling karier.</p>
      </div>
    </div>
  </div>
</section>
@endsection