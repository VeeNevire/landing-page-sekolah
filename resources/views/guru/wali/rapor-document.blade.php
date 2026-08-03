<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #111; line-height: 1.35; }

  @page { margin: 15mm 12mm; }

  .kop { text-align: center; border-bottom: 3px solid #111; padding-bottom: 6px; margin-bottom: 3px; }
  .kop-line2 { border-bottom: 1px solid #111; margin-bottom: 12px; }
  .kop .logo { position: absolute; left: 0; top: 0; height: 62px; }
  .kop .nama { font-size: 17pt; font-weight: bold; letter-spacing: .5px; }
  .kop .tagline { font-size: 8.5pt; font-weight: bold; margin-top: 2px; }
  .kop .alamat { font-size: 8.5pt; margin-top: 3px; }
  .kop .judul { font-size: 14pt; font-weight: bold; margin-top: 10px; text-decoration: underline; }
  .kop .subjudul { font-size: 11pt; font-weight: bold; margin-top: 2px; }

  h3.seksi { font-size: 10.5pt; margin: 14px 0 6px; border-bottom: 1px solid #444; padding-bottom: 2px; }

  table { width: 100%; border-collapse: collapse; }
  table.identitas td { padding: 2.5px 4px; font-size: 10pt; vertical-align: top; }
  table.identitas .label { width: 130px; }

  table.nilai { font-size: 7.8pt; }
  table.nilai th, table.nilai td { border: 1px solid #555; padding: 3px 2px; text-align: center; }
  table.nilai th { background: #eee; font-size: 7.5pt; }
  table.nilai td.mapel { text-align: left; padding-left: 5px; }

  table.kehadiran, table.sikap, table.ekskul { font-size: 9pt; }
  table.kehadiran th, table.kehadiran td,
  table.sikap th, table.sikap td,
  table.ekskul th, table.ekskul td { border: 1px solid #555; padding: 4px 6px; text-align: center; }

  /* Section kenaikan kelas & peringkat */
  table.ringkasan { font-size: 9.5pt; margin-top: 4px; page-break-inside: avoid; }
  table.ringkasan td { border: 1px solid #555; padding: 3px 8px; }
  table.ringkasan .label { width: 45%; font-weight: bold; }
  table.ringkasan .keputusan { font-weight: bold; }

  /* Catatan wali kelas - pakai tabel, bukan div, supaya tinggi minimum konsisten di dompdf */
  .catatan { margin-top: 12px; page-break-inside: avoid; }
  table.kotak-catatan { border: 1px solid #555; }
  table.kotak-catatan td { padding: 6px; font-size: 9.5pt; vertical-align: top; height: 42px; }

  table.ttd { margin-top: 14px; table-layout: fixed; page-break-inside: avoid; }
  table.ttd td { text-align: center; font-size: 9.5pt; vertical-align: top; }
  table.ttd .garis { display: block; border-bottom: 1px solid #111; height: 34px; margin: 4px 10px 0; }
  table.ttd .ket-garis { font-size: 8pt; font-style: italic; color: #333; }

  .keterangan-predikat { font-size: 8pt; margin-top: 8px; color: #333; }

  .footer-halaman { position: fixed; bottom: -12mm; left: 0; right: 0; text-align: center; font-size: 8pt; color: #555; border-top: 0.5px solid #999; padding-top: 3px; }
</style>

<div class="kop">
  @if (!empty($school['logo']))
    <img src="{{ $school['logo'] }}" class="logo">
  @endif
  <div class="nama">{{ $school['name'] }}</div>
  <div class="tagline">{{ $school['tagline'] }}</div>
  <div class="alamat">
    {{ $school['address'] }}
    <br>Telp. {{ $school['phone'] }} &bull; Email: {{ $school['email'] }} &bull; Website: {{ $school['website'] }}
    <br>NPSN: {{ $school['npsn'] }}
  </div>
  <div class="judul">RAPOR PESERTA DIDIK</div>
  <div class="subjudul">Semester {{ $report['semester'] }} &mdash; Tahun Pelajaran {{ $report['academic_year'] }}</div>
</div>
<div class="kop-line2"></div>

<table class="identitas">
  <tr>
    <td class="label">Nama Peserta Didik</td>
    <td>: <b>{{ $report['name'] }}</b></td>
    <td class="label">Kelas</td>
    <td>: {{ $report['class'] }}</td>
  </tr>
  <tr>
    <td class="label">NISN</td>
    <td>: {{ $report['nisn'] }}</td>
    <td class="label">Program</td>
    <td>: {{ $report['program'] }}</td>
  </tr>
  <tr>
    <td class="label">NIS</td>
    <td>: {{ $report['nis'] ?: '-' }}</td>
    <td class="label">Wali Kelas</td>
    <td>: {{ ucwords($report['homeroom_teacher']) }}</td>
  </tr>
  <tr>
    <td class="label">Tempat, Tanggal Lahir</td>
    <td>: {{ $report['birth_place'] ?? '-' }}, {{ $report['birth_date'] ?: '-' }}</td>
    <td class="label">Jumlah Siswa</td>
    <td>: {{ $report['class_size'] ?? '-' }} siswa</td>
  </tr>
</table>

<h3 class="seksi">A. HASIL PENILAIAN AKHIR SEMESTER</h3>
<table class="nilai">
  <thead>
    <tr>
      <th rowspan="2" style="width:3%">No</th>
      <th rowspan="2" style="width:24%">Mata Pelajaran</th>
      <th rowspan="2" style="width:6%">KKM</th>
      <th colspan="6" style="width:44%">Komponen Penilaian</th>
      <th rowspan="2" style="width:7%">Nilai<br>Akhir</th>
      <th rowspan="2" style="width:7%">Predikat</th>
      <th rowspan="2" style="width:9%">Ket.</th>
    </tr>
    <tr>
      <th style="width:7%">Kuis<br>15%</th>
      <th style="width:7%">PR<br>10%</th>
      <th style="width:7%">Tugas<br>10%</th>
      <th style="width:7%">Proyek<br>20%</th>
      <th style="width:8%">UTS<br>20%</th>
      <th style="width:8%">UAS<br>25%</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($report['subjects'] as $i => $subject)
      @php $c = $subject['components']; @endphp
      <tr>
        <td>{{ $i + 1 }}</td>
        <td class="mapel"><b>{{ $subject['name'] }}</b></td>
        <td>{{ number_format($subject['kkm'], 0) }}</td>
        <td>{{ $c['quiz'] > 0 ? number_format($c['quiz'], 1, ',', '.') : '-' }}</td>
        <td>{{ $c['homework'] > 0 ? number_format($c['homework'], 1, ',', '.') : '-' }}</td>
        <td>{{ $c['assignment'] > 0 ? number_format($c['assignment'], 1, ',', '.') : '-' }}</td>
        <td>{{ $c['project'] > 0 ? number_format($c['project'], 1, ',', '.') : '-' }}</td>
        <td>{{ $c['uts'] > 0 ? number_format($c['uts'], 1, ',', '.') : '-' }}</td>
        <td>{{ $c['uas'] > 0 ? number_format($c['uas'], 1, ',', '.') : '-' }}</td>
        <td><b>{{ number_format($subject['final'], 1, ',', '.') }}</b></td>
        <td><b>{{ $subject['letter'] }}</b></td>
        <td>{{ $subject['mastery'] }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="12">Belum ada data nilai yang dipublikasikan.</td>
      </tr>
    @endforelse
  </tbody>
</table>

@if ($report['subjects'])
<div class="keterangan-predikat">
  Rata-rata Nilai Akhir: <b>{{ number_format($report['average'], 1, ',', '.') }}</b> &nbsp;&bull;&nbsp;
  KKM Sekolah: <b>{{ number_format($report['kkm'], 1) }}</b> &nbsp;&bull;&nbsp;
  Predikat: A = 90&ndash;100, A- = 85&ndash;89, B+ = 80&ndash;84, B = 75&ndash;79, C+ = 70&ndash;74, C = 65&ndash;69, D &lt; 65
</div>
@endif

<h3 class="seksi">B. REKAPITULASI KEHADIRAN</h3>
<table class="kehadiran">
  <thead>
    <tr><th>Hadir</th><th>Sakit</th><th>Izin</th><th>Tanpa Keterangan</th><th>Terlambat</th><th>Total</th></tr>
  </thead>
  <tbody>
    <tr>
      <td>{{ $report['attendance']['present'] }}</td>
      <td>{{ $report['attendance']['sick'] }}</td>
      <td>{{ $report['attendance']['excused'] }}</td>
      <td>{{ $report['attendance']['unexcused'] }}</td>
      <td>{{ $report['attendance']['late'] }}</td>
      <td>{{ $report['attendance']['present'] + $report['attendance']['sick'] + $report['attendance']['excused'] + $report['attendance']['unexcused'] + $report['attendance']['late'] }}</td>
    </tr>
  </tbody>
</table>

<h3 class="seksi">C. SIKAP / KEPRIBADIAN</h3>
@if ($report['behavior'])
<table class="sikap">
  <thead><tr><th>Aspek</th><th>Predikat</th></tr></thead>
  <tbody>
    @foreach ($report['behavior'] as $aspect => $grade)
      <tr><td>{{ ucfirst($aspect) }}</td><td>{{ $grade }}</td></tr>
    @endforeach
  </tbody>
</table>
@else
<div style="font-size:9.5pt;border:1px solid #555;padding:6px;">Belum ada data sikap/kepribadian untuk periode ini.</div>
@endif

<h3 class="seksi">D. KEGIATAN EKSTRAKURIKULER</h3>
@if ($report['extracurricular'])
<table class="ekskul">
  <thead><tr><th style="width:8%">No</th><th>Kegiatan</th><th style="width:15%">Nilai</th><th>Keterangan</th></tr></thead>
  <tbody>
    @foreach ($report['extracurricular'] as $i => $eks)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $eks['name'] }}</td>
        <td>{{ $eks['score'] ?: '-' }}</td>
        <td>{{ $eks['note'] ?: '-' }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
@else
<div style="font-size:9.5pt;border:1px solid #555;padding:6px;">Tidak ada kegiatan ekstrakurikuler tercatat.</div>
@endif

<h3 class="seksi">E. PERINGKAT &amp; KEPUTUSAN</h3>
<table class="ringkasan">
  <tr>
    <td class="label">Peringkat di Kelas</td>
    <td>{{ $report['rank'] ?? '-' }} dari {{ $report['class_size'] ?? '-' }} siswa</td>
  </tr>
  @if (!empty($report['promotion']))
  <tr>
    <td class="label">Keputusan</td>
    <td class="keputusan">
      {{ $report['promotion']['status'] === 'promoted' ? 'Naik ke Kelas ' . $report['promotion']['next_class'] : 'Tinggal di Kelas ' . $report['class'] }}
    </td>
  </tr>
  @endif
</table>

<div class="catatan">
  <b>Catatan Wali Kelas:</b>
  <table class="kotak-catatan">
    <tr><td>{{ $report['teacher_note'] ?: '-' }}</td></tr>
  </table>
</div>

<table class="ttd">
  <tr>
    <td style="width:33%">
      Mengetahui,<br>Orang Tua / Wali
      <span class="garis"></span>
      <span class="ket-garis">(Nama &amp; Tanda Tangan)</span>
    </td>
    <td style="width:34%">
      {{ $school['city'] }}, {{ now()->isoFormat('D MMMM YYYY') }}<br>Wali Kelas
      <span class="garis"></span>
      <b>{{ ucwords($report['homeroom_teacher']) }}</b>
    </td>
    <td style="width:33%">
      <br>Kepala Sekolah
      <span class="garis"></span>
      <b>{{ ucwords($report['principal']['name']) }}</b><br>
      {{ $report['principal']['credentials'] }}<br>
      NIP. {{ $report['principal']['nip'] }}
    </td>
  </tr>
</table>

<div class="footer-halaman">
  {{ $school['name'] }} &bull; Rapor Semester {{ $report['semester'] }} {{ $report['academic_year'] }}
</div>