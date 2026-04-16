@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <div class="docs-page-actions">
        <a
            href="/docs/{{ $slug }}/edit"
            class="docs-button docs-button-secondary"
        >
            Editar
        </a>
    </div>

    @if (count($breadcrumb))
        <nav class="docs-breadcrumb" aria-label="Breadcrumb">
            @foreach ($breadcrumb as $item)
                <a href="/docs/{{ $item['slug'] }}">
                    {{ $item['title'] }}
                </a>

                @if (!$loop->last)
                    <span class="docs-breadcrumb-separator">/</span>
                @endif
            @endforeach
        </nav>
    @endif

    <article class="docs-article">
        {!! $html !!}
    </article>

    @if ($nav['prev'] || $nav['next'])
        <div class="docs-pagination">
            <div>
                @if ($nav['prev'])
                    <a href="/docs/{{ $nav['prev'] }}" class="docs-pagination-link">
                        ← {{ $nav['prev'] }}
                    </a>
                @endif
            </div>

            <div class="docs-pagination-next">
                @if ($nav['next'])
                    <a href="/docs/{{ $nav['next'] }}" class="docs-pagination-link">
                        {{ $nav['next'] }} →
                    </a>
                @endif
            </div>
        </div>
    @endif
@endsection
