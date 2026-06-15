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
                <h2 class="docs-sidebar-title">
                    <a href="{{ url('/docs') }}" class="docs-sidebar-title-link">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--docs-accent); display: inline-block; vertical-align: middle; margin-right: 6px;">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Docs
                    </a>
                </h2>

                <form method="GET" action="{{ url('/docs/search') }}" class="docs-search-form">
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

                @if (isset($availableLanguages) && is_array($availableLanguages) && count($availableLanguages) > 0)
                    <div class="docs-language-selector-wrapper">
                        <label for="docs-lang-select" class="docs-sr-only">Selecionar idioma</label>
                        <div class="docs-select-wrapper">
                            <select 
                                id="docs-lang-select" 
                                class="docs-select" 
                                data-translations="{{ json_encode($translations ?? []) }}"
                                onchange="changeDocsLanguage(this)"
                            >
                                <option value="">Todos os idiomas</option>
                                @foreach ($availableLanguages as $code => $name)
                                    <option value="{{ $code }}" {{ ($selectedLanguage ?? null) === $code ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

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

                <a href="{{ url('/docs') }}" class="docs-mobile-brand">Docs</a>

                <form method="GET" action="{{ url('/docs/search') }}" class="docs-mobile-search">
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
        window.changeDocsLanguage = function(select) {
            var lang = select.value;
            var translations = JSON.parse(select.getAttribute('data-translations') || '{}');
            if (lang && translations[lang]) {
                window.location.href = translations[lang];
            } else {
                window.location.href = '{{ url("/docs") }}' + (lang ? '?lang=' + lang : '?lang=');
            }
        };

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
