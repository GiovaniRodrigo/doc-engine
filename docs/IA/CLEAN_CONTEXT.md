# CLEAN_CONTEXT: Documentation Engine

## 1. Visão Geral e Objetivo

**Domínio:** motor de documentação Markdown para aplicações Laravel, com suporte a versionamento via Git e integração com IA.

**Objetivo:** fornecer um pacote Laravel (library) que sincroniza arquivos Markdown do sistema de arquivos ou Git para o banco de dados, permitindo servir, buscar, editar e enriquecer documentação com auxílio de IA (OpenAI/Gemini).

**Stack atual:** PHP 8.2+, Laravel (10, 11 e 12), CommonMark (GFM), Eloquent, OpenAI/Gemini SDKs (via HTTP), PHPUnit.

## 2. Entidades de Negócio Core

### Document (Documento)

- Identidade estável: slug único.
- Metadados: título, tags, projeto (opcional).
- Estado de publicação: aponta para a versão atualmente ativa.

### DocumentVersion (Versão do Documento)

- Conteúdo bruto (Markdown) e renderizado (HTML).
- Controle de integridade: checksum (hash do conteúdo).
- Estados: `draft` (rascunho), `published` (publicado), `archived` (arquivado).
- Metadados de origem: commit hash (quando sync via Git).

### SyncResult (Resultado da Sincronização)

- Resumo da operação de sync: arquivos lidos, criados, alterados, ignorados e erros.
- Idempotência: não cria nova versão se o checksum for idêntico.

## 3. Regras de Negócio Inegociáveis

| ID | Regra | Restrição |
| --- | --- | --- |
| RN01 | Versionamento por conteúdo | Uma nova versão só é criada se o conteúdo (checksum) for diferente do anterior. |
| RN02 | Fluxo editorial | Leitores só veem a versão `published`. Edições via UI criam `drafts`. |
| RN03 | IA opcional e segura | Funcionalidades de IA exigem `DOC_ENGINE_AI_ENABLED=true` e nunca gravam chaves ou dados sensíveis em logs. |
| RN04 | Sincronização resiliente | Falhas em arquivos individuais no sync não interrompem o processamento dos demais. |
| RN05 | Cache inteligente | Renderização HTML deve ser cacheada por slug+versão e invalidada em mudanças de estado. |

## 4. Padrões de Implementação

**Arquitetura:** Baseada em Use Cases (Application), Entidades (Domain) e Infraestrutura isolada (AI, Storage, Rendering).

Camadas principais:

- `src/Application/UseCases/`: Lógica central (Sync, Publish, Generate, etc).
- `src/Domain/Entities/`: Modelos Eloquent com lógica de domínio.
- `src/Infrastructure/`: Implementações de drivers (AI Providers, Git, Markdown).
- `src/Http/`: Controladores e rotas do pacote.

**Estilo de código:** PSR-12, Tipagem forte (PHP 8.2+), Testes de Feature cobrindo fluxos completos, Mocks para chamadas externas (IA).

## 5. Mapeamento de Arquivos Atuais

- `ROADMAP.md`: Fases de evolução do pacote.
- `USAGE.md`: Guia de instalação e uso para desenvolvedores.
- `docs/IA/IMPLEMENTATION_PLAN.md`: Plano detalhado de IA e novas funcionalidades.
- `docs/IA/AI_FEATURE_WORKFLOW.md`: Fluxo de implementação orientado a regras e testes.
- `src/DocumentationServiceProvider.php`: Registro do pacote no Laravel.
- `src/routes.php`: Definição das rotas web, API e webhooks.
- `config/documentation-engine.php`: Configurações publicáveis.
