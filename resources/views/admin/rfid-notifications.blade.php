@extends('layouts.admin')
@section('title', 'Notifikasi RFID')
@section('content')
<div class="portal-heading"><div><span class="kicker">Kehadiran siswa</span><h1>Notifikasi RFID</h1><p>Notifikasi aplikasi tetap tersedia untuk siswa dan wali, termasuk wali yang belum menghubungkan Telegram.</p></div></div>
@include('admin.partials.rfid-tabs', ['active' => 'notifications'])
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
@if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
<section class="portal-panel" style="margin-bottom:20px">
  <div class="portal-panel-header"><div><h2>Telegram otomatis</h2><p>Pilih bot pengirim dan template absensi. Wali harus terhubung dengan bot tersebut.</p></div></div>
  <form method="POST" action="{{ route('admin.rfid.notifications.save') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:end">
    @csrf
    <label for="attendance-template">Template absensi
      <select id="attendance-template" name="telegram_template_id">
        <option value="">Hanya notifikasi aplikasi</option>
        @foreach($templates as $template)<option value="{{ $template->id }}" @selected((int)$selectedTemplate === $template->id)>{{ $template->name }}</option>@endforeach
      </select>
    </label>
    <label for="attendance-bot">Bot pengirim
      <select id="attendance-bot" name="telegram_bot_id"><option value="">Telegram nonaktif</option>
        @foreach($bots as $bot)<option value="{{ $bot->id }}" @selected((int)$selectedBot === $bot->id)>{{ $bot->name }}</option>@endforeach
      </select>
    </label>
    <button class="btn btn-primary">Simpan Pengaturan</button>
    <a class="btn btn-outline" href="{{ route('admin.telegram.index') }}">Kelola Bot dan Template</a>
  </form>
  @if(($selectedTemplate && !$templates->contains('id', (int)$selectedTemplate)) || ($selectedBot && !$bots->contains('id', (int)$selectedBot)))<p role="alert">Template atau bot yang sebelumnya dipilih tidak aktif. Pilih ulang agar pengiriman Telegram berjalan.</p>@endif
  <p style="color:var(--muted);margin-top:12px">Gunakan parameter nama_siswa, kelas, tanggal, jam_masuk, status, dan link_detail pada template absensi. Perubahan berlaku untuk absensi berikutnya.</p>
</section>
<section class="portal-panel">
  <div class="portal-panel-header"><div><h2>Pengiriman ke wali</h2><p>Pesan dikirim satu kali untuk setiap kehadiran RFID baru. Muat ulang halaman untuk melihat status terbaru.</p></div></div>
  <div style="overflow-x:auto"><table class="data-table"><thead><tr><th>Waktu</th><th>Siswa</th><th>Wali</th><th>Bot</th><th>Status Telegram</th><th>Percobaan</th><th>Aksi</th></tr></thead><tbody>
  @forelse($deliveries as $delivery)
    <tr>
      <td>{{ $delivery->created_at->format('d/m/Y H:i:s') }}</td><td>{{ $delivery->attendance?->student?->full_name ?? '-' }}</td><td>{{ $delivery->parent?->name ?? '-' }}</td><td>{{ $delivery->bot?->name ?? '-' }}</td>
      <td>{{ ['pending'=>'Menunggu antrean','queued'=>'Dalam antrean','retrying'=>'Mencoba ulang','sent'=>'Terkirim','failed'=>'Gagal','skipped'=>'Aplikasi saja'][$delivery->status] ?? $delivery->status }}
        @if($delivery->reason)<div style="font-size:.8rem;color:var(--muted)">{{ ['not_connected'=>'Telegram wali belum terhubung','not_configured'=>'Bot/template absensi belum aktif','recipient_unavailable'=>'Wali atau koneksi bot tidak tersedia','transport_error'=>'Pengiriman Telegram gagal'][$delivery->reason] ?? 'Periksa pengaturan Telegram' }}</div>@endif
        @if($delivery->sent_at)<div style="font-size:.8rem">{{ $delivery->sent_at->format('H:i:s') }} WIB</div>@endif
      </td><td>{{ $delivery->attempts }}</td><td>@if($delivery->status === 'failed')<form method="POST" action="{{ route('admin.rfid.notifications.retry', $delivery) }}">@csrf<button class="btn btn-outline">Coba Lagi</button></form>@else — @endif</td>
    </tr>
  @empty<tr><td colspan="7">Belum ada pengiriman notifikasi RFID.</td></tr>@endforelse
  </tbody></table></div>
  {{ $deliveries->links() }}
</section>
@endsection
