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
                <nav class="docs-nav" aria-label="Documentacao">
                    @include('documentation-engine::sidebar-node', ['nodes' => $sidebar, 'depth' => 0])
                </nav>
            </div>
        </aside>

        <main class="docs-content">
            <div class="docs-content-inner">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
