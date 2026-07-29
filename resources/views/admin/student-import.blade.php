@extends('layouts.admin')

@section('title', 'Import Siswa (CSV)')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen siswa</span>
    <h1>Import Siswa dari CSV</h1>
    <p>Upload file CSV untuk menambahkan siswa secara massal.</p>
  </div>
</div>

@if (session('error'))
<div class="portal-error" style="background:color-mix(in srgb,#ef4444 8%,var(--card));border:1px solid color-mix(in srgb,#ef4444 20%,var(--line));color:#dc2626;padding:14px 18px;border-radius:10px;margin-bottom:16px;font-size:.9rem">
  {{ session('error') }}
</div>
@endif

@if (session('warning'))
<div class="portal-warning" style="background:color-mix(in srgb,#f59e0b 8%,var(--card));border:1px solid color-mix(in srgb,#f59e0b 20%,var(--line));color:#b45309;padding:14px 18px;border-radius:10px;margin-bottom:16px;font-size:.9rem">
  {{ session('warning') }}
  @if (session('import_errors'))
  <details style="margin-top:8px">
    <summary style="cursor:pointer;font-weight:600">Lihat detail error</summary>
    <ul style="margin:8px 0 0;padding-left:20px;font-size:.85rem">
      @foreach (session('import_errors') as $err)
      <li style="margin-bottom:4px">{{ $err }}</li>
      @endforeach
    </ul>
  </details>
  @endif
</div>
@endif

<section class="portal-panel" style="max-width:720px">
  <div style="padding:24px">
    <h3 style="margin:0 0 12px">Format CSV</h3>
    <p style="color:var(--muted);margin:0 0 16px;font-size:.9rem">
      File CSV harus memiliki header kolom berikut. Kolom <strong>wajib</strong> ditandai <span style="color:#ef4444">*</span>:
    </p>

    <div style="background:var(--bg);border-radius:12px;padding:16px;margin-bottom:20px;font-family:monospace;font-size:.82rem;line-height:1.8;overflow-x:auto">
      <div style="color:var(--primary-2);font-weight:700;white-space:nowrap">
        nisn,full_name,student_email,birth_date,class_name,program_name,parent_name,parent_email,parent_relation
      </div>
      <div style="white-space:nowrap">0098765432,Alif Pratama,alif@mail.com,2009-05-15,XI RPL 1,RPL,Budi Pratama,budi@mail.com,Ayah</div>
      <div style="white-space:nowrap">0101234567,Alya Pratama,alya@mail.com,2009-08-20,X SMA 2,Sains,Siti Pratama,siti@mail.com,Ibu</div>
      <div style="white-space:nowrap">0101234568,Dimas Putra,dimas@mail.com,2010-01-10,X RPL 1,RPL,,,</div>
    </div>

    <div style="background:color-mix(in srgb,var(--accent) 10%,var(--card));border-radius:12px;padding:16px;margin-bottom:20px;border:1px solid color-mix(in srgb,var(--accent) 20%,var(--line))">
      <strong style="color:#7a5500">Ketentuan:</strong>
      <ul style="color:var(--muted);margin:8px 0 0;padding-left:20px;font-size:.88rem;line-height:1.7">
        <li><code>nisn</code> <span style="color:#ef4444">*</span> — Nomor Induk Siswa Nasional, unik (tidak bisa duplikat)</li>
        <li><code>full_name</code> <span style="color:#ef4444">*</span> — Nama lengkap siswa</li>
        <li><code>student_email</code> <span style="color:#ef4444">*</span> — Email untuk login siswa, unik</li>
        <li><code>birth_date</code> — Format <code>YYYY-MM-DD</code></li>
        <li><code>class_name</code> — Nama kelas (contoh: XI RPL 1)</li>
        <li><code>program_name</code> — Nama program/jurusan (contoh: RPL, Sains)</li>
        <li><code>parent_name</code> & <code>parent_email</code> — Data orang tua (opsional, harus diisi berdua)</li>
        <li><code>parent_relation</code> — Hubungan orang tua, default "Orang Tua"</li>
      </ul>
    </div>

    <div style="background:color-mix(in srgb,var(--success) 8%,var(--card));border-radius:12px;padding:16px;margin-bottom:20px;border:1px solid color-mix(in srgb,var(--success) 20%,var(--line))">
      <strong style="color:var(--success)">Proses import akan:</strong>
      <ul style="color:var(--muted);margin:8px 0 0;padding-left:20px;font-size:.88rem;line-height:1.7">
        <li>Membuat akun login siswa secara otomatis</li>
        <li>Mengirim email kredensial ke email siswa</li>
        <li>Membuat akun orang tua & mengirim kredensial (jika data parent diisi)</li>
        <li>Menghubungkan siswa dengan orang tua di sistem</li>
      </ul>
    </div>

    <form method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="field">
        <label for="csv_file">Pilih File CSV</label>
        <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt" required>
        @error('csv_file') <small style="color:#ef4444">{{ $message }}</small> @enderror
      </div>

      <div style="display:flex;gap:10px;margin-top:20px">
        <button type="submit" class="btn btn-primary">Import Sekarang</button>
        <a href="{{ asset('samples/sample-student-import.csv') }}" class="btn btn-outline" download>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download Template CSV
        </a>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline" style="margin-left:auto">Kembali</a>
      </div>
    </form>
  </div>
</section>
@endsection
