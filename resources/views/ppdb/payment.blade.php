@extends('layouts.public')

@section('title', 'Pembayaran PPDB | InvestaSchool')

@section('content')
<form id="ppdbLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none">
  @csrf
  <input type="hidden" name="redirect_to" value="/">
</form>

<section style="background:var(--bg);padding:1.5rem 0 4rem">
  <div class="container" style="max-width:640px">

    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem">
      <div>
        <div style="font-size:.85rem;color:var(--muted);margin-bottom:.25rem">Beranda / PPDB / Pembayaran</div>
        <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-family:Calistoga,Georgia,serif;font-weight:400;margin:0;color:var(--ink)">Pembayaran PPDB</h1>
        <p style="font-size:.9rem;color:var(--muted);margin:.25rem 0 0">Selesaikan pembayaran untuk menyelesaikan pendaftaran</p>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:0.5rem 1rem;border-radius:10px;border:1px solid var(--line);background:var(--card);color:var(--ink);font-size:.82rem;font-weight:700;cursor:pointer;transition:0.2s;font-family:inherit" onmouseover="this.style.borderColor='var(--primary-2)'" onmouseout="this.style.borderColor='var(--line)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Keluar
        </button>
      </form>
    </div>

    <div class="ppdb-card" style="padding:2rem">

    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius);padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:flex-start;gap:1rem">
      <div style="width:40px;height:40px;border-radius:50%;background:var(--success);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div>
        <h3 style="font-size:1.05rem;font-weight:400;font-family:Calistoga,Georgia,serif;margin:0 0 .25rem;color:var(--ink)">Selamat! Anda Diterima</h3>
        <p style="font-size:.85rem;color:var(--muted);margin:0;line-height:1.6">Pendaftaran Anda telah diverifikasi dan diterima oleh sekolah. Silakan lakukan pembayaran untuk menyelesaikan proses pendaftaran.</p>
      </div>
    </div>

    <div style="background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:1.25rem 1.5rem;margin-bottom:1.25rem">
      <h3 style="font-size:1rem;font-weight:400;font-family:Calistoga,Georgia,serif;margin:0 0 1rem;color:var(--ink)">Ringkasan Pendaftar</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
        <div>
          <p style="font-size:.78rem;color:var(--muted);margin:0 0 .15rem;font-weight:600">Nama Lengkap</p>
          <p style="font-size:.9rem;font-weight:700;color:var(--ink);margin:0">{{ $applicant->full_name }}</p>
        </div>
        <div>
          <p style="font-size:.78rem;color:var(--muted);margin:0 0 .15rem;font-weight:600">Asal Sekolah</p>
          <p style="font-size:.9rem;font-weight:700;color:var(--ink);margin:0">{{ $applicant->asal_sekolah ?? '-' }}</p>
        </div>
        <div>
          <p style="font-size:.78rem;color:var(--muted);margin:0 0 .15rem;font-weight:600">Jenjang</p>
          <p style="font-size:.9rem;font-weight:700;color:var(--ink);margin:0">{{ $applicant->jenjang ?? '-' }}</p>
        </div>
        <div>
          <p style="font-size:.78rem;color:var(--muted);margin:0 0 .15rem;font-weight:600">Program Diminati</p>
          <p style="font-size:.9rem;font-weight:700;color:var(--ink);margin:0">{{ $applicant->program_diminati ?? '-' }}</p>
        </div>
      </div>
    </div>

    <div style="background:var(--card);border:1.5px solid var(--accent);border-radius:var(--radius);padding:1.25rem 1.5rem;margin-bottom:1.25rem">
      <h3 style="font-size:1rem;font-weight:400;font-family:Calistoga,Georgia,serif;margin:0 0 1rem;color:var(--ink)">Informasi Pembayaran</h3>
      <div style="display:grid;gap:.5rem">
        <div style="display:flex;justify-content:space-between;font-size:.9rem">
          <span style="color:var(--muted)">Bank</span>
          <span style="font-weight:700;color:var(--ink)">BCA</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.9rem">
          <span style="color:var(--muted)">No. Rekening</span>
          <span style="font-weight:700;color:var(--ink)">1234567890</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:.9rem">
          <span style="color:var(--muted)">Atas Nama</span>
          <span style="font-weight:700;color:var(--ink)">InvestaSchool</span>
        </div>
        <div style="border-top:1px solid var(--line);margin:.5rem 0"></div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span style="font-size:1rem;font-weight:800;color:var(--ink)">Nominal</span>
          <span style="font-size:1.4rem;font-weight:900;color:var(--success)">Rp 250.000</span>
        </div>
      </div>
    </div>

    <div style="background:color-mix(in srgb, var(--primary) 6%, var(--card));border:1px solid color-mix(in srgb, var(--primary) 16%, var(--line));border-radius:var(--radius-sm);padding:1rem 1.25rem;margin-bottom:1.5rem">
      <p style="font-size:.85rem;color:var(--primary);margin:0;line-height:1.6">
        <strong>Catatan:</strong> Setelah melakukan transfer, klik tombol "Sudah Bayar" di bawah. Akun siswa dan portal orang tua akan otomatis dibuat dan dikirim ke email masing-masing.
      </p>
    </div>

    <form id="paymentForm" action="{{ route('ppdb.pay') }}" method="POST">
      @csrf
      <button type="button" onclick="confirmPayment()" style="width:100%;padding:0.9rem;border-radius:12px;border:none;font-size:1rem;font-weight:800;cursor:pointer;transition:opacity .2s;background:var(--success);color:white;font-family:inherit" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
        Sudah Bayar
      </button>
    </form>

    <div style="text-align:center;margin-top:1rem">
      <a href="{{ route('ppdb.status') }}" style="font-size:.85rem;font-weight:600;color:var(--muted);text-decoration:underline">
        Kembali ke Status
      </a>
    </div>
    </div>

  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmPayment() {
  Swal.fire({
    title: 'Konfirmasi Pembayaran',
    html: '<div style="text-align:left"><p style="margin-bottom:8px">Apakah Anda sudah melakukan pembayaran?</p><p style="font-size:.85rem;color:#666">Setelah konfirmasi, akun siswa dan portal orang tua akan otomatis dibuat.</p></div>',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#1f8f62',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Sudah Bayar',
    cancelButtonText: 'Belum',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Mengirim Konfirmasi...',
        html: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });
      setTimeout(() => { document.getElementById('paymentForm').submit(); }, 1500);
    }
  });
}
</script>

<script>
document.querySelectorAll('.nav-links a, .nav-dropdown-menu a, .nav-dropdown-trigger').forEach(el => {
  el.addEventListener('click', function(e) {
    if (this.closest('.nav-dropdown-trigger')) {
      e.stopPropagation();
      return;
    }
    e.preventDefault();
    const href = this.getAttribute('href');
    if (!href || href === '#') return;
    Swal.fire({
      title: 'Keluar dari pembayaran?',
      text: 'Progres Anda akan tetap tersimpan.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#0b3b75',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, keluar',
      cancelButtonText: 'Tetap di sini',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.getElementById('ppdbLogoutForm');
        const input = form.querySelector('input[name="redirect_to"]');
        if (input) input.value = href;
        form.submit();
      }
    });
  });
});
</script>
@endsection


