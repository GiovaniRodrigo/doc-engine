@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @php
        $toc = $toc ?? [];
    @endphp

    <div class="docs-page-actions">
        <a
            href="/docs/{{ $slug }}/versions"
            class="docs-button docs-button-secondary"
        >
            Versões
        </a>

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

    <div class="docs-readable-layout">
        <article class="docs-article">
            {!! $html !!}
        </article>

        @if (count($toc))
            <aside class="docs-toc" aria-label="Sumário do documento">
                <h2 class="docs-toc-title">Nesta página</h2>

                <nav>
                    @foreach ($toc as $item)
                        <a
                            href="#{{ $item['id'] }}"
                            class="docs-toc-link docs-toc-level-{{ $item['level'] }}"
                        >
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>
        @endif
    </div>

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
