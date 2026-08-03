@extends('layouts.portal')

@section('title', 'Izin / Sakit')

@push('styles')
<style>
.izin-upload {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 16px;
  border: 2px dashed var(--line);
  border-radius: 12px;
  background: var(--card);
  cursor: pointer;
  transition: border-color .15s ease, background .15s ease;
}
.izin-upload:hover { border-color: var(--primary-3); background: color-mix(in srgb, var(--primary-4) 6%, var(--card)); }
.izin-upload-input { position: absolute; width: 1px; height: 1px; opacity: 0; overflow: hidden; }
.izin-upload-icon {
  width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
  display: grid; place-items: center;
  background: color-mix(in srgb, var(--primary-2) 12%, var(--card));
  color: var(--primary-2);
}
.izin-upload-icon svg { width: 20px; height: 20px; }
.izin-upload-text strong { display: block; font-size: .88rem; color: var(--ink); }
.izin-upload-text small { display: block; font-size: .74rem; color: var(--muted); margin-top: 2px; }
.izin-upload-file {
  display: flex; align-items: center; gap: 10px;
  margin-top: 8px; padding: 10px 14px;
  border-radius: 10px;
  background: color-mix(in srgb, var(--primary-4) 8%, var(--card));
  border: 1px solid var(--line);
  font-size: .84rem; color: var(--ink);
}
.izin-upload-file button {
  margin-left: auto; border: none; background: transparent;
  color: #dc2626; cursor: pointer; font-size: .78rem; font-weight: 700; font-family: inherit;
}
</style>
@endpush

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Perizinan</span>
        <h1>Izin / Sakit</h1>
        <p>Ajukan izin atau sakit untuk {{ $demoStudent['name'] }} yang akan ditinjau oleh wali kelas.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
        <p>Wali Kelas {{ $demoStudent['homeroom_teacher'] }}</p>
      </div>
    </div>

    <section class="portal-panel" style="margin-bottom:20px">
      <div class="portal-panel-header">
        <div><h2>Ajukan Izin / Sakit</h2><p>Rentang tanggal dan keterangan akan diteruskan ke wali kelas untuk disetujui.</p></div>
      </div>

      <form method="POST" action="{{ route('portal.izin.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($students->count() > 1)
        <div style="margin-bottom:14px">
          <label style="font-size:.82rem;font-weight:700;display:block;margin-bottom:6px;color:var(--ink)">Pilih siswa</label>
          <select name="student_id" onchange="window.location='{{ route('portal.izin') }}?student_id='+this.value" style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.9rem;font-family:inherit">
            @foreach ($students as $s)
              <option value="{{ $s->id }}" @if ($s->id === $selectedStudent->id) selected @endif>{{ $s->full_name }} ({{ $s->class_name }})</option>
            @endforeach
          </select>
        </div>
        @else
          <input type="hidden" name="student_id" value="{{ $selectedStudent->id }}">
        @endif

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
          <div>
            <label style="font-size:.82rem;font-weight:700;display:block;margin-bottom:6px;color:var(--ink)">Jenis</label>
            <div>
              <label style="display:inline-flex;align-items:center;gap:6px;margin-right:20px;font-size:.9rem;font-weight:600;color:var(--ink);cursor:pointer">
                <input type="radio" name="type" value="sick" required @checked(old('type', 'sick') === 'sick') style="margin:0;width:15px;height:15px;accent-color:var(--primary)"> Sakit
              </label>
              <label style="display:inline-flex;align-items:center;gap:6px;font-size:.9rem;font-weight:600;color:var(--ink);cursor:pointer">
                <input type="radio" name="type" value="excused" @checked(old('type') === 'excused') style="margin:0;width:15px;height:15px;accent-color:var(--primary)"> Izin
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:.82rem;font-weight:700;display:block;margin-bottom:6px;color:var(--ink)">Lampiran (opsional)</label>
            <label for="izinAttachment" class="izin-upload" id="izinUploadZone">
              <input type="file" id="izinAttachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="izin-upload-input">
              <span class="izin-upload-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              </span>
              <span class="izin-upload-text" id="izinUploadText">
                <strong>Pilih file</strong>
                <small>JPG, PNG, atau PDF &bull; maks 2 MB</small>
              </span>
            </label>
            <div class="izin-upload-file" id="izinUploadFile" style="display:none">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary-2);flex-shrink:0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <span id="izinUploadFileName" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
              <button type="button" id="izinUploadRemove">Hapus</button>
            </div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
          <div>
            <label style="font-size:.82rem;font-weight:700;display:block;margin-bottom:6px;color:var(--ink)">Mulai tanggal</label>
            <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.9rem;font-family:inherit">
          </div>
          <div>
            <label style="font-size:.82rem;font-weight:700;display:block;margin-bottom:6px;color:var(--ink)">Sampai tanggal</label>
            <input type="date" name="end_date" value="{{ old('end_date', now()->format('Y-m-d')) }}" required style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.9rem;font-family:inherit">
          </div>
        </div>

        <div style="margin-bottom:18px">
          <label style="font-size:.82rem;font-weight:700;display:block;margin-bottom:6px;color:var(--ink)">Keterangan / alasan</label>
          <textarea name="reason" rows="3" required placeholder="Tuliskan alasan izin/sakit..." style="width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--line);background:var(--card);color:var(--ink);font-size:.9rem;font-family:inherit;resize:vertical">{{ old('reason') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/></svg>
          Kirim Pengajuan
        </button>
      </form>
    </section>

    <section class="portal-panel">
      <div class="portal-panel-header">
        <div><h2>Riwayat Pengajuan</h2><p>{{ $requestCounts['pending'] }} menunggu &bull; {{ $requestCounts['approved'] }} disetujui &bull; {{ $requestCounts['rejected'] }} ditolak</p></div>
      </div>

      @if ($requests->isEmpty())
        <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.88rem">Belum ada pengajuan izin/sakit.</div>
      @else
      <div class="table-wrap">
        <table class="grade-table">
          <thead>
            <tr><th>Jenis</th><th>Tanggal</th><th>Keterangan</th><th>Lampiran</th><th>Status</th><th>Catatan Wali</th></tr>
          </thead>
          <tbody>
            @foreach ($requests as $r)
              @php
                $statusColor = ['pending' => '#d97706', 'approved' => '#16a34a', 'rejected' => '#dc2626'][$r->status];
              @endphp
              <tr>
                <td><strong>{{ $r->type_label }}</strong></td>
                <td>{{ $r->start_date->format('d M Y') }} - {{ $r->end_date->format('d M Y') }}</td>
                <td style="color:var(--muted);max-width:260px">{{ $r->reason }}</td>
                <td>
                  @if ($r->attachment_path)
                    <a href="{{ route('download.izin', $r->id) }}" class="text-link" style="font-size:.82rem">Lihat lampiran</a>
                  @else
                    <span style="color:var(--line)">-</span>
                  @endif
                </td>
                <td><span style="display:inline-block;padding:3px 10px;border-radius:99px;font-size:.72rem;font-weight:700;background:{{ $statusColor }}18;color:{{ $statusColor }}">{{ $r->status_label }}</span></td>
                <td style="color:var(--muted);font-size:.82rem">{{ $r->response_note ?: '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif
    </section>
@endif
@endsection

@push('scripts')
<script>
(function () {
  var input = document.getElementById('izinAttachment');
  var zone = document.getElementById('izinUploadZone');
  var text = document.getElementById('izinUploadText');
  var fileBox = document.getElementById('izinUploadFile');
  var fileName = document.getElementById('izinUploadFileName');

  if (!input) return;

  var defaultText = '<strong>Pilih file</strong><small>JPG, PNG, atau PDF &bull; maks 2 MB</small>';

  function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  function reset() {
    input.value = '';
    fileBox.style.display = 'none';
    text.innerHTML = defaultText;
  }

  input.addEventListener('change', function () {
    var f = input.files[0];
    if (!f) { reset(); return; }
    fileName.textContent = f.name + ' (' + formatSize(f.size) + ')';
    fileBox.style.display = 'flex';
    text.innerHTML = '<strong>Ganti file</strong><small>' + f.name + '</small>';
  });

  document.getElementById('izinUploadRemove').addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    reset();
  });
})();
</script>
@endpush

@push('scripts')
<script>
@if (session('success'))
Swal.fire({ icon: 'success', title: 'Berhasil', text: {!! json_encode(session('success')) !!}, confirmButtonColor: '#16a34a' });
@endif
@if (session('error'))
  Swal.fire({ icon: 'error', title: 'Gagal', text: {!! json_encode(session('error')) !!}, confirmButtonColor: '#dc2626' });
@endif
@if ($errors->any())
  var izinErrors = @json($errors->all());
  Swal.fire({ icon: 'error', title: 'Periksa kembali', html: izinErrors.map(function (m) { return '&bull; ' + m; }).join('<br>'), confirmButtonColor: '#dc2626' });
@endif
</script>
@endpush