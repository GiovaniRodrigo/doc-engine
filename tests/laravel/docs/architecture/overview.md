# Architecture Overview

Documentation Engine is structured around four layers that keep the package decoupled from any specific Laravel application.

## Layers

### 1. Storage — Markdown on Disk

Markdown files live in your `docs/` directory. The `FilesystemMarkdownStorage` class reads and writes them directly. No proprietary format — plain `.md` files you can edit with any editor.

### 2. Git — Version Source of Truth

`GitVersionResolver` runs `git log` and `git diff` to detect which files changed since the last sync. Every `document_versions` row stores the commit hash it was created from, giving you a full audit trail.

### 3. Database — Metadata and Versions

Two tables power the engine:

| Table | Purpose |
|---|---|
| `documents` | Slug, title, tags, active/archived state |
| `document_versions` | Content, checksum, state (draft/published/archived), commit hash |

Only a new version is written when the content checksum changes — syncing the same files twice produces no duplicates.

### 4. Web UI — Read, Edit, Publish

Blade views handle the reader interface (`/docs/{slug}`), the browser editor (`/docs/{slug}/edit`), version history, and diff comparison. All views are publishable and overridable.

## Request Flow

```
Browser → Laravel Router → DocumentationController
  → EloquentDocumentRepository (reads published version)
  → FilesystemMarkdownStorage (optional, for sync)
  → Cache (rendered HTML, sidebar, navigation)
  → Blade view
```

## AI Integration

When `DOC_ENGINE_AI_ENABLED=true`, the `AiProvider` interface is bound to either `OpenAiProvider` or `GeminiProvider` based on `DOCUMENTATION_AI_PROVIDER`. Requests flow through `POST /docs/{slug}/generate` and `POST /docs/{slug}/chat`.
