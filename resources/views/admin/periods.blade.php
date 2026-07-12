@extends('layouts.admin')

@section('title', 'Periode Akademik')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Manajemen akademik</span>
    <h1>Periode Akademik</h1>
    <p>Kelola tahun ajaran dan semester. Aktifkan periode yang berlaku.</p>
  </div>
</div>

<section class="portal-panel">
  <div class="table-wrap">
    <table class="grade-table">
      <thead>
        <tr>
          <th>Tahun Ajaran</th>
          <th>Semester</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Penugasan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($periods as $period)
          <tr>
            <td><strong>{{ $period->academic_year }}</strong></td>
            <td style="text-transform:capitalize">{{ $period->semester }}</td>
            <td style="font-size:.85rem">{{ $period->start_date->format('d M Y') }} — {{ $period->end_date->format('d M Y') }}</td>
            <td>
              @if ($period->is_active)
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--success) 12%,var(--card));color:var(--success)">Aktif</span>
              @else
                <span style="padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:700;background:color-mix(in srgb,var(--muted) 12%,var(--card));color:var(--muted)">Nonaktif</span>
              @endif
            </td>
            <td style="font-size:.85rem">{{ $period->teaching_assignments_count }} penugasan</td>
            <td>
              <div style="display:flex;gap:6px;flex-wrap:wrap">
                @if (!$period->is_active)
                  <form method="POST" action="{{ route('admin.periods.activate', $period) }}" style="display:inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 10px;font-size:.78rem;color:var(--success)">Aktifkan</button>
                  </form>
                @endif
                <form method="POST" action="{{ route('admin.periods.destroy', $period) }}" style="display:inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-outline" style="min-height:32px;padding:0 10px;font-size:.78rem;color:#ef4444" onclick="return confirm('Hapus periode {{ $period->academic_year }} {{ $period->semester }}?')">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada periode akademik.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="padding:16px">{{ $periods->links() }}</div>
</section>

<section class="portal-panel" style="max-width:560px;margin-top:20px">
  <div style="padding:24px">
    <h3 style="margin:0 0 16px">Tambah Periode Akademik</h3>
    <form method="POST" action="{{ route('admin.periods.store') }}">
      @csrf
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="field">
          <label for="academic_year">Tahun Ajaran</label>
          <input id="academic_year" name="academic_year" type="text" required placeholder="2026/2027" value="{{ old('academic_year') }}">
        </div>
        <div class="field">
          <label for="semester">Semester</label>
          <select id="semester" name="semester" required>
            <option value="ganjil" {{ old('semester') === 'ganjil' ? 'selected' : '' }}>Ganjil</option>
            <option value="genap" {{ old('semester') === 'genap' ? 'selected' : '' }}>Genap</option>
          </select>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
        <div class="field">
          <label for="start_date">Tanggal Mulai</label>
          <input id="start_date" name="start_date" type="date" required value="{{ old('start_date') }}">
        </div>
        <div class="field">
          <label for="end_date">Tanggal Selesai</label>
          <input id="end_date" name="end_date" type="date" required value="{{ old('end_date') }}">
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:18px">Tambah Periode</button>
    </form>
  </div>
</section>
@endsection
