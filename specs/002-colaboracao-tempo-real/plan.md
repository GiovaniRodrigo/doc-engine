# Plano de Implementação: Colaboração em Tempo Real

Este plano descreve a abordagem técnica para implementar a colaboração em tempo real e o controle de acesso no editor de documentos do pacote `doc-engine`.

---

## 1. Arquivos a serem Criados/Editados

### 1.1. Backend (Laravel Package)
*   **[src/routes.php](file:///home/giovani/Documents/projects/doc-engine/src/routes.php):**
    *   Definir rota `POST /docs/{slug}/collaboration` no grupo de rotas protegido por `edit_middleware` e `docs_middleware`.
*   **[src/Http/DocumentationController.php](file:///home/giovani/Documents/projects/doc-engine/src/Http/DocumentationController.php):**
    *   Implementar o método `collaboration(Request $request, string $slug)` para registrar presença no cache do Laravel e retornar a lista de colaboradores ativos.
*   **[tests/Feature/RealTimeCollaborationTest.php](file:///home/giovani/Documents/projects/doc-engine/tests/Feature/RealTimeCollaborationTest.php):**
    *   Testes automatizados para validar o registro de presença, expiração de tempo limite (timeout), controle de acesso (middleware) e proteção CSRF.

### 1.2. Frontend (Material Design 3 & JS)
*   **[resources/views/edit.blade.php](file:///home/giovani/Documents/projects/doc-engine/resources/views/edit.blade.php):**
    *   Inserir estrutura HTML para exibir a lista de editores ativos e alerta de conflito potencial.
    *   Adicionar script JavaScript para fazer requisições periódicas (5s) via `fetch` ao endpoint `/docs/{slug}/collaboration` e atualizar a interface.
*   **[resources/css/docs/components.css](file:///home/giovani/Documents/projects/doc-engine/resources/css/docs/components.css):**
    *   Adicionar regras CSS para badges/chips de colaboradores com cores harmonizadas em HSL, e posicionamento sutil acima do editor.

---

## 2. Estratégia Técnica e Arquitetura

### 2.1. Identificação do Usuário
*   Se o usuário estiver autenticado: usar o nome (`auth()->user()->name`) ou e-mail (`auth()->user()->email`).
*   Se o usuário não estiver autenticado (ou sem nome/email): usar uma string genérica baseada na sessão, ex: `"Editor #" . substr(session()->getId(), 0, 5)`.
*   Para isolamento em múltiplas abas do mesmo navegador, o cliente pode opcionalmente enviar um identificador único de aba (tabId) via JSON para evitar que o próprio usuário se veja duplicado na lista.

### 2.2. Armazenamento e Expirabilidade (Cache)
*   Utilizaremos o `Cache` do Laravel (`Cache::get` e `Cache::put`).
*   A estrutura no cache para a chave `documentation-engine:collaboration:{slug}` será um array associativo do tipo:
    ```php
    [
        'session_or_user_id' => [
            'name' => 'Nome do Usuário',
            'last_seen' => 1718294400, // timestamp
        ]
    ]
    ```
*   A cada heartbeat, limpamos do array qualquer registro cuja diferença (`time() - last_seen`) seja maior que 15 segundos.
*   Salvamos o array atualizado com TTL de 60 segundos (para não deixar lixo persistente caso todos saiam).

### 2.3. Segurança e Acesso
*   Como a rota de colaboração estará no grupo `edit_middleware`, o Laravel cuidará automaticamente da autenticação/autorização antes mesmo de chegar ao controlador.
*   Se a verificação falhar, o cliente receberá `401` ou `403` e interromperá as requisições.
*   Será necessário passar o token CSRF (`X-CSRF-TOKEN`) nos cabeçalhos da requisição `fetch`.
