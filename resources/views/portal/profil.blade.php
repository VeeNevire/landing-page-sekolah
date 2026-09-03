@extends('layouts.portal')

@section('title', 'Profil Siswa')

@section('content')
@if (session('success'))
  <div class="portal-panel" style="margin-bottom:20px;color:#166534;background:#f0fdf4">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="portal-panel" style="margin-bottom:20px;color:#991b1b;background:#fef2f2">{{ session('error') }}</div>
@endif
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Profil siswa</span>
        <h1>Profil {{ $demoStudent['name'] }}</h1>
        <p>Data lengkap siswa pada Semester {{ $demoStudent['semester'] }} Tahun Ajaran {{ $demoStudent['academic_year'] }}.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
      </div>
    </div>

    <div class="portal-dashboard-grid">
      <section class="portal-panel">
        <div class="portal-panel-header"><div><h2>Data Siswa</h2><p>Informasi akademik dan identitas.</p></div></div>
        <div style="display:grid;gap:14px">
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Nama Lengkap</span><strong>{{ $demoStudent['name'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">NISN</span><strong>{{ $demoStudent['nisn'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Kelas</span><strong>{{ $demoStudent['class'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Program</span><strong>{{ $demoStudent['program'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Semester</span><strong>{{ $demoStudent['semester'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--line)"><span style="color:var(--muted)">Tahun Ajaran</span><strong>{{ $demoStudent['academic_year'] }}</strong></div>
          <div style="display:flex;justify-content:space-between;padding:12px 0"><span style="color:var(--muted)">Wali Kelas</span><strong>{{ $demoStudent['homeroom_teacher'] }}</strong></div>
        </div>
      </section>

      <div style="display:grid;gap:20px">
        <section class="portal-panel">
          <div class="portal-panel-header"><div><h3>Karakter & Sikap</h3><p>Penilaian dari wali kelas.</p></div></div>
          <div class="competency-list">
            @foreach ($demoStudent['behavior'] as $label => $value)
              <div class="competency-row">
                <span class="competency-label">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                <span class="competency-value">{{ $value }}</span>
              </div>
            @endforeach
          </div>
        </section>

        <section class="portal-panel">
          <div class="portal-panel-header"><div><h3>Ekstrakurikuler</h3><p>Kegiatan di luar kelas.</p></div></div>
          <div class="activity-feed">
            @foreach ($demoStudent['extracurricular'] as $item)
              <div class="activity-item">
                <span class="activity-icon" style="background:color-mix(in srgb,var(--accent) 15%,var(--card))">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                </span>
                <div><strong>{{ $item['name'] }} &bull; {{ $item['score'] }}</strong><span>{{ $item['note'] }}</span></div>
              </div>
            @endforeach
          </div>
      </section>

      @if(!isset($telegramBots) || $telegramBots->isEmpty())
      <section class="portal-panel telegram-card {{ $telegramConnected ? 'is-connected' : 'is-disconnected' }}">
        <svg class="telegram-card-watermark" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4Z"/>
        </svg>
        <div class="telegram-card-head">
          <span class="telegram-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4Z"/></svg>
          </span>
          <div class="telegram-card-title">
            <span>Terhubung langsung</span>
            <h3>Notifikasi Telegram</h3>
          </div>
          <span class="telegram-status" role="status">
            <span class="telegram-status-dot"></span>
            {{ $telegramConnected ? 'Aktif' : 'Belum aktif' }}
          </span>
        </div>

        @if ($telegramConnected)
          <div class="telegram-card-copy">
            <strong>Semua siap, Bapak/Ibu.</strong>
            <p>Informasi nilai baru akan langsung masuk ke akun Telegram yang terhubung.</p>
          </div>
          <div class="telegram-message-preview" aria-label="Contoh notifikasi Telegram">
            <span class="telegram-preview-mark" aria-hidden="true">N</span>
            <div>
              <span class="telegram-preview-label">Notifikasi nilai</span>
              <strong>{{ $selectedStudent->full_name }}</strong>
              <small>Nilai, status KKM, dan catatan guru</small>
            </div>
            <span class="telegram-preview-check" aria-hidden="true">✓✓</span>
          </div>
          <div class="telegram-card-footer">
            <span class="telegram-secure-note">
              <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Khusus informasi sekolah
            </span>
            <form method="POST" action="{{ route('portal.telegram.disconnect') }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="telegram-action telegram-action-secondary">Putuskan</button>
            </form>
          </div>
        @else
          <div class="telegram-card-copy">
            <strong>Nilai terbaru, tanpa perlu mengecek portal.</strong>
            <p>Hubungkan Telegram untuk menerima pembaruan akademik saat guru selesai menilai.</p>
          </div>
          <ul class="telegram-benefits" aria-label="Manfaat notifikasi Telegram">
            <li><span>✓</span> Nilai dan status KKM</li>
            <li><span>✓</span> Catatan langsung dari guru</li>
            <li><span>✓</span> Terkirim otomatis dan pribadi</li>
          </ul>
          <form method="POST" action="{{ route('portal.telegram.connect') }}">
            @csrf
            <button type="submit" class="telegram-action telegram-action-primary">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4Z"/></svg>
              Hubungkan Telegram
              <span aria-hidden="true">→</span>
            </button>
          </form>
          <p class="telegram-privacy">Bot hanya mengirim informasi sekolah. Tidak ada pesan promosi.</p>
        @endif
      </section>
      @endif
      @if(isset($telegramBots) && $telegramBots->isNotEmpty())
      <section class="portal-panel telegram-bot-panel" style="margin-top:16px">
        <div class="portal-panel-header"><div><h2>Bot Telegram</h2><p>Tekan Hubungkan lalu tekan <strong>Start</strong> pada masing-masing bot.</p></div></div>
        <div style="display:grid;gap:10px">
        @foreach($telegramBots as $bot)
          @php $connection = $bot->connections->first(); @endphp
          <div class="telegram-bot-row">
            <div class="telegram-bot-info"><strong>{{ $bot->name }}</strong><div>{{ $bot->username ? '@'.ltrim($bot->username, '@') : 'Bot Telegram' }}</div></div>
            @if($connection?->chat_id)
              <span class="status-pass">Terhubung</span>
            @else
              <form method="POST" action="{{ route('portal.telegram.bot.connect', $bot) }}">@csrf<button class="telegram-action telegram-bot-connect" type="submit">Hubungkan</button></form>
            @endif
          </div>
        @endforeach
        </div>
      </section>
      @endif
      </div>
    </div>
@endif
@endsection



