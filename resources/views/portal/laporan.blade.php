@extends('layouts.portal')

@section('title', 'Laporan Perkembangan')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Laporan perkembangan siswa</span>
        <h1>Nilai & Evaluasi Semester</h1>
        <p>Rekap komponen akademik, kehadiran, karakter, kegiatan, serta catatan tindak lanjut.</p>
      </div>
      <div class="portal-actions no-print">
        <button class="btn btn-outline" onclick="window.print()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Cetak / Simpan PDF
        </button>
        <a class="btn btn-primary" href="{{ route('portal.laporan.csv', ['student_id' => $selectedStudentId]) }}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Unduh CSV
        </a>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
        <p>Semester {{ $demoStudent['semester'] }} &bull; Tahun Ajaran {{ $demoStudent['academic_year'] }} &bull; Wali Kelas {{ $demoStudent['homeroom_teacher'] }}</p>
      </div>
    </div>

    <section class="portal-kpis" style="margin-bottom:20px">
      <article class="portal-kpi"><div class="portal-kpi-label"><span>Rata-rata Akhir</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg></span></div><strong class="portal-kpi-value">{{ number_format($average, 1, ',', '.') }}</strong><span class="portal-kpi-note">Predikat {{ \App\Helpers\PortalHelper::gradeLetter($average) }}</span></article>
      <article class="portal-kpi"><div class="portal-kpi-label"><span>KKM Sekolah</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg></span></div><strong class="portal-kpi-value">{{ $demoStudent['kkm'] }}</strong><span class="portal-kpi-note good">Batas ketuntasan minimum</span></article>
      <article class="portal-kpi"><div class="portal-kpi-label"><span>Kehadiran</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span></div><strong class="portal-kpi-value">{{ number_format($attendanceRate, 1, ',', '.') }}%</strong><span class="portal-kpi-note">{{ $demoStudent['attendance']['present'] }} hari hadir</span></article>
      <article class="portal-kpi"><div class="portal-kpi-label"><span>Mata Pelajaran</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20"/></svg></span></div><strong class="portal-kpi-value">{{ count($subjects) }}</strong><span class="portal-kpi-note">Seluruh nilai telah direkap</span></article>
    </section>

    <section class="portal-panel">
      <div class="portal-panel-header">
        <div><h2>Komponen dan Bobot Penilaian</h2><p>Nilai akhir dihitung otomatis menggunakan bobot berikut.</p></div>
      </div>
      <div class="assessment-weights">
        <div class="weight-chip"><strong>15%</strong><span>Kuis</span></div>
        <div class="weight-chip"><strong>20%</strong><span>PR / Tugas</span></div>
        <div class="weight-chip"><strong>20%</strong><span>Proyek / Praktik</span></div>
        <div class="weight-chip"><strong>20%</strong><span>UTS</span></div>
        <div class="weight-chip"><strong>25%</strong><span>UAS</span></div>
      </div>

      <div class="no-print" style="max-width:300px;margin-bottom:18px">
        <label for="subjectFilter">Filter mata pelajaran</label>
        <select id="subjectFilter">
          <option value="all">Semua mata pelajaran</option>
          @foreach ($subjects as $subject)
            <option value="{{ $subject['code'] }}">{{ $subject['name'] }}</option>
          @endforeach
        </select>
      </div>

      <div class="table-wrap">
        <table class="grade-table">
          <thead>
            <tr>
              <th>Mata Pelajaran</th><th>Kuis</th><th>PR/Tugas</th><th>Proyek/Praktik</th><th>UTS</th><th>UAS</th><th>Nilai Akhir</th><th>Predikat</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($subjects as $subject)
              @php
                $scores = \App\Helpers\PortalHelper::componentScores($subject);
                $final = \App\Helpers\PortalHelper::finalScore($subject);
                $pass = $final >= (float)$demoStudent['kkm'];
              @endphp
              <tr data-subject-row="{{ $subject['code'] }}">
                <td><strong>{{ $subject['name'] }}</strong><br><small style="color:var(--muted)">{{ $subject['teacher'] }}</small></td>
                <td>{{ number_format($scores['quiz'], 1, ',', '.') }}</td>
                <td>{{ number_format($scores['homework'], 1, ',', '.') }}</td>
                <td>{{ number_format($scores['project'], 1, ',', '.') }}</td>
                <td>{{ number_format($scores['uts'], 1, ',', '.') }}</td>
                <td>{{ number_format($scores['uas'], 1, ',', '.') }}</td>
                <td><strong>{{ number_format($final, 1, ',', '.') }}</strong></td>
                <td><span class="grade-badge {{ \App\Helpers\PortalHelper::gradeClass($final) }}">{{ \App\Helpers\PortalHelper::gradeLetter($final) }}</span></td>
                <td><span class="{{ $pass ? 'status-pass' : 'status-remedial' }}">{{ $pass ? 'Tuntas' : 'Perlu Remedial' }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>

    <section class="portal-panel" style="margin-top:20px">
      <div class="portal-panel-header"><div><h2>Rincian Skor Penilaian</h2><p>Nilai setiap kuis, PR/tugas, proyek/praktik, dan ujian.</p></div></div>
      <div class="assessment-details">
        @foreach ($subjects as $subject)
          @php $final = \App\Helpers\PortalHelper::finalScore($subject); @endphp
          <details data-subject-detail="{{ $subject['code'] }}">
            <summary>{{ $subject['name'] }} — Nilai Akhir {{ number_format($final, 1, ',', '.') }}</summary>
            <div class="detail-score-grid">
              <div class="detail-score-card"><strong>Kuis</strong><span>Bobot 15%</span><ul>@foreach ($subject['quiz'] as $i => $score)<li>Kuis {{ $i + 1 }}: <b>{{ $score }}</b></li>@endforeach</ul></div>
              <div class="detail-score-card"><strong>PR / Tugas</strong><span>Bobot 20%</span><ul>@foreach ($subject['homework'] as $i => $score)<li>Tugas {{ $i + 1 }}: <b>{{ $score }}</b></li>@endforeach</ul></div>
              <div class="detail-score-card"><strong>Proyek / Praktik</strong><span>Bobot 20%</span><ul>@foreach ($subject['project'] as $i => $score)<li>Proyek {{ $i + 1 }}: <b>{{ $score }}</b></li>@endforeach</ul></div>
              <div class="detail-score-card"><strong>UTS</strong><span>Bobot 20%</span><ul><li>Skor: <b>{{ $subject['uts'] }}</b></li></ul></div>
              <div class="detail-score-card"><strong>UAS</strong><span>Bobot 25%</span><ul><li>Skor: <b>{{ $subject['uas'] }}</b></li></ul></div>
            </div>
            <div class="portal-note" style="margin-bottom:17px"><strong>Catatan Guru &bull; {{ $subject['mastery'] }}</strong><p>{{ $subject['note'] }}</p></div>
          </details>
        @endforeach
      </div>
    </section>

    <div class="portal-dashboard-grid" id="kehadiran">
      <section class="portal-panel">
        <div class="portal-panel-header"><div><h2>Rekap Kehadiran</h2><p>Semester berjalan berdasarkan pencatatan sekolah.</p></div></div>
        <div class="attendance-grid">
          <div class="attendance-box"><strong>{{ $demoStudent['attendance']['present'] }}</strong><span>Hadir</span></div>
          <div class="attendance-box"><strong>{{ $demoStudent['attendance']['sick'] }}</strong><span>Sakit</span></div>
          <div class="attendance-box"><strong>{{ $demoStudent['attendance']['excused'] }}</strong><span>Izin</span></div>
          <div class="attendance-box"><strong>{{ $demoStudent['attendance']['unexcused'] }}</strong><span>Tanpa Keterangan</span></div>
        </div>
        <div class="portal-note" style="margin-top:18px"><strong>Tingkat kehadiran {{ number_format($attendanceRate, 1, ',', '.') }}%</strong><p>Kehadiran berada dalam kategori sangat baik.</p></div>
      </section>

      <section class="portal-panel" id="karakter">
        <div class="portal-panel-header"><div><h2>Karakter & Sikap</h2><p>Pengamatan wali kelas dan guru.</p></div></div>
        <div class="competency-list">
          @foreach ($demoStudent['behavior'] as $label => $value)
            <div class="competency-row">
              <span class="competency-label">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
              <span class="competency-value">{{ $value }}</span>
            </div>
          @endforeach
        </div>
      </section>
    </div>

    <div class="portal-dashboard-grid">
      <section class="portal-panel">
        <div class="portal-panel-header"><div><h2>Kegiatan Ekstrakurikuler</h2><p>Penilaian partisipasi dan pengembangan minat.</p></div></div>
        <div class="activity-feed">
          @foreach ($demoStudent['extracurricular'] as $item)
            <div class="activity-item">
              <span class="activity-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
              <div><strong>{{ $item['name'] }} &bull; {{ $item['score'] }}</strong><span>{{ $item['note'] }}</span></div>
            </div>
          @endforeach
        </div>
      </section>
      <section class="portal-panel" id="catatan">
        <div class="portal-panel-header"><div><h2>Catatan & Tindak Lanjut</h2><p>{{ $demoStudent['homeroom_teacher'] }}</p></div></div>
        <div class="portal-note"><strong>Catatan wali kelas</strong><p>{{ $demoStudent['teacher_note'] }}</p></div>
        <div style="margin-top:17px"><strong>Rekomendasi pendampingan orang tua</strong><p style="color:var(--muted)">Diskusikan target belajar mingguan, berikan ruang latihan mandiri, dan pantau konsistensi penyelesaian tugas melalui portal.</p></div>
      </section>
    </div>

    <p class="security-note">Laporan ini merupakan data demonstrasi. Dokumen resmi harus divalidasi dan ditandatangani sesuai kebijakan sekolah.</p>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('subjectFilter')?.addEventListener('change', function() {
  var val = this.value;
  document.querySelectorAll('[data-subject-row]').forEach(function(row) {
    row.style.display = (val === 'all' || row.dataset.subjectRow === val) ? '' : 'none';
  });
  document.querySelectorAll('[data-subject-detail]').forEach(function(detail) {
    detail.style.display = (val === 'all' || detail.dataset.subjectDetail === val) ? '' : 'none';
  });
});
</script>
@endpush



