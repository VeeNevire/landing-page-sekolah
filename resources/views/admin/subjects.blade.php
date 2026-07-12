@extends('layouts.admin')

@section('title', 'Kelola Mata Pelajaran')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen akademik</span>
    <h1>Mata Pelajaran</h1>
    <p>Kelola daftar mata pelajaran dan nilai KKM.</p>
  </div>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama Mata Pelajaran</th>
          <th>KKM</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subjects as $subject)
          <tr>
            <td style="font-family:monospace;font-weight:700">{{ $subject->code }}</td>
            <td>{{ $subject->name }}</td>
            <td>
              <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" style="display:flex;align-items:center;gap:8px">
                @csrf @method('PUT')
                <input type="number" name="kkm" value="{{ $subject->kkm }}" min="0" max="100" step="0.01" style="width:80px;padding:6px 10px;border-radius:8px;border:1px solid var(--line);background:var(--card);font-size:.88rem">
                <input type="hidden" name="code" value="{{ $subject->code }}">
                <input type="hidden" name="name" value="{{ $subject->name }}">
                <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 10px;font-size:.78rem">Simpan</button>
              </form>
            </td>
            <td>
              <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 10px;font-size:.78rem;color:#ef4444" onclick="return confirm('Hapus mata pelajaran {{ $subject->name }}?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--muted)">Belum ada mata pelajaran.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $subjects->links() }}</div>
</section>

<section class="portal-panel" style="max-width:480px;margin-top:20px">
  <div style="padding:24px">
    <h3 style="margin:0 0 16px">Tambah Mata Pelajaran</h3>
    <form method="POST" action="{{ route('admin.subjects.store') }}">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 2fr 1fr;gap:12px">
        <div class="field">
          <label for="code">Kode</label>
          <input id="code" name="code" type="text" required placeholder="MTK" style="text-transform:uppercase">
        </div>
        <div class="field">
          <label for="name">Nama</label>
          <input id="name" name="name" type="text" required placeholder="Matematika">
        </div>
        <div class="field">
          <label for="kkm">KKM</label>
          <input id="kkm" name="kkm" type="number" required value="75" min="0" max="100" step="0.01">
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:14px">Tambah</button>
    </form>
  </div>
</section>
@endsection
