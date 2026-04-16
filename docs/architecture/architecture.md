# Arquitetura

## Visão Geral

O sistema segue arquitetura baseada em DDD + Clean Architecture.

## Fluxo principal

Commit → Webhook → Sync → Version → Render → Publish → Cache → UI

## Camadas

### Domain
- Entidades
- Regras de negócio

### Application
- UseCases
- Orquestração

### Infrastructure
- Banco
- Git
- Render

### Interface
- Controllers
- Commands

## Principais componentes

- Git Sync
- Versionamento
- Renderer
- Navigation Builder
- Cache