@extends('layouts.public')

@section('title', 'PPDB | SMA/SMK Cakrawala')

@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumb">Beranda / PPDB</div><h1>Penerimaan Peserta Didik Baru</h1><p class="lead">Informasi jalur pendaftaran, persyaratan, jadwal, biaya, dan formulir minat calon siswa tahun ajaran 2026/2027.</p><div class="hero-actions"><a class="btn btn-primary" href="#form-ppdb">Isi Formulir Minat</a><a class="btn btn-outline" href="#jadwal">Lihat Jadwal</a></div></div></section>
<section class="section"><div class="container">
  <span class="kicker">Alur pendaftaran</span><h2 class="section-title">Daftar dalam empat langkah mudah.</h2>
  <div class="grid grid-2" style="margin-top:30px">
    <div class="card step reveal"><h3>Isi formulir pendaftaran</h3><p style="color:var(--muted)">Lengkapi identitas calon siswa, orang tua, dan pilihan program.</p></div>
    <div class="card step reveal"><h3>Unggah dokumen</h3><p style="color:var(--muted)">Siapkan rapor, kartu keluarga, akta lahir, dan pas foto.</p></div>
    <div class="card step reveal"><h3>Seleksi dan wawancara</h3><p style="color:var(--muted)">Ikuti tes pemetaan akademik dan sesi wawancara.</p></div>
    <div class="card step reveal"><h3>Daftar ulang</h3><p style="color:var(--muted)">Konfirmasi kelulusan dan selesaikan administrasi.</p></div>
  </div>
</div></section>
<section class="section" id="jadwal" style="background:var(--card)"><div class="container split">
  <div><span class="kicker">Jadwal PPDB</span><h2 class="section-title">Gelombang penerimaan 2026/2027.</h2><p class="section-desc">Tanggal berikut merupakan contoh dan dapat diganti sesuai kalender resmi sekolah.</p></div>
  <div class="timeline">
    <div class="timeline-item reveal"><div class="timeline-date">1 Jul–31 Agu</div><div><strong>Gelombang Prestasi</strong><span>Seleksi nilai rapor, prestasi akademik, dan nonakademik.</span></div></div>
    <div class="timeline-item reveal"><div class="timeline-date">1 Sep–30 Nov</div><div><strong>Gelombang I</strong><span>Pendaftaran umum dengan kuota prioritas.</span></div></div>
    <div class="timeline-item reveal"><div class="timeline-date">1 Des–28 Feb</div><div><strong>Gelombang II</strong><span>Pendaftaran lanjutan sesuai ketersediaan kursi.</span></div></div>
    <div class="timeline-item reveal"><div class="timeline-date">Mar–Jun</div><div><strong>Gelombang Akhir</strong><span>Dibuka apabila kuota program masih tersedia.</span></div></div>
  </div>
</div></section>
<section class="section"><div class="container"><span class="kicker">Persyaratan</span><h2 class="section-title">Dokumen yang perlu disiapkan.</h2>
  <div class="grid grid-3" style="margin-top:30px">
    <div class="card reveal"><h3>Dokumen Siswa</h3><p style="color:var(--muted)">Akta lahir, kartu keluarga, NISN, pas foto, dan identitas.</p></div>
    <div class="card reveal"><h3>Dokumen Akademik</h3><p style="color:var(--muted)">Rapor semester 1–5 dan sertifikat prestasi apabila ada.</p></div>
    <div class="card reveal"><h3>Dokumen Pendukung</h3><p style="color:var(--muted)">Surat keterangan lulus serta dokumen program beasiswa.</p></div>
  </div>
</div></section>
<section class="section" style="background:var(--card)"><div class="container"><span class="kicker">Biaya pendidikan</span><h2 class="section-title">Transparan dan dapat disesuaikan dengan kebijakan sekolah.</h2>
<div class="table-wrap" style="margin-top:28px"><table><thead><tr><th>Komponen</th><th>SMA</th><th>SMK</th><th>Keterangan</th></tr></thead><tbody>
<tr><td>Formulir</td><td>Rp250.000</td><td>Rp250.000</td><td>Dibayar saat pendaftaran</td></tr>
<tr><td>Uang Pangkal</td><td>Mulai Rp7.500.000</td><td>Mulai Rp8.500.000</td><td>Skema cicilan tersedia</td></tr>
<tr><td>SPP Bulanan</td><td>Mulai Rp950.000</td><td>Mulai Rp1.050.000</td><td>Termasuk akses LMS</td></tr>
<tr><td>Praktikum</td><td>Sesuai program</td><td>Sesuai jurusan</td><td>Dirinci pada saat daftar ulang</td></tr>
</tbody></table></div>
<p style="color:var(--muted);font-size:.88rem">* Angka di atas hanya data contoh pada template.</p></div></section>
<section class="section" id="form-ppdb"><div class="container split">
  <div><span class="kicker">Formulir minat</span><h2 class="section-title">Mulai langkah pertama bersama kami.</h2><p class="section-desc">Tim PPDB akan menghubungi calon siswa untuk memberikan informasi program dan jadwal kunjungan sekolah.</p></div>
  <form class="card form-card" data-demo-form>
    <div class="form-grid">
      <div class="field"><label>Nama calon siswa</label><input required placeholder="Nama lengkap"></div>
      <div class="field"><label>Asal sekolah</label><input required placeholder="Nama SMP/MTs"></div>
      <div class="field"><label>Nomor WhatsApp orang tua</label><input required type="tel" placeholder="08xxxxxxxxxx"></div>
      <div class="field"><label>Email</label><input type="email" placeholder="nama@email.com"></div>
      <div class="field"><label>Pilihan jenjang</label><select required><option value="">Pilih jenjang</option><option>SMA</option><option>SMK</option></select></div>
      <div class="field"><label>Program diminati</label><select required><option value="">Pilih program</option><option>SMA Sains & Teknologi</option><option>SMA Sosial & Humaniora</option><option>RPL</option><option>DKV</option><option>Akuntansi</option><option>Bisnis Digital</option></select></div>
      <div class="field full"><label>Pesan</label><textarea placeholder="Pertanyaan atau informasi tambahan"></textarea></div>
      <div class="field full"><button class="btn btn-primary" type="submit">Kirim Formulir</button><div class="notice"></div></div>
    </div>
  </form>
</div></section>
@endsection
