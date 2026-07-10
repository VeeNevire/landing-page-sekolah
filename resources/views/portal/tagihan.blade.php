@extends('layouts.portal')

@section('title', 'Tagihan')

@section('content')
@if (!$selectedStudent)
  <div class="portal-empty">
    <h2>Belum ada siswa terdaftar</h2>
    <p>Hubungi admin sekolah untuk menautkan akun Anda dengan data siswa.</p>
  </div>
@else
    <div class="portal-heading">
      <div>
        <span class="kicker">Pembayaran</span>
        <h1>Tagihan</h1>
        <p>Rekap tagihan dan status pembayaran {{ $demoStudent['name'] }} Semester {{ $demoStudent['semester'] }}.</p>
      </div>
    </div>

    <div class="report-profile">
      <span class="student-avatar">{{ $demoStudent['initials'] }}</span>
      <div>
        <h2>{{ $demoStudent['name'] }}</h2>
        <p>NISN {{ $demoStudent['nisn'] }} &bull; {{ $demoStudent['class'] }} &bull; {{ $demoStudent['program'] }}</p>
      </div>
    </div>

    <section class="portal-kpis" style="margin-bottom:20px">
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Total Tagihan</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></span></div>
        <strong class="portal-kpi-value">Rp{{ number_format($totalAmount, 0, ',', '.') }}</strong>
        <span class="portal-kpi-note">{{ count($billing) }} item</span>
      </article>
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Sudah Dibayar</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span></div>
        <strong class="portal-kpi-value" style="color:var(--success)">Rp{{ number_format($paidAmount, 0, ',', '.') }}</strong>
        <span class="portal-kpi-note good">Lunas</span>
      </article>
      <article class="portal-kpi">
        <div class="portal-kpi-label"><span>Belum Dibayar</span><span class="kpi-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></span></div>
        <strong class="portal-kpi-value" style="color:var(--danger)">Rp{{ number_format($unpaidAmount, 0, ',', '.') }}</strong>
        <span class="portal-kpi-note" style="color:var(--danger)">Menunggu pembayaran</span>
      </article>
    </section>

    <section class="portal-panel">
      <div class="portal-panel-header"><div><h2>Rincian Tagihan</h2><p>Semua tagihan terkait biaya pendidikan semester berjalan.</p></div></div>
      <div class="table-wrap">
        <table class="grade-table">
          <thead>
            <tr><th>Nama Tagihan</th><th>Jumlah</th><th>Jatuh Tempo</th><th>Status</th></tr>
          </thead>
          <tbody>
            @foreach ($billing as $item)
              <tr>
                <td><strong>{{ $item['name'] }}</strong></td>
                <td>Rp{{ number_format($item['amount'], 0, ',', '.') }}</td>
                <td>{{ date('d M Y', strtotime($item['date'])) }}</td>
                <td>
                  @if ($item['status'] === 'lunas')
                    <span class="status-pass">Lunas</span>
                  @else
                    <span class="status-remedial">Belum Bayar</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
@endif
@endsection
