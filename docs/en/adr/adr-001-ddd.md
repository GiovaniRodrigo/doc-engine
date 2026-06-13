# ADR-001 — Use of DDD (Domain-Driven Design)

**Author:** Giovani Fernandes  
**Date:** 2026-04-15  
**Status:** Accepted  

---

## 🎯 Context

The documentation system has characteristics that significantly increase domain complexity:

### 📌 Identified Complexities

- **Document Versioning**
  - Multiple versions per file
  - Relationship with Git commits
  - Prevention of duplicate versions via content hash

- **Publishing Pipeline**
  - States (`draft`, `published`, and future `review`/`approved`)
  - State transition control
  - Consistency between versions

- **Rendering**
  - Markdown → HTML transformation
  - Extensibility support (plugins, syntax highlighting, etc.)
  - Need for asynchronous processing (queues)

- **Git Integration**
  - Reading diffs per commit
  - Repository synchronization
  - Branch and history support

- **Structured Navigation**
  - Derived from the directory tree
  - Must reflect the actual repository structure

---

## ⚠️ Architectural Problem

A traditional approach based only on:

- Controllers + Services
- Models (Eloquent) holding the business logic

Would lead to:

- High coupling with the framework
- Scattered business rules
- Difficulty in evolving the application (e.g., adding states, workflows, events)
- Low testability
- Growing, uncontrolled complexity

---

## ✅ Decision

Adopt:

> **DDD (Domain-Driven Design) + Clean Architecture**

with a clear separation of responsibilities into layers.

---

## 🧱 Adopted Structure

### 1. Domain Layer (System Core)

Responsible for containing pure business rules.

**Contains:**
- Entities (`Document`, `DocumentVersion`)
- Value Objects (`CommitHash`, `DocumentSlug`)
- Rules (`PublicationStateMachine`)
- Interfaces (`DocumentRepository`)

**Characteristics:**
- Framework-independent
- No infrastructure dependencies
- High focus on consistency

---

### 2. Application Layer

Responsible for orchestrating use cases.

**Contains:**
- Use Cases (`PublishVersion`, `BuildNavigationTree`)
- Application services (`DocumentationService`)
- DTOs

**Responsibility:**
- Coordinate domain + infrastructure
- Avoid complex business rules within this layer

---

### 3. Infrastructure Layer

Responsible for external integrations.

**Contains:**
- Persistence (`EloquentDocumentRepository`)
- Git (`GitCliRepository`, `CommitDiffAnalyzer`)
- Render (`CommonMarkRenderer`)

---

### 4. Interface Layer

Responsible for system entry points.

**Contains:**
- Controllers (`ShowDocumentationController`)
- Commands (`SyncDocsCommand`)
- Routes / CLI

---

## 🔄 Architectural Flow
Interface → Application → Domain ← Infrastructure

- Interface calls Application
- Application uses Domain
- Infrastructure implements Domain interfaces

---

## 🧠 Complementary Decisions

### ✔ Use of the Repository Pattern
- Isolates the domain from the database
- Allows switching the ORM or storage engine

### ✔ Use of Value Objects
- Ensures consistency (e.g., valid commit format)
- Avoids primitive obsession (e.g., scattered string validations)

### ✔ Separation by Context (Bounded Contexts)
- Documentation
- SourceControl
- Publishing
- Navigation

### ✔ Preparation for Event-Driven Architecture
- Domain events can be added without breaking the architecture

---

## 📊 Consequences

### 👍 Positive

- Highly organized code
- Explicit and understandable domain
- High testability (unit tests in the domain)
- Ease of future evolution
- Ready to scale (complex features)
- Low coupling with the framework

---

### ⚠️ Negative

- Steeper learning curve
- More files and abstractions
- Initial implementation overhead
- Might be excessive for simpler systems

---

## 🚀 Future Impact

This decision enables the system to evolve into:

- Full editorial versioning
- Workflow-based publishing (review, approval)
- Distributed rendering (queue)
- Semantic search
- Multi-tenancy
- SaaS documentation platform

---

## 🧭 Alternatives Considered

### ❌ Traditional Architecture (MVC + Services)
- Simpler initially
- But does not scale well with domain complexity

### ❌ Modular Monolith without Explicit DDD
- Partial organization
- Rules still tend to bleed into each other

---

## 📌 Conclusion

DDD was chosen because:

> The system is oriented toward complex business rules and continuous evolution.

This decision prioritizes **domain clarity, scalability, and long-term maintenance** over initial simplicity.
