# 🧠 Architecture

## 📌 Overview

The system follows the principles of **DDD (Domain-Driven Design)** combined with **Clean Architecture**, ensuring:

* Clear separation of concerns
* Low coupling
* High testability
* Ease of future evolution

---

## 🔄 Main Flow

```text
Commit → Webhook → Sync → Version → Render → Publish → Cache → UI
```

### 📍 Steps

#### 1. Commit

* Modifications in `.md` files
* The commit is the source of truth
* Generates a unique, immutable hash

---

#### 2. Webhook

* Triggered by GitHub/GitLab
* Notifies the system of repository changes

```http
POST /webhook/git
```

---

#### 3. Sync (SourceControl)

Responsible for synchronizing the repository and detecting changes.

**Actions:**

* `git pull`
* Branch checkout
* Diff analysis

**Output:**

```php
[
  "docs/architecture.md",
  "docs/domain.md"
]
```

---

#### 4. Version (Domain)

Transforms files into versioned entities.

**Rules:**

* Same content → no new version is created
* Different content → creates a new version
* Always linked to a commit

**Entities:**

* `Document`
* `DocumentVersion`

**Value Objects:**

* `DocumentPath`
* `DocumentSlug`
* `CommitHash`

---

#### 5. Render

Converts Markdown to HTML.

```php
$html = $renderer->render($markdown);
```

**Base Engine:**

* CommonMark

---

#### 6. Publish

Controls the version state.

**States:**

```text
draft → published
```

---

#### 7. Cache

Avoids re-processing and improves response times.

```php
cache()->remember("doc:$slug", fn() => $html);
```

---

#### 8. UI (Interface)

Delivers the final output to the user.

```http
GET /docs/{project}/{slug}
```

---

## 🧱 Architectural Layers

---

### 🟣 Domain

Contains the core of the system:

* Entities
* Business rules
* Invariants

🚫 Does not depend on:

* Laravel
* Databases
* HTTP libraries

---

### 🔵 Application

Responsible for orchestration:

* Use Cases
* Application Services

**Typical flow:**

```text
Sync → Version → Render → Save
```

---

### 🟡 Infrastructure

Implements technical details:

* Databases
* Git Integration
* Cache stores
* Renderers

---

### 🟢 Interface

System entry points:

* Controllers
* Console Commands
* Webhook Handlers

---

## 🧩 Main Components

---

### 🔄 Git Sync

* Synchronizes repository code
* Detects changed files

---

### 🧬 Versioning

* Full history tracked per commit
* State workflow control
* Prevents version duplication

---

### 🎨 Renderer

* Converts Markdown to HTML
* Highly extensible

---

### 🌳 Navigation Builder

* Generates navigation tree based on directory structures

```text
docs/
 ├── architecture/
 └── domain/
```

---

### ⚡ Cache

* Document caching
* Version caching
* Project-wide navigation caching

---

## 🧠 Conclusion

The system functions as a:

> **Git-Driven Documentation Engine with versioning and an automated pipeline.**

---

## 🚀 Next Steps

* Structured database persistence
* Caching with Redis
* Queues for async rendering
* Meilisearch integration
* Rich reading interface
