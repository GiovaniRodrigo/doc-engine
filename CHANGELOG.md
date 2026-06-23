# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2026-06-23

### Added
- UML diagram support in the browser editor via Mermaid (flowchart, sequence, class, use case, state diagrams).
- Internationalisation (i18n): all UI strings extracted to language files; English and Portuguese bundles included; `documentation-translations` publish tag.
- Language filter in the sidebar — users can narrow the document list by language namespace.
- AI chat panel available directly from the reading view (`POST /docs/{slug}/chat`).
- Real-time collaborative editing indicator — shows other active editors and a conflict warning.
- Activity log (`document_activity_log` table) tracking views, drafts, publishes, and syncs.
- Document analytics table for read-count tracking.
- Editor toolbar with formatting shortcuts (bold, italic, heading, code, link, list, table) and Mermaid diagram insertion.
- Responsive sidebar with mobile drawer and improved accessibility.
- Docker Compose development environment for running the test suite with a full browser stack.
- Middleware for authentication in documentation routes.
- Composer scripts: `test`, `analyse`, `format`.
- GitHub Actions CI pipeline.
- PHPStan static analysis (level 5).
- Laravel Pint code formatting.
- `documentation-migrations` publish tag.
- Comprehensive `README.md` with badges and screenshots.

### Fixed
- `display: flex` on `.docs-dropdown-menu` was overriding the HTML `hidden` attribute, causing the Diagram dropdown to render open on page load. Fixed by scoping the display rule to `:not([hidden])`.
- Redundant cache invalidation in `UpdateDocument` and `DocumentationController`.
- `CacheTest` failure when `doc_slugs_all` is called.
- PHPStan warning in `PublishDocumentVersion`.

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
