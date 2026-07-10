@extends('layouts.public')

@section('title', 'Akademik | SMA/SMK Cakrawala')

@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumb">Beranda / Akademik</div><h1>Program Akademik & Vokasi</h1><p class="lead">Kurikulum yang fleksibel, berbasis proyek, terhubung dengan teknologi, perguruan tinggi, dan kebutuhan industri.</p></div></section>
<section class="section"><div class="container">
  <span class="kicker">Program unggulan</span><h2 class="section-title">Jalur belajar yang relevan untuk masa depan.</h2>
  <div class="tabs"><button class="tab-btn active" data-filter="all">Semua</button><button class="tab-btn" data-filter="sma">SMA</button><button class="tab-btn" data-filter="smk">SMK</button></div>
  <div class="grid grid-3">
    <article class="card program-card filter-item reveal" data-category="sma"><span class="tag">SMA</span><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg></div><h3>Sains & Teknologi</h3><p>Matematika lanjut, sains terapan, riset, coding, dan data.</p><div class="program-meta"><span>Riset siswa</span><span>Olimpiade</span></div></article>
    <article class="card program-card filter-item reveal" data-category="sma"><span class="tag">SMA</span><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div><h3>Sosial & Humaniora</h3><p>Ekonomi, geografi, sosiologi, komunikasi, dan studi global.</p><div class="program-meta"><span>Debat</span><span>Proyek sosial</span></div></article>
    <article class="card program-card filter-item reveal" data-category="smk"><span class="tag">SMK</span><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div><h3>Rekayasa Perangkat Lunak</h3><p>Pengembangan web, aplikasi, basis data, cloud, dan UI/UX.</p><div class="program-meta"><span>Portofolio</span><span>Sertifikasi</span></div></article>
    <article class="card program-card filter-item reveal" data-category="smk"><span class="tag">SMK</span><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div><h3>Desain Komunikasi Visual</h3><p>Branding, ilustrasi, fotografi, video, dan animasi.</p><div class="program-meta"><span>Studio</span><span>Pameran karya</span></div></article>
    <article class="card program-card filter-item reveal" data-category="smk"><span class="tag">SMK</span><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg></div><h3>Akuntansi & Keuangan</h3><p>Akuntansi, perpajakan, aplikasi keuangan, dan administrasi bisnis.</p><div class="program-meta"><span>Praktik industri</span><span>Uji kompetensi</span></div></article>
    <article class="card program-card filter-item reveal" data-category="smk"><span class="tag">SMK</span><div class="icon-box"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></div><h3>Bisnis Digital</h3><p>E-commerce, pemasaran digital, analitik, dan kewirausahaan.</p><div class="program-meta"><span>Proyek usaha</span><span>Marketplace</span></div></article>
  </div>
</div></section>
<section class="section" style="background:var(--card)"><div class="container split">
  <div><span class="kicker">Model pembelajaran</span><h2 class="section-title">Belajar melalui pengalaman, proyek, dan refleksi.</h2><p class="section-desc">Guru berperan sebagai fasilitator yang membantu siswa memahami konsep, memecahkan masalah, dan menghasilkan karya.</p></div>
  <div class="grid grid-2">
    <div class="card reveal"><h3>Project-Based Learning</h3><p style="color:var(--muted)">Proyek lintas mata pelajaran yang menghasilkan solusi nyata.</p></div>
    <div class="card reveal"><h3>Blended Learning</h3><p style="color:var(--muted)">Perpaduan tatap muka, LMS, materi digital, dan asesmen daring.</p></div>
    <div class="card reveal"><h3>Industry Exposure</h3><p style="color:var(--muted)">Kunjungan, guru tamu, magang, dan studi kasus dari dunia kerja.</p></div>
    <div class="card reveal"><h3>Student Mentoring</h3><p style="color:var(--muted)">Pendampingan akademik, karakter, portofolio, dan rencana karier.</p></div>
  </div>
</div></section>
<section class="section"><div class="container"><span class="kicker">Ekstrakurikuler</span><h2 class="section-title">Ruang untuk bertumbuh di luar kelas.</h2><div class="table-wrap" style="margin-top:28px"><table>
<thead><tr><th>Bidang</th><th>Pilihan Kegiatan</th><th>Fokus Pengembangan</th></tr></thead>
<tbody>
<tr><td>Akademik</td><td>Klub Sains, Bahasa Inggris, Debat, Karya Ilmiah</td><td>Riset, komunikasi, kompetisi</td></tr>
<tr><td>Teknologi</td><td>Robotics, Coding Club, E-Sports Edukatif</td><td>Logika, kolaborasi, inovasi</td></tr>
<tr><td>Seni</td><td>Band, Tari, Teater, Fotografi, Film</td><td>Kreativitas dan ekspresi</td></tr>
<tr><td>Olahraga</td><td>Basket, Futsal, Badminton, Pencak Silat</td><td>Kebugaran dan sportivitas</td></tr>
<tr><td>Kepemimpinan</td><td>OSIS, Pramuka, PMR, Paskibra</td><td>Disiplin, pelayanan, organisasi</td></tr>
</tbody></table></div></div></section>
<section class="section accent-section"><div class="container split"><div><span class="kicker" style="color:#ffdc8c">Kesiapan lulusan</span><h2 class="section-title">Satu sekolah, beragam jalur sukses.</h2><p class="section-desc">Lulusan dipersiapkan untuk melanjutkan kuliah, memasuki dunia kerja, atau merintis usaha dengan bekal portofolio dan kompetensi.</p></div><div class="grid grid-3"><div class="card" style="color:var(--ink);text-align:center"><strong style="font-size:2rem">Kuliah</strong><p style="color:var(--muted)">Bimbingan peminatan dan seleksi masuk.</p></div><div class="card" style="color:var(--ink);text-align:center"><strong style="font-size:2rem">Kerja</strong><p style="color:var(--muted)">Sertifikasi dan penyaluran mitra.</p></div><div class="card" style="color:var(--ink);text-align:center"><strong style="font-size:2rem">Usaha</strong><p style="color:var(--muted)">Mentoring dan proyek bisnis.</p></div></div></div></section>
@endsection
