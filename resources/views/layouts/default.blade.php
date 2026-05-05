<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Documentacao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if (config('documentation-engine.css'))
        <link rel="stylesheet" href="{{ config('documentation-engine.css') }}">
    @else
        @include('documentation-engine::partials.styles')
    @endif
</head>

<body class="docs-body">
    <div class="docs-layout">
        <aside class="docs-sidebar">
            <div class="docs-sidebar-inner">
                <h2 class="docs-sidebar-title">Docs</h2>

                <form method="GET" action="/docs/search" class="docs-search-form">
                    <label for="docs-sidebar-search" class="docs-sr-only">Buscar documentação</label>
                    <input
                        id="docs-sidebar-search"
                        name="q"
                        value="{{ $query ?? '' }}"
                        class="docs-search-input"
                        type="search"
                        placeholder="Buscar"
                    >
                </form>

                <nav class="docs-nav" aria-label="Documentacao">
                    @include('documentation-engine::sidebar-node', ['nodes' => $sidebar, 'depth' => 0])
                </nav>
            </div>
        </aside>

        <main class="docs-content">
            <header class="docs-mobile-header">
                <a href="/docs" class="docs-mobile-brand">Docs</a>

                <form method="GET" action="/docs/search" class="docs-mobile-search">
                    <label for="docs-mobile-search" class="docs-sr-only">Buscar documentação</label>
                    <input
                        id="docs-mobile-search"
                        name="q"
                        value="{{ $query ?? '' }}"
                        class="docs-search-input"
                        type="search"
                        placeholder="Buscar"
                    >
                </form>
            </header>

            <div class="docs-content-inner">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
