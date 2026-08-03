<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Rapor {{ $report['name'] }} - {{ $school['name'] }}</title>
  <link rel="icon" href="{{ asset('img/logo.svg') }}">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #eef1f5; font-family: 'Segoe UI', Arial, sans-serif; }
    .toolbar {
      position: sticky; top: 0; z-index: 10;
      display: flex; align-items: center; justify-content: space-between; gap: 12px;
      padding: 10px 20px; background: #ffffff; border-bottom: 1px solid #dbe0e6;
    }
    .toolbar-info { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .toolbar-info img { width: 30px; height: 30px; }
    .toolbar-info strong { font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .toolbar-info span { font-size: 12px; color: #6b7280; }
    .toolbar-actions { display: flex; gap: 8px; }
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 8px 16px; border-radius: 8px; border: 1px solid transparent;
      font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
    }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-outline { background: #fff; color: #374151; border-color: #d1d5db; }
    .btn-outline:hover { background: #f3f4f6; }
    .paper {
      width: 210mm; min-height: 297mm; margin: 24px auto;
      background: #fff; padding: 20mm 18mm; box-shadow: 0 2px 16px rgba(0,0,0,.12);
    }
    @media print {
      body { background: #fff; }
      .toolbar { display: none; }
      .paper { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
      @page { size: A4 portrait; margin: 16mm 15mm; }
    }
  </style>
</head>
<body>
  <div class="toolbar">
    <div class="toolbar-info">
      <img src="{{ asset('img/logo.svg') }}" alt="logo">
      <div>
        <strong>{{ $report['name'] }}</strong>
        <br><span>{{ $report['class'] }} &bull; NISN {{ $report['nisn'] }} &bull; Semester {{ $report['semester'] }} {{ $report['academic_year'] }}</span>
      </div>
    </div>
    <div class="toolbar-actions">
      <a href="{{ route('guru.wali.rapor.pdf', $report['id']) }}" class="btn btn-primary">
        Unduh PDF
      </a>
      <button type="button" class="btn btn-outline" onclick="window.print()">
        Cetak / Simpan PDF
      </button>
      <a href="{{ route('guru.wali.rapor', ['student_id' => $report['id']]) }}" class="btn btn-outline">Kembali</a>
    </div>
  </div>

  <div class="paper">
    @include('guru.wali.rapor-document')
  </div>
</body>
</html>
