@extends('layouts.admin')
@section('title', 'Absensi RFID')
@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Kehadiran siswa</span>
    <h1>Absensi RFID</h1>
    <p>Registrasi kartu dan pantau kehadiran {{ today()->format('d/m/Y') }} · WIB.</p>
  </div>
</div>

<div class="tabs">
  <a class="tab-btn {{ $tab === 'today' ? 'active' : '' }}" href="{{ route('admin.rfid.index') }}">Absensi Hari Ini</a>
  <a class="tab-btn {{ $tab === 'registration' ? 'active' : '' }}" href="{{ route('admin.rfid.index', ['tab'=>'registration']) }}">Registrasi Kartu</a>
</div>

<form method="GET" class="admin-toolbar">
  <input type="hidden" name="tab" value="{{ $tab }}">
  <div class="field"><label for="rfid-search">Cari siswa</label><input id="rfid-search" name="search" value="{{ request('search') }}" placeholder="Nama, NIS atau NISN"></div>
  <div class="field"><label for="rfid-class">Kelas</label><select id="rfid-class" name="class"><option value="">Semua kelas</option>@foreach($classes as $class)<option @selected(request('class') === $class)>{{ $class }}</option>@endforeach</select></div>
  <button class="btn btn-primary">Terapkan</button>
  <a class="btn btn-outline" href="{{ route('admin.rfid.index', ['tab'=>$tab]) }}">Reset</a>
</form>

@if($tab === 'today')
<div class="rfid-refresh-bar">
  <span id="rfid-refresh-status">Diperbarui pukul {{ now()->format('H:i:s') }} WIB</span>
</div>
<div id="rfid-live-region">
  @include('admin.partials.rfid-today-table', ['rows' => $rows, 'summary' => $summary, 'lastScan' => $lastScan])
</div>
@else
<p class="rfid-hint">Klik <strong>Mulai Scan</strong>, lalu tempelkan kartu pada reader. Konfirmasi UID untuk menyimpan. Sesi berakhir setelah dua menit; selama registrasi, pembaca tidak mencatat absensi.</p>
<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead><tr><th>Siswa</th><th>NIS</th><th>Kelas</th><th>UID Kartu</th><th>Aksi</th></tr></thead>
      <tbody>
      @forelse($rows as $student)
        <tr id="reg-row-{{ $student->id }}" data-id="{{ $student->id }}">
          <td>
            <div class="rfid-student-cell">
              <span class="rfid-avatar">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
              <strong>{{ $student->full_name }}</strong>
            </div>
          </td>
          <td>{{ $student->nis ?: $student->nisn }}</td>
          <td><span class="rfid-class-badge">{{ $student->class_name }}</span></td>
          <td class="uid-cell"><code>{{ $student->rfid_uid ?: 'Belum terdaftar' }}</code></td>
          <td class="action-cell">
            <button type="button" class="btn btn-primary rfid-btn-sm start-card"
              data-id="{{ $student->id }}" data-name="{{ $student->full_name }}"
              data-nis="{{ $student->nis ?: $student->nisn }}" data-class="{{ $student->class_name }}"
              data-uid="{{ $student->rfid_uid }}">{{ $student->rfid_uid ? 'Ganti Kartu' : 'Mulai Scan' }}</button>
            <button type="button" class="btn btn-outline rfid-btn-sm unlink-card"
              data-url="{{ route('admin.rfid.unlink', $student) }}" data-uid="{{ $student->rfid_uid }}"
              @if(!$student->rfid_uid) hidden @endif>Lepaskan</button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" style="padding:0">
            <div class="empty-state">
              <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              </div>
              <h3>Tidak Ada Siswa</h3>
              <p>Tidak ada siswa aktif yang sesuai dengan filter ini.</p>
            </div>
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $rows->links('vendor.pagination.admin') }}</div>
</section>

{{-- Registrasi kartu: modal --}}
<div class="admin-modal-overlay" id="cardModal">
  <div class="admin-modal-box rfid-modal-box" role="dialog" aria-modal="true" aria-labelledby="cardModalTitle" tabindex="-1" style="max-width:440px">
    <div class="admin-modal-header">
      <h2 id="cardModalTitle">Registrasi Kartu</h2>
      <button type="button" class="admin-modal-close" id="cardModalClose" aria-label="Tutup">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="admin-modal-body">
      <div class="rfid-modal-student">
        <span class="rfid-avatar rfid-avatar-lg" id="cardModalAvatar"></span>
        <div><strong id="cardModalName"></strong><span id="cardModalMeta"></span></div>
      </div>
      <div class="rfid-stage">
        <div class="rfid-stage-icon" id="cardStageIcon" aria-hidden="true"></div>
        <p class="rfid-stage-text" id="cardStageText" role="status" aria-live="polite"></p>
        <p class="rfid-stage-sub" id="cardStageSub"></p>
      </div>
    </div>
    <div class="admin-modal-footer">
      <button type="button" class="btn btn-outline" id="cardCancelBtn">Batalkan</button>
      <button type="button" class="btn btn-outline" id="cardRetryBtn" hidden>Coba Lagi</button>
      <button type="button" class="btn btn-primary" id="cardSaveBtn" hidden disabled>Simpan Kartu</button>
    </div>
  </div>
</div>
@endif
@endsection

@push('styles')
<style>
  .rfid-hint{color:var(--muted);font-size:.9rem;margin:-4px 0 18px}
  .rfid-refresh-bar{display:flex;justify-content:flex-end;margin:-6px 0 12px}
  #rfid-refresh-status{font-size:.78rem;color:var(--muted);font-weight:600}
  #rfid-refresh-status.rfid-refresh-error{color:var(--danger)}

  .rfid-summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin:0 0 18px}
  .rfid-stat-card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:18px 20px}
  .rfid-stat-label{display:block;color:var(--muted);font-size:.82rem;font-weight:700}
  .rfid-stat-value{display:block;font-size:1.9rem;font-weight:900;margin-top:6px}

  .rfid-lastscan{display:flex;gap:12px;align-items:flex-start;background:var(--card);border:1px solid var(--line);border-radius:14px;padding:14px 18px;margin:0 0 18px}
  .rfid-lastscan-icon{width:34px;height:34px;border-radius:10px;flex-shrink:0;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2)}
  .rfid-lastscan strong{display:block;font-size:.86rem}
  .rfid-lastscan p{margin:2px 0 0;font-size:.85rem;color:var(--muted)}
  .rfid-lastscan-time{color:var(--muted)}

  .rfid-student-cell{display:flex;align-items:center;gap:10px}
  .rfid-avatar{width:36px;height:36px;border-radius:10px;flex-shrink:0;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.82rem}
  .rfid-avatar-lg{width:44px;height:44px;font-size:1rem;border-radius:12px}
  .rfid-class-badge{padding:4px 10px;border-radius:8px;font-weight:700;font-size:.8rem;background:color-mix(in srgb,var(--primary-2) 10%,var(--card));color:var(--primary-2)}
  .rfid-status-badge{padding:4px 10px;border-radius:8px;font-weight:700;font-size:.78rem;display:inline-block}
  .action-cell{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
  .rfid-btn-sm{min-height:34px;padding:0 14px;font-size:.82rem}

  .rfid-modal-student{display:flex;align-items:center;gap:12px;margin-bottom:18px}
  .rfid-modal-student strong{display:block;font-size:.98rem}
  .rfid-modal-student span:last-child{font-size:.82rem;color:var(--muted)}
  .rfid-stage{text-align:center;padding:14px 0 4px}
  .rfid-stage-icon{margin:0 auto 16px;min-height:64px;display:grid;place-items:center}
  .rfid-stage-text{font-weight:800;font-size:1rem;margin:0}
  .rfid-stage-sub{margin:8px 0 0;font-size:.85rem;color:var(--muted)}
  .rfid-countdown{font-variant-numeric:tabular-nums;font-weight:800;color:var(--ink)}

  .rfid-spinner{width:44px;height:44px;border-radius:50%;border:3px solid var(--line);border-top-color:var(--primary-2);animation:rfidSpin .8s linear infinite}
  @keyframes rfidSpin{to{transform:rotate(360deg)}}

  .rfid-wave{position:relative;width:88px;height:88px;display:grid;place-items:center}
  .rfid-wave-ring{position:absolute;inset:0;border-radius:18px;border:2px solid var(--primary-2);opacity:0;animation:rfidWave 1.8s ease-out infinite}
  .rfid-wave-ring:nth-child(2){animation-delay:.6s}
  .rfid-wave-ring:nth-child(3){animation-delay:1.2s}
  @keyframes rfidWave{0%{opacity:.55;transform:scale(.72)}100%{opacity:0;transform:scale(1.35)}}
  .rfid-wave-icon{position:relative;z-index:1;width:46px;height:46px;border-radius:12px;background:color-mix(in srgb,var(--primary-2) 14%,var(--card));display:grid;place-items:center;color:var(--primary-2)}

  .rfid-check{width:64px;height:64px;border-radius:50%;background:color-mix(in srgb,var(--success) 16%,var(--card));color:var(--success);display:grid;place-items:center;animation:rfidPop .3s ease}
  @keyframes rfidPop{from{transform:scale(.6);opacity:0}to{transform:scale(1);opacity:1}}

  .rfid-warn{width:56px;height:56px;border-radius:50%;background:color-mix(in srgb,#d97706 16%,var(--card));color:#d97706;display:grid;place-items:center}
  .rfid-warn svg{width:28px;height:28px}

  @media (prefers-reduced-motion: reduce){
    .rfid-spinner,.rfid-wave-ring,.rfid-check{animation:none!important}
    .rfid-wave-ring{opacity:.35}
  }

  @media (max-width:640px){
    .action-cell{flex-direction:column;align-items:stretch}
    .rfid-btn-sm{width:100%}
  }
</style>
@endpush

@push('scripts')
@if($tab === 'today')
<script>
(() => {
  const dataUrl = @json(route('admin.rfid.data'));
  const region = document.getElementById('rfid-live-region');
  const statusEl = document.getElementById('rfid-refresh-status');
  let inflight = false, seq = 0;

  function currentQuery() {
    const params = new URLSearchParams(window.location.search);
    return params.toString();
  }

  async function refresh() {
    if (inflight || document.hidden) return;
    inflight = true;
    const mySeq = ++seq;
    try {
      const response = await fetch(dataUrl + (currentQuery() ? '?' + currentQuery() : ''), { headers: { 'Accept': 'application/json' } });
      if (!response.ok) throw new Error('HTTP ' + response.status);
      const json = await response.json();
      if (mySeq !== seq) return;
      region.innerHTML = json.html;
      statusEl.textContent = 'Diperbarui pukul ' + json.server_time + ' WIB';
      statusEl.classList.remove('rfid-refresh-error');
    } catch (error) {
      if (mySeq !== seq) return;
      statusEl.textContent = 'Gagal memperbarui. Data terakhir tetap ditampilkan.';
      statusEl.classList.add('rfid-refresh-error');
    } finally {
      inflight = false;
    }
  }

  setInterval(refresh, 5000);
})();
</script>
@else
<script>
(() => {
  const urls = @json(['state'=>route('admin.rfid.state'),'start'=>route('admin.rfid.start'),'finish'=>route('admin.rfid.finish')]);
  const csrf = @json(csrf_token());

  async function api(url, data, method = 'POST') {
    const response = await fetch(url, {
      method,
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      ...(method === 'GET' ? {} : { body: JSON.stringify(data) }),
    });
    let result = {};
    try { result = await response.json(); } catch (e) {}
    if (!response.ok) {
      const err = new Error(result.message || 'Permintaan gagal. Coba lagi.');
      err.status = response.status;
      throw err;
    }
    return result;
  }

  const modal = document.getElementById('cardModal');
  const modalBox = modal.querySelector('.admin-modal-box');
  const stageIcon = document.getElementById('cardStageIcon');
  const stageText = document.getElementById('cardStageText');
  const stageSub = document.getElementById('cardStageSub');
  const modalName = document.getElementById('cardModalName');
  const modalMeta = document.getElementById('cardModalMeta');
  const modalAvatar = document.getElementById('cardModalAvatar');
  const modalTitle = document.getElementById('cardModalTitle');
  const saveBtn = document.getElementById('cardSaveBtn');
  const cancelBtn = document.getElementById('cardCancelBtn');
  const retryBtn = document.getElementById('cardRetryBtn');
  const closeBtn = document.getElementById('cardModalClose');

  let student = null, sessionId = null, uid = null, expiresAt = null, stage = null;
  let errorMessage = '', triggerEl = null, busyNetwork = false, modalOpen = false;
  let pollTimer = null, countdownTimer = null, pollSeq = 0;

  function fmtCountdown(ms) {
    const s = Math.max(0, Math.floor(ms / 1000));
    return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0');
  }

  function setStage(next) { stage = next; render(); }

  function render() {
    stageIcon.className = 'rfid-stage-icon';
    stageIcon.innerHTML = '';
    saveBtn.hidden = true; saveBtn.disabled = true;
    cancelBtn.hidden = false; cancelBtn.disabled = false;
    retryBtn.hidden = true;

    if (stage === 'starting') {
      stageIcon.classList.add('rfid-spinner');
      stageText.textContent = 'Memulai sesi untuk ' + student.name + '...';
      stageSub.textContent = '';
    } else if (stage === 'waiting') {
      stageIcon.classList.add('rfid-wave');
      stageIcon.innerHTML = '<span class="rfid-wave-ring"></span><span class="rfid-wave-ring"></span><span class="rfid-wave-ring"></span>'
        + '<span class="rfid-wave-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></span>';
      stageText.textContent = 'Menunggu kartu — tempelkan pada reader';
      stageSub.innerHTML = 'Berlaku hingga <span class="rfid-countdown" id="cardCountdown">' + fmtCountdown(expiresAt - Date.now()) + '</span>';
    } else if (stage === 'detected') {
      stageIcon.classList.add('rfid-check');
      stageIcon.innerHTML = '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
      stageText.textContent = 'Kartu terdeteksi';
      stageSub.innerHTML = 'UID: <code>' + uid + '</code>' + (student.uid ? '<br>Kartu lama <code>' + student.uid + '</code> akan diganti.' : '');
      saveBtn.hidden = false; saveBtn.disabled = false;
    } else if (stage === 'saving') {
      stageIcon.classList.add('rfid-spinner');
      stageText.textContent = 'Menyimpan kartu...';
      stageSub.textContent = '';
      saveBtn.hidden = false; saveBtn.disabled = true; cancelBtn.disabled = true;
    } else if (stage === 'cancelling') {
      stageIcon.classList.add('rfid-spinner');
      stageText.textContent = 'Membatalkan sesi...';
      stageSub.textContent = '';
      cancelBtn.disabled = true;
    } else if (stage === 'busy') {
      stageIcon.classList.add('rfid-warn');
      stageIcon.innerHTML = warnIcon();
      stageText.textContent = 'Pembaca sedang digunakan Admin lain';
      stageSub.textContent = 'Tunggu hingga sesi tersebut selesai, lalu coba lagi.';
      cancelBtn.hidden = true; retryBtn.hidden = false;
    } else if (stage === 'expired') {
      stageIcon.classList.add('rfid-warn');
      stageIcon.innerHTML = warnIcon();
      stageText.textContent = 'Sesi kedaluwarsa';
      stageSub.textContent = 'Sesi registrasi berakhir setelah dua menit tanpa kartu terdeteksi.';
      cancelBtn.hidden = true; retryBtn.hidden = false;
    } else if (stage === 'error') {
      stageIcon.classList.add('rfid-warn');
      stageIcon.innerHTML = warnIcon();
      stageText.textContent = errorMessage || 'Terjadi kesalahan.';
      if (sessionId) {
        stageSub.textContent = 'Sesi masih berjalan. Batalkan atau coba simpan kembali.';
        if (uid) { saveBtn.hidden = false; saveBtn.disabled = false; }
      } else {
        stageSub.textContent = '';
        cancelBtn.hidden = true; retryBtn.hidden = false;
      }
    }
  }

  function warnIcon() {
    return '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
  }

  function openModal(btn) {
    triggerEl = btn;
    student = { id: Number(btn.dataset.id), name: btn.dataset.name, nis: btn.dataset.nis, className: btn.dataset.class, uid: btn.dataset.uid || null };
    modalName.textContent = student.name;
    modalMeta.textContent = [student.nis, student.className].filter(Boolean).join(' · ');
    modalAvatar.textContent = student.name.charAt(0).toUpperCase();
    modalTitle.textContent = (student.uid ? 'Ganti Kartu — ' : 'Registrasi Kartu — ') + student.name;
    sessionId = null; uid = null; expiresAt = null; errorMessage = ''; modalOpen = true;
    modal.classList.add('open');
    document.addEventListener('keydown', onModalKeydown);
    setStage('starting');
    modalBox.focus();
    startSession();
  }

  async function startSession() {
    try {
      const res = await api(urls.start, { student_id: student.id });
      if (!modalOpen) return;
      sessionId = res.session;
      expiresAt = Date.now() + 120000;
      setStage('waiting');
      startPolling();
      startCountdown();
    } catch (err) {
      if (!modalOpen) return;
      sessionId = null;
      if (err.status === 409) setStage('busy');
      else { errorMessage = err.message; setStage('error'); }
    }
  }

  function startPolling() { stopPolling(); pollTimer = setInterval(pollState, 1500); }
  function stopPolling() { if (pollTimer) clearInterval(pollTimer); pollTimer = null; }
  function startCountdown() { stopCountdown(); countdownTimer = setInterval(tickCountdown, 1000); }
  function stopCountdown() { if (countdownTimer) clearInterval(countdownTimer); countdownTimer = null; }

  function tickCountdown() {
    const el = document.getElementById('cardCountdown');
    const remain = expiresAt - Date.now();
    if (remain <= 0) {
      stopCountdown(); stopPolling(); sessionId = null;
      setStage('expired');
      return;
    }
    if (el) el.textContent = fmtCountdown(remain);
  }

  async function pollState() {
    const seq = ++pollSeq;
    try {
      const res = await api(urls.state, null, 'GET');
      if (!modalOpen || seq !== pollSeq || (stage !== 'waiting' && stage !== 'detected')) return;
      if (!res.active) {
        stopPolling(); stopCountdown(); sessionId = null;
        setStage('expired');
        return;
      }
      if (!res.mine) {
        stopPolling(); stopCountdown(); sessionId = null;
        setStage('busy');
        return;
      }
      if (res.expires_at) expiresAt = new Date(res.expires_at.replace(' ', 'T')).getTime();
      if (res.uid && stage === 'waiting') { uid = res.uid; setStage('detected'); }
    } catch (e) { /* transient network issue, keep last known state and retry next tick */ }
  }

  async function saveCard() {
    if (!sessionId || !uid || busyNetwork) return;
    busyNetwork = true;
    setStage('saving');
    try {
      const res = await api(urls.finish, { session: sessionId, uid, confirm: true, approved: true });
      stopPolling(); stopCountdown();
      const savedStudent = res.student;
      closeModalNow();
      updateRegistrationRow(savedStudent);
      toast(res.message || 'Kartu berhasil disimpan.');
    } catch (err) {
      errorMessage = err.message; setStage('error');
    } finally { busyNetwork = false; }
  }

  async function cancelSession() {
    if (busyNetwork) return;
    if (!sessionId) { closeModalNow(); return; }
    busyNetwork = true;
    setStage('cancelling');
    try {
      await api(urls.finish, { session: sessionId, confirm: false });
      stopPolling(); stopCountdown();
      sessionId = null;
      closeModalNow();
    } catch (err) {
      errorMessage = err.message; setStage('error');
    } finally { busyNetwork = false; }
  }

  function retry() { setStage('starting'); startSession(); }

  function requestClose() {
    if (busyNetwork) return;
    if (sessionId) cancelSession(); else closeModalNow();
  }

  function closeModalNow() {
    modalOpen = false;
    stopPolling(); stopCountdown();
    modal.classList.remove('open');
    document.removeEventListener('keydown', onModalKeydown);
    if (triggerEl) { triggerEl.focus(); triggerEl = null; }
  }

  function onModalKeydown(e) {
    if (e.key === 'Escape') { e.preventDefault(); requestClose(); return; }
    if (e.key === 'Tab') trapFocus(e);
  }

  function trapFocus(e) {
    const focusables = Array.from(modalBox.querySelectorAll('button:not([hidden]):not([disabled]), [tabindex]:not([tabindex="-1"])'));
    if (!focusables.length) return;
    const first = focusables[0], last = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }

  function updateRegistrationRow(s) {
    if (!s) return;
    const row = document.getElementById('reg-row-' + s.id);
    if (!row) return;
    row.querySelector('.uid-cell').innerHTML = '<code>' + (s.rfid_uid || 'Belum terdaftar') + '</code>';
    const startBtn = row.querySelector('.start-card');
    startBtn.dataset.uid = s.rfid_uid || '';
    startBtn.textContent = s.rfid_uid ? 'Ganti Kartu' : 'Mulai Scan';
    const unlinkBtn = row.querySelector('.unlink-card');
    unlinkBtn.dataset.uid = s.rfid_uid || '';
    unlinkBtn.hidden = !s.rfid_uid;
  }

  function toast(message) {
    if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2500 });
  }

  document.querySelectorAll('.start-card').forEach(btn => btn.addEventListener('click', () => openModal(btn)));
  closeBtn.addEventListener('click', requestClose);
  cancelBtn.addEventListener('click', requestClose);
  retryBtn.addEventListener('click', retry);
  saveBtn.addEventListener('click', saveCard);

  document.querySelectorAll('.unlink-card').forEach(btn => btn.addEventListener('click', () => {
    if (!window.Swal) return;
    Swal.fire({
      title: 'Lepaskan kartu ini?',
      text: 'Kartu tidak dapat digunakan untuk absensi sampai didaftarkan kembali.',
      icon: 'warning', showCancelButton: true,
      confirmButtonText: 'Lepaskan', cancelButtonText: 'Batal', confirmButtonColor: '#d94a4a',
    }).then(result => {
      if (!result.isConfirmed) return;
      api(btn.dataset.url, { uid: btn.dataset.uid, approved: true }, 'DELETE')
        .then(res => {
          updateRegistrationRow({ id: Number(btn.closest('tr').dataset.id), rfid_uid: null });
          toast(res.message);
        })
        .catch(err => Swal.fire('Gagal', err.message, 'error'));
    });
  }));

  let bgBusy = false;
  async function backgroundPoll() {
    if (bgBusy || modal.classList.contains('open')) return;
    bgBusy = true;
    try {
      const res = await api(urls.state, null, 'GET');
      document.querySelectorAll('.start-card').forEach(b => { b.disabled = res.active; });
    } catch (e) { /* ignore transient errors */ }
    bgBusy = false;
  }
  backgroundPoll();
  setInterval(backgroundPoll, 3000);
})();
</script>
@endif
@endpush
