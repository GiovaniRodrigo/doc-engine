@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <section class="docs-empty-state">
        <p class="docs-empty-kicker">{{ __('documentation-engine::messages.title') }}</p>
        <h1 class="docs-empty-title">{{ __('documentation-engine::messages.empty_title') }}</h1>
        <p class="docs-empty-description">
            {{ __('documentation-engine::messages.empty_description') }}
        </p>

        <code class="docs-command">php artisan docs:sync</code>
    </section>
@endsection
