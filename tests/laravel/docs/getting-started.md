# Getting Started

Documentation Engine turns your Markdown files into a versioned, searchable documentation site — with an editorial workflow, Git-tracked history, and optional AI assistance.

## Installation

```bash
composer require giovani/documentation-engine
```

Run the environment setup script and follow the prompts:

```bash
vendor/bin/documentation-engine-env
```

Publish assets and run migrations:

```bash
php artisan vendor:publish --tag="documentation-config"
php artisan vendor:publish --tag="documentation-assets"
php artisan migrate
```

## Syncing Documents

Place your Markdown files in the `docs/` directory and run:

```bash
php artisan docs:sync
```

Your documentation is now available at `/docs`.

## Key Features

- **Git versioning** — every sync is tied to a commit hash.
- **Editorial workflow** — save drafts, compare versions, publish explicitly.
- **Full-text search** — search by title, slug, and content.
- **AI assistance** — generate summaries, suggest tags, chat with documents.
- **Webhooks** — auto-sync on GitHub or GitLab push events.
- **Customizable** — publish views, CSS, and layout to your app.
