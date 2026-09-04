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
    <input type="hidden" name="mode" value="bot_api">
    <label>Nama<input name="name" required placeholder="Bot Nilai"></label>
    <label>Username<input name="username" placeholder="InvestaNilaiBot"></label>
    <label>Token<input name="token" required type="password" autocomplete="new-password"></label>
    <button class="btn btn-primary" type="submit">Simpan Bot</button>
  </form>
</section>

<section class="portal-panel" style="margin-bottom:18px">
  <div class="portal-panel-header"><div><h2>MTProto Telegram</h2><p>Hubungkan akun Telegram sekolah dengan QR Code sebelum membuat bot otomatis.</p></div></div>
  <div style="display:grid;gap:16px;max-width:560px">
    <div style="padding:14px;border-radius:12px;background:var(--bg);border:1px solid var(--line)">
      <strong>Status koneksi</strong>
      <div id="mtprotoStatus" style="color:var(--muted);margin-top:5px">{{ $mtprotoSession?->status === 'connected' ? 'Terhubung sebagai @'.($mtprotoSession->username ?: $mtprotoSession->first_name) : 'Belum terhubung' }}</div>
    </div>
    <div id="mtprotoLoginForm" style="display:{{ $mtprotoSession?->status === 'connected' ? 'none' : 'grid' }};gap:12px">
      <p style="color:var(--muted);font-size:.82rem;margin:0">API ID dan API hash dibaca dari konfigurasi server. Admin cukup melakukan scan QR Telegram.</p>
      <button type="button" id="startMtprotoQr" class="btn btn-primary">Tampilkan QR Telegram</button>
      <div id="qrPanel" style="display:none;text-align:center;padding:18px;border:1px solid var(--line);border-radius:12px;background:#fff">
        <div id="qrImage" style="min-height:280px;display:grid;place-items:center"></div><div id="qrExpiry" style="color:var(--muted);font-size:.8rem;margin-top:8px"></div>
        <p style="font-size:.82rem;color:var(--muted);margin:10px 0 0">Buka Telegram di ponsel → Settings → Devices → Link Desktop Device, lalu scan QR ini.</p>
      </div>
    </div>
    @if($mtprotoSession?->status === 'connected')
      <form method="POST" action="{{ route('admin.telegram.mtproto.logout') }}">@csrf<button class="btn btn-outline" type="submit">Putuskan Koneksi Telegram</button></form>
    @endif
    <div id="mtprotoCreatePanel" style="display:{{ $mtprotoSession?->status === 'connected' ? 'grid' : 'none' }};gap:12px;padding-top:14px;border-top:1px solid var(--line)">
      <h3 style="margin:0">Buat Bot MTProto</h3>
      <p style="color:var(--muted);font-size:.82rem;margin:0">Bot dibuat otomatis oleh sistem setelah akun Telegram terhubung.</p>
      <form method="POST" action="{{ route('admin.telegram.mtproto.bots.store') }}" style="display:grid;gap:12px">
        @csrf
        <label>Nama Bot<input name="name" required maxlength="64" placeholder="Bot Akademik Sekolah"></label>
        <label>Username Bot<input name="username" required maxlength="32" pattern="[A-Za-z][A-Za-z0-9_]*bot" placeholder="AkademikSekolahBot"></label>
        <button id="createMtprotoBotButton" class="btn btn-primary" type="submit">Buat dan Simpan Bot MTProto</button>
      </form>
      @if($creation)
        <div id="creationProgress" data-status-url="{{ route('admin.telegram.mtproto.bots.status', $creation) }}" style="padding:12px;border-radius:10px;background:var(--bg);color:var(--muted)">
          Status pembuatan bot: <strong id="creationProgressText">{{ $creation->status === 'pending' ? 'Sedang diproses...' : $creation->status }}</strong>
        </div>
      @endif
    </div>
  </div>
</section>

<section class="portal-panel" style="margin-bottom:18px">
  <div class="portal-panel-header"><div><h2>Bot Terdaftar</h2><p>Token tidak pernah ditampilkan kembali setelah disimpan.</p></div></div>
  <div style="display:grid;gap:12px">
  @forelse($bots as $bot)
    <div style="padding:16px;border:1px solid var(--line);border-radius:12px;display:flex;gap:14px;align-items:center;justify-content:space-between">
      <div><strong>{{ $bot->name }}</strong><div style="color:var(--muted);font-size:.82rem">{{ $bot->mode === 'mtproto_bot' ? 'MTProto Bot' : 'Bot API' }} · {{ $bot->username ? '@'.ltrim($bot->username, '@') : 'Username belum diisi' }} · {{ $bot->connections_count }} koneksi</div></div>
      <div style="display:flex;gap:8px;align-items:center"><span class="{{ $bot->is_active ? 'status-pass' : '' }}">{{ $bot->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        <details><summary class="btn btn-outline" style="cursor:pointer">Edit</summary>
          <form method="POST" action="{{ route('admin.telegram.bots.update', $bot) }}" style="display:grid;gap:8px;min-width:260px;padding:12px;background:var(--bg);border-radius:10px;margin-top:8px">
            @csrf @method('PUT')
            <input name="name" value="{{ $bot->name }}" required placeholder="Nama bot">
            <select name="mode"><option value="bot_api" @selected($bot->mode === 'bot_api')>Bot API</option><option value="mtproto_bot" @selected($bot->mode === 'mtproto_bot')>MTProto Bot</option></select>
            <input name="username" value="{{ $bot->username }}" placeholder="Username">
            <input name="token" type="password" autocomplete="new-password" placeholder="Token baru (kosongkan jika tetap)">
            <input name="api_id" type="number" min="1" value="{{ $bot->api_id }}" placeholder="API ID">
            <input name="api_hash" type="password" autocomplete="new-password" placeholder="API hash baru (kosongkan jika tetap)">
            <label style="display:flex;align-items:center;gap:7px"><input type="checkbox" name="is_active" value="1" @checked($bot->is_active)> Aktif</label>
            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
          </form>
        </details>
        <form method="POST" action="{{ route('admin.telegram.bots.test', $bot) }}">@csrf<button class="btn btn-outline" type="submit">Test</button></form>
        <form method="POST" action="{{ route('admin.telegram.bots.destroy', $bot) }}" onsubmit="return confirm('Hapus bot ini?')">@csrf @method('DELETE')<button class="btn btn-outline" type="submit">Hapus</button></form>
      </div>
    </div>
  @empty <p style="color:var(--muted)">Belum ada bot tambahan. Bot lama dari .env tetap dapat digunakan.</p> @endforelse
  </div>
</section>

<section class="portal-panel">
  <div class="portal-panel-header"><div><h2>Template Bot</h2><p>Susun isi pesan dengan memilih card yang mudah dipahami.</p></div></div>
  @if($bots->isNotEmpty())
  <form method="POST" action="{{ route('admin.telegram.templates.store') }}" style="display:grid;gap:12px">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px"><label>Nama template<input name="name" required placeholder="Nilai Baru"></label><label>Jenis laporan<select name="report_type"><option value="grade">Publikasi Nilai</option><option value="exam_plan">Rencana Ujian</option><option value="attendance">Absensi</option></select></label></div>
    <div>
      <label for="templateBody">Isi pesan</label>
      <p style="color:var(--muted);font-size:.8rem;margin:6px 0 10px">Klik card untuk menambahkan bagian pesan. Teks teknis akan diproses otomatis oleh sistem.</p>
      <div id="templateCards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-bottom:12px"></div>
      <div id="templateEditor" contenteditable="true" style="min-height:170px;padding:12px;border:1px solid var(--line);border-radius:10px;background:var(--card);color:var(--ink);line-height:1.6" data-placeholder="Tulis pesan Telegram di sini..."></div>
      <textarea id="templateBody" name="body" hidden required></textarea>
      <div style="margin-top:10px"><span style="display:block;color:var(--muted);font-size:.78rem;margin-bottom:5px">Preview dengan data contoh</span><pre id="templatePreview" style="white-space:pre-wrap;margin:0;padding:10px;border-radius:9px;background:var(--bg);color:var(--muted);font:inherit">Tulis pesan untuk melihat preview.</pre></div>
    </div>
    <button class="btn btn-primary" type="submit" style="justify-self:start">Simpan Template</button>
  </form>
  @endif
  <div style="display:grid;gap:12px;margin-top:18px">
  @forelse($templates as $template)
    <div style="padding:16px;border:1px solid var(--line);border-radius:12"><div style="display:flex;justify-content:space-between;gap:12px"><div><strong>{{ $template->name }}</strong><div style="color:var(--muted);font-size:.82rem">{{ ['grade'=>'Publikasi Nilai','exam_plan'=>'Rencana Ujian','attendance'=>'Absensi'][$template->report_type] ?? $template->report_type }} · Bot dipilih Guru saat publikasi</div></div><div style="display:flex;gap:8px"><details><summary class="btn btn-outline" style="cursor:pointer">Edit</summary><form method="POST" action="{{ route('admin.telegram.templates.update', $template) }}" style="display:grid;gap:8px;min-width:260px;padding:12px;background:var(--bg);border-radius:10px;margin-top:8px">@csrf @method('PUT')<input name="name" value="{{ $template->name }}" required><select name="report_type"><option value="grade" @selected($template->report_type === 'grade')>Publikasi Nilai</option><option value="exam_plan" @selected($template->report_type === 'exam_plan')>Rencana Ujian</option><option value="attendance" @selected($template->report_type === 'attendance')>Absensi</option></select><textarea name="body" required rows="5">{{ $template->body }}</textarea><label style="display:flex;align-items:center;gap:7px"><input type="checkbox" name="is_active" value="1" @checked($template->is_active)> Aktif</label><button class="btn btn-primary" type="submit">Simpan Perubahan</button></form></details><form method="POST" action="{{ route('admin.telegram.templates.destroy', $template) }}" onsubmit="return confirm('Hapus template ini?')">@csrf @method('DELETE')<button class="btn btn-outline" type="submit">Hapus</button></form></div></div><pre style="white-space:pre-wrap;margin:12px 0 0;color:var(--muted);font:inherit">{{ $template->body }}</pre></div>
  @empty <p style="color:var(--muted);margin-top:18px">Belum ada template pesan.</p> @endforelse
  </div>
</section>
@endsection

@push('scripts')
<script>
const schema = @json($parameterSchemas);
const qrStartUrl = @json(route('admin.telegram.mtproto.qr'));
const qrStatusUrl = @json(route('admin.telegram.mtproto.qr.status'));
const csrf = @json(csrf_token());
const openPlaceholder = String.fromCharCode(123, 123);
const closePlaceholder = String.fromCharCode(125, 125);
const placeholder = key => openPlaceholder + key + closePlaceholder;
const contentCards = {
  grade: [
    ['greeting', '👋', 'Sapaan', 'Pembuka pesan yang sopan.', 'Assalamu’alaikum Bapak/Ibu,\\n\\n'],
    ['student', '👤', 'Identitas siswa', 'Nama siswa yang menerima laporan.', 'Siswa: ' + placeholder('nama_siswa') + '\\n'],
    ['grade', '🏆', 'Nilai dan status', 'Mata pelajaran, nilai, KKM, dan status.', 'Mata Pelajaran: ' + placeholder('mata_pelajaran') + '\\nPenilaian: ' + placeholder('penilaian') + '\\nNilai: ' + placeholder('nilai') + ' / ' + placeholder('nilai_maksimal') + '\\nKKM: ' + placeholder('kkm') + '\\nStatus: ' + placeholder('status') + '\\n'],
    ['teacher', '👨‍🏫', 'Guru dan tanggal', 'Guru yang memberi nilai dan tanggalnya.', 'Guru: ' + placeholder('guru') + '\\nTanggal: ' + placeholder('tanggal') + '\\n'],
    ['note', '💬', 'Catatan guru', 'Catatan tambahan dari guru.', 'Catatan: ' + placeholder('catatan') + '\\n'],
    ['link', '🔗', 'Link portal', 'Tautan untuk melihat detail di website.', 'Lihat detail: ' + placeholder('link_detail') + '\\n'],
    ['closing', '🏫', 'Penutup sekolah', 'Salam penutup dengan nama sekolah.', '\\nSalam,\\n' + placeholder('nama_sekolah')],
  ],
  exam_plan: [
    ['greeting', '👋', 'Sapaan', 'Pembuka pesan yang sopan.', 'Assalamu’alaikum Bapak/Ibu,\\n\\n'],
    ['student', '👤', 'Identitas siswa', 'Nama siswa yang menerima informasi.', 'Siswa: ' + placeholder('nama_siswa') + '\\n'],
    ['schedule', '🗓️', 'Jadwal ujian', 'Mata pelajaran, jenis penilaian, dan tanggal.', 'Mata Pelajaran: ' + placeholder('mata_pelajaran') + '\\nKegiatan: ' + placeholder('penilaian') + '\\nTanggal: ' + placeholder('tanggal') + '\\n'],
    ['teacher', '👨‍🏫', 'Guru', 'Nama guru terkait.', 'Guru: ' + placeholder('guru') + '\\n'],
    ['note', '💬', 'Catatan', 'Informasi tambahan untuk orang tua.', 'Catatan: ' + placeholder('catatan') + '\\n'],
    ['link', '🔗', 'Link portal', 'Tautan detail informasi.', 'Lihat detail: ' + placeholder('link_detail') + '\\n'],
    ['closing', '🏫', 'Penutup sekolah', 'Salam penutup.', '\\nSalam,\\n' + placeholder('nama_sekolah')],
  ],
  attendance: [
    ['greeting', '👋', 'Sapaan', 'Pembuka pesan yang sopan.', 'Assalamu’alaikum Bapak/Ibu,\\n\\n'],
    ['student', '👤', 'Identitas siswa', 'Nama siswa yang dilaporkan.', 'Siswa: ' + placeholder('nama_siswa') + '\\n'],
    ['attendance', '✅', 'Status kehadiran', 'Status kehadiran siswa.', 'Status Kehadiran: ' + placeholder('status') + '\\nTanggal: ' + placeholder('tanggal') + '\\n'],
    ['teacher', '👨‍🏫', 'Guru', 'Nama guru yang mencatat.', 'Guru: ' + placeholder('guru') + '\\n'],
    ['note', '💬', 'Catatan', 'Catatan kehadiran.', 'Catatan: ' + placeholder('catatan') + '\\n'],
    ['link', '🔗', 'Link portal', 'Tautan detail kehadiran.', 'Lihat detail: ' + placeholder('link_detail') + '\\n'],
    ['closing', '🏫', 'Penutup sekolah', 'Salam penutup.', '\\nSalam,\\n' + placeholder('nama_sekolah')],
  ],
};
let qrPollTimer;
let qrCountdownTimer;
let qrExpiresAt = 0;

function renderQr(data) {
  const panel = document.getElementById('qrPanel');
  const image = document.getElementById('qrImage');
  const expiry = document.getElementById('qrExpiry');
  if (data.svg) {
    panel.style.display = 'block';
    image.innerHTML = data.svg;
    image.querySelector('svg')?.setAttribute('style', 'width:min(280px, 100%);height:auto;display:block;margin:auto');
    qrExpiresAt = Date.now() + (Number(data.expires_in || 0) * 1000);
    updateQrCountdown();
  }
  if (data.logged_in) {
    stopQrPolling();
    document.getElementById('mtprotoStatus').textContent = 'Terhubung ke Telegram.';
    document.getElementById('mtprotoLoginForm').style.display = 'none';
    document.getElementById('mtprotoCreatePanel').style.display = 'grid';
    panel.style.display = 'none';
  }
  if (data.needs_2fa) {
    stopQrPolling();
    document.getElementById('mtprotoStatus').textContent = 'Akun membutuhkan 2FA; QR saja belum dapat menyelesaikan login.';
  }
}

function updateQrCountdown() {
  const expiry = document.getElementById('qrExpiry');
  const seconds = Math.max(0, Math.ceil((qrExpiresAt - Date.now()) / 1000));
  expiry.textContent = seconds > 0 ? 'QR diperbarui dalam ' + seconds + ' detik.' : 'QR kedaluwarsa, membuat QR baru...';
}

function stopQrPolling() {
  clearTimeout(qrPollTimer);
  clearInterval(qrCountdownTimer);
  qrPollTimer = null;
  qrCountdownTimer = null;
}

async function pollQrStatus() {
  try {
    const result = await fetch(qrStatusUrl, {headers: {'Accept': 'application/json'}});
    if (result.ok) renderQr(await result.json());
  } finally {
    if (qrPollTimer !== null) qrPollTimer = setTimeout(pollQrStatus, 2500);
  }
}

document.getElementById('startMtprotoQr')?.addEventListener('click', async function () {
  const response = await fetch(qrStartUrl, {method: 'POST', headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}});
  const data = await response.json();
  if (!response.ok) { alert(data.message || 'QR gagal dibuat.'); return; }
  stopQrPolling();
  renderQr(data);
  qrCountdownTimer = setInterval(updateQrCountdown, 1000);
  qrPollTimer = setTimeout(pollQrStatus, 1500);
});

document.querySelector('form[action*="/telegram/mtproto/bots"]')?.addEventListener('submit', function () {
  const button = document.getElementById('createMtprotoBotButton');
  if (!button) return;
  button.disabled = true;
  button.textContent = 'Sedang membuat bot...';
  button.style.opacity = '.7';
  button.style.cursor = 'wait';
});

const creationProgress = document.getElementById('creationProgress');
if (creationProgress) {
  const progressText = document.getElementById('creationProgressText');
  const checkCreation = async () => {
    const response = await fetch(creationProgress.dataset.statusUrl, {headers: {'Accept': 'application/json'}});
    if (!response.ok) return;
    const data = await response.json();
    if (data.status === 'completed') {
      progressText.textContent = 'Berhasil dibuat: @' + (data.bot?.username || '');
      return;
    }
    if (data.status === 'failed') {
      progressText.textContent = 'Gagal: ' + (data.error || 'Proses ditolak Telegram.');
      return;
    }
    progressText.textContent = 'Sedang diproses...';
    setTimeout(checkCreation, 1500);
  };
  if (['pending', 'processing'].includes(@json($creation?->status))) checkCreation();
}

function renderContentCards(type) {
  const cards = document.getElementById('templateCards');
  if (!cards) return;
  cards.innerHTML = '';
  const items = [
    ['greeting', '👋', 'Sapaan', 'Pembuka pesan yang sopan.', 'Assalamu’alaikum Bapak/Ibu,\\n\\n', false],
    ...(schema[type] || []).map(parameter => [parameter.key, '＋', parameter.label, 'Tambahkan '+parameter.label.toLowerCase()+'.', null, true]),
    ['closing', '🏫', 'Penutup sekolah', 'Salam penutup.', '\\nSalam,\\n' + placeholder('nama_sekolah'), false],
  ];
  items.forEach(([key, icon, title, description, content, isToken]) => {
    const card = document.createElement('button');
    card.type = 'button';
    card.style.cssText = 'text-align:left;padding:12px;border:1px solid var(--line);border-radius:11px;background:var(--card);color:var(--ink);cursor:pointer;transition:.15s';
    card.innerHTML = '<span style="font-size:1.3rem;display:block;margin-bottom:5px">' + icon + '</span><strong style="display:block;font-size:.84rem">' + title + '</strong><small style="display:block;color:var(--muted);margin-top:4px;line-height:1.35">' + description + '</small>';
    card.addEventListener('mouseenter', () => card.style.borderColor = 'var(--primary-2)');
    card.addEventListener('mouseleave', () => card.style.borderColor = 'var(--line)');
    card.addEventListener('click', () => isToken ? insertToken(key, title) : insertText(content));
    cards.appendChild(card);
  });
}

function insertAtCursor(node) {
  const editor = document.getElementById('templateEditor');
  editor.focus();
  const selection = window.getSelection();
  if (!selection || !selection.rangeCount || !editor.contains(selection.anchorNode)) {
    editor.appendChild(node);
    return;
  }
  const range = selection.getRangeAt(0);
  range.deleteContents();
  range.insertNode(node);
  range.setStartAfter(node);
  range.collapse(true);
  selection.removeAllRanges();
  selection.addRange(range);
}

function insertToken(key, label) {
  const token = document.createElement('span');
  token.className = 'template-token';
  token.contentEditable = 'false';
  token.dataset.token = key;
  token.textContent = label;
  token.style.cssText = 'display:inline-block;padding:2px 8px;margin:0 2px;border-radius:7px;background:#dbeafe;color:#1d4ed8;font-weight:700;cursor:default';
  insertAtCursor(token);
  insertAtCursor(document.createTextNode(' '));
  syncTemplateBody();
}

function insertText(content) {
  content = content.replaceAll('\\n', '\n');
  insertAtCursor(document.createTextNode(content));
  syncTemplateBody();
}

function serializeEditor() {
  const editor = document.getElementById('templateEditor');
  let output = '';
  const walk = node => {
    if (node.nodeType === Node.TEXT_NODE) output += node.nodeValue;
    else if (node.nodeType === Node.ELEMENT_NODE) {
      if (node.classList.contains('template-token')) output += placeholder(node.dataset.token);
      else if (node.tagName === 'BR') output += '\\n';
      else node.childNodes.forEach(walk);
    }
  };
  editor?.childNodes.forEach(walk);
  return output;
}

function syncTemplateBody() {
  document.getElementById('templateBody').value = serializeEditor();
  updateTemplatePreview();
}

document.querySelector('select[name="report_type"]')?.addEventListener('change', function () {
  renderContentCards(this.value);
  updateTemplatePreview();
});

renderContentCards(document.querySelector('select[name="report_type"]')?.value || 'grade');

function updateTemplatePreview() {
  const body = document.getElementById('templateBody');
  const type = document.querySelector('select[name="report_type"]')?.value;
  const preview = document.getElementById('templatePreview');
  if (!body || !preview) return;
  let output = body.value || 'Tulis pesan untuk melihat preview.';
  (schema[type] || []).forEach(parameter => {
    output = output.replaceAll(placeholder(parameter.key), parameter.example);
  });
  preview.textContent = output;
}

document.getElementById('templateEditor')?.addEventListener('input', syncTemplateBody);
document.querySelector('select[name="report_type"]')?.addEventListener('change', updateTemplatePreview);
updateTemplatePreview();
</script>
@endpush
