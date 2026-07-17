@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" class="pagination-wrap">

    {{-- Mobile --}}
    <div class="pagination-mobile">
      @if ($paginator->onFirstPage())
        <span class="pagination-btn pagination-btn-disabled">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          Sebelumnya
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-btn pagination-btn-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          Sebelumnya
        </a>
      @endif

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-btn pagination-btn-link">
          Selanjutnya
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      @else
        <span class="pagination-btn pagination-btn-disabled">
          Selanjutnya
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      @endif
    </div>

    {{-- Desktop --}}
    <div class="pagination-desktop">
      <div class="pagination-info">
        <p>
          Menampilkan
          @if ($paginator->firstItem())
            <span class="pagination-highlight">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="pagination-highlight">{{ $paginator->lastItem() }}</span>
          @else
            {{ $paginator->count() }}
          @endif
          dari
          <span class="pagination-highlight">{{ $paginator->total() }}</span>
          hasil
        </p>
      </div>

      <div class="pagination-links">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
          <span class="pagination-page pagination-page-disabled" aria-disabled="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </span>
        @else
          <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-page" aria-label="Sebelumnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          </a>
        @endif

        {{-- Elements --}}
        @foreach ($elements as $element)
          @if (is_string($element))
            <span class="pagination-page pagination-page-dots" aria-disabled="true">{{ $element }}</span>
          @endif

          @if (is_array($element))
            @foreach ($element as $page => $url)
              @if ($page == $paginator->currentPage())
                <span class="pagination-page pagination-page-active" aria-current="page">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="pagination-page" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
              @endif
            @endforeach
          @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
          <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-page" aria-label="Selanjutnya">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </a>
        @else
          <span class="pagination-page pagination-page-disabled" aria-disabled="true">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </span>
        @endif
      </div>
    </div>
  </nav>
@endif



