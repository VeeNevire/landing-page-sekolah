@php
  $notificationCursor = \App\Http\Controllers\NotificationFeedController::visibleTo(auth()->user())->max('id') ?? 0;
@endphp
<div id="live-notification-widget" style="position:fixed;right:18px;bottom:18px;z-index:45;max-width:calc(100vw - 36px)">
  <button id="live-notification-toggle" type="button" class="btn btn-primary" aria-expanded="false" aria-controls="live-notification-panel">Notifikasi <span id="live-notification-count" hidden></span></button>
  <section id="live-notification-panel" hidden aria-label="Notifikasi terbaru" style="background:var(--card,#fff);color:var(--text,#111);border:1px solid var(--line,#ddd);border-radius:12px;padding:16px;width:340px;max-width:calc(100vw - 36px);max-height:60vh;overflow:auto;margin-top:8px;box-shadow:0 4px 24px #0002">
    <strong>Notifikasi terbaru</strong><p id="live-notification-state" style="font-size:.85rem">Memuat notifikasi…</p><div id="live-notification-list"></div>
    <a href="{{ auth()->user()->role === 'parent' ? route('portal.notifikasi') : route('siswa.dashboard') }}">Lihat di aplikasi</a>
  </section>
</div>
<div id="live-notification-toast" role="status" aria-live="polite" hidden style="position:fixed;left:18px;bottom:18px;z-index:46;background:var(--card,#fff);color:var(--text,#111);border:1px solid var(--line,#ddd);border-radius:12px;padding:16px;max-width:360px;width:calc(100vw - 36px);box-shadow:0 4px 24px #0002">
  <strong id="live-notification-toast-title"></strong><p id="live-notification-toast-body" style="margin:8px 0"></p><button id="live-notification-dismiss" type="button" class="btn btn-outline">Tutup</button>
</div>
<script>
(() => {
  let cursor = @json((int)$notificationCursor), inflight = false, fresh = 0;
  const url = @json(route('notifications.feed'));
  const toggle = document.getElementById('live-notification-toggle');
  const panel = document.getElementById('live-notification-panel');
  const badge = document.getElementById('live-notification-count');
  const toast = document.getElementById('live-notification-toast');
  const state = document.getElementById('live-notification-state');
  toggle.addEventListener('click', () => {
    panel.hidden = !panel.hidden;
    toggle.setAttribute('aria-expanded', String(!panel.hidden));
    if (!panel.hidden) { fresh = 0; badge.hidden = true; refresh(); }
  });
  document.getElementById('live-notification-dismiss').addEventListener('click', () => { toast.hidden = true; });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') { panel.hidden = true; toast.hidden = true; toggle.setAttribute('aria-expanded', 'false'); }
  });
  async function refresh() {
    if (inflight || document.hidden) return;
    inflight = true;
    try {
      const response = await fetch(url + '?after=' + cursor, {headers: {'Accept':'application/json'}, cache:'no-store'});
      if (response.status === 401 || response.status === 403) { clearInterval(timer); return; }
      if (!response.ok) throw new Error('feed');
      const data = await response.json();
      const list = document.getElementById('live-notification-list');
      list.replaceChildren();
      for (const item of data.recent) {
        const article = document.createElement('article');
        article.style.cssText = 'padding:12px 0;border-bottom:1px solid var(--line,#ddd)';
        const title = document.createElement('strong'), body = document.createElement('p'), time = document.createElement('small');
        title.textContent = item.title; body.textContent = item.body; time.textContent = item.time;
        body.style.cssText = 'font-size:.85rem;margin:6px 0';
        article.append(title, body, time); list.append(article);
      }
      state.textContent = data.recent.length ? '10 pesan terakhir atau lebih sedikit.' : 'Belum ada notifikasi.';
      if (data.new.length) {
        fresh += data.new.length; badge.textContent = '(' + fresh + ')'; badge.hidden = !panel.hidden;
        const latest = data.new[data.new.length - 1];
        document.getElementById('live-notification-toast-title').textContent = latest.title;
        document.getElementById('live-notification-toast-body').textContent = latest.body;
        toast.hidden = false;
      }
      cursor = data.cursor;
    } catch (error) { state.textContent = 'Koneksi terputus. Mencoba kembali…'; }
    finally { inflight = false; }
  }
  const timer = setInterval(refresh, 10000);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
  refresh();
})();
</script>
