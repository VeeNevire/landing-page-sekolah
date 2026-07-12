@extends('layouts.admin')

@section('title', 'Hubungan Orang Tua–Siswa')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen relasi</span>
    <h1>Orang Tua &amp; Siswa</h1>
    <p>Hubungkan akun orang tua dengan siswa yang bersangkutan.</p>
  </div>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Orang Tua</th>
          <th>Siswa</th>
          <th>Hubungan</th>
          <th>Primer</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($parentStudent as $ps)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <span style="width:32px;height:32px;border-radius:8px;display:grid;place-items:center;background:color-mix(in srgb,var(--accent) 12%,var(--card));color:#7a5500;font-weight:800;font-size:.78rem;flex-shrink:0">{{ strtoupper(substr($ps->parent_name ?? '?', 0, 1)) }}</span>
                <div>
                  <strong style="display:block;font-size:.88rem">{{ $ps->parent_full_name ?: $ps->parent_name }}</strong>
                  <span style="font-size:.78rem;color:var(--muted)">{{ $ps->parent_email }}</span>
                </div>
              </div>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <span style="width:32px;height:32px;border-radius:8px;display:grid;place-items:center;background:color-mix(in srgb,var(--primary-2) 12%,var(--card));color:var(--primary-2);font-weight:800;font-size:.78rem;flex-shrink:0">{{ strtoupper(substr($ps->student_name ?? '?', 0, 1)) }}</span>
                <div>
                  <strong style="display:block;font-size:.88rem">{{ $ps->student_name }}</strong>
                  <span style="font-size:.78rem;color:var(--muted)">{{ $ps->student_class }}</span>
                </div>
              </div>
            </td>
            <td>{{ $ps->relationship ?? '-' }}</td>
            <td>
              @if ($ps->is_primary)
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Primer</span>
              @else
                <span style="color:var(--muted);font-size:.85rem">-</span>
              @endif
            </td>
            <td>
              <form method="POST" action="{{ route('admin.parent-student.destroy') }}" style="display:inline">
                @csrf @method('DELETE')
                <input type="hidden" name="parent_id" value="{{ $ps->parent_id }}">
                <input type="hidden" name="student_id" value="{{ $ps->student_id }}">
                <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 10px;font-size:.78rem;color:#ef4444" onclick="return confirm('Hapus hubungan ini?')">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--muted)">Belum ada hubungan terdaftar.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<section class="portal-panel" style="max-width:560px;margin-top:20px">
  <div style="padding:24px">
    <h3 style="margin:0 0 16px">Tambah Hubungan</h3>
    <form method="POST" action="{{ route('admin.parent-student.store') }}">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="field">
          <label for="parent_id">Orang Tua <span style="color:#ef4444">*</span></label>
          <select id="parent_id" name="parent_id" required>
            <option value="">-- Pilih Orang Tua --</option>
            @foreach ($parents as $parent)
              <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                {{ $parent->full_name ?: $parent->name }} ({{ $parent->students->count() }} siswa)
              </option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label for="student_id">Siswa <span style="color:#ef4444">*</span></label>
          <select id="student_id" name="student_id" required>
            <option value="">-- Pilih Siswa --</option>
            @foreach ($students as $student)
              <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                {{ $student->full_name }} ({{ $student->class_name }})
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr auto;gap:14px;align-items:end;margin-top:14px">
        <div class="field">
          <label for="relationship">Hubungan</label>
          <input id="relationship" name="relationship" type="text" value="{{ old('relationship') }}" placeholder="Contoh: Ayah, Ibu, Wali">
        </div>
        <div class="field" style="display:flex;align-items:center;gap:8px;padding-top:4px">
          <input type="checkbox" name="is_primary" value="1" id="is_primary" {{ old('is_primary') ? 'checked' : '' }}>
          <label for="is_primary" style="margin:0;font-size:.88rem">Primer</label>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:18px">Tambah Hubungan</button>
    </form>
  </div>
</section>
@endsection
