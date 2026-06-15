@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <section class="docs-empty-state">
        <p class="docs-empty-kicker">Documentação</p>
        <h1 class="docs-empty-title">Nenhum documento encontrado</h1>
        <p class="docs-empty-description">
            Sincronize arquivos Markdown para popular esta área.
        </p>

        <code class="docs-command">php artisan docs:sync</code>
    </section>
@endsection
