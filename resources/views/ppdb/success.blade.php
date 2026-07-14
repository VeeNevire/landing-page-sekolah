@extends('layouts.public')

@section('title', 'Pendaftaran Berhasil | SMK MADYA DEPOK')

@section('content')
<section class="page-hero">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center">
    <div>
      <div class="breadcrumb">Beranda / PPDB / Sukses</div>
      <h1>Pendaftaran Terkirim</h1>
    </div>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-outline" style="display:inline-flex;align-items:center;gap:6px;font-size:.85rem;white-space:nowrap">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar
      </button>
    </form>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:560px;text-align:center">
    <div style="width:72px;height:72px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12" />
      </svg>
    </div>

    <span class="kicker">Langkah 3 dari 3</span>
    <h2>
      @if($applicant->status === 'paid')
        Selamat! Anda Resmi Diterima
      @else
        Pendaftaran Berhasil!
      @endif
    </h2>
    <p style="color:var(--muted);margin-top:.75rem;line-height:1.7">
      @if($applicant->status === 'paid')
        Selamat, <strong>{{ $applicant->full_name }}</strong>!
        Pembayaran Anda telah berhasil. Anda kini resmi terdaftar sebagai siswa di SMK MADYA DEPOK.
      @else
        Terima kasih, <strong>{{ $applicant->full_name }}</strong>.
        Data pendaftaran Anda telah kami terima dan akan diproses oleh tim PPDB.
      @endif
    </p>

    <div class="card" style="text-align:left;margin-top:2rem;padding:1.5rem">
      <h4 style="margin-bottom:.75rem">Informasi Penting</h4>
      <ul style="padding-left:1.25rem;line-height:2;color:var(--muted)">
        @if($applicant->status === 'paid')
          <li>Akun siswa Anda telah dibuat dan siap digunakan</li>
          <li>Akun portal orang tua telah dikirim ke email yang terdaftar</li>
          <li>Informasi lebih lanjut akan dikirim via email dalam 1x24 jam</li>
        @else
          <li>Tim PPDB akan memverifikasi data dalam 3-7 hari kerja</li>
          <li>Akun portal orang tua akan dikirim ke email ayah/ibu</li>
          <li>Pantau status pendaftaran melalui email yang terdaftar</li>
        @endif
      </ul>
    </div>

    <div style="margin-top:2rem;display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="{{ route('beranda') }}" class="btn btn-outline">Kembali ke Beranda</a>
      <a href="{{ route('ppdb.start') }}" class="btn btn-primary">Daftarkan Siswa Lain</a>
    </div>
  </div>
</section>
@endsection
