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
    <div id="docs-sidebar-overlay" class="docs-sidebar-overlay"></div>
    <div class="docs-layout">
        <aside id="docs-sidebar" class="docs-sidebar">
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
                <button type="button" id="docs-mobile-toggle" class="docs-mobile-toggle" aria-expanded="false" aria-controls="docs-sidebar" aria-label="Abrir menu de navegação">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggle = document.getElementById('docs-mobile-toggle');
            var sidebar = document.getElementById('docs-sidebar');
            var overlay = document.getElementById('docs-sidebar-overlay');

            if (toggle && sidebar && overlay) {
                function toggleSidebar() {
                    var isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', !isExpanded);
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                }

                function closeSidebar() {
                    toggle.setAttribute('aria-expanded', 'false');
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }

                toggle.addEventListener('click', toggleSidebar);
                overlay.addEventListener('click', closeSidebar);

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                        closeSidebar();
                        toggle.focus();
                    }
                });
            }
        });
    </script>
</body>

</html>
