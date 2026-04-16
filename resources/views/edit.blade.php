@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @if (count($breadcrumb))
        <nav class="docs-breadcrumb" aria-label="Breadcrumb">
            @foreach ($breadcrumb as $item)
                <a href="/docs/{{ $item['slug'] }}">
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
            href="/docs/{{ $slug }}"
            class="docs-button docs-button-secondary"
        >
            Voltar
        </a>
    </div>

    @if ($errors->any())
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

    <form method="POST" action="/docs/{{ $slug }}" class="docs-form">
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
                href="/docs/{{ $slug }}"
                class="docs-button docs-button-secondary"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="docs-button docs-button-primary"
            >
                Salvar
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
                        const response = await fetch('/docs/{{ $slug }}/generate', {
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
@endsection
