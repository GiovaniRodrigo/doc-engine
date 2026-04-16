# Domínio

## Entidades

### Document

Representa um documento lógico dentro do sistema.

- project
- path
- slug
- title

### DocumentVersion

Representa uma versão do documento.

- markdown
- html
- commit_hash
- state

## Regras

- Cada alteração gera nova versão
- Conteúdo igual não gera nova versão
- Versões possuem estado (draft, published)

## Value Objects

- DocumentSlug
- DocumentPath
- CommitHash