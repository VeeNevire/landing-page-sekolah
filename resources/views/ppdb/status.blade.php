@extends('layouts.public')

@section('title', 'Status Pendaftaran - PPDB | InvestaSchool')

@section('content')
<form id="ppdbLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none">
  @csrf
  <input type="hidden" name="redirect_to" value="/">
</form>

<section style="background:var(--bg);padding:1.5rem 0 4rem">
  <div class="container" style="max-width:720px">

    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem">
      <div>
        <div style="font-size:.85rem;color:var(--muted);margin-bottom:.25rem">Beranda / PPDB / Status</div>
        <h1 style="font-size:clamp(1.8rem,4vw,2.5rem);font-family:Calistoga,Georgia,serif;font-weight:400;margin:0;color:var(--ink)">Status Pendaftaran</h1>
        <p style="font-size:.9rem;color:var(--muted);margin:.25rem 0 0">{{ $applicant->full_name }}</p>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:0.5rem 1rem;border-radius:10px;border:1px solid var(--line);background:var(--card);color:var(--ink);font-size:.82rem;font-weight:700;cursor:pointer;transition:0.2s;font-family:inherit" onmouseover="this.style.borderColor='var(--primary-2)'" onmouseout="this.style.borderColor='var(--line)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Keluar
        </button>
      </form>
    </div>

    <div class="ppdb-card" style="padding:2rem">

    @php
    $steps = [
    ['key' => 'draft', 'label' => 'Draft'],
    ['key' => 'submitted', 'label' => 'Terkirim'],
    ['key' => 'verified', 'label' => 'Terverifikasi'],
    ['key' => 'paid', 'label' => 'Lunas'],
    ];
    $statusIndex = array_search($applicant->status, array_column($steps, 'key'));
    if ($statusIndex === false) $statusIndex = 0;
    @endphp

    <div style="display:flex;align-items:center;gap:0;margin-bottom:2.5rem">
      @foreach($steps as $index => $step)
      <div style="display:flex;flex-direction:column;align-items:center;gap:8px;flex:0 0 auto;text-align:center">
        <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;
            {{ $index < $statusIndex ? 'background:var(--success);color:white' : '' }}
            {{ $index === $statusIndex ? 'background:var(--primary);color:white' : '' }}
            {{ $index > $statusIndex ? 'background:var(--line);color:var(--muted)' : '' }}
          ">
          @if($index < $statusIndex)
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12" /></svg>
            @else
            {{ $index + 1 }}
            @endif
        </div>
        <span style="font-size:.72rem;font-weight:700;white-space:nowrap;{{ $index <= $statusIndex ? 'color:var(--ink)' : 'color:var(--muted)' }}">{{ $step['label'] }}</span>
      </div>
      @if($index < count($steps) - 1)
        <div style="flex:1;height:2px;margin:0 8px;margin-bottom:24px;max-width:80px;{{ $index < $statusIndex ? 'background:var(--success)' : 'background:var(--line)' }}">
    </div>
    @endif
    @endforeach
  </div>

  <div style="background:var(--card);border:1px solid var(--line);border-radius:var(--radius);padding:1.5rem;margin-bottom:1.5rem">
    <h2 style="font-size:1.15rem;font-weight:400;font-family:Calistoga,Georgia,serif;margin:0 0 .5rem;color:var(--ink)">
      Status Saat Ini:
      <span style="
          {{ $applicant->status === 'draft' ? 'color:var(--muted)' : '' }}
          {{ $applicant->status === 'submitted' ? 'color:var(--primary-2)' : '' }}
          {{ $applicant->status === 'verified' ? 'color:var(--primary)' : '' }}
          {{ $applicant->status === 'paid' ? 'color:var(--success)' : '' }}
          {{ $applicant->status === 'rejected' ? 'color:var(--danger)' : '' }}
        ">{{ ucfirst($applicant->status) }}</span>
    </h2>
    <p style="font-size:.9rem;color:var(--muted);margin:0;line-height:1.7">
      @if($applicant->status === 'draft')
      Pendaftaran Anda masih dalam tahap draft. Silakan lengkapi formulir pendaftaran.
      @elseif($applicant->status === 'submitted')
      Data Anda sedang ditinjau oleh tim admin sekolah. Harap tunggu verifikasi.
      @elseif($applicant->status === 'verified')
      Data Anda telah diverifikasi. Silakan lakukan pembayaran untuk melanjutkan pendaftaran.
      @elseif($applicant->status === 'rejected')
      Mohon maaf, pendaftaran Anda ditolak.
      @if($applicant->admin_note)
      <br><strong>Catatan:</strong> {{ $applicant->admin_note }}
      @endif
      @elseif($applicant->status === 'paid')
      Pembayaran berhasil! Anda telah resmi terdaftar sebagai siswa.
      @endif
    </p>
  </div>

  @if($applicant->status === 'verified')
  <div style="text-align:center;margin-top:2rem">
    <a href="{{ route('ppdb.payment') }}" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.8rem 2rem;border-radius:12px;border:none;font-size:.95rem;font-weight:800;cursor:pointer;background:var(--success);color:white;text-decoration:none;transition:opacity .2s" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
      Lanjut ke Pembayaran →
    </a>
  </div>
  @endif

  @if($applicant->status === 'paid')
  <div style="text-align:center;margin-top:2rem">
    <form action="{{ route('logout') }}" method="POST" style="display:inline">
      @csrf
      <input type="hidden" name="redirect_to" value="{{ route('beranda') }}">
      <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.8rem 2rem;border-radius:12px;border:none;font-size:.95rem;font-weight:800;cursor:pointer;background:var(--primary);color:white;transition:opacity .2s;font-family:inherit" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
        Kembali ke Beranda
      </button>
    </form>
  </div>
  @endif

  </div>
  </div>
</section>
@endsection

@push('scripts')
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
        const form = document.getElementById('ppdbLogoutForm');
        const input = form.querySelector('input[name="redirect_to"]');
        if (input) input.value = href;
        form.submit();
      }
    });
  });
});
</script>
@endpush


