@extends('layouts.alumni')

@section('title', 'Dashboard')

@section('content')
<div class="portal-heading">
  <div>
    <h1>Halo, {{ $student->full_name }}!</h1>
    <p>Selamat datang di Portal Alumni InvestaSchool. Kelola profil, sertifikat, dan proyek portofolio Anda.</p>
  </div>
</div>

<div class="portal-kpis" style="margin-bottom: 30px;">
  <div class="portal-kpi">
    <span class="portal-kpi-label">
      <span>Tahun Kelulusan</span>
      <span class="kpi-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
      </span>
    </span>
    <span class="portal-kpi-value">{{ $stats['graduation_year'] ?? '—' }}</span>
    <span class="portal-kpi-note">Program Keahlian: {{ $stats['program'] ?? '—' }}</span>
  </div>
  <div class="portal-kpi">
    <span class="portal-kpi-label">
      <span>Sertifikat Keterampilan</span>
      <span class="kpi-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="8" r="7"/>
          <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>
        </svg>
      </span>
    </span>
    <span class="portal-kpi-value">{{ $stats['certificates'] }}</span>
    <span class="portal-kpi-note"><a href="{{ route('alumni.sertifikat') }}" style="color: var(--primary-2); text-decoration: none; font-weight: 600;">Kelola sertifikat &rarr;</a></span>
  </div>
  <div class="portal-kpi">
    <span class="portal-kpi-label">
      <span>Portofolio Proyek</span>
      <span class="kpi-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
          <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
        </svg>
      </span>
    </span>
    <span class="portal-kpi-value">{{ $stats['projects'] }}</span>
    <span class="portal-kpi-note"><a href="{{ route('alumni.proyek') }}" style="color: var(--primary-2); text-decoration: none; font-weight: 600;">Kelola proyek &rarr;</a></span>
  </div>
  <div class="portal-kpi">
    <span class="portal-kpi-label">
      <span>Status Alumni</span>
      <span class="kpi-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </span>
    </span>
    <span class="portal-kpi-value" style="font-size: 1.6rem; margin: 23px 0 14px; text-transform: uppercase;">
      @if ($stats['alumni_status'] === 'working')
        Bekerja
      @elseif ($stats['alumni_status'] === 'studying')
        Kuliah
      @else
        Mencari Kerja
      @endif
    </span>
    <span class="portal-kpi-note"><a href="{{ route('alumni.profil') }}" style="color: var(--primary-2); text-decoration: none; font-weight: 600;">Perbarui status &rarr;</a></span>
  </div>
</div>

<div class="portal-dashboard-grid" style="grid-template-columns: 1.2fr 0.8fr;">
  <div class="portal-panel">
    <div class="portal-panel-header">
      <h2>Sertifikat & Proyek Terbaru</h2>
    </div>
    <div style="display: grid; gap: 20px;">
      <div>
        <h4 style="margin-bottom: 10px; color: var(--muted);">Sertifikat Terakhir</h4>
        @forelse($certificates->take(2) as $cert)
          <div style="background: var(--bg); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--line); margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
            <div>
              <strong style="display:block; font-size: 0.95rem;">{{ $cert->name }}</strong>
              <small style="color: var(--muted);">Penerbit: {{ $cert->issuer }} &middot; {{ $cert->issue_date->isoFormat('D MMMM YYYY') }}</small>
            </div>
            @if($cert->certificate_file)
              <a href="{{ asset('storage/' . $cert->certificate_file) }}" target="_blank" class="btn btn-outline" style="padding: 4px 10px; height: auto; font-size: 0.8rem;">Lihat File</a>
            @elseif($cert->certificate_url)
              <a href="{{ $cert->certificate_url }}" target="_blank" class="btn btn-outline" style="padding: 4px 10px; height: auto; font-size: 0.8rem;">Lihat Link</a>
            @endif
          </div>
        @empty
          <p style="color: var(--muted); font-size: 0.9rem; font-style: italic;">Belum ada sertifikat keterampilan yang diunggah.</p>
        @endforelse
      </div>

      <div style="margin-top: 15px;">
        <h4 style="margin-bottom: 10px; color: var(--muted);">Proyek Terakhir</h4>
        @forelse($projects->take(2) as $project)
          <div style="background: var(--bg); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--line); margin-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
              <strong style="font-size: 0.95rem;">{{ $project->title }}</strong>
              <div style="display: flex; gap: 6px;">
                @if($project->github_url)
                  <a href="{{ $project->github_url }}" target="_blank" class="btn btn-outline" style="padding: 2px 6px; height: auto; font-size: 0.75rem;">GitHub</a>
                @endif
                @if($project->project_url)
                  <a href="{{ $project->project_url }}" target="_blank" class="btn btn-outline" style="padding: 2px 6px; height: auto; font-size: 0.75rem;">Demo</a>
                @endif
              </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--muted); margin: 0;">{{ Str::limit($project->description, 120) }}</p>
          </div>
        @empty
          <p style="color: var(--muted); font-size: 0.9rem; font-style: italic;">Belum ada proyek portofolio yang ditambahkan.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="portal-panel">
    <div class="portal-panel-header">
      <h2>Informasi Kelulusan</h2>
    </div>
    <div style="display: grid; gap: 15px; font-size: 0.9rem;">
      <div style="border-bottom: 1px solid var(--line); padding-bottom: 10px;">
        <span style="color: var(--muted); display: block; font-size: 0.8rem;">Nama Lengkap</span>
        <strong>{{ $student->full_name }}</strong>
      </div>
      <div style="border-bottom: 1px solid var(--line); padding-bottom: 10px;">
        <span style="color: var(--muted); display: block; font-size: 0.8rem;">NISN / NIS</span>
        <strong>{{ $student->nisn }} / {{ $student->nis ?? '—' }}</strong>
      </div>
      <div style="border-bottom: 1px solid var(--line); padding-bottom: 10px;">
        <span style="color: var(--muted); display: block; font-size: 0.8rem;">Kelas Terakhir</span>
        <strong>{{ $student->class_name }}</strong>
      </div>
      <div>
        <span style="color: var(--muted); display: block; font-size: 0.8rem;">Program Keahlian</span>
        <strong>{{ $student->program_name }}</strong>
      </div>
    </div>
  </div>
</div>
@endsection