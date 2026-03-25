TENANTS
PROJECTS
DOCUMENTS
DOCUMENT_VERSIONS
DOCUMENT_NODES
DOCUMENT_EVENTS
DOCUMENT_ANALYTICS
DOCUMENT_EMBEDDINGS
USERS

Relacionamentos:

Tenant → Projects (1:N)
Project → Documents (1:N)
Document → Versions (1:N)
Version → Embedding (1:1)



---

# 📄 platform/render-pipeline.md

```md
## Pipeline

Pull Repo
→ Detect Diff
→ Create Versions
→ Dispatch Render Jobs
→ Persist HTML
→ Invalidate Cache
→ Update Search

Estratégias:

Incremental render
Lazy render
Diff render
Batch render



---

# 📄 platform/shard-strategy.md

```md
Shard por:

- tenant_id
- project_id
- hash(slug)

Objetivo:

- distribuir IO
- reduzir lock
- escalar horizontalmente