@extends('layouts.public')

@section('title', 'Profil Sekolah | SMA/SMK Cakrawala')

@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumb">Beranda / Profil</div><h1>Profil Sekolah</h1><p class="lead">Mengenal visi, nilai, sejarah, kepemimpinan, fasilitas, dan budaya SMA/SMK Cakrawala Nusantara.</p></div></section>
<section class="section"><div class="container profile-highlight">
  <div class="principal-card reveal"><div class="principal-photo">CN</div><h3 style="margin-bottom:4px">Dr. Ananta Pradipta, M.Pd.</h3><span style="color:var(--muted)">Kepala Sekolah</span></div>
  <div class="reveal"><span class="kicker">Sambutan kepala sekolah</span><h2 class="section-title">Mendampingi setiap siswa menemukan versi terbaik dirinya.</h2><p>Selamat datang di SMA/SMK Cakrawala Nusantara. Sekolah kami dibangun atas keyakinan bahwa pendidikan yang baik harus relevan, manusiawi, dan memberi ruang kepada siswa untuk bertumbuh.</p><p>Kami memadukan pencapaian akademik, keterampilan vokasi, pembentukan karakter, kemampuan digital, dan pengalaman nyata agar lulusan siap melanjutkan pendidikan, bekerja, maupun berwirausaha.</p></div>
</div></section>
<section class="section" style="background:var(--card)"><div class="container split">
  <div class="reveal"><span class="kicker">Visi</span><h2 class="section-title">Menjadi sekolah unggul yang melahirkan pembelajar berkarakter dan siap berkarya.</h2></div>
  <div class="card reveal"><h3>Misi Sekolah</h3><div class="check-list">
    <div class="check-item"><span class="check">✓</span><span>Menyelenggarakan pembelajaran aktif, adaptif, dan berpusat pada siswa.</span></div>
    <div class="check-item"><span class="check">✓</span><span>Mengembangkan kompetensi akademik, vokasi, digital, dan kewirausahaan.</span></div>
    <div class="check-item"><span class="check">✓</span><span>Membangun budaya integritas, disiplin, empati, dan tanggung jawab.</span></div>
    <div class="check-item"><span class="check">✓</span><span>Memperluas kolaborasi dengan perguruan tinggi, industri, dan masyarakat.</span></div>
  </div>
</div></section>
<section class="section"><div class="container"><span class="kicker">Nilai utama</span><h2 class="section-title">Budaya yang kami hidupkan setiap hari.</h2><div class="values" style="margin-top:30px">
  <div class="card value reveal"><h3>Integritas</h3><p style="color:var(--muted)">Jujur, konsisten, dan dapat dipercaya.</p></div>
  <div class="card value reveal"><h3>Keunggulan</h3><p style="color:var(--muted)">Selalu belajar dan meningkatkan kualitas.</p></div>
  <div class="card value reveal"><h3>Kolaborasi</h3><p style="color:var(--muted)">Tumbuh bersama melalui kerja tim.</p></div>
  <div class="card value reveal"><h3>Kepedulian</h3><p style="color:var(--muted)">Menghargai manusia dan lingkungan.</p></div>
</div></div></section>
<section class="section" style="background:var(--card)"><div class="container split">
  <div><span class="kicker">Perjalanan kami</span><h2 class="section-title">Bertumbuh bersama perubahan zaman.</h2><p class="section-desc">Template linimasa ini dapat disesuaikan dengan sejarah resmi sekolah.</p></div>
  <div class="timeline">
    <div class="timeline-item reveal"><div class="timeline-date">2008</div><div><strong>Sekolah didirikan</strong><span>Memulai layanan pendidikan menengah berbasis karakter.</span></div></div>
    <div class="timeline-item reveal"><div class="timeline-date">2014</div><div><strong>Pengembangan SMK</strong><span>Membuka program vokasi sesuai kebutuhan industri.</span></div></div>
    <div class="timeline-item reveal"><div class="timeline-date">2021</div><div><strong>Transformasi digital</strong><span>Menerapkan LMS, laboratorium digital, dan pembelajaran hibrida.</span></div></div>
    <div class="timeline-item reveal"><div class="timeline-date">2026</div><div><strong>Ekosistem pendidikan terpadu</strong><span>Penguatan kemitraan, sertifikasi, dan pusat karier siswa.</span></div></div>
  </div>
</div></section>
<section class="section"><div class="container"><span class="kicker">Fasilitas</span><h2 class="section-title">Ruang belajar yang mendukung eksplorasi.</h2><div class="grid grid-3" style="margin-top:30px">
  <div class="card card-hover reveal"><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg></div><h3>Laboratorium Sains</h3><p style="color:var(--muted)">Fisika, kimia, biologi, dan ruang praktikum terintegrasi.</p></div>
  <div class="card card-hover reveal"><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="13" x="4" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></div><h3>Laboratorium Komputer</h3><p style="color:var(--muted)">Perangkat terkini untuk coding, desain, dan simulasi bisnis.</p></div>
  <div class="card card-hover reveal"><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg></div><h3>Perpustakaan Digital</h3><p style="color:var(--muted)">Koleksi cetak, e-book, ruang baca, dan ruang diskusi.</p></div>
  <div class="card card-hover reveal"><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div><h3>Studio Kreatif</h3><p style="color:var(--muted)">Fotografi, video, desain, animasi, dan podcast.</p></div>
  <div class="card card-hover reveal"><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg></div><h3>Area Olahraga</h3><p style="color:var(--muted)">Lapangan multifungsi dan fasilitas kebugaran siswa.</p></div>
  <div class="card card-hover reveal"><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg></div><h3>UKS & Konseling</h3><p style="color:var(--muted)">Dukungan kesehatan fisik, psikologis, dan konseling karier.</p></div>
</div></div></section>
@endsection
