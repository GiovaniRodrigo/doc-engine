# Usage

Documentation Engine is a Laravel package for serving Markdown documentation
from a `docs` directory, syncing it into database-backed versions, and optionally
using AI helpers to generate, summarize, tag, or chat with document content.

## Requirements

- PHP 8.2 or newer.
- A Laravel application.
- A configured database connection for the package migrations.
- Git available in the host environment if you want `docs:sync` to pull and
  attach commit hashes to document versions.

## Installation

Install the package in the Laravel application that will serve the
documentation:

```bash
composer require giovani/documentation-engine
```

For local development with this repository, add it as a path repository in the
consumer application's `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../doc-engine-lib",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

Then require it normally:

```bash
composer require giovani/documentation-engine
```

Laravel auto-discovers the package service provider:

```php
Giovani\DocumentationEngine\DocumentationServiceProvider::class
```

## Bootstrap

Add the package environment variables to `.env` and `.env.example`:

```bash
vendor/bin/documentation-engine-env
```

You can also run the Composer script from this package repository:

```bash
composer doc-engine:env
```

To update a specific env file:

```bash
vendor/bin/documentation-engine-env --env=/absolute/path/to/.env
```

Existing variables are preserved. Only missing keys are appended.

Publish the package config when you need to customize it:

```bash
php artisan vendor:publish --tag=documentation-config
```

Publish the default views and CSS assets when you want to customize the UI:

```bash
php artisan vendor:publish --tag=documentation-views
php artisan vendor:publish --tag=documentation-assets
```

Run migrations:

```bash
php artisan migrate
```

The package creates:

- `documents`: document identity, slug, title, and tags.
- `document_versions`: document content versions, checksum, state, and optional
  Git commit hash.

## Environment

The most common configuration lives in `.env`:

```dotenv
DOC_ENGINE_PATH=docs
DOC_ENGINE_LAYOUT=documentation-engine::layouts.default
DOC_ENGINE_CSS=
DOC_ENGINE_EDIT_MIDDLEWARE=
DOC_ENGINE_WEBHOOK_SECRET=
DOC_ENGINE_WEBHOOK_BRANCH=main
DOC_ENGINE_AI_ENABLED=false
DOCUMENTATION_AI_PROVIDER=openai

OPENAI_API_KEY=
OPENAI_MODEL=gpt-5-mini
OPENAI_TIMEOUT=30
OPENAI_RETRY_TIMES=1
OPENAI_MAX_OUTPUT_TOKENS=4000

GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
GEMINI_TIMEOUT=30
GEMINI_RETRY_TIMES=1
GEMINI_MAX_OUTPUT_TOKENS=4000
```

`DOC_ENGINE_PATH` is relative to the Laravel application's base path unless an
absolute path is passed to the sync command with `--path`.

`DOC_ENGINE_LAYOUT` can point to a custom Blade layout. The default layout is:

```dotenv
DOC_ENGINE_LAYOUT=documentation-engine::layouts.default
```

`DOC_ENGINE_CSS` can point to a custom CSS file if your layout expects one.

## Markdown Files

Create Markdown files under the configured docs path:

```text
docs/
  README.md
  onboarding/onboarding.md
  backend/backend.md
  architecture/architecture.md
```

Slugs are generated from relative paths:

```text
docs/README.md                    -> readme
docs/onboarding/onboarding.md     -> onboarding.onboarding
docs/backend/backend.md           -> backend.backend
```

The `/docs` index redirects to the first available home slug in this order:

```text
readme, index, home, introducao
```

If none exists, it redirects to the first synced slug.

## Sync Documents

Sync all Markdown files from the configured docs path:

```bash
php artisan docs:sync
```

Sync from a custom path:

```bash
php artisan docs:sync --path=/absolute/path/to/docs
```

Preview a sync without writing documents, versions, summaries, or cache changes:

```bash
php artisan docs:sync --dry-run
```

Sync files under a project prefix:

```bash
php artisan docs:sync meu-projeto
```

With a project prefix, generated slugs are namespaced:

```text
README.md        -> meu-projeto
backend/api.md   -> meu-projeto.backend.api
```

During sync, the package:

- attempts `git pull`;
- reads the current Git commit hash when available;
- detects changed Markdown files when Git diff information is available;
- reports sync metadata such as read, changed, ignored, archived, and errored
  files;
- creates missing `documents` records;
- creates a new `document_versions` record only when the content checksum
  changes;
- marks documents removed from the filesystem as archived;
- invalidates the rendered HTML cache for changed slugs;
- writes a generated summary Markdown file.

## Routes

The package registers these routes:

```text
GET    /docs
GET    /docs/search?q=termo
GET    /docs/{slug}
GET    /docs/{slug}/edit
PUT    /docs/{slug}
GET    /docs/{slug}/versions
GET    /docs/{slug}/versions/compare?from=...&to=...
POST   /docs/{slug}/versions/{version}/publish
POST   /docs/{slug}/generate
POST   /docs/{slug}/chat
POST   /docs/webhooks/github
POST   /docs/webhooks/gitlab
```

Typical browser usage:

```text
/docs
/docs/search?q=instalacao
/docs/readme
/docs/backend.backend
/docs/backend.backend/edit
/docs/backend.backend/versions
```

The reading interface includes:

- an empty state when no documents are synced;
- friendly not-found pages for missing documents;
- local search by title, slug, and published content;
- a table of contents generated from document headings;
- responsive default layout with scoped CSS and `.dark` theme support.

## Editorial Workflow

The browser editor saves changes as `draft` versions. Published readers keep
seeing the current `published` version until a draft is explicitly published.

Protect editing routes with middleware when needed:

```dotenv
DOC_ENGINE_EDIT_MIDDLEWARE=auth
```

You can use multiple comma-separated middleware names:

```dotenv
DOC_ENGINE_EDIT_MIDDLEWARE=web,auth
```

Version states:

- `draft`: saved from the editor, not visible on the public document route.
- `published`: used by `/docs/{slug}` and search.
- `archived`: superseded or removed from the active documentation set.

Publishing a version archives the previous published version and invalidates the
rendered HTML cache for that document.

The edit form updates Markdown content through `PUT /docs/{slug}` and clears the
render cache for that slug.

## AI Features

Enable AI support:

```dotenv
DOC_ENGINE_AI_ENABLED=true
DOCUMENTATION_AI_PROVIDER=openai
OPENAI_API_KEY=...
```

Or use Gemini:

```dotenv
DOC_ENGINE_AI_ENABLED=true
DOCUMENTATION_AI_PROVIDER=gemini
GEMINI_API_KEY=...
```

Generate content for a document:

```bash
curl -X POST http://localhost/docs/readme/generate \
  -H "Content-Type: application/json" \
  -d '{"prompt":"Melhore a clareza deste documento"}'
```

Generate a TL;DR:

```bash
curl -X POST http://localhost/docs/readme/generate \
  -H "Content-Type: application/json" \
  -d '{"type":"tldr"}'
```

Suggest tags:

```bash
curl -X POST http://localhost/docs/readme/generate \
  -H "Content-Type: application/json" \
  -d '{"type":"suggest_tags"}'
```

Chat with a document:

```bash
curl -X POST http://localhost/docs/readme/chat \
  -H "Content-Type: application/json" \
  -d '{"message":"Quais sao os principais pontos deste documento?"}'
```

You can override the provider per generation request:

```json
{
  "provider": "gemini",
  "model": "gemini-2.5-flash",
  "prompt": "Reescreva em formato de checklist"
}
```

## Performance and Cache

The package uses Laravel's cache to store rendered HTML and navigation structures, significantly improving response times for large documentation sets.

### Caching Strategy

- **Rendered HTML**: Cached per slug (`doc_render_{slug}`). This cache is cleared when a new version is published or edited.
- **Navigation (Slugs & Sidebar)**: Cached per project (`doc_slugs_{projectKey}` and `doc_sidebar_{projectKey}`). These are cleared after a successful `docs:sync` or when a document is published/archived.

By default, these caches have a TTL of 24 hours, but they are designed to be explicitly invalidated by the application's editorial actions.

### Redis Recommendation

For production environments, it is highly recommended to use **Redis** as the cache driver. You can configure a dedicated cache store for the documentation engine in your `config/cache.php` to avoid interfering with other application data.

### Performance Monitoring

The package logs execution metrics at different levels:

- **Sync**: Logs total time, number of changes, and archived documents at `info` level.
- **Rendering**: Logs time taken to convert Markdown to HTML at `debug` level.

Example log output:
`[2026-05-05 10:00:00] local.INFO: Sync completed in 45.1234ms (changes: 2, versions: 2, archived: 1)`

## Webhooks

Configure a shared secret:

```dotenv
DOC_ENGINE_WEBHOOK_SECRET=change-me
```

GitHub webhook endpoint:

```text
POST /docs/webhooks/github
```

When GitHub sends `X-Hub-Signature-256`, the package validates the HMAC SHA-256
signature against `DOC_ENGINE_WEBHOOK_SECRET`.

GitLab-style webhooks are also accepted by the same controller action when the
request sends `X-Gitlab-Token`; the token is compared with
`DOC_ENGINE_WEBHOOK_SECRET` when the secret is configured.

After validation, the webhook runs the same Markdown sync use case used by
`php artisan docs:sync`.

## Customizing Views

Publish the package views:

```bash
php artisan vendor:publish --tag=documentation-views
```

Laravel will copy them to:

```text
resources/views/documentation-engine
```

You can customize:

- document display;
- edit form;
- layout wrappers;
- sidebar node rendering;
- partial styles.

To switch layouts without publishing package views, point `DOC_ENGINE_LAYOUT` to
an application view:

```dotenv
DOC_ENGINE_LAYOUT=layouts.docs
```

## Customizing CSS

Publish CSS assets:

```bash
php artisan vendor:publish --tag=documentation-assets
```

The default assets are published to:

```text
resources/css/documentation-engine/docs
```

The package CSS is organized by concern:

```text
layout.css
components.css
sidebar.css
themes.css
article.css
```

## Development

From this package repository, install dependencies:

```bash
composer install
```

Run the test suite:

```bash
vendor/bin/phpunit
```

Useful local files:

```text
config/documentation-engine.php
src/routes.php
src/Console/SyncDocsCommand.php
src/Http/DocumentationController.php
scripts/documentation-engine-env
```

## Troubleshooting

If `/docs` returns 404 with `Nenhuma documentacao encontrada.`, make sure you
have Markdown files under `DOC_ENGINE_PATH` and run:

```bash
php artisan docs:sync
```

If AI routes return 404, confirm:

```dotenv
DOC_ENGINE_AI_ENABLED=true
```

If AI routes return provider errors, confirm the selected provider and API key:

```dotenv
DOCUMENTATION_AI_PROVIDER=openai
OPENAI_API_KEY=...
```

If custom views are not loading, clear Laravel's cached config and views:

```bash
php artisan config:clear
php artisan view:clear
```
