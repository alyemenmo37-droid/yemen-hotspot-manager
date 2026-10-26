@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" style="display: flex; justify-content: center; gap: 5px; flex-wrap: wrap;">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 8px 12px; background: #eee; color: #999; border-radius: 6px;">←</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 12px; background: white; color: #333; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">←</a>
        @endif
        
        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding: 8px 12px; background: white; color: #333; border-radius: 6px;">{{ $element }}</span>
            @endif
            
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding: 8px 12px; background: #009639; color: white; border-radius: 6px; font-weight: bold;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 8px 12px; background: white; color: #333; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
        
        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 12px; background: white; color: #333; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">→</a>
        @else
            <span style="padding: 8px 12px; background: #eee; color: #999; border-radius: 6px;">→</span>
        @endif
    </nav>
@endif
