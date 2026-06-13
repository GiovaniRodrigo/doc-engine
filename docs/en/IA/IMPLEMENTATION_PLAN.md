# Implementation Plan

This plan organizes the evolution of the Documentation Engine from a basic Markdown synchronizer to a complete documentation engine with versioning, editorial workflow, and artificial intelligence.

## Objective

Provide a Laravel package that transforms Markdown files into a rich, navigable, and editable documentation platform, facilitating technical maintenance and reading for end users.

Developers should be able to:

* Install the package via Composer;
* Sync documents locally or via Git;
* Configure webhooks for automatic updates;
* Use AI to summarize, categorize, or answer questions about documents;
* Manage versions (draft, published, archived) via a web interface.

## Current State

The project already has the solid foundations described in `ROADMAP.md`:

* Core synchronization and versioning (Phases 1 and 2);
* Support for multiple AI providers (Phase 6);
* Reading routes and functional editor (Phases 4 and 5);
* Webhook support (Phase 3).

## AI Workflow for New Features

For any new implementation, the AI must follow `docs/IA/AI_FEATURE_WORKFLOW.md`:

1. Consult `docs/IA/CLEAN_CONTEXT.md`.
2. Identify the business rule in `docs/` or propose a new one.
3. Present the plan and wait for approval.
4. Create or update tests under `tests/Feature/`.
5. Implement in the smallest possible scope under `src/`.
6. Validate using `vendor/bin/phpunit`.

## Architecture

```text
[Filesystem/Git] -> [SyncUseCase] -> [DocumentRepository] -> [Database]
                                            |
                                            v
[AI Provider] <-> [AI Services] <-> [DocumentViewData] -> [Blade Views]
                                            |
                                            v
                                     [Render Cache]
```

### Main Modules

* `src/Application/UseCases/`: Orchestration of business rules.
* `src/Domain/Entities/`: Document and DocumentVersion models.
* `src/Infrastructure/Rendering/`: Markdown to HTML conversion.
* `src/Infrastructure/AI/`: Adapters for OpenAI and Gemini.
* `src/Infrastructure/Persistence/`: Eloquent repository implementations.

## Implementation Phases (Summary Roadmap)

### Phase 7 - Performance and Cache (Completed)

* Implement rendered HTML cache per slug+version.
* Cache sidebar and navigation per project to avoid excessive database queries.
* Invalidate cache on sync or publish events.

### Phase 8 - Quality and Distribution (Next Step)

* Add Composer scripts for static analysis and formatting.
* Configure CI for automatic test execution.
* Review compatibility with multiple Laravel versions.
* Document extensibility and asset publishing.

## AI Strategy

AI features must be treated as optional "augmentations":

1. **Tag Suggestion:** Analyze content and suggest tags for the `Document`.
2. **TL;DR / Summaries:** Generate quick summaries for display in search or headers.
3. **Context-Aware Chat:** Simple RAG passing the document's content to the model.
4. **Text Improvement:** Propose grammatical or clarity revisions in the editor.

## Testing

Every new Use Case must be accompanied by a test under `tests/Feature/` covering:
- Successful operation.
- Validation or configuration errors.
- Correct database persistence.
- Cache invalidation when applicable.
