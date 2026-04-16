# ADR-001 — Uso de DDD (Domain-Driven Design)

**Autor:** Giovani Fernandes  
**Data:** 2026-04-15  
**Status:** Aceito  

---

## 🎯 Contexto

O sistema de documentação possui características que aumentam significativamente a complexidade do domínio:

### 📌 Complexidades identificadas

- **Versionamento de documentos**
  - múltiplas versões por arquivo
  - relação com commits Git
  - prevenção de duplicidade por hash

- **Pipeline de publicação**
  - estados (draft, published, futuramente review/approved)
  - controle de transição de estado
  - consistência entre versões

- **Renderização**
  - transformação Markdown → HTML
  - possibilidade de extensões (plugins, syntax highlight, etc.)
  - necessidade de processamento assíncrono (fila)

- **Integração com Git**
  - leitura de diffs por commit
  - sincronização de repositório
  - suporte a branch e histórico

- **Navegação estruturada**
  - derivada da árvore de diretórios
  - precisa refletir estrutura real do repositório

---

## ⚠️ Problema arquitetural

Uma abordagem tradicional baseada apenas em:

- Controllers + Services
- Models (Eloquent) como regra de negócio

levaria a:

- Alto acoplamento com framework
- Regras de negócio espalhadas
- Dificuldade de evolução (ex: adicionar estados, workflows, eventos)
- Baixa testabilidade
- Complexidade crescente não controlada

---

## ✅ Decisão

Adotar:

> **DDD (Domain-Driven Design) + Clean Architecture**

com separação clara de responsabilidades em camadas.

---

## 🧱 Estrutura adotada

### 1. Domain Layer (núcleo do sistema)

Responsável por conter as regras de negócio puras.

**Contém:**
- Entidades (`Document`, `DocumentVersion`)
- Value Objects (`CommitHash`, `DocumentSlug`)
- Regras (`PublicationStateMachine`)
- Interfaces (`DocumentRepository`)

**Características:**
- Independente de framework
- Sem dependência de infraestrutura
- Alto foco em consistência

---

### 2. Application Layer

Responsável por orquestrar os casos de uso.

**Contém:**
- Use Cases (`PublishVersion`, `BuildNavigationTree`)
- Serviços de aplicação (`DocumentationService`)
- DTOs

**Responsabilidade:**
- Coordenar domínio + infraestrutura
- Não conter regra de negócio complexa

---

### 3. Infrastructure Layer

Responsável por integrações externas.

**Contém:**
- Persistência (`EloquentDocumentRepository`)
- Git (`GitCliRepository`, `CommitDiffAnalyzer`)
- Render (`CommonMarkRenderer`)

---

### 4. Interface Layer

Responsável pela entrada do sistema.

**Contém:**
- Controllers (`ShowDocumentationController`)
- Commands (`SyncDocsCommand`)
- Rotas / CLI

---

## 🔄 Fluxo arquitetural
Interface → Application → Domain ← Infrastructure

- Interface chama Application
- Application usa Domain
- Infrastructure implementa interfaces do Domain

---

## 🧠 Decisões complementares

### ✔ Uso de Repository Pattern
- Isola domínio do banco
- Permite troca de ORM ou storage

### ✔ Uso de Value Objects
- Garante consistência (ex: commit válido)
- Evita strings primitivas espalhadas

### ✔ Separação por contexto (Bounded Contexts)
- Documentation
- SourceControl
- Publishing
- Navigation

### ✔ Preparação para Event-Driven
- Eventos de domínio podem ser adicionados sem quebrar arquitetura

---

## 📈 Consequências

### 👍 Positivas

- Código altamente organizado
- Domínio explícito e compreensível
- Alta testabilidade (unit tests no domínio)
- Facilidade para evolução futura
- Pronto para escalar (features complexas)
- Baixo acoplamento com framework

---

### ⚠️ Negativas

- Curva de aprendizado maior
- Mais arquivos e abstrações
- Overhead inicial de implementação
- Pode ser excessivo para sistemas simples

---

## 🚀 Impacto futuro

Essa decisão permite evoluir o sistema para:

- versionamento editorial completo
- publicação com workflow (review, approval)
- render distribuído (fila)
- busca semântica
- multi-tenant
- sistema SaaS de documentação

---

## 🧭 Alternativas consideradas

### ❌ Arquitetura tradicional (MVC + Services)
- mais simples inicialmente
- porém não escala bem com complexidade de domínio

### ❌ Modular monolith sem DDD explícito
- organização parcial
- regras ainda tendem a se misturar

---

## 📌 Conclusão

DDD foi escolhido porque:

> O sistema é orientado a regras de negócio complexas e evolução contínua.

Essa decisão prioriza **clareza de domínio, escalabilidade e manutenção de longo prazo** em detrimento de simplicidade inicial.

---