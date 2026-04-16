# 🧠 Arquitetura

## 📌 Visão Geral

O sistema segue os princípios de **DDD (Domain-Driven Design)** combinados com **Clean Architecture**, garantindo:

* Separação clara de responsabilidades
* Baixo acoplamento
* Alta testabilidade
* Facilidade de evolução

---

## 🔄 Fluxo Principal

```text
Commit → Webhook → Sync → Version → Render → Publish → Cache → UI
```

### 📍 Etapas

#### 1. Commit

* Alterações em arquivos `.md`
* Commit é a fonte da verdade
* Gera um hash único (imutável)

---

#### 2. Webhook

* Disparado por GitHub/GitLab
* Notifica o sistema sobre mudanças

```http
POST /webhook/git
```

---

#### 3. Sync (SourceControl)

Responsável por sincronizar e detectar mudanças.

**Ações:**

* `git pull`
* checkout de branch
* análise de diff

**Saída:**

```php
[
  "docs/architecture.md",
  "docs/domain.md"
]
```

---

#### 4. Version (Domain)

Transforma arquivos em entidades versionadas.

**Regras:**

* Conteúdo igual → não gera nova versão
* Conteúdo diferente → cria nova versão
* Sempre vinculado a um commit

**Entidades:**

* `Document`
* `DocumentVersion`

**Value Objects:**

* `DocumentPath`
* `DocumentSlug`
* `CommitHash`

---

#### 5. Render

Converte Markdown em HTML.

```php
$html = $renderer->render($markdown);
```

**Base:**

* CommonMark

---

#### 6. Publish

Controla o estado da versão.

**Estados:**

```text
draft → published
```

---

#### 7. Cache

Evita reprocessamento e melhora performance.

```php
cache()->remember("doc:$slug", fn() => $html);
```

---

#### 8. UI (Interface)

Entrega final ao usuário.

```http
GET /docs/{project}/{slug}
```

---

## 🧱 Camadas da Arquitetura

---

### 🟣 Domain

Contém o núcleo do sistema:

* Entidades
* Regras de negócio
* Invariantes

🚫 Não depende de:

* Laravel
* Banco
* HTTP

---

### 🔵 Application

Responsável pela orquestração:

* UseCases
* Services

**Fluxo típico:**

```text
Sync → Version → Render → Save
```

---

### 🟡 Infrastructure

Implementa detalhes técnicos:

* Banco de dados
* Git
* Cache
* Renderer

---

### 🟢 Interface

Ponto de entrada do sistema:

* Controllers
* Commands
* Webhooks

---

## 🧩 Componentes Principais

---

### 🔄 Git Sync

* Sincroniza repositório
* Detecta alterações

---

### 🧬 Versionamento

* Histórico completo por commit
* Controle de estado
* Evita duplicidade

---

### 🎨 Renderer

* Markdown → HTML
* Extensível

---

### 🌳 Navigation Builder

* Gera árvore baseada em diretórios

```text
docs/
 ├── architecture/
 └── domain/
```

---

### ⚡ Cache

* Cache por documento
* Cache por versão
* Cache por projeto

---

## 🧠 Conclusão

O sistema é uma:

> **Documentation Engine Git-Driven com versionamento e pipeline automatizado**

---

## 🚀 Próximos Passos

* Banco de dados estruturado
* Cache com Redis
* Filas (render async)
* Busca (Meilisearch)
* Interface completa de leitura
