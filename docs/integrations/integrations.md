# Integrações

## Git

### Visão geral

O Git é utilizado como **fonte da verdade** para toda a documentação do sistema.

Isso significa que:

* O sistema não cria conteúdo diretamente
* Toda documentação vem de arquivos versionados
* Cada commit pode gerar uma nova versão de documentação

---

### Funcionamento

Fluxo básico:

```text
Repository → Pull → Diff → Process → Version
```

Etapas:

1. Atualização do repositório:

   ```bash
   git pull
   ```

2. Identificação de mudanças:

   ```bash
   git diff-tree -r <commit>
   ```

3. Filtragem de arquivos:

   * Apenas `.md`

4. Processamento:

   * Criação de versão
   * Renderização
   * Publicação

---

### Benefícios

* Versionamento automático
* Auditoria completa
* Facilidade de rollback
* Colaboração distribuída

---

## Webhooks

### Visão geral

Webhooks permitem sincronização automática sempre que há alterações no repositório.

Suportado:

* GitHub
* GitLab

---

### Fluxo

```text
Developer → Push → Webhook → API → Sync → Render → Publish
```

---

### Implementação

#### Rota

```php
Route::post('/webhooks/git', WebhookController::class);
```

---

#### Controller

```php
class WebhookController
{
    public function __invoke(Request $request, DocumentationService $service)
    {
        $project = $request->input('repository.name');
        $commit = $request->input('after');

        $repoPath = base_path("docs/{$project}");

        $service->syncAndRender(
            project: $project,
            repositoryPath: $repoPath,
            commit: $commit
        );

        return response()->json(['ok' => true]);
    }
}
```

---

### Segurança (IMPORTANTE)

Nunca aceite requisições sem validação.

#### GitHub

* Header: `X-Hub-Signature-256`

#### GitLab

* Token secreto configurado no webhook

---

### Boas práticas

* Validar assinatura
* Processar via fila (queue)
* Garantir idempotência por commit
* Registrar logs de execução

---

## Pipeline de processamento

Após o webhook, o sistema executa:

```text
Sync → Version → Render → Publish
```

---

### Etapas

1. **Sync**

   * Atualiza repositório
   * Checkout de branch

2. **Version**

   * Cria nova versão do documento
   * Associa commit

3. **Render**

   * Markdown → HTML

4. **Publish**

   * Define estado como `published`

---

## Search (Futuro)

### Objetivo

Permitir busca eficiente dentro da documentação:

* Full-text search
* Autocomplete
* Ranking de relevância

---

### Arquitetura

```text
DocumentVersionCreated → IndexJob → Search Engine
```

---

### Estrutura de indexação

```json
{
  "id": "doc-123",
  "project": "core",
  "slug": "arquitetura",
  "title": "Arquitetura",
  "content": "...",
  "tags": []
}
```

---

### Tecnologias sugeridas

* Meilisearch
* Elasticsearch

---

## AI Services (Futuro)

### Objetivo

Adicionar inteligência à documentação.

---

### Casos de uso

#### Resumo automático

* Geração de TL;DR

#### Tagging inteligente

* Classificação automática

#### Detecção de desatualização

* Comparação com código

#### Chat com documentação

* Interface conversacional

---

### Arquitetura

```text
DocumentVersionCreated → AI Processor → Enrichment
```

---

### Exemplo de enriquecimento

```json
{
  "summary": "...",
  "keywords": ["DDD", "Laravel"],
  "embedding": [0.123, 0.98]
}
```

---

## Resumo

| Integração | Papel                    |
| ---------- | ------------------------ |
| Git        | Fonte base               |
| Webhook    | Gatilho de sincronização |
| Pipeline   | Processamento            |
| Search     | Descoberta               |
| AI         | Inteligência             |

---

## Conclusão

A camada de integrações transforma o sistema em uma plataforma automatizada e escalável, permitindo que a documentação evolua junto com o código de forma contínua e confiável.
