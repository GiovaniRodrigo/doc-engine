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
                        Descreva o que voce quer criar ou ajustar e insira o resultado no editor.
                    </p>
                </div>

                <div id="ai-feedback" hidden class="docs-alert" aria-live="polite"></div>

                <div>
                    <label for="ai-prompt" class="docs-label">
                        Instrucao para a IA
                    </label>

                    <textarea
                        id="ai-prompt"
                        rows="4"
                        class="docs-textarea"
                        placeholder="Ex.: gere uma introducao curta explicando como instalar essa biblioteca e um exemplo basico de uso."
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
                        O texto atual do documento sera enviado como contexto.
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
@endsection
