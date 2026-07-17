@extends('layouts.portal')

@section('title', 'Pesan & Notifikasi')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Pesan & notifikasi</span>
        <h1>Pesan dari Sekolah</h1>
        <p>Informasi dan pemberitahuan terkini untuk {{ $demoStudent['name'] }}.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
      </div>
    </div>

    <section class="portal-panel">
      <div class="portal-panel-header"><div><h2>Notifikasi</h2><p>{{ count($notifications) }} pesan baru.</p></div></div>
      <div style="display:grid;gap:14px">
        @foreach ($notifications as $notif)
          <div style="display:flex;gap:14px;padding:18px;border-radius:15px;border:1px solid var(--line);background:var(--card)">
            <span style="flex-shrink:0;width:40px;height:40px;border-radius:12px;display:grid;place-items:center;
              @if ($notif['type'] === 'warning') background:#fef3c7;color:#92400e;
              @elseif ($notif['type'] === 'success') background:#d1fae5;color:#065f46;
              @else background:#e0e7ff;color:#3730a3; @endif
            ">
              @if ($notif['type'] === 'warning')
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              @elseif ($notif['type'] === 'success')
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              @else
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
              @endif
            </span>
            <div style="flex:1">
              <strong style="display:block;margin-bottom:3px">{{ $notif['title'] }}</strong>
              <p style="color:var(--muted);font-size:.88rem;margin:0 0 6px">{{ $notif['body'] }}</p>
              <span style="font-size:.8rem;color:var(--muted)">{{ date('d M Y', strtotime($notif['date'])) }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </section>
@endif
@endsection



