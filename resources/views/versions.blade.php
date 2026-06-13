@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @if (count($breadcrumb))
        <nav class="docs-breadcrumb" aria-label="Breadcrumb">
            @foreach ($breadcrumb as $item)
                <a href="/docs/{{ $item['slug'] }}">{{ $item['title'] }}</a>

                @if (!$loop->last)
                    <span class="docs-breadcrumb-separator">/</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="docs-page-header">
        <div>
            <h1 class="docs-page-title">Histórico de versões</h1>
            <p class="docs-page-subtitle">{{ $slug }}</p>
        </div>

        <a href="{{ url('/docs/' . $slug) }}" class="docs-button docs-button-secondary">Voltar</a>
    </div>

    @if (session('documentation_engine_status'))
        <div class="docs-alert docs-alert-success">
            {{ session('documentation_engine_status') }}
        </div>
    @endif

    @if (count($versions) >= 2)
        <div class="docs-page-actions">
            <a
                href="{{ url('/docs/' . $slug . '/versions/compare?from=' . $versions[1]->version . '&to=' . $versions[0]->version) }}"
                class="docs-button docs-button-secondary"
            >
                Comparar últimas versões
            </a>
        </div>
    @endif

    <div class="docs-version-list">
        @foreach ($versions as $version)
            <article class="docs-version-item">
                <div>
                    <h2 class="docs-version-title">{{ $version->version }}</h2>
                    <p class="docs-version-meta">
                        {{ $version->createdAt ?? 'sem data' }}
                        @if ($version->gitCommit)
                            · {{ $version->gitCommit }}
                        @endif
                    </p>
                </div>

                <div class="docs-version-actions">
                    <span class="docs-badge docs-badge-{{ $version->state }}">
                        {{ $version->state }}
                    </span>

                    @if ($version->state !== 'published')
                        <form method="POST" action="{{ url('/docs/' . $slug . '/versions/' . $version->version . '/publish') }}">
                            @csrf
                            <button type="submit" class="docs-button docs-button-primary">
                                Publicar
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endsection
