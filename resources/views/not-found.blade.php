@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    <section class="docs-empty-state">
        <p class="docs-empty-kicker">Documento não encontrado</p>
        <h1 class="docs-empty-title">{{ $slug }}</h1>
        <p class="docs-empty-description">
            Este documento não está disponível na versão publicada da documentação.
        </p>

        <a href="/docs" class="docs-button docs-button-primary">Voltar para docs</a>
    </section>
@endsection
