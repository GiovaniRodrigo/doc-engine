# Plano de Implementação

Este plano organiza a evolução do Documentation Engine de um sincronizador básico de Markdown para um motor completo de documentação com versionamento, fluxo editorial e inteligência artificial.

## Objetivo

Fornecer um pacote Laravel que transforme arquivos Markdown em uma plataforma de documentação rica, navegável e editável, facilitando a manutenção técnica e a leitura para usuários finais.

O desenvolvedor deve conseguir:

* instalar o pacote via composer;
* sincronizar documentos localmente ou via Git;
* configurar webhooks para atualização automática;
* usar IA para resumir, categorizar ou tirar dúvidas sobre os documentos;
* gerenciar versões (draft, published, archived) via interface web.

## Estado atual

O projeto já possui as bases sólidas descritas no `ROADMAP.md`:

* core de sincronização e versionamento (Fases 1 e 2);
* suporte a múltiplos provedores de IA (Fase 6);
* rotas de leitura e editor funcional (Fases 4 e 5);
* suporte a webhooks (Fase 3).

## Fluxo IA para novas funcionalidades

Para qualquer nova implementação, a IA deve seguir `docs/IA/AI_FEATURE_WORKFLOW.md`:

1. Consultar `docs/IA/CLEAN_CONTEXT.md`.
2. Identificar a regra de negócio em `docs/` ou propor uma nova.
3. Apresentar plano e aguardar aprovação.
4. Criar ou atualizar testes em `tests/Feature/`.
5. Implementar no menor escopo possível em `src/`.
6. Validar com `vendor/bin/phpunit`.

## Arquitetura

```text
[Filesystem/Git] -> [SyncUseCase] -> [DocumentRepository] -> [Database]
                                            |
                                            v
[AI Provider] <-> [AI Services] <-> [DocumentViewData] -> [Blade Views]
                                            |
                                            v
                                     [Render Cache]
```

### Módulos Principais

* `src/Application/UseCases/`: Orquestração de regras de negócio.
* `src/Domain/Entities/`: Modelos Document e DocumentVersion.
* `src/Infrastructure/Rendering/`: Conversão Markdown para HTML.
* `src/Infrastructure/AI/`: Adaptadores para OpenAI e Gemini.
* `src/Infrastructure/Persistence/`: Implementação Eloquent dos repositórios.

## Fases de Implementação (Roadmap Resumido)

### Fase 7 - Performance e Cache (Concluída)

* Implementar cache de HTML renderizado por slug+versão.
* Cachear sidebar e navegação por projeto para evitar consultas excessivas ao banco.
* Invalidar cache em eventos de sync ou publicação.

### Fase 8 - Qualidade e Distribuição (Próximo Passo)

* Adicionar scripts Composer para análise estática e formatação.
* Configurar CI para execução automática de testes.
* Revisar compatibilidade com múltiplas versões do Laravel.
* Documentar extensibilidade e publicação de assets.

## Estratégia de IA

As funcionalidades de IA devem ser tratadas como "augmentations" opcionais:

1. **Sugestão de Tags:** Analisar conteúdo e propor tags para o `Document`.
2. **TL;DR / Resumos:** Gerar resumos rápidos para visualização na busca ou cabeçalho.
3. **Chat com Contexto:** RAG simples passando o conteúdo do documento para o modelo.
4. **Melhoria de Texto:** Propor revisões gramaticais ou de clareza no editor.

## Testes

Todo novo Use Case deve ser acompanhado de um teste em `tests/Feature/` que cubra:
- Sucesso da operação.
- Erros de validação ou configuração.
- Persistência correta no banco de dados.
- Cache invalidado quando aplicável.
