@extends(config('documentation-engine.layout', 'documentation-engine::layouts.default'))

@section('content')
    @php
        $toc = $toc ?? [];
    @endphp

    <div class="docs-page-actions">
        <a
            href="{{ url('/docs/' . $slug . '/versions') }}"
            class="docs-button docs-button-secondary"
        >
            Versões
        </a>

        <a
            href="{{ url('/docs/' . $slug . '/edit') }}"
            class="docs-button docs-button-secondary"
        >
            Editar
        </a>
    </div>

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

    <div class="docs-readable-layout">
        <article class="docs-article">
            {!! $html !!}
        </article>

        @if (count($toc))
            <aside class="docs-toc" aria-label="Sumário do documento">
                <h2 class="docs-toc-title">Nesta página</h2>

                <nav>
                    @foreach ($toc as $item)
                        <a
                            href="#{{ $item['id'] }}"
                            class="docs-toc-link docs-toc-level-{{ $item['level'] }}"
                        >
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>
        @endif
    </div>

    @if ($nav['prev'] || $nav['next'])
        <div class="docs-pagination">
            <div>
                @if ($nav['prev'])
                    <a href="{{ url('/docs/' . $nav['prev']) }}" class="docs-pagination-link">
                        ← {{ $nav['prev'] }}
                    </a>
                @endif
            </div>

            <div class="docs-pagination-next">
                @if ($nav['next'])
                    <a href="{{ url('/docs/' . $nav['next']) }}" class="docs-pagination-link">
                        {{ $nav['next'] }} →
                    </a>
                @endif
            </div>
        </div>
    @endif

    @if (config('documentation-engine.ai.enabled', true))
        <div class="docs-chat-widget" id="docs-chat-widget">
            <button class="docs-chat-fab" id="docs-chat-toggle" aria-label="Conversar com a IA" aria-expanded="false" aria-controls="docs-chat-card">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
            </button>

            <div class="docs-chat-card" id="docs-chat-card" aria-hidden="true">
                <header class="docs-chat-header">
                    <h3 class="docs-chat-header-title">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--docs-accent)">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        Perguntar ao Documento
                    </h3>
                    <button class="docs-chat-close" id="docs-chat-close" aria-label="Fechar chat">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </header>

                <div class="docs-chat-body" id="docs-chat-messages">
                    <div class="docs-chat-message docs-chat-message-system">
                        Tire dúvidas sobre este documento específico. A IA utilizará o contexto do texto para responder.
                    </div>
                </div>

                <form class="docs-chat-footer" id="docs-chat-form" autocomplete="off">
                    <input 
                        type="text" 
                        id="docs-chat-input" 
                        class="docs-chat-input" 
                        placeholder="Perguntar..." 
                        aria-label="Mensagem para a IA"
                        required
                    >
                    <button type="submit" class="docs-chat-send" aria-label="Enviar mensagem">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('docs-chat-toggle');
                const closeBtn = document.getElementById('docs-chat-close');
                const chatCard = document.getElementById('docs-chat-card');
                const chatForm = document.getElementById('docs-chat-form');
                const chatInput = document.getElementById('docs-chat-input');
                const chatMessages = document.getElementById('docs-chat-messages');

                if (!toggleBtn || !chatCard || !chatForm) return;

                function toggleChat() {
                    const isVisible = chatCard.classList.contains('active');
                    if (isVisible) {
                        closeChat();
                    } else {
                        openChat();
                    }
                }

                function openChat() {
                    chatCard.classList.add('active');
                    chatCard.setAttribute('aria-hidden', 'false');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    chatInput.focus();
                    scrollToBottom();
                }

                function closeChat() {
                    chatCard.classList.remove('active');
                    chatCard.setAttribute('aria-hidden', 'true');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.focus();
                }

                function scrollToBottom() {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                toggleBtn.addEventListener('click', toggleChat);
                closeBtn.addEventListener('click', closeChat);

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && chatCard.classList.contains('active')) {
                        closeChat();
                    }
                });

                chatForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const messageText = chatInput.value.trim();
                    if (!messageText) return;

                    appendMessage(messageText, 'user');
                    chatInput.value = '';
                    chatInput.focus();
                    scrollToBottom();

                    const typingIndicator = addTypingIndicator();
                    scrollToBottom();

                    try {
                        const response = await fetch('{{ url('/docs/' . $slug . '/chat') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message: messageText
                            })
                        });

                        const data = await response.json();
                        removeTypingIndicator(typingIndicator);

                        if (!response.ok) {
                            throw new Error(data.message || 'Erro ao obter resposta da IA.');
                        }

                        appendMessage(data.response, 'ai');
                    } catch (error) {
                        removeTypingIndicator(typingIndicator);
                        appendMessage('Desculpe, ocorreu um erro: ' + error.message, 'system');
                    }
                    scrollToBottom();
                });

                function appendMessage(text, sender) {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = `docs-chat-message docs-chat-message-${sender}`;
                    
                    let htmlContent = text
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;");

                    htmlContent = htmlContent.replace(/```([\s\S]+?)```/g, '<pre><code>$1</code></pre>');
                    htmlContent = htmlContent.replace(/`([^`]+)`/g, '<code>$1</code>');
                    
                    const paragraphs = htmlContent.split('\n\n');
                    msgDiv.innerHTML = paragraphs.map(p => `<p>${p.replace(/\n/g, '<br>')}</p>`).join('');

                    chatMessages.appendChild(msgDiv);
                }

                function addTypingIndicator() {
                    const indicator = document.createElement('div');
                    indicator.className = 'docs-chat-typing';
                    indicator.innerHTML = `
                        <div class="docs-chat-typing-dot"></div>
                        <div class="docs-chat-typing-dot"></div>
                        <div class="docs-chat-typing-dot"></div>
                    `;
                    chatMessages.appendChild(indicator);
                    return indicator;
                }

                function removeTypingIndicator(indicator) {
                    if (indicator && indicator.parentNode) {
                        indicator.parentNode.removeChild(indicator);
                    }
                }
            });
        </script>
    @endif
@endsection
