@extends('layouts.guru')

@section('title', 'Catatan Siswa')

@section('content')
<div class="portal-heading">
  <div>
    <span class="kicker">Catatan</span>
    <h1>Catatan Siswa</h1>
    <p>Buat dan lihat catatan perkembangan untuk siswa yang Anda ajar.</p>
  </div>
</div>

@if (session('success'))
  <div style="padding:12px 16px;border-radius:12px;background:#d1fae5;color:#065f46;font-weight:700;margin-bottom:16px">{{ session('success') }}</div>
@endif

<div class="portal-dashboard-grid">
  <section class="portal-panel">
    <div class="portal-panel-header">
      <div><h2>Buat Catatan Baru</h2><p>Pilih siswa dan tulis catatan perkembangan.</p></div>
    </div>
    <form method="POST" action="{{ route('guru.catatan.store') }}">
      @csrf
      <div class="field" style="margin-bottom:14px">
        <label>Pilih Siswa</label>
        <select name="student_id" required>
          <option value="">— Pilih Siswa —</option>
          @foreach ($students as $student)
            <option value="{{ $student->id }}" @selected($selectedStudent == $student->id)>{{ $student->full_name }} — {{ $student->class_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label>Kategori</label>
        <select name="category" required>
          <option value="academic">Akademik</option>
          <option value="behavior">Perilaku</option>
          <option value="career">Karir</option>
          <option value="general">Umum</option>
        </select>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label>Catatan</label>
        <textarea name="note" required placeholder="Tulis catatan perkembangan siswa..." style="min-height:100px">{{ old('note') }}</textarea>
      </div>
      <div class="field" style="margin-bottom:14px">
        <label>Tindak Lanjut (opsional)</label>
        <input name="follow_up" placeholder="Rencana tindak lanjut..." value="{{ old('follow_up') }}">
      </div>
      <button class="btn btn-primary" type="submit" style="width:100%">Simpan Catatan</button>
    </form>
  </section>

  <section class="portal-panel">
    <div class="portal-panel-header">
      <div><h2>Riwayat Catatan</h2><p>{{ $existingNotes->count() }} catatan untuk siswa ini.</p></div>
    </div>
    @if ($existingNotes->count())
      <div class="activity-feed">
        @foreach ($existingNotes as $note)
          <div class="activity-item">
            <span class="activity-icon" style="background:
              @if ($note->category === 'academic') color-mix(in srgb,var(--primary-2) 14%,var(--card))
              @elseif ($note->category === 'behavior') color-mix(in srgb,var(--accent) 14%,var(--card))
              @elseif ($note->category === 'career') color-mix(in srgb,var(--success) 14%,var(--card))
              @else var(--bg) @endif
            ">
              @if ($note->category === 'academic')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 20 4-4-4-4"/><path d="M20 16H9a4 4 0 0 1-4-4V4"/></svg>
              @elseif ($note->category === 'behavior')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
              @else
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              @endif
            </span>
            <div>
              <strong>{{ ucfirst($note->category) }} — {{ $note->created_at->format('d M Y') }}</strong>
              <span style="display:block;color:var(--muted);font-size:.88rem;margin-top:4px">{{ $note->note }}</span>
              @if ($note->follow_up)
                <span style="display:block;margin-top:6px;padding:8px 12px;border-radius:8px;background:var(--bg);font-size:.82rem"><strong>Tindak lanjut:</strong> {{ $note->follow_up }}</span>
              @endif
              <span style="font-size:.78rem;color:var(--muted);display:block;margin-top:4px">oleh {{ $note->author->full_name ?? $note->author->name }}</span>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="portal-empty" style="padding:30px;text-align:center">
        <p style="color:var(--muted)">{{ $selectedStudent ? 'Belum ada catatan untuk siswa ini.' : 'Pilih siswa untuk melihat catatan.' }}</p>
      </div>
    @endif
  </section>
</div>
@endsection
