# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
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
