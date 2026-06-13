# 🔄 Workflows

## 📌 Overview

Workflows describe how documentation flows through the system.

---

## 🚀 Main Workflow — Automatic Publication

```text
Commit → Webhook → Sync → Version → Render → Publish → Cache → UI
```

---

## 🔍 Steps

---

### 1. Commit

* Developer modifies a `.md` file
* Commit is pushed to the repository

---

### 2. Webhook

* Event triggered automatically
* Backend receives webhook notification

---

### 3. Sync

* Runs `git pull`
* Detects changed files

---

### 4. Version

* Creates a new document version
* Associates it with the commit hash

---

### 5. Render

* Converts Markdown → HTML

---

### 6. Publish

* Updates state to `published`

---

### 7. Cache

* Stores rendered HTML
* Avoids re-processing on subsequent requests

---

### 8. UI

* User accesses documentation
* Content served quickly from cache

---

## 🛠 Manual Workflow

Executed via CLI:

```bash
php artisan docs:sync {project}
```

### When to use

* Webhook delivery failure
* Manual reprocessing
* Debugging purposes

---

## ⚠️ Error Workflow

### Possible failures

* Git pull failure
* Rendering error
* Inconsistent cache state

### Actions

1. Check application logs
2. Execute manual sync
3. Validate queue status

---

## 🔄 Update Workflow

```text
New Commit → New Version → Re-render → Cache Update
```

---

## 🧠 Notes

* The pipeline is idempotent
* Can be run multiple times safely
* Event-based (future evolution)

---

## 🚀 Future Evolutions

* Asynchronous processing
* Automatic retry mechanism
* Dead-letter queue
* Observability (logging + metrics dashboard)
