@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <div class="docs-page-header">
        <div>
            <h1 class="docs-page-title">{{ __('documentation-engine::messages.search_title') }}</h1>
            <p class="docs-page-subtitle">
                {{ $query !== '' ? __('documentation-engine::messages.search_results', ['query' => $query]) : __('documentation-engine::messages.search_prompt') }}
            </p>
        </div>
    </div>

    <form method="GET" action="{{ url('/docs/search') }}" class="docs-search-page-form">
        <label for="docs-search-page-input" class="docs-label">{{ __('documentation-engine::messages.search_aria_label') }}</label>
        <div class="docs-search-page-row">
            <input
                id="docs-search-page-input"
                name="q"
                value="{{ $query }}"
                class="docs-search-input docs-search-input-large"
                type="search"
                autofocus
            >

            <button type="submit" class="docs-button docs-button-primary">{{ __('documentation-engine::messages.search') }}</button>
        </div>
    </form>

    @if ($query !== '' && count($results) === 0)
        <section class="docs-empty-state docs-empty-state-compact">
            <h2 class="docs-empty-title">{{ __('documentation-engine::messages.no_results') }}</h2>
            <p class="docs-empty-description">
                {{ __('documentation-engine::messages.no_results_description') }}
            </p>
        </section>
    @endif

    @if (count($results))
        <div class="docs-search-results">
            @foreach ($results as $result)
                <article class="docs-search-result">
                    <a href="{{ url('/docs/' . $result['slug']) }}" class="docs-search-result-title">
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
