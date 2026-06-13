# ⚙️ Backend

## 📌 Overview

The backend is responsible for:

* Orchestrating the documentation pipeline
* Processing version history
* Rendering Markdown content
* Exposing endpoints for consumption

Built with:

* **Laravel 12**
* **PHP 8.3**
* **DDD + Clean Architecture**

---

## 🧱 Structure

```text
src/
 ├── Domain/
 ├── Application/
 ├── Infrastructure/
 └── Interface/
```

---

## 🔵 Application Layer

Responsible for coordinating system workflows.

### Main Use Cases

#### SyncDocumentation

* Sincronizes repository
* Detects changed files

#### RenderDocumentation

* Converts Markdown → HTML

#### PublishDocumentation

* Publishes document versions

---

### Workflow Example

```text
Sync → Version → Render → Persist
```

---

## 🟣 Domain Layer

Contains pure business rules.

### Entities

* Document
* DocumentVersion

### Responsibilities

* Ensure consistency
* State control workflow
* Prevent duplicate versions

---

## 🟡 Infrastructure Layer

Implements technical details.

### Components

* Git CLI integration
* Eloquent ORM
* Renderer (CommonMark)
* Cache (Redis)

---

## 🟢 Interface Layer

System entry points.

### HTTP

* Controllers
* Webhooks

### CLI

```bash
php artisan docs:sync {project}
```

---

## 🧩 Patterns Used

* Repository Pattern
* Value Objects
* Domain Services
* Application Services

---

## ⚡ Performance

### Strategies

* Caching per document
* Caching per version
* On-demand or asynchronous rendering

---

## 🔐 Security Considerations

* HTML Sanitization
* Access control (Middleware integration)
* Project-based isolation

---

## 🚀 Future Evolutions

* Queued processing (Redis / Horizon)
* Full-text search
* Semantic indexing
* Public API
