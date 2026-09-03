@extends('layouts.admin')

@section('title', 'Bot Telegram')

@section('content')
<div class="portal-heading"><div><span class="kicker">Integrasi</span><h1>Bot Telegram</h1><p>Kelola bot pengirim dan template pesan yang dapat dipilih Guru.</p></div></div>
@if(session('success'))<div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>@endif
@if(session('error'))<div style="padding:12px 16px;border-radius:12px;background:#fee2e2;color:#991b1b;font-weight:700;margin-bottom:16px">{{ session('error') }}</div>@endif

<section class="portal-panel" style="margin-bottom:18px">
  <div class="portal-panel-header"><div><h2>Daftarkan Bot</h2><p>Buat bot terlebih dahulu melalui @BotFather, lalu masukkan tokennya di sini.</p></div></div>
  <form method="POST" action="{{ route('admin.telegram.bots.store') }}" style="display:grid;grid-template-columns:1fr 1fr 1.5fr auto;gap:12px;align-items:end">
    @csrf
    <label>Nama<input name="name" required placeholder="Bot Nilai"></label>
    <label>Username<input name="username" placeholder="InvestaNilaiBot"></label>
    <label>Token<input name="token" required type="password" autocomplete="new-password"></label>
    <button class="btn btn-primary" type="submit">Simpan Bot</button>
  </form>
</section>

<section class="portal-panel" style="margin-bottom:18px">
  <div class="portal-panel-header"><div><h2>Bot Terdaftar</h2><p>Token tidak pernah ditampilkan kembali setelah disimpan.</p></div></div>
  <div style="display:grid;gap:12px">
  @forelse($bots as $bot)
    <div style="padding:16px;border:1px solid var(--line);border-radius:12px;display:flex;gap:14px;align-items:center;justify-content:space-between">
      <div><strong>{{ $bot->name }}</strong><div style="color:var(--muted);font-size:.82rem">{{ $bot->username ? '@'.ltrim($bot->username, '@') : 'Username belum diisi' }} · {{ $bot->connections_count }} koneksi</div></div>
      <div style="display:flex;gap:8px;align-items:center"><span class="{{ $bot->is_active ? 'status-pass' : '' }}">{{ $bot->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        <form method="POST" action="{{ route('admin.telegram.bots.test', $bot) }}">@csrf<button class="btn btn-outline" type="submit">Test</button></form>
        <form method="POST" action="{{ route('admin.telegram.bots.destroy', $bot) }}" onsubmit="return confirm('Hapus bot ini?')">@csrf @method('DELETE')<button class="btn btn-outline" type="submit">Hapus</button></form>
      </div>
    </div>
  @empty <p style="color:var(--muted)">Belum ada bot tambahan. Bot lama dari .env tetap dapat digunakan.</p> @endforelse
  </div>
</section>

<section class="portal-panel">
  <div class="portal-panel-header"><div><h2>Template Bot</h2><p>Gunakan placeholder: <code>&#123;&#123;nama_siswa&#125;&#125;</code>, <code>&#123;&#123;mata_pelajaran&#125;&#125;</code>, <code>&#123;&#123;nilai&#125;&#125;</code>, <code>&#123;&#123;link_detail&#125;&#125;</code>.</p></div></div>
  @if($bots->isNotEmpty())
  <form method="POST" action="{{ route('admin.telegram.templates.store') }}" style="display:grid;gap:12px">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px"><label>Nama template<input name="name" required placeholder="Nilai Baru"></label><label>Jenis laporan<select name="report_type"><option value="grade">Publikasi Nilai</option><option value="exam_plan">Rencana Ujian</option><option value="attendance">Absensi</option></select></label></div>
    <div>
      <label for="templateBody">Isi pesan</label>
      <div style="display:flex;gap:8px;align-items:center;margin:6px 0 8px;flex-wrap:wrap">
        <select id="placeholderPicker" style="min-height:36px;padding:0 10px;border:1px solid var(--line);border-radius:9px;background:var(--card);color:var(--ink)">
          <option value="">Pilih data yang ingin dimasukkan...</option>
          <option value="&#123;&#123;nama_siswa&#125;&#125;">Nama Siswa</option>
          <option value="&#123;&#123;mata_pelajaran&#125;&#125;">Mata Pelajaran</option>
          <option value="&#123;&#123;penilaian&#125;&#125;">Nama Penilaian</option>
          <option value="&#123;&#123;nilai&#125;&#125;">Nilai</option>
          <option value="&#123;&#123;nilai_maksimal&#125;&#125;">Nilai Maksimal</option>
          <option value="&#123;&#123;kkm&#125;&#125;">KKM</option>
          <option value="&#123;&#123;status&#125;&#125;">Status Nilai</option>
          <option value="&#123;&#123;guru&#125;&#125;">Nama Guru</option>
          <option value="&#123;&#123;tanggal&#125;&#125;">Tanggal</option>
          <option value="&#123;&#123;catatan&#125;&#125;">Catatan Guru</option>
          <option value="&#123;&#123;link_detail&#125;&#125;">Link Detail InvestaSchool</option>
          <option value="&#123;&#123;nama_sekolah&#125;&#125;">Nama Sekolah</option>
        </select>
        <button type="button" id="insertPlaceholder" class="btn btn-outline" style="min-height:36px">Masukkan</button>
        <span style="color:var(--muted);font-size:.78rem">Pilih data, lalu klik Masukkan.</span>
      </div>
      <textarea id="templateBody" name="body" required rows="7" placeholder="Tulis pesan Telegram di sini..."></textarea>
    </div>
    <button class="btn btn-primary" type="submit" style="justify-self:start">Simpan Template</button>
  </form>
  @endif
  <div style="display:grid;gap:12px;margin-top:18px">
  @forelse($templates as $template)
    <div style="padding:16px;border:1px solid var(--line);border-radius:12px"><div style="display:flex;justify-content:space-between;gap:12px"><div><strong>{{ $template->name }}</strong><div style="color:var(--muted);font-size:.82rem">{{ ['grade'=>'Publikasi Nilai','exam_plan'=>'Rencana Ujian','attendance'=>'Absensi'][$template->report_type] ?? $template->report_type }} · Bot dipilih Guru saat publikasi</div></div><form method="POST" action="{{ route('admin.telegram.templates.destroy', $template) }}" onsubmit="return confirm('Hapus template ini?')">@csrf @method('DELETE')<button class="btn btn-outline" type="submit">Hapus</button></form></div><pre style="white-space:pre-wrap;margin:12px 0 0;color:var(--muted);font:inherit">{{ $template->body }}</pre></div>
  @empty <p style="color:var(--muted);margin-top:18px">Belum ada template pesan.</p> @endforelse
  </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('insertPlaceholder')?.addEventListener('click', function () {
  const picker = document.getElementById('placeholderPicker');
  const body = document.getElementById('templateBody');
  if (!picker.value) return;
  const start = body.selectionStart ?? body.value.length;
  const end = body.selectionEnd ?? start;
  body.value = body.value.slice(0, start) + picker.value + body.value.slice(end);
  body.focus();
  body.selectionStart = body.selectionEnd = start + picker.value.length;
  picker.value = '';
});
</script>
@endpush
