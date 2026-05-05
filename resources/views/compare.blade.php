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
            <h1 class="docs-page-title">Comparar versões</h1>
            <p class="docs-page-subtitle">{{ $slug }}</p>
        </div>

        <a href="/docs/{{ $slug }}/versions" class="docs-button docs-button-secondary">Histórico</a>
    </div>

    <form method="GET" action="/docs/{{ $slug }}/versions/compare" class="docs-compare-form">
        <label class="docs-label" for="from">De</label>
        <select id="from" name="from" class="docs-select">
            @foreach ($versions as $version)
                <option value="{{ $version->version }}" @selected($comparison['from']->version === $version->version)>
                    {{ $version->version }} ({{ $version->state }})
                </option>
            @endforeach
        </select>

        <label class="docs-label" for="to">Para</label>
        <select id="to" name="to" class="docs-select">
            @foreach ($versions as $version)
                <option value="{{ $version->version }}" @selected($comparison['to']->version === $version->version)>
                    {{ $version->version }} ({{ $version->state }})
                </option>
            @endforeach
        </select>

        <button type="submit" class="docs-button docs-button-primary">Comparar</button>
    </form>

    <pre class="docs-diff">@foreach ($comparison['lines'] as $line)<span class="docs-diff-line docs-diff-{{ $line['type'] }}">{{ $line['type'] === 'added' ? '+ ' : ($line['type'] === 'removed' ? '- ' : '  ') }}{{ $line['content'] }}</span>
@endforeach</pre>
@endsection
