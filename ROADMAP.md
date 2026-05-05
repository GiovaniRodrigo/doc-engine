# Roadmap

Este roadmap organiza a evolucao do Documentation Engine em entregas pequenas,
testaveis e alinhadas ao que ja existe no pacote.

## Estado atual

Ja implementado:

- Pacote Laravel com auto-discovery via `DocumentationServiceProvider`.
- Configuracao publicavel em `config/documentation-engine.php`.
- Rotas para leitura, edicao, webhook, geracao com IA e chat.
- Sincronizacao de Markdown com `php artisan docs:sync {project?} {--path=}`.
- Persistencia de documentos e versoes em banco.
- Renderizacao Markdown com CommonMark/GFM.
- Sidebar, breadcrumb e navegacao anterior/proxima.
- Cache basico de HTML renderizado por slug.
- Provedores OpenAI e Gemini configuraveis.
- Script `vendor/bin/documentation-engine-env` para completar variaveis de `.env`.

## Fase 1 - Instalacao e bootstrap

Objetivo: deixar a instalacao previsivel em qualquer app Laravel consumidor.

- [x] Documentar instalacao via Composer, incluindo repositorio VCS quando usado em projetos privados.
- [x] Documentar o uso de `vendor/bin/documentation-engine-env`.
- [x] Garantir que o script de env possa atualizar `.env` e `.env.example` quando solicitado.
- [x] Adicionar testes para o script de env:
  - cria `.env` quando nao existe;
  - preserva variaveis existentes;
  - adiciona apenas chaves ausentes;
  - aceita `--env=/caminho/.env`;
  - nao duplica bloco em execucoes repetidas.
- [x] Publicar uma referencia curta de variaveis:
  - `DOC_ENGINE_PATH`;
  - `DOC_ENGINE_LAYOUT`;
  - `DOC_ENGINE_CSS`;
  - `DOC_ENGINE_WEBHOOK_SECRET`;
  - `DOC_ENGINE_WEBHOOK_BRANCH`;
  - `DOC_ENGINE_AI_ENABLED`;
  - `DOCUMENTATION_AI_PROVIDER`;
  - `OPENAI_*`;
  - `GEMINI_*`.

Aceite:

- Um app Laravel novo consegue instalar o pacote, rodar o bootstrap de env e
  acessar `/docs` sem configuracoes manuais escondidas.

## Fase 2 - Sync e versionamento

Objetivo: tornar a sincronizacao idempotente, rastreavel e segura.

- [x] Consolidar comportamento de slug para README/index:
  - `README.md` na raiz do projeto pode virar o slug do projeto;
  - demais arquivos mantem prefixo do projeto.
- [x] Padronizar mensagens do comando `docs:sync` nos testes e na saida real.
- [x] Registrar metadados de sync:
  - commit atual;
  - arquivos lidos;
  - arquivos alterados;
  - arquivos ignorados.
- [x] Evitar nova versao quando o conteudo nao mudou.
- [x] Marcar documentos removidos do filesystem como arquivados ou inativos.
- [x] Suportar modo dry-run: `php artisan docs:sync --dry-run`.
- [x] Melhorar tratamento de erros para path inexistente, arquivo ilegivel e falha de Git.

Aceite:

- Rodar `docs:sync` varias vezes com os mesmos arquivos nao cria versoes
  duplicadas e retorna uma saida clara sobre o que mudou.

## Fase 3 - Webhooks e automacao Git

Objetivo: permitir atualizacao automatica por GitHub/GitLab com seguranca.

- [x] Separar rotas de webhook por provedor:
  - `/docs/webhooks/github`;
  - `/docs/webhooks/gitlab`.
- [x] Validar assinatura GitHub somente quando `DOC_ENGINE_WEBHOOK_SECRET` estiver configurado.
- [x] Validar token GitLab somente quando `DOC_ENGINE_WEBHOOK_SECRET` estiver configurado.
- [x] Extrair processamento de webhook para uma classe dedicada.
- [x] Permitir configurar branch alvo.
- [x] Retornar resposta JSON com detalhes minimos do sync.
- [x] Adicionar logs estruturados para sucesso e falha.

Aceite:

- Um push no provedor configurado dispara sync apenas quando a assinatura/token
  for valido e a branch for aceita.

## Fase 4 - Experiencia de leitura

Objetivo: transformar a interface de docs em uma experiencia confortavel para uso diario.

- [x] Melhorar layout responsivo para desktop e mobile.
- [x] Adicionar busca local por titulo, slug e conteudo.
- [x] Adicionar sumario do documento por headings.
- [x] Adicionar estado vazio para `/docs`.
- [x] Adicionar paginas de erro amigaveis para documento inexistente.
- [x] Suportar tema claro/escuro via CSS customizavel.
- [x] Garantir que layout customizado receba todos os dados necessarios.

Aceite:

- Um usuario consegue navegar, buscar e ler a documentacao sem depender da
  estrutura de arquivos original.

## Fase 5 - Edicao e fluxo editorial

Objetivo: permitir revisao controlada antes de publicar alteracoes.

- [x] Proteger rotas de edicao por middleware configuravel.
- [x] Criar estados claros para versoes:
  - `draft`;
  - `published`;
  - `archived`.
- [x] Permitir salvar rascunho sem publicar.
- [x] Permitir publicar uma versao especifica.
- [x] Exibir historico de versoes por documento.
- [x] Permitir comparar duas versoes.
- [x] Invalidar cache ao editar, publicar ou arquivar.

Aceite:

- Alteracoes feitas pela UI nao substituem imediatamente a versao publicada
  sem uma acao explicita de publicacao.

## Fase 6 - IA

Objetivo: tornar as funcionalidades de IA uteis, previsiveis e seguras.

- [ ] Validar configuracao no primeiro uso e retornar erro acionavel.
- [ ] Suportar selecao de provedor por request e por configuracao.
- [ ] Melhorar prompts para:
  - TL;DR;
  - sugestao de tags;
  - melhoria de texto;
  - chat com contexto.
- [ ] Adicionar limites de tamanho de entrada.
- [ ] Adicionar timeout e retry por provedor.
- [ ] Registrar provider/model usados sem gravar conteudo sensivel.
- [ ] Adicionar testes para Gemini e OpenAI sem chamadas externas.

Aceite:

- Com `GEMINI_API_KEY` ou `OPENAI_API_KEY` configurado, as rotas de IA funcionam
  com mensagens de erro claras quando a API falha.

## Fase 7 - Performance e cache

Objetivo: reduzir custo de renderizacao e preparar uso em bases maiores.

- [ ] Cachear lista de slugs por projeto.
- [ ] Cachear sidebar e navegacao por projeto.
- [ ] Usar chave de cache baseada em slug + versao publicada.
- [ ] Invalidar cache por evento de sync/publicacao.
- [ ] Documentar estrategia recomendada para Redis.
- [ ] Medir tempo de sync e renderizacao em logs.

Aceite:

- Alterar um documento invalida somente os caches relacionados a ele e ao
  indice/navegacao do projeto.

## Fase 8 - Qualidade e distribuicao

Objetivo: deixar o pacote pronto para manutencao e reutilizacao.

- [ ] Adicionar scripts Composer para testes e analise:
  - `composer test`;
  - `composer analyse`;
  - `composer format`.
- [ ] Adicionar CI para PHPUnit.
- [ ] Revisar compatibilidade com Laravel 10, 11 e 12 ou declarar suporte exato.
- [ ] Criar README principal do pacote.
- [ ] Documentar tags de publish:
  - config;
  - views;
  - assets;
  - migrations.
- [ ] Criar changelog por versao.
- [ ] Definir politica de versionamento semantico.

Aceite:

- O pacote pode ser instalado e validado por outro projeto sem conhecimento
  interno do repositorio.

## Prioridade sugerida

1. Fase 1: instalacao e bootstrap.
2. Fase 2: sync e versionamento.
3. Fase 6: IA, por estar ligada diretamente a configuracao de env.
4. Fase 4: experiencia de leitura.
5. Fases 3, 5, 7 e 8 conforme uso real em producao.

## Proxima tarefa recomendada

Avancar para a Fase 6: validar configuracao de IA no primeiro uso, melhorar
mensagens de erro acionaveis e cobrir OpenAI/Gemini com testes sem chamadas
externas.
