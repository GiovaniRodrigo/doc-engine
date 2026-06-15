@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @if (count($breadcrumb))
        <nav class="docs-breadcrumb" aria-label="Breadcrumb">
            @foreach ($breadcrumb as $item)
                <a href="{{ url('/docs/' . $item['slug']) }}">{{ $item['title'] }}</a>

                @if (!$loop->last)
                    <span class="docs-breadcrumb-separator">/</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="docs-page-header">
        <div>
            <h1 class="docs-page-title">{{ __('documentation-engine::messages.compare_versions') }}</h1>
            <p class="docs-page-subtitle">{{ $slug }}</p>
        </div>

        <a href="{{ url('/docs/' . $slug . '/versions') }}" class="docs-button docs-button-secondary">{{ __('documentation-engine::messages.history') }}</a>
    </div>

    <form method="GET" action="{{ url('/docs/' . $slug . '/versions/compare') }}" class="docs-compare-form">
        <label class="docs-label" for="from">{{ __('documentation-engine::messages.from') }}</label>
        <select id="from" name="from" class="docs-select">
            @foreach ($versions as $version)
                <option value="{{ $version->version }}" @selected($comparison['from']->version === $version->version)>
                    {{ strlen($version->version) === 36 ? substr($version->version, 0, 8) : $version->version }} ({{ $version->state }})
                </option>
            @endforeach
        </select>

        <label class="docs-label" for="to">{{ __('documentation-engine::messages.to') }}</label>
        <select id="to" name="to" class="docs-select">
            @foreach ($versions as $version)
                <option value="{{ $version->version }}" @selected($comparison['to']->version === $version->version)>
                    {{ strlen($version->version) === 36 ? substr($version->version, 0, 8) : $version->version }} ({{ $version->state }})
                </option>
            @endforeach
        </select>

        <button type="submit" class="docs-button docs-button-primary">{{ __('documentation-engine::messages.compare') }}</button>
    </form>

    <pre class="docs-diff">@foreach ($comparison['lines'] as $line)<span class="docs-diff-line docs-diff-{{ $line['type'] }}">{{ $line['type'] === 'added' ? '+ ' : ($line['type'] === 'removed' ? '- ' : '  ') }}{{ $line['content'] }}</span>
@endforeach</pre>
@endsection
