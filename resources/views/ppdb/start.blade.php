@extends('layouts.public')

@section('title', 'Daftar PPDB Online | SMK MADYA DEPOK')

@push('styles')
<style>
.auth-tabs {
  display: flex;
  gap: 0;
  margin-bottom: 2rem;
  border-bottom: 2px solid #e0e0e0;
  justify-content: center;
}
.auth-tab {
  flex: 0 0 auto;
  padding: 1rem 2.5rem;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  color: var(--muted);
  transition: all .2s;
  border-bottom: 3px solid transparent;
  margin-bottom: -2px;
}
.auth-tab:hover {
  color: var(--text);
  background: rgba(59, 130, 246, 0.05);
}
.auth-tab.active {
  color: var(--primary);
  border-bottom-color: var(--primary);
}
.tab-content {
  display: none;
  animation: fadeIn 0.3s ease-out;
}
.tab-content.active {
  display: block;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
@endpush

@section('content')
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb">Beranda / PPDB / Daftar</div>
    <h1>PPDB Online</h1>
    <p class="lead">Masuk ke akun Anda atau daftar baru untuk melanjutkan pendaftaran PPDB.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:560px">
    @if (session('error'))
    <div style="background:#fef2f2;color:#b91c1c;padding:1rem;border-radius:8px;margin-bottom:1.5rem;text-align:center">
      {{ session('error') }}
    </div>
    @endif

    @if (session('success'))
    <div style="background:#f0fdf4;color:#15803d;padding:1rem;border-radius:8px;margin-bottom:1.5rem;text-align:center">
      {{ session('success') }}
    </div>
    @endif

    <div class="auth-tabs">
      <button class="auth-tab active" onclick="switchTab('login')">Masuk</button>
      <button class="auth-tab" onclick="switchTab('register')">Daftar Baru</button>
    </div>

    <div id="login-content" class="tab-content active">
      <div style="text-align:center;margin-bottom:2rem">
        <span class="kicker">Sudah Punya Akun</span>
        <h2>Masuk ke Akun PPDB</h2>
        <p style="color:var(--muted);margin-top:.5rem">Lanjutkan proses pendaftaran Anda</p>
      </div>

      <form method="POST" action="{{ route('login') }}" style="background:#fff;padding:2rem;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)">
        @csrf
        <input type="hidden" name="role" value="applicant">

        <div style="margin-bottom:1.5rem">
          <label for="login_email" style="display:block;font-weight:600;margin-bottom:.5rem;color:#333">Email</label>
          <input 
            type="email" 
            id="login_email" 
            name="email" 
            value="{{ old('email') }}"
            required
            autofocus
            style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:1rem;transition:border-color .2s"
            onfocus="this.style.borderColor='#3b82f6'"
            onblur="this.style.borderColor='#e0e0e0'"
            placeholder="contoh@email.com">
          @error('email')
          <p style="color:#dc2626;font-size:.875rem;margin-top:.5rem">{{ $message }}</p>
          @enderror
        </div>

        <div style="margin-bottom:2rem">
          <label for="login_password" style="display:block;font-weight:600;margin-bottom:.5rem;color:#333">Password</label>
          <input 
            type="password" 
            id="login_password" 
            name="password" 
            required
            style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:1rem;transition:border-color .2s"
            onfocus="this.style.borderColor='#3b82f6'"
            onblur="this.style.borderColor='#e0e0e0'"
            placeholder="Masukkan password Anda">
          @error('password')
          <p style="color:#dc2626;font-size:.875rem;margin-top:.5rem">{{ $message }}</p>
          @enderror
        </div>

        <button 
          type="submit"
          style="width:100%;padding:14px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-size:1.1rem;font-weight:600;cursor:pointer;transition:background .2s"
          onmouseover="this.style.background='#2563eb'"
          onmouseout="this.style.background='#3b82f6'">
          Masuk Sekarang
        </button>

        <div style="margin-top:1.5rem;text-align:center;font-size:.9rem;color:var(--muted)">
          <p>Belum punya akun? <a href="javascript:void(0)" onclick="switchTab('register')" class="text-link">Daftar di sini</a></p>
        </div>
      </form>
    </div>

    <div id="register-content" class="tab-content">
      <div style="text-align:center;margin-bottom:2rem">
        <span class="kicker">Langkah 1 dari 3</span>
        <h2>Registrasi Akun PPDB</h2>
        <p style="color:var(--muted);margin-top:.5rem">Isi formulir di bawah untuk membuat akun pendaftaran</p>
      </div>

      <form method="POST" action="{{ route('ppdb.manual.register') }}" style="background:#fff;padding:2rem;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08)">
        @csrf

        <div style="margin-bottom:1.5rem">
          <label for="full_name" style="display:block;font-weight:600;margin-bottom:.5rem;color:#333">Nama Lengkap</label>
          <input 
            type="text" 
            id="full_name" 
            name="full_name" 
            value="{{ old('full_name') }}"
            required 
            style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:1rem;transition:border-color .2s"
            onfocus="this.style.borderColor='#3b82f6'"
            onblur="this.style.borderColor='#e0e0e0'"
            placeholder="Masukkan nama lengkap">
          @error('full_name')
          <p style="color:#dc2626;font-size:.875rem;margin-top:.5rem">{{ $message }}</p>
          @enderror
        </div>

        <div style="margin-bottom:1.5rem">
          <label for="email" style="display:block;font-weight:600;margin-bottom:.5rem;color:#333">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}"
            required
            style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:1rem;transition:border-color .2s"
            onfocus="this.style.borderColor='#3b82f6'"
            onblur="this.style.borderColor='#e0e0e0'"
            placeholder="contoh@email.com">
          @error('email')
          <p style="color:#dc2626;font-size:.875rem;margin-top:.5rem">{{ $message }}</p>
          @enderror
        </div>

        <div style="margin-bottom:1.5rem">
          <label for="password" style="display:block;font-weight:600;margin-bottom:.5rem;color:#333">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            required
            style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:1rem;transition:border-color .2s"
            onfocus="this.style.borderColor='#3b82f6'"
            onblur="this.style.borderColor='#e0e0e0'"
            placeholder="Minimal 8 karakter">
          @error('password')
          <p style="color:#dc2626;font-size:.875rem;margin-top:.5rem">{{ $message }}</p>
          @enderror
        </div>

        <div style="margin-bottom:2rem">
          <label for="password_confirmation" style="display:block;font-weight:600;margin-bottom:.5rem;color:#333">Konfirmasi Password</label>
          <input 
            type="password" 
            id="password_confirmation" 
            name="password_confirmation" 
            required
            style="width:100%;padding:12px 16px;border:2px solid #e0e0e0;border-radius:8px;font-size:1rem;transition:border-color .2s"
            onfocus="this.style.borderColor='#3b82f6'"
            onblur="this.style.borderColor='#e0e0e0'"
            placeholder="Ulangi password">
        </div>

        <button 
          type="submit"
          style="width:100%;padding:14px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-size:1.1rem;font-weight:600;cursor:pointer;transition:background .2s"
          onmouseover="this.style.background='#2563eb'"
          onmouseout="this.style.background='#3b82f6'">
          Daftar Sekarang
        </button>

        <div style="margin-top:1.5rem;text-align:center;font-size:.9rem;color:var(--muted)">
          <p>Sudah punya akun? <a href="javascript:void(0)" onclick="switchTab('login')" class="text-link">Masuk di sini</a></p>
        </div>
      </form>
    </div>

    <div class="grid grid-3" style="margin-top:2rem;gap:1rem">
      <div class="card" style="text-align:center;padding:1.5rem">
        <h4 style="margin-bottom:.3rem">1. Registrasi</h4>
        <p style="font-size:.85rem;color:var(--muted)">Buat akun pendaftaran</p>
      </div>
      <div class="card" style="text-align:center;padding:1.5rem">
        <h4 style="margin-bottom:.3rem">2. Isi Formulir</h4>
        <p style="font-size:.85rem;color:var(--muted)">Data siswa & orang tua</p>
      </div>
      <div class="card" style="text-align:center;padding:1.5rem">
        <h4 style="margin-bottom:.3rem">3. Konfirmasi</h4>
        <p style="font-size:.85rem;color:var(--muted)">Pantau status pendaftaran</p>
      </div>
    </div>
  </div>
</section>

<script>
function switchTab(tab) {
  const loginTab = document.querySelector('.auth-tab:first-child');
  const registerTab = document.querySelector('.auth-tab:last-child');
  const loginContent = document.getElementById('login-content');
  const registerContent = document.getElementById('register-content');
  
  if (tab === 'login') {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginContent.classList.add('active');
    registerContent.classList.remove('active');
    setTimeout(() => document.getElementById('login_email')?.focus(), 100);
  } else {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    registerContent.classList.add('active');
    loginContent.classList.remove('active');
    setTimeout(() => document.getElementById('full_name')?.focus(), 100);
  }
}

// Auto switch to register tab if there are validation errors for registration
@if($errors->has('full_name') || $errors->has('password_confirmation'))
  switchTab('register');
@endif
</script>
@endsection
