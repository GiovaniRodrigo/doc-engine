# Integrations

## Git

### Overview

Git is used as the **source of truth** for all system documentation.

This means:

* The system does not create content directly
* All documentation comes from versioned files
* Each commit can generate a new documentation version

---

### How It Works

Basic workflow:

```text
Repository → Pull → Diff → Process → Version
```

Steps:

1. Update repository:

   ```bash
   git pull
   ```

2. Identify changes:

   ```bash
   git diff-tree -r <commit>
   ```

3. Filter files:

   * Only `.md` files

4. Process:

   * Create version
   * Render HTML
   * Publish version

---

### Benefits

* Automatic versioning
* Full audit trail
* Easy rollbacks
* Distributed collaboration

---

## Webhooks

### Overview

Webhooks allow automatic synchronization whenever changes occur in the repository.

Supported:

* GitHub
* GitLab

---

### Flow

```text
Developer → Push → Webhook → API → Sync → Render → Publish
```

---

### Implementation

#### Route

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

### Security (IMPORTANT)

Never accept requests without validation.

#### GitHub

* Header: `X-Hub-Signature-256`

#### GitLab

* Secret token configured in the webhook

---

### Best Practices

* Validate signatures
* Process via queue
* Ensure idempotency per commit
* Keep execution logs

---

## Processing Pipeline

After a webhook triggers, the system runs:

```text
Sync → Version → Render → Publish
```

---

### Steps

1. **Sync**
   * Updates repository
   * Checkouts target branch

2. **Version**
   * Creates a new document version
   * Associates the Git commit hash

3. **Render**
   * Markdown → HTML

4. **Publish**
   * Sets version state to `published`

---

## Search (Future)

### Objective

Enable efficient searching within the documentation:

* Full-text search
* Autocomplete
* Relevance ranking

---

### Architecture

```text
DocumentVersionCreated → IndexJob → Search Engine
```

---

### Indexing Structure

```json
{
  "id": "doc-123",
  "project": "core",
  "slug": "architecture",
  "title": "Architecture",
  "content": "...",
  "tags": []
}
```

---

### Suggested Technologies

* Meilisearch
* Elasticsearch

---

## AI Services (Future)

### Objective

Add intelligence features to the documentation.

---

### Use Cases

#### Automatic Summarization

* TL;DR generation

#### Smart Tagging

* Automatic classification

#### Outdated Document Detection

* Comparison with codebase changes

#### Documentation Chat

* Conversational interface

---

### Architecture

```text
DocumentVersionCreated → AI Processor → Enrichment
```

---

### Enrichment Example

```json
{
  "summary": "...",
  "keywords": ["DDD", "Laravel"],
  "embedding": [0.123, 0.98]
}
```

---

## Summary

| Integration | Role |
| ----------- | ----------------------- |
| Git         | Source of truth |
| Webhook     | Synchronization trigger |
| Pipeline    | Processing |
| Search      | Discovery |
| AI          | Intelligence |

---

## Conclusion

The integration layer transforms the system into an automated, scalable platform, enabling documentation to evolve alongside the code in a continuous and reliable manner.
