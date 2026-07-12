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

<section class="portal-panel" style="max-width:640px">
  <div style="padding:24px">
    <h3 style="margin:0 0 12px">Format CSV</h3>
    <p style="color:var(--muted);margin:0 0 16px;font-size:.9rem">File CSV harus memiliki header kolom berikut:</p>

    <div style="background:var(--bg);border-radius:12px;padding:16px;margin-bottom:20px;font-family:monospace;font-size:.85rem;line-height:1.8">
      <div style="color:var(--primary-2);font-weight:700">nisn,full_name,birth_date,class_name,program_name</div>
      <div>0098765432,Alif Pratama,2009-05-15,XI RPL 1,Rekayasa Perangkat Lunak</div>
      <div>0101234567,Alya Pratama,2009-08-20,X SMA 2,Sains &amp; Teknologi</div>
    </div>

    <div style="background:color-mix(in srgb,var(--accent) 10%,var(--card));border-radius:12px;padding:16px;margin-bottom:20px;border:1px solid color-mix(in srgb,var(--accent) 20%,var(--line))">
      <strong style="color:#7a5500">Catatan:</strong>
      <ul style="color:var(--muted);margin:8px 0 0;padding-left:20px;font-size:.88rem;line-height:1.7">
        <li>Kolom <code>nisn</code> dan <code>full_name</code> wajib diisi</li>
        <li>Jika NISN sudah ada, data akan diperbarui</li>
        <li>Kolom <code>birth_date</code> format: YYYY-MM-DD</li>
        <li>Kolom lainnya opsional</li>
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
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</section>
@endsection
