@extends('layouts.public')

@section('title', 'Perpustakaan | InvestaSchool')
@section('meta_description', 'Katalog dan koleksi buku Perpustakaan InvestaSchool. Baca ebook digital untuk siswa.')

@section('content')
{{-- PAGE HERO --}}
<section class="page-hero">
  <div class="page-hero-shapes">
    <div class="page-hero-shape page-hero-shape-1"></div>
    <div class="page-hero-shape page-hero-shape-2"></div>
  </div>
  <div class="container">
    <div class="breadcrumb">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
        <polyline points="9 22 9 12 15 12 15 22" />
      </svg>
      <span>Beranda</span>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="m9 18 6-6-6-6" />
      </svg>
      <span>Perpustakaan</span>
    </div>
    <h1>Perpustakaan</h1>
    <p class="lead">
      <span class="lead-bar"></span>
      <span>Jelajahi katalog buku InvestaSchool. Siswa dapat membaca dan mengunduh ebook digital.</span>
    </p>
  </div>
</section>

{{-- KATALOG --}}
<section class="section">
  <div class="container">
    <div class="newsbody" style="display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <span class="kicker">Katalog buku</span>
        <h2 class="section-title" style="margin-bottom:0">Koleksi Perpustakaan</h2>
      </div>
      <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
        <div class="field" style="flex:1;min-width:220px">
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, atau kategori...">
        </div>
        <select name="category" onchange="this.form.submit()" style="max-width:190px">
          <option value="">Semua Kategori</option>
          @foreach ($categories as $cat)
          <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
          @endforeach
        </select>
        <button class="btn btn-primary" type="submit">Cari</button>
        @if (request('search') || request('category'))
        <a href="{{ route('perpustakaan') }}" class="btn btn-outline">Reset</a>
        @endif
      </form>
    </div>

    @if ($books->isEmpty())
    <div class="card" style="text-align:center;padding:60px 24px;color:var(--muted)">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:.5;margin-bottom:14px">
        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
      </svg>
      <p style="font-size:1.05rem;font-weight:600">Belum ada buku yang tersedia.</p>
    </div>
    @else
    <div class="grid grid-3 mt-4">
      @foreach ($books as $book)
      <article class="card news-card reveal">
        <x-book-cover :book="$book" size="lg" />
        <div class="news-body">
          <h3>{{ $book->title }}</h3>
          <p style="font-size:.95rem;margin-bottom:8px">
            <strong style="color:var(--text)">{{ $book->author ?: 'Anonim' }}</strong>
            @if ($book->year) &middot; {{ $book->year }} @endif
          </p>
          @if ($book->description)
          <p style="-webkit-box-orient:vertical;display:-webkit-box;-webkit-line-clamp:3;overflow:hidden">{{ $book->description }}</p>
          @endif
          <div style="display:flex;align-items:center;gap:8px;margin-top:14px;flex-wrap:wrap">
            @if ($book->file_path)
              @if ($isStudent)
              <a class="btn btn-primary" style="font-size:.85rem" href="{{ route('perpustakaan.baca', $book) }}">Baca</a>
              <a class="btn btn-outline" style="font-size:.85rem" href="{{ route('perpustakaan.unduh', $book) }}">Unduh</a>
              @else
              <a class="btn btn-primary" style="font-size:.85rem" href="{{ route('login') }}?role=student">Masuk sebagai Siswa</a>
              @endif
            @else
              <span style="padding:9px 16px;border-radius:12px;font-size:.8rem;font-weight:700;background:color-mix(in srgb,var(--muted) 12%,transparent);color:var(--muted)">Tersedia di perpustakaan</span>
            @endif
          </div>
        </div>
      </article>
      @endforeach
    </div>
    <div style="margin-top:28px">{{ $books->links('vendor.pagination.custom') }}</div>
    @endif
  </div>
</section>
@endsection