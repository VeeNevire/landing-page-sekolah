@extends('layouts.public')

@section('title', 'Daftar PPDB Online | SMK MADYA DEPOK')

@push('styles')
<style>
.auth-tab-segmented {
  color: var(--muted);
  background: transparent;
  cursor: pointer;
  font-family: inherit;
}
.auth-tab-segmented.active {
  background: var(--primary);
  color: white;
}
.ppdb-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s;
  background: var(--card);
  border: 1.5px solid var(--line);
  color: var(--ink);
  font-family: inherit;
}
.ppdb-input:focus {
  border-color: var(--primary-2);
  box-shadow: 0 0 0 3px rgba(20, 87, 166, 0.1);
}
.ppdb-input::placeholder {
  color: var(--muted);
  opacity: 0.6;
}
.step-dot {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
  font-weight: 800;
  transition: 0.2s ease;
}
</style>
@endpush

@section('content')
<form id="ppdbLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none">
  @csrf
  <input type="hidden" name="redirect_to" value="/">
</form>

<section style="min-height:calc(100vh - 100px);background:var(--bg);padding:2rem 0 4rem">
  <div class="container" style="max-width:580px">

    <div class="text-sm" style="color:var(--muted);margin-bottom:.35rem">Beranda / PPDB / Daftar</div>
    <h1 style="font-size:clamp(2rem,4.5vw,3rem);font-family:Calistoga,Georgia,serif;font-weight:400;margin:0 0 .35rem;color:var(--ink)">PPDB Online</h1>
    <p style="font-size:1rem;color:var(--muted);margin:0 0 2rem">Masuk ke akun Anda atau daftar baru untuk melanjutkan pendaftaran PPDB.</p>

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:2rem">
      <div style="display:flex;align-items:center;gap:8px">
        <div class="step-dot" style="background:var(--primary);color:white">1</div>
        <span style="font-size:.78rem;font-weight:700;color:var(--primary);white-space:nowrap">Registrasi</span>
      </div>
      <div style="flex:1;height:1px;background:var(--line);max-width:48px"></div>
      <div style="display:flex;align-items:center;gap:8px">
        <div class="step-dot" style="background:var(--line);color:var(--muted)">2</div>
        <span style="font-size:.78rem;font-weight:700;color:var(--muted);white-space:nowrap">Isi Formulir</span>
      </div>
      <div style="flex:1;height:1px;background:var(--line);max-width:48px"></div>
      <div style="display:flex;align-items:center;gap:8px">
        <div class="step-dot" style="background:var(--line);color:var(--muted)">3</div>
        <span style="font-size:.78rem;font-weight:700;color:var(--muted);white-space:nowrap">Konfirmasi</span>
      </div>
    </div>

    @if (session('error'))
    <div style="background:#fef2f2;color:#b91c1c;padding:12px 16px;border-radius:12px;font-weight:600;font-size:.9rem;margin-bottom:1.5rem;text-align:center">{{ session('error') }}</div>
    @endif
    @if (session('success'))
    <div style="background:#f0fdf4;color:#15803d;padding:12px 16px;border-radius:12px;font-weight:600;font-size:.9rem;margin-bottom:1.5rem;text-align:center">{{ session('success') }}</div>
    @endif

    <div class="ppdb-card" style="padding:2rem 2rem 1.75rem">
      <div style="display:inline-flex;border-radius:999px;overflow:hidden;border:1px solid var(--line);background:var(--bg);margin-bottom:2rem">
        <button class="auth-tab-segmented active" onclick="switchTab('login')" style="padding:0.6rem 1.75rem;font-size:.9rem;font-weight:700;border:none">Masuk</button>
        <button class="auth-tab-segmented" onclick="switchTab('register')" style="padding:0.6rem 1.75rem;font-size:.9rem;font-weight:700;border:none">Daftar Baru</button>
      </div>

      <div id="login-content" class="tab-content active">
        <h2 style="font-size:1.5rem;font-family:Calistoga,Georgia,serif;font-weight:400;margin:0 0 .25rem;color:var(--ink)">Masuk ke Akun PPDB</h2>
        <p style="font-size:.9rem;color:var(--muted);margin:0 0 1.5rem">Lanjutkan proses pendaftaran Anda</p>

        <form method="POST" action="{{ route('login') }}">
          @csrf
          <input type="hidden" name="role" value="applicant">

          <div class="field" style="margin-bottom:1.25rem">
            <label for="login_email" style="font-size:.85rem;font-weight:700;margin-bottom:.4rem;color:var(--ink)">Email</label>
            <input type="email" id="login_email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh@email.com" class="ppdb-input">
            @error('email')<p style="color:var(--danger);font-size:.8rem;margin-top:.35rem">{{ $message }}</p>@enderror
          </div>

          <div class="field" style="margin-bottom:1.5rem">
            <label for="login_password" style="font-size:.85rem;font-weight:700;margin-bottom:.4rem;color:var(--ink)">Password</label>
            <input type="password" id="login_password" name="password" required placeholder="Masukkan password Anda" class="ppdb-input">
            @error('password')<p style="color:var(--danger);font-size:.8rem;margin-top:.35rem">{{ $message }}</p>@enderror
          </div>

          <button type="submit" style="width:100%;padding:0.8rem;border-radius:12px;border:none;font-size:.95rem;font-weight:800;cursor:pointer;transition:opacity .2s;background:var(--primary);color:white" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
            Masuk Sekarang
          </button>

          <p style="text-align:center;font-size:.85rem;color:var(--muted);margin-top:1.25rem">
            Belum punya akun? <a href="javascript:void(0)" onclick="switchTab('register')" style="font-weight:700;color:var(--primary-2)">Daftar di sini</a>
          </p>
        </form>
      </div>

      <div id="register-content" class="tab-content">
        <h2 style="font-size:1.5rem;font-family:Calistoga,Georgia,serif;font-weight:400;margin:0 0 .25rem;color:var(--ink)">Registrasi Akun PPDB</h2>
        <p style="font-size:.9rem;color:var(--muted);margin:0 0 1.5rem">Isi formulir di bawah untuk membuat akun pendaftaran</p>

        <form method="POST" action="{{ route('ppdb.manual.register') }}">
          @csrf

          <div class="field" style="margin-bottom:1.25rem">
            <label for="full_name" style="font-size:.85rem;font-weight:700;margin-bottom:.4rem;color:var(--ink)">Nama Lengkap</label>
            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="Masukkan nama lengkap" class="ppdb-input">
            @error('full_name')<p style="color:var(--danger);font-size:.8rem;margin-top:.35rem">{{ $message }}</p>@enderror
          </div>

          <div class="field" style="margin-bottom:1.25rem">
            <label for="email" style="font-size:.85rem;font-weight:700;margin-bottom:.4rem;color:var(--ink)">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com" class="ppdb-input">
            @error('email')<p style="color:var(--danger);font-size:.8rem;margin-top:.35rem">{{ $message }}</p>@enderror
          </div>

          <div class="field" style="margin-bottom:1.25rem">
            <label for="password" style="font-size:.85rem;font-weight:700;margin-bottom:.4rem;color:var(--ink)">Password</label>
            <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter" class="ppdb-input">
            @error('password')<p style="color:var(--danger);font-size:.8rem;margin-top:.35rem">{{ $message }}</p>@enderror
          </div>

          <div class="field" style="margin-bottom:1.5rem">
            <label for="password_confirmation" style="font-size:.85rem;font-weight:700;margin-bottom:.4rem;color:var(--ink)">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password" class="ppdb-input">
          </div>

          <button type="submit" style="width:100%;padding:0.8rem;border-radius:12px;border:none;font-size:.95rem;font-weight:800;cursor:pointer;transition:opacity .2s;background:var(--primary);color:white" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
            Daftar Sekarang
          </button>

          <p style="text-align:center;font-size:.85rem;color:var(--muted);margin-top:1.25rem">
            Sudah punya akun? <a href="javascript:void(0)" onclick="switchTab('login')" style="font-weight:700;color:var(--primary-2)">Masuk di sini</a>
          </p>
        </form>
      </div>
    </div>

  </div>
</section>

<script>
function switchTab(tab) {
  const tabs = document.querySelectorAll('.auth-tab-segmented');
  const loginEl = document.getElementById('login-content');
  const registerEl = document.getElementById('register-content');
  tabs.forEach(t => t.classList.remove('active'));
  loginEl?.classList.remove('active');
  registerEl?.classList.remove('active');
  if (tab === 'login') {
    tabs[0].classList.add('active');
    loginEl?.classList.add('active');
  } else {
    tabs[1].classList.add('active');
    registerEl?.classList.add('active');
  }
}
@if($errors->has('full_name') || $errors->has('password_confirmation'))
  switchTab('register');
@endif

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
      title: 'Keluar dari PPDB?',
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
        document.getElementById('ppdbLogoutForm').submit();
      }
    });
  });
});
</script>
@endsection