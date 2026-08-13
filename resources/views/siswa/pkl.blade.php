@extends('layouts.siswa')
@section('title', 'PKL / Aktivitas')
@section('content')
<div style="margin-bottom:16px">
  <h2 style="font-size:1.1rem;font-weight:700;color:var(--s-ink);margin:0">PKL / Aktivitas</h2>
  <p style="font-size:.82rem;color:var(--s-muted);margin:2px 0 0">Catat kegiatan Praktik Kerja Lapangan dan pantau persetujuan pembimbing.</p>
</div>

@if (!$placements->isEmpty())
{{-- ===== Ringkasan ===== --}}
<div class="bento bento-4" style="margin-bottom:16px">
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Total Jurnal</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#007AFF,#0A84FF);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $stats['total'] }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#007AFF,#0A84FF);box-shadow:0 4px 12px rgba(0,122,255,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
    </div>
  </div>
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Disetujui</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#34C759,#30D158);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $stats['approved'] }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#34C759,#30D158);box-shadow:0 4px 12px rgba(52,199,89,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
    </div>
  </div>
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Menunggu</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF9F0A,#FFD60A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $stats['pending'] }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF9F0A,#FFD60A);box-shadow:0 4px 12px rgba(255,159,10,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
    </div>
  </div>
  <div class="b-card-stat">
    <div class="b-flex-between">
      <div>
        <div class="b-stat-label">Ditolak</div>
        <div class="b-stat-value" style="background:linear-gradient(135deg,#FF3B30,#FF453A);-webkit-background-clip:text;-webkit-text-fill-color:transparent">{{ $stats['rejected'] }}</div>
      </div>
      <div class="b-stat-icon" style="background:linear-gradient(135deg,#FF3B30,#FF453A);box-shadow:0 4px 12px rgba(255,59,48,0.2)">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </div>
    </div>
  </div>
</div>

{{-- ===== Penempatan aktif ===== --}}
@if ($activePlacement)
<div class="b-card" style="padding:18px;margin-bottom:16px;background:linear-gradient(135deg,color-mix(in srgb,var(--s-primary) 7%,var(--s-card)),var(--s-card))">
  <div style="display:flex;align-items:flex-start;gap:14px;flex-wrap:wrap">
    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(145deg,var(--s-primary-dark),var(--s-primary));display:grid;place-items:center;flex-shrink:0">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
    </div>
    <div style="flex:1;min-width:220px">
      <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--s-muted);margin-bottom:2px">Penempatan PKL Aktif</div>
      <h3 style="font-size:1rem;font-weight:800;color:var(--s-ink);margin:0">{{ $activePlacement->company?->nama ?? '-' }}</h3>
      @if ($activePlacement->company?->bidang || $activePlacement->company?->kota)
      <p style="font-size:.78rem;color:var(--s-muted);margin:2px 0 0">
        {{ $activePlacement->company->bidang }}@if($activePlacement->company->bidang && $activePlacement->company->kota) &bull; @endif{{ $activePlacement->company->kota }}
      </p>
      @endif
    </div>
    <div style="display:grid;gap:6px;font-size:.78rem">
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span style="color:var(--s-ink)"><strong>{{ $activePlacement->start_date->format('d M Y') }} — {{ $activePlacement->end_date->format('d M Y') }}</strong></span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span style="color:var(--s-ink)"><strong>{{ $activePlacement->guruPembimbing?->full_name ?? $activePlacement->guruPembimbing?->name ?? 'Belum ditentukan' }}</strong> <span style="color:var(--s-muted)">— Pembimbing</span></span>
      </div>
    </div>
    <span style="padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:700;background:color-mix(in srgb,#34C759 12%,transparent);color:#34C759;align-self:center">{{ $activePlacement->status_label }}</span>
  </div>
</div>

<div class="bento bento-2">
  {{-- ===== Form Jurnal ===== --}}
  <div class="b-card" style="padding:18px">
    <h3 class="b-section-title" style="margin-bottom:14px">Catat Aktivitas Harian</h3>
    <form method="POST" action="{{ route('siswa.pkl.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="placement_id" value="{{ $activePlacement->id }}">

      <div style="margin-bottom:12px">
        <label style="font-size:.78rem;font-weight:700;display:block;margin-bottom:6px;color:var(--s-ink)">Tanggal <span style="color:#ef4444">*</span></label>
        <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
          min="{{ $activePlacement->start_date->format('Y-m-d') }}" max="{{ now()->format('Y-m-d') }}"
          style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--s-line);background:var(--s-card);color:var(--s-ink);font-size:.88rem;font-family:inherit">
        @error('tanggal')<small style="color:#ef4444;display:block;margin-top:4px">{{ $message }}</small>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="font-size:.78rem;font-weight:700;display:block;margin-bottom:6px;color:var(--s-ink)">Kegiatan yang Dilakukan <span style="color:#ef4444">*</span></label>
        <textarea name="aktivitas" rows="4" required placeholder="Contoh: Membantu menyusun laporan stok barang menggunakan spreadsheet di bawah bimbingan pembimbing lapangan..."
          style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--s-line);background:var(--s-card);color:var(--s-ink);font-size:.88rem;font-family:inherit;resize:vertical">{{ old('aktivitas') }}</textarea>
        @error('aktivitas')<small style="color:#ef4444;display:block;margin-top:4px">{{ $message }}</small>@enderror
      </div>

      <div style="margin-bottom:12px">
        <label style="font-size:.78rem;font-weight:700;display:block;margin-bottom:6px;color:var(--s-ink)">Keterangan / Hasil</label>
        <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Berhasil, selesai dibantu oleh tim" maxlength="500"
          style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--s-line);background:var(--s-card);color:var(--s-ink);font-size:.88rem;font-family:inherit">
      </div>

      <div style="margin-bottom:16px">
        <label style="font-size:.78rem;font-weight:700;display:block;margin-bottom:6px;color:var(--s-ink)">Foto Kegiatan (opsional)</label>
        <label for="pklFoto" style="display:flex;align-items:center;gap:12px;padding:14px 16px;border:2px dashed var(--s-line);border-radius:12px;background:var(--s-card);cursor:pointer;transition:border-color .15s ease,background .15s ease">
          <input type="file" id="pklFoto" name="foto" accept=".jpg,.jpeg,.png" style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden">
          <span style="width:40px;height:40px;border-radius:10px;flex-shrink:0;display:grid;place-items:center;background:color-mix(in srgb,var(--s-primary) 12%,transparent);color:var(--s-primary)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </span>
          <span id="pklFotoText" style="line-height:1.3">
            <strong style="display:block;font-size:.86rem;color:var(--s-ink)">Pilih file</strong>
            <small style="display:block;font-size:.74rem;color:var(--s-muted);margin-top:2px">JPG atau PNG &bull; maks 5 MB</small>
          </span>
        </label>
        @error('foto')<small style="color:#ef4444;display:block;margin-top:4px">{{ $message }}</small>@enderror
      </div>

      <button type="submit" style="width:100%;padding:13px;border:none;border-radius:10px;background:linear-gradient(135deg,var(--s-primary-dark),var(--s-primary));color:#fff;font-size:.9rem;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Simpan Aktivitas
      </button>
    </form>
  </div>

  {{-- ===== Riwayat Jurnal ===== --}}
  <div class="b-card" style="padding:0">
    <div style="padding:16px 18px 12px;border-bottom:1px solid var(--s-line)">
      <h3 class="b-section-title">Riwayat Jurnal</h3>
    </div>
    @if ($activities->count() > 0)
    <div style="padding:8px 14px">
      @php
        $statusColors = ['pending' => ['#FF9F0A', 'Menunggu'], 'approved' => ['#34C759', 'Disetujui'], 'rejected' => ['#FF3B30', 'Ditolak']];
      @endphp
      @foreach ($activities as $a)
      <div style="padding:12px 8px;border-bottom:1px solid color-mix(in srgb,var(--s-line) 40%,transparent);display:flex;gap:12px">
        <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
          <span style="width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:.7rem;font-weight:800;color:#fff;background:{{ $statusColors[$a->status][0] }}">{{ $a->tanggal->format('d') }}</span>
          <span style="font-size:.62rem;font-weight:700;color:var(--s-muted);margin-top:3px">{{ $a->tanggal->translatedFormat('M') }}</span>
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:4px">
            <span style="font-size:.7rem;font-weight:700;color:{{ $statusColors[$a->status][0] }};background:{{ $statusColors[$a->status][0] }}14;padding:2px 9px;border-radius:20px">{{ $statusColors[$a->status][1] }}</span>
            @if ($a->status === 'pending')
            <form method="POST" action="{{ route('siswa.pkl.destroy', $a->id) }}" onsubmit="return confirm('Hapus jurnal ini?');">
              @csrf
              @method('DELETE')
              <button type="submit" title="Hapus" style="border:none;background:transparent;cursor:pointer;color:#FF3B30;padding:4px;display:inline-flex">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
              </button>
            </form>
            @endif
          </div>
          <p style="font-size:.85rem;color:var(--s-ink);margin:0 0 4px;line-height:1.5">{{ $a->aktivitas }}</p>
          @if ($a->keterangan)
          <p style="font-size:.74rem;color:var(--s-muted);margin:0 0 4px">Hasil: {{ $a->keterangan }}</p>
          @endif
          @if ($a->foto_path)
          <a href="{{ Storage::url($a->foto_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:5px;font-size:.72rem;font-weight:700;color:var(--s-primary);text-decoration:none;margin-top:2px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ $a->foto_name ?? 'Lihat foto' }}
          </a>
          @endif
          @if ($a->status !== 'pending' && $a->catatan_pembimbing)
          <div style="margin-top:8px;padding:10px 12px;border-radius:10px;background:color-mix(in srgb,var(--s-bg) 60%,var(--s-card));border:1px solid var(--s-line);font-size:.78rem;color:var(--s-ink)">
            <strong style="font-size:.7rem;color:{{ $statusColors[$a->status][0] }};text-transform:uppercase;letter-spacing:.04em">Catatan pembimbing</strong>
            <p style="margin:3px 0 0">{{ $a->catatan_pembimbing }}</p>
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div style="padding:28px;text-align:center;color:var(--s-muted);font-size:.85rem">Belum ada aktivitas PKL dicatat.</div>
    @endif
  </div>
</div>
@endif
@endif

@if ($placements->isEmpty())
<div class="b-card" style="text-align:center;padding:48px">
  <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
  </div>
  <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">Belum ada penempatan PKL</h3>
  <p style="font-size:.82rem;color:var(--s-muted);margin:0">Saat admin menempatkan Anda di sebuah perusahaan, Anda bisa mulai mencatat aktivitas di sini.</p>
</div>
@endif

@if (!$placements->isEmpty() && !$activePlacement)
<div class="b-card" style="text-align:center;padding:48px">
  <div style="width:48px;height:48px;border-radius:14px;background:var(--s-bg);display:grid;place-items:center;margin:0 auto 14px">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--s-muted)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
  </div>
  <h3 style="font-size:.9rem;font-weight:600;color:var(--s-ink);margin:0 0 4px">Tidak ada penempatan aktif</h3>
  <p style="font-size:.82rem;color:var(--s-muted);margin:0">Penempatan PKL Anda saat ini tidak berstatus aktif, sehingga jurnal tidak dapat ditambahkan.</p>
</div>
@endif
@endsection

@push('scripts')
<script>
  document.getElementById('pklFoto')?.addEventListener('change', function() {
    const text = document.getElementById('pklFotoText');
    if (this.files && this.files[0]) {
      text.innerHTML = '<strong style="display:block;font-size:.86rem;color:var(--s-ink)">' + this.files[0].name + '</strong><small style="display:block;font-size:.74rem;color:var(--s-muted);margin-top:2px">' + Math.round(this.files[0].size / 1024) + ' KB</small>';
    } else {
      text.innerHTML = '<strong style="display:block;font-size:.86rem;color:var(--s-ink)">Pilih file</strong><small style="display:block;font-size:.74rem;color:var(--s-muted);margin-top:2px">JPG atau PNG &bull; maks 5 MB</small>';
    }
  });
</script>
@endpush
