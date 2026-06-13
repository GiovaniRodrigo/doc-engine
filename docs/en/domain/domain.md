# 🧠 Domain

## 📌 Overview

The domain represents the **system core**.

Here are the:

* Business rules
* Entities
* Invariants

Without framework dependencies.

---

## 🧬 Entities

---

### 📄 Document

Represents a logical document.

#### Attributes

* project
* path
* slug
* title

#### Responsibilities

* Unique identification
* Logical organization

---

### 📄 DocumentVersion

Represents a version of a document.

#### Attributes

* markdown
* html
* commit_hash
* state

#### States

```text
draft
published
```

---

## 🧠 Business Rules

* Each modification generates a new version
* Identical content does not generate a new version
* Versions have a controlled state workflow
* Every version belongs to a Git commit

---

## 🧱 Value Objects

---

### DocumentSlug

* Human-readable identifier
* Used in URLs

---

### DocumentPath

* Physical file path
* Based on the repository root

---

### CommitHash

* Represents a valid commit hash
* Immutable

---

## 🧩 Domain Services

---

### PublicationStateMachine

Controls state transitions.

```text
draft → published
```

---

### RepositoryDiffService

* Detects changes in the repository
* Filters relevant files

---

## 🧠 Invariants

* A document cannot exist without a path
* A version must have a valid commit hash
* State must remain consistent
* No duplicate content versioning

---

## 🔄 Lifecycle

```text
File → Document → Version → Publish
```

---

## 🚀 Future Evolutions

* Full editorial versioning workflow
* Review and approval process
* Comparative history (diffs)
* Domain events
