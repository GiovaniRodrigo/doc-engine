@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @if (count($breadcrumb))
        <nav class="mb-6 text-sm text-gray-500">
            @foreach ($breadcrumb as $item)
                <a href="/docs/{{ $item['slug'] }}" class="hover:text-gray-700">
                    {{ $item['title'] }}
                </a>

                @if (!$loop->last)
                    <span class="mx-2 text-gray-300">/</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar documento</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $slug }}</p>
        </div>

        <a
            href="/docs/{{ $slug }}"
            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:text-gray-900"
        >
            Voltar
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('content') }}
        </div>
    @endif

    @if (config('documentation-engine.ai.enabled', true))
        <section class="mb-6 rounded-xl border border-blue-100 bg-blue-50/70 p-5">
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-blue-900">Gerar com IA</h2>
                    <p class="mt-1 text-sm text-blue-800">
                        Descreva o que voce quer criar ou ajustar e insira o resultado no editor.
                    </p>
                </div>

                <div id="ai-feedback" class="hidden rounded-lg px-4 py-3 text-sm"></div>

                <div>
                    <label for="ai-prompt" class="mb-2 block text-sm font-medium text-gray-700">
                        Instrucao para a IA
                    </label>

                    <textarea
                        id="ai-prompt"
                        rows="4"
                        class="block w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        placeholder="Ex.: gere uma introducao curta explicando como instalar essa biblioteca e um exemplo basico de uso."
                    ></textarea>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        id="generate-with-ai"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-300"
                    >
                        Gerar texto
                    </button>

                    <p class="text-xs text-blue-900/80">
                        O texto atual do documento sera enviado como contexto.
                    </p>
                </div>
            </div>
        </section>
    @endif

    <form method="POST" action="/docs/{{ $slug }}" class="space-y-4">
        @csrf
        @method('PUT')

        <label for="content" class="block text-sm font-medium text-gray-700">
            Conteúdo em Markdown
        </label>

        <textarea
            id="content"
            name="content"
            rows="24"
            class="block min-h-[32rem] w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-sm text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
        >{{ old('content', $content) }}</textarea>

        <div class="flex items-center justify-end gap-3">
            <a
                href="/docs/{{ $slug }}"
                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-400 hover:text-gray-900"
            >
                Cancelar
            </a>

            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
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
                        error: 'block border border-red-200 bg-red-50 text-red-700',
                        info: 'block border border-blue-200 bg-white text-blue-900',
                        success: 'block border border-green-200 bg-green-50 text-green-700',
                    };

                    feedback.className = `rounded-lg px-4 py-3 text-sm ${styles[tone] || styles.info}`;
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
