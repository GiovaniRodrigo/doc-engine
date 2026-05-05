@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <div class="docs-page-header">
        <div>
            <h1 class="docs-page-title">Busca</h1>
            <p class="docs-page-subtitle">
                {{ $query !== '' ? 'Resultados para "' . $query . '"' : 'Digite um termo para pesquisar.' }}
            </p>
        </div>
    </div>

    <form method="GET" action="/docs/search" class="docs-search-page-form">
        <label for="docs-search-page-input" class="docs-label">Pesquisar documentação</label>
        <div class="docs-search-page-row">
            <input
                id="docs-search-page-input"
                name="q"
                value="{{ $query }}"
                class="docs-search-input docs-search-input-large"
                type="search"
                autofocus
            >

            <button type="submit" class="docs-button docs-button-primary">Buscar</button>
        </div>
    </form>

    @if ($query !== '' && count($results) === 0)
        <section class="docs-empty-state docs-empty-state-compact">
            <h2 class="docs-empty-title">Sem resultados</h2>
            <p class="docs-empty-description">
                Nenhum documento publicado corresponde a esse termo.
            </p>
        </section>
    @endif

    @if (count($results))
        <div class="docs-search-results">
            @foreach ($results as $result)
                <article class="docs-search-result">
                    <a href="/docs/{{ $result['slug'] }}" class="docs-search-result-title">
                        {{ $result['title'] }}
                    </a>

                    <p class="docs-search-result-slug">{{ $result['slug'] }}</p>

                    @if ($result['excerpt'] !== '')
                        <p class="docs-search-result-excerpt">{{ $result['excerpt'] }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection
