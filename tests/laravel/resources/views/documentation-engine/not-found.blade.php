@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <section class="docs-empty-state">
        <p class="docs-empty-kicker">{{ __('documentation-engine::messages.not_found_title') }}</p>
        <h1 class="docs-empty-title">{{ $slug }}</h1>
        <p class="docs-empty-description">
            {{ __('documentation-engine::messages.not_found_description') }}
        </p>

        <a href="{{ url('/docs') }}" class="docs-button docs-button-primary">{{ __('documentation-engine::messages.back_to_docs') }}</a>
    </section>
@endsection
