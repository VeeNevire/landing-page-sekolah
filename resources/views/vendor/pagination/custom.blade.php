@if ($paginator->hasPages())
  <nav role="navigation" aria-label="Pagination Navigation" style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;justify-content:center;margin-top:8px">
    {{-- Mobile --}}
    <div style="display:flex;gap:8px;width:100%;justify-content:center">
      @if ($paginator->onFirstPage())
        <span class="btn btn-outline" style="opacity:.45;pointer-events:none">Sebelumnya</span>
      @else
        <a class="btn btn-outline" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
      @endif

      @if ($paginator->hasMorePages())
        <a class="btn btn-outline" href="{{ $paginator->nextPageUrl() }}" rel="next">Selanjutnya</a>
      @else
        <span class="btn btn-outline" style="opacity:.45;pointer-events:none">Selanjutnya</span>
      @endif
    </div>

    {{-- Desktop --}}
    <div style="display:flex;align-items:center;gap:6px;width:100%;justify-content:center">
      @if (!$paginator->onFirstPage())
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"
          style="display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;border-radius:11px;border:1px solid var(--line);color:var(--primary-2);background:var(--card);font-weight:800;text-decoration:none;transition:.2s">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span style="padding:0 6px;color:var(--muted);font-weight:700">{{ $element }}</span>
        @endif
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span style="display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,var(--primary-4),var(--primary-2));color:var(--text);font-weight:800">{{ $page }}</span>
            @else
              <a href="{{ $url }}" aria-label="Ke halaman {{ $page }}"
                style="display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;border-radius:11px;border:1px solid var(--line);color:var(--text);background:var(--card);font-weight:700;text-decoration:none;transition:.2s">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Selanjutnya"
          style="display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;border-radius:11px;border:1px solid var(--line);color:var(--primary-2);background:var(--card);font-weight:800;text-decoration:none;transition:.2s">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      @endif
    </div>
  </nav>
@endif