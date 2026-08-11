@php
    $cat = ($book->category ?? 'Perpustakaan') !== '' ? $book->category : 'Perpustakaan';
    $size = $size ?? 'lg';

    $palettes = [
        ['linear-gradient(135deg,#f59e0b,#92400e)', '#fff7ed'],
        ['linear-gradient(135deg,#3b82f6,#1e40af)', '#eff6ff'],
        ['linear-gradient(135deg,#8b5cf6,#5b21b6)', '#f5f3ff'],
        ['linear-gradient(135deg,#10b981,#047857)', '#ecfdf5'],
        ['linear-gradient(135deg,#ef4444,#991b1b)', '#fef2f2'],
        ['linear-gradient(135deg,#0ea5e9,#0369a1)', '#f0f9ff'],
        ['linear-gradient(135deg,#14b8a6,#0f766e)', '#f0fdfa'],
        ['linear-gradient(135deg,#f97316,#9a3412)', '#fff7ed'],
        ['linear-gradient(135deg,#6366f1,#4338ca)', '#eef2ff'],
        ['linear-gradient(135deg,#ec4899,#9d174d)', '#fdf2f8'],
    ];
    $idx = crc32(strtolower($cat)) % count($palettes);
    $grad = $palettes[$idx][0];
    $fg = $palettes[$idx][1];
@endphp

@once
@push('styles')
<style>
  .bcover {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    background: color-mix(in srgb, var(--muted, #94a3b8) 18%, #e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .bcover img { width: 100%; height: 100%; object-fit: cover; }
  .bcover-auto {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 14px;
    text-align: left;
  }
  .bcover-auto .bcover-cat {
    align-self: flex-start;
    font-size: .62rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 4px 9px;
    border-radius: 7px;
    background: rgba(0,0,0,.18);
  }
  .bcover-auto .bcover-body {
    display: flex;
    flex-direction: column;
    gap: 5px;
    flex: 1;
    justify-content: center;
  }
  .bcover-auto .bcover-title {
    font-family: 'Calistoga', serif;
    font-size: 1.02rem;
    line-height: 1.25;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .bcover-auto .bcover-author {
    font-size: .7rem;
    font-weight: 700;
    opacity: .85;
  }
  .bcover-auto .bcover-brand {
    font-size: .6rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
    opacity: .6;
  }
  .bcover-lg { width: 100%; aspect-ratio: 2 / 3; }
  .bcover-sm { width: 100%; height: 58px; aspect-ratio: 2 / 3; border-radius: 8px; }
  .bcover-sm .bcover-title { font-size: .8rem !important; -webkit-line-clamp: 3; }
  .bcover-sm .bcover-cat, .bcover-sm .bcover-brand, .bcover-sm .bcover-author { display: none; }
</style>
@endpush
@endonce

@if ($book->cover_path)
<div class="bcover bcover-{{ $size }}">
  <img src="{{ asset('storage/' . $book->cover_path) }}" alt="Cover {{ $book->title }}" loading="lazy">
</div>
@else
<div class="bcover bcover-{{ $size }} bcover-auto" style="background:{{ $grad }};color:{{ $fg }}">
  <span class="bcover-cat">{{ $cat }}</span>
  <div class="bcover-body">
    <strong class="bcover-title">{{ $book->title ?: 'Tanpa Judul' }}</strong>
    <span class="bcover-author">{{ $book->author ?: 'Perpustakaan' }}</span>
  </div>
  <span class="bcover-brand">InvestaSchool</span>
</div>
@endif