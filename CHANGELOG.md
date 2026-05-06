# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Middleware for authentication in documentation routes.
- Composer scripts: `test`, `analyse`, `format`.
- GitHub Actions for CI.
- PHPStan static analysis (Level 5).
- Laravel Pint formatting.
- `documentation-migrations` publish tag.
- Main `README.md` and `CHANGELOG.md`.

### Fixed
- Fixed redundant cache invalidation in `UpdateDocument` and `DocumentationController`.
- Fixed `CacheTest` failure when `doc_slugs_all` is called.
- Fixed PHPStan warning in `PublishDocumentVersion`.

## [2.0.0] - 2026-05-04

### Added
- Implementation of all core phases (1 to 8).
- Core Markdown synchronization engine from filesystem and Git.
- Document versioning system with states: `draft`, `published`, `archived`.
- AI integration with OpenAI and Google Gemini (summaries, tags, chat).
- Local search functionality (titles, slugs, content).
- Editorial workflow UI for reading, editing, and comparing versions.
- Webhook support for GitHub and GitLab automatic synchronization.
- Intelligent caching for rendered HTML, sidebars, and navigation.
- Responsive Blade views and customizable layout support.
- Environment setup script `vendor/bin/documentation-engine-env`.

## [1.0.0] - 2026-03-25

### Added
- Initial project structure.
- Basic document synchronization command.
- Initial database migrations for documents and versions.
