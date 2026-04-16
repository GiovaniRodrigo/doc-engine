# 🧠 Domain

## 📌 Visão Geral

O domínio representa o **núcleo do sistema**.

Aqui estão:

* Regras de negócio
* Entidades
* Invariantes

Sem dependência de frameworks.

---

## 🧬 Entidades

---

### 📄 Document

Representa um documento lógico.

#### Atributos

* project
* path
* slug
* title

#### Responsabilidades

* Identificação única
* Organização lógica

---

### 📄 DocumentVersion

Representa uma versão do documento.

#### Atributos

* markdown
* html
* commit_hash
* state

#### Estados

```text
draft
published
```

---

## 🧠 Regras de Negócio

* Cada alteração gera uma nova versão
* Conteúdo idêntico não gera nova versão
* Versões possuem estado controlado
* Toda versão pertence a um commit

---

## 🧱 Value Objects

---

### DocumentSlug

* Identificador amigável
* Usado em URLs

---

### DocumentPath

* Caminho físico do arquivo
* Baseado no repositório

---

### CommitHash

* Representa um commit válido
* Imutável

---

## 🧩 Serviços de Domínio

---

### PublicationStateMachine

Controla transições de estado.

```text
draft → published
```

---

### RepositoryDiffService

* Detecta mudanças no repositório
* Filtra arquivos relevantes

---

## 🧠 Invariantes

* Um documento não pode existir sem path
* Uma versão deve ter commit válido
* Estado deve ser consistente
* Não pode haver duplicação de conteúdo

---

## 🔄 Ciclo de Vida

```text
File → Document → Version → Publish
```

---

## 🚀 Evoluções Futuras

* Versionamento editorial completo
* Revisão (review workflow)
* Histórico comparativo (diff)
* Eventos de domínio
