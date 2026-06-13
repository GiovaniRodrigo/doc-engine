# CLEAN_CONTEXT: Documentation Engine

## 1. Overview and Objective

**Domain:** Markdown documentation engine for Laravel applications, featuring Git versioning and AI integration.

**Objective:** Provide a Laravel package (library) that synchronizes Markdown files from the filesystem or Git into the database, allowing serving, searching, editing, and enriching documentation with the help of AI (OpenAI/Gemini).

**Current Stack:** PHP 8.2+, Laravel (10, 11, and 12), CommonMark (GFM), Eloquent, OpenAI/Gemini SDKs (via HTTP), PHPUnit.

## 2. Core Business Entities

### Document

- Stable Identity: Unique slug.
- Metadata: Title, tags, project (optional).
- Publication State: Points to the currently active version.

### DocumentVersion

- Raw content (Markdown) and rendered content (HTML).
- Integrity Control: Checksum (content hash).
- States: `draft`, `published`, `archived`.
- Origin Metadata: Commit hash (when synced via Git).

### SyncResult

- Summary of the sync operation: read, created, modified, ignored files, and errors.
- Idempotency: Does not create a new version if the checksum is identical.

## 3. Non-negotiable Business Rules

| ID | Rule | Restriction |
| --- | --- | --- |
| RN01 | Content versioning | A new version is only created if the content (checksum) differs from the previous one. |
| RN02 | Editorial workflow | Readers only see the `published` version. UI edits create `drafts`. |
| RN03 | Optional & secure AI | AI features require `DOC_ENGINE_AI_ENABLED=true` and never write keys or sensitive data to logs. |
| RN04 | Resilient synchronization | Failures in individual files during sync do not interrupt the processing of others. |
| RN05 | Smart cache | HTML rendering must be cached by slug+version and invalidated upon state changes. |

## 4. Implementation Patterns

**Architecture:** Based on Use Cases (Application), Entities (Domain), and isolated Infrastructure (AI, Storage, Rendering).

Main layers:

- `src/Application/UseCases/`: Core logic (Sync, Publish, Generate, etc).
- `src/Domain/Entities/`: Eloquent models with domain logic.
- `src/Infrastructure/`: Driver implementations (AI Providers, Git, Markdown).
- `src/Http/`: Package controllers and routes.

**Code Style:** PSR-12, strong typing (PHP 8.2+), Feature tests covering complete workflows, Mocks for external calls (AI).

## 5. Current File Mapping

- `ROADMAP.md`: Evolution phases of the package.
- `USAGE.md`: Installation and usage guide for developers.
- `docs/IA/IMPLEMENTATION_PLAN.md`: Detailed AI and new features plan.
- `docs/IA/AI_FEATURE_WORKFLOW.md`: Test- and rule-oriented implementation workflow.
- `src/DocumentationServiceProvider.php`: Laravel package registration.
- `src/routes.php`: Web, API, and webhooks route definition.
- `config/documentation-engine.php`: Publishable configuration.
