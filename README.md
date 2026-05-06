# Documentation Engine

[![Tests](https://github.com/giovani/documentation-engine/actions/workflows/tests.yml/badge.svg)](https://github.com/giovani/documentation-engine/actions/workflows/tests.yml)

A powerful Markdown documentation engine for Laravel applications with Git versioning and AI integration.

## Features

- **Git-Driven**: Source of truth is your Git repository.
- **Versioning**: Track multiple versions of your documents, linked to Git commits.
- **AI Integration**: Summarize, tag, and chat with your documentation using OpenAI or Google Gemini.
- **Search**: Built-in local search for titles and content.
- **Editorial Workflow**: Manage drafts and published versions through a simple UI.
- **Webhooks**: Automatic synchronization via GitHub or GitLab webhooks.
- **Customizable**: Flexible layout and CSS.

## Installation

You can install the package via composer:

```bash
composer require giovani/documentation-engine
```

After installation, run the environment setup script:

```bash
vendor/bin/documentation-engine-env
```

This will guide you through setting up the necessary `.env` variables.

Next, publish the configuration and assets:

```bash
php artisan vendor:publish --tag="documentation-config"
php artisan vendor:publish --tag="documentation-assets"
php artisan vendor:publish --tag="documentation-views"
php artisan vendor:publish --tag="documentation-migrations"
```

Run the migrations:

```bash
php artisan migrate
```

## Usage

### Syncing Documents

To sync your Markdown files to the database, run:

```bash
php artisan docs:sync {project?} {--path=}
```

### Accessing Documentation

The documentation is available at `/docs` by default.

## Configuration

The configuration file is located at `config/documentation-engine.php`. Key settings include:

- `docs_path`: Path where your Markdown files are stored.
- `layout`: The base layout for documentation views.
- `middleware`: Middleware applied to documentation UI routes. Use `web,auth` to require authentication.
- `edit_middleware`: Additional middleware applied to editorial routes.
- `ai.enabled`: Enable or disable AI features.
- `ai.driver`: Choose between `openai` and `gemini`.

## Publish Tags

- `documentation-config`: Configuration file.
- `documentation-views`: Blade templates for customization.
- `documentation-assets`: CSS files.
- `documentation-migrations`: Database migrations.

## Quality Assurance

We maintain high standards of quality using the following tools:

```bash
composer test      # Run PHPUnit tests
composer analyse   # Run PHPStan static analysis
composer format    # Format code with Laravel Pint
```

## Security

If you discover any security-related issues, please email instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
