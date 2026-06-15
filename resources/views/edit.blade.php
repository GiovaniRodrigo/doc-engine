@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @if (count($breadcrumb))
        <nav class="docs-breadcrumb" aria-label="Breadcrumb">
            @foreach ($breadcrumb as $item)
                <a href="{{ url('/docs/' . $item['slug']) }}">
                    {{ $item['title'] }}
                </a>

                @if (!$loop->last)
                    <span class="docs-breadcrumb-separator">/</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="docs-page-header">
        <div>
            <h1 class="docs-page-title">Editar documento</h1>
            <p class="docs-page-subtitle">{{ $slug }}</p>
        </div>

        <a
            href="{{ url('/docs/' . $slug . '/versions') }}"
            class="docs-button docs-button-secondary"
        >
            Versões
        </a>
    </div>

    <div id="docs-collaboration-container" class="docs-collaboration-container" hidden>
        <div class="docs-collaboration-header">
            <span class="docs-label" style="margin: 0; font-size: 0.85rem; color: var(--docs-text-muted);">Editando agora:</span>
            <div class="docs-collaboration-users" id="collaboration-users-list">
                <!-- Avatars / chips of active editors -->
            </div>
        </div>
        <div id="docs-collaboration-conflict-alert" class="docs-alert docs-alert-error" hidden style="margin-top: 12px; margin-bottom: 0; background: var(--docs-error-bg); color: var(--docs-error-text); border: 1px solid var(--docs-error-border);">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <span style="font-weight: 500;">Atenção: Outro usuário está editando este documento no momento. Suas alterações podem sobrescrever rascunhos paralelos.</span>
        </div>
    </div>

    @if (session('documentation_engine_status'))
        <div class="docs-alert docs-alert-success">
            {{ session('documentation_engine_status') }}
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="docs-alert docs-alert-error">
            {{ $errors->first('content') }}
        </div>
    @endif

    @if (config('documentation-engine.ai.enabled', true))
        <section class="docs-panel">
            <div class="docs-stack">
                <div>
                    <h2 class="docs-panel-title">Gerar com IA</h2>
                    <p class="docs-panel-description">
                        Descreva o que você quer criar ou ajustar e insira o resultado no editor.
                    </p>
                </div>

                <div id="ai-feedback" hidden class="docs-alert" aria-live="polite"></div>

                <div>
                    <label for="ai-prompt" class="docs-label">
                        Instrução para a IA
                    </label>

                    <textarea
                        id="ai-prompt"
                        rows="4"
                        class="docs-textarea"
                        placeholder="Ex.: gere uma introdução curta explicando como instalar essa biblioteca e um exemplo básico de uso."
                    ></textarea>
                </div>

                <div class="docs-inline-actions">
                    <button
                        type="button"
                        id="generate-with-ai"
                        class="docs-button docs-button-primary"
                    >
                        Gerar texto
                    </button>

                    <p class="docs-hint">
                        O texto atual do documento será enviado como contexto.
                    </p>
                </div>
            </div>
        </section>
    @endif

    <form method="POST" action="{{ url('/docs/' . $slug) }}" class="docs-form">
        @csrf
        @method('PUT')

        <label for="content" class="docs-label">
            Conteúdo em Markdown
        </label>

        <div class="docs-editor-toolbar">
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('bold')" title="Negrito" aria-label="Negrito">
                <strong>B</strong>
            </button>
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('italic')" title="Itálico" aria-label="Itálico">
                <em>I</em>
            </button>
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('heading')" title="Título" aria-label="Título H2">
                H
            </button>
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('code')" title="Bloco de Código" aria-label="Bloco de Código">
                &lt;/&gt;
            </button>
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('link')" title="Inserir Link" aria-label="Inserir Link">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </button>
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('list')" title="Lista" aria-label="Lista não ordenada">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
            </button>
            <button type="button" class="docs-toolbar-btn" onclick="insertMarkdown('table')" title="Tabela" aria-label="Inserir Tabela">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="12" y1="3" x2="12" y2="21"></line></svg>
            </button>
            <button type="button" class="docs-toolbar-btn docs-toolbar-btn-accent" onclick="insertMarkdown('mermaid')" title="Inserir Diagrama/Formas (Mermaid)" aria-label="Inserir Diagrama (Mermaid)" style="margin-left: auto;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                <span style="font-size: 0.8rem; font-weight: 700;">Diagrama</span>
            </button>
        </div>

        <textarea
            id="content"
            name="content"
            rows="24"
            class="docs-textarea docs-textarea-editor"
        >{{ old('content', $content) }}</textarea>

        <div class="docs-form-actions">
            <a
                href="{{ url('/docs/' . $slug) }}"
                class="docs-button docs-button-secondary"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="docs-button docs-button-primary"
            >
                Salvar rascunho
            </button>
        </div>
    </form>

    @if (config('documentation-engine.ai.enabled', true))
        <script>
            (() => {
                const button = document.getElementById('generate-with-ai');
                const promptField = document.getElementById('ai-prompt');
                const contentField = document.getElementById('content');
                const feedback = document.getElementById('ai-feedback');

                if (!button || !promptField || !contentField || !feedback) {
                    return;
                }

                const showFeedback = (message, tone) => {
                    const styles = {
                        error: 'docs-alert docs-alert-error',
                        info: 'docs-alert docs-alert-info',
                        success: 'docs-alert docs-alert-success',
                    };

                    feedback.hidden = false;
                    feedback.className = styles[tone] || styles.info;
                    feedback.textContent = message;
                };

                button.addEventListener('click', async () => {
                    const prompt = promptField.value.trim();

                    if (!prompt) {
                        showFeedback('Escreva uma instrucao para gerar o texto.', 'error');
                        promptField.focus();
                        return;
                    }

                    button.disabled = true;
                    button.textContent = 'Gerando...';
                    showFeedback('Gerando conteudo com IA...', 'info');

                    try {
                        const response = await fetch('{{ url('/docs/' . $slug . '/generate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                prompt,
                                content: contentField.value,
                            }),
                        });

                        const payload = await response.json();

                        if (!response.ok) {
                            throw new Error(payload.message || 'Nao foi possivel gerar conteudo.');
                        }

                        contentField.value = payload.content || '';
                        showFeedback('Conteudo gerado e inserido no editor.', 'success');
                    } catch (error) {
                        showFeedback(error.message || 'Nao foi possivel gerar conteudo.', 'error');
                    } finally {
                        button.disabled = false;
                        button.textContent = 'Gerar texto';
                    }
                });
            })();
        </script>
    @endif

    <script>
        (() => {
            const container = document.getElementById('docs-collaboration-container');
            const list = document.getElementById('collaboration-users-list');
            const alertBox = document.getElementById('docs-collaboration-conflict-alert');

            if (!container || !list || !alertBox) return;

            const slug = '{{ $slug }}';
            const url = '{{ url("/docs") }}/' + encodeURIComponent(slug) + '/collaboration';

            let timer = null;

            const getInitials = (name) => {
                return name
                    .split(' ')
                    .map(word => word.charAt(0))
                    .slice(0, 2)
                    .join('');
            };

            const hashString = (str) => {
                let hash = 0;
                for (let i = 0; i < str.length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash);
                }
                return Math.abs(hash);
            };

            const updatePresence = async () => {
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (response.status === 401 || response.status === 403) {
                        clearInterval(timer);
                        return;
                    }

                    if (!response.ok) return;

                    const data = await response.json();
                    const users = data.users || [];

                    if (users.length <= 1) {
                        container.hidden = true;
                        alertBox.hidden = true;
                        list.innerHTML = '';
                        return;
                    }

                    container.hidden = false;
                    alertBox.hidden = !data.has_conflict;

                    list.innerHTML = '';
                    users.forEach(user => {
                        const initials = getInitials(user.name);
                        const colorIndex = hashString(user.name) % 5;

                        const chip = document.createElement('div');
                        chip.className = 'docs-user-chip';
                        chip.title = user.name + (user.is_current ? ' (Você)' : '');
                        
                        const avatar = document.createElement('div');
                        avatar.className = `docs-user-avatar docs-user-avatar-${colorIndex}`;
                        avatar.textContent = initials;

                        const nameSpan = document.createElement('span');
                        nameSpan.textContent = user.name + (user.is_current ? ' (Você)' : '');

                        chip.appendChild(avatar);
                        chip.appendChild(nameSpan);
                        list.appendChild(chip);
                    });

                } catch (error) {
                    console.error('Erro de colaboração em tempo real:', error);
                }
            };

            updatePresence();
            timer = setInterval(updatePresence, 5000);
        })();
    </script>
    <script>
        window.insertMarkdown = function(type) {
            const textarea = document.getElementById('content');
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selectedText = text.substring(start, end);

            let prefix = '';
            let suffix = '';
            let placeholder = '';

            switch (type) {
                case 'bold':
                    prefix = '**';
                    suffix = '**';
                    placeholder = 'texto';
                    break;
                case 'italic':
                    prefix = '*';
                    suffix = '*';
                    placeholder = 'texto';
                    break;
                case 'heading':
                    prefix = '\n## ';
                    suffix = '\n';
                    placeholder = 'Título';
                    break;
                case 'code':
                    prefix = '\n```\n';
                    suffix = '\n```\n';
                    placeholder = 'código';
                    break;
                case 'link':
                    prefix = '[';
                    suffix = '](https://url)';
                    placeholder = 'Link';
                    break;
                case 'list':
                    prefix = '\n- ';
                    suffix = '';
                    placeholder = 'Item';
                    break;
                case 'table':
                    prefix = '\n| Cabeçalho 1 | Cabeçalho 2 |\n| ----------- | ----------- |\n| ';
                    suffix = '    | Célula 2    |\n';
                    placeholder = 'Célula 1';
                    break;
                case 'mermaid':
                    prefix = '\n```mermaid\ngraph TD\n    ';
                    suffix = '\n```\n';
                    placeholder = 'A[Início] --> B(Processo)';
                    break;
            }

            const contentToInsert = selectedText || placeholder;
            const replacement = prefix + contentToInsert + suffix;

            textarea.value = text.substring(0, start) + replacement + text.substring(end);
            textarea.focus();

            const newSelectionStart = start + prefix.length;
            const newSelectionEnd = newSelectionStart + contentToInsert.length;

            textarea.setSelectionRange(newSelectionStart, newSelectionEnd);
        };
    </script>
@endsection
