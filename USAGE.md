# Usage

Documentation Engine is a Laravel package for serving Markdown documentation
from a `docs` directory, syncing it into database-backed versions, and optionally
using AI helpers to generate, summarize, tag, or chat with document content.

## Requirements

- PHP 8.1 or newer.
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
DOC_ENGINE_WEBHOOK_SECRET=
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
- creates missing `documents` records;
- creates a new `document_versions` record only when the content checksum
  changes;
- invalidates the rendered HTML cache for changed slugs;
- writes a generated summary Markdown file.

## Routes

The package registers these routes:

```text
GET    /docs
GET    /docs/{slug}
GET    /docs/{slug}/edit
PUT    /docs/{slug}
POST   /docs/{slug}/generate
POST   /docs/{slug}/chat
POST   /docs/webhooks/github
```

Typical browser usage:

```text
/docs
/docs/readme
/docs/backend.backend
/docs/backend.backend/edit
```

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
