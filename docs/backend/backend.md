# ⚙️ Backend

## 📌 Visão Geral

O backend é responsável por:

* Orquestrar o pipeline de documentação
* Processar versionamento
* Renderizar conteúdo
* Expor endpoints para consumo

Construído com:

* **Laravel 12**
* **PHP 8.3**
* **Arquitetura DDD + Clean**

---

## 🧱 Estrutura

```text
src/
 ├── Domain/
 ├── Application/
 ├── Infrastructure/
 └── Interface/
```

---

## 🔵 Camada Application

Responsável por coordenar os fluxos do sistema.

### Principais UseCases

#### SyncDocumentation

* Sincroniza repositório
* Detecta arquivos alterados

#### RenderDocumentation

* Converte markdown → HTML

#### PublishDocumentation

* Publica versão do documento

---

### Exemplo de fluxo

```text
Sync → Version → Render → Persist
```

---

## 🟣 Camada Domain

Contém regras de negócio puras.

### Entidades

* Document
* DocumentVersion

### Responsabilidades

* Garantir consistência
* Controlar estados
* Evitar duplicação

---

## 🟡 Camada Infrastructure

Implementa detalhes técnicos.

### Componentes

* Git CLI (Process)
* Eloquent ORM
* Renderer (CommonMark)
* Cache (Redis)

---

## 🟢 Camada Interface

Ponto de entrada do sistema.

### HTTP

* Controllers
* Webhooks

### CLI

```bash
php artisan docs:sync {project}
```

---

## 🧩 Padrões Utilizados

* Repository Pattern
* Value Objects
* Domain Services
* Application Services

---

## ⚡ Performance

### Estratégias

* Cache por documento
* Cache por versão
* Render sob demanda ou async

---

## 🔐 Considerações

* Sanitização de HTML
* Controle de acesso (futuro)
* Isolamento por projeto

---

## 🚀 Evoluções Futuras

* Filas (Redis / Horizon)
* Busca full-text
* Indexação semântica
* API pública
