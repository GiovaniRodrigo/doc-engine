# 🔄 Workflows

## 📌 Visão Geral

Os workflows descrevem como a documentação flui pelo sistema.

---

## 🚀 Workflow Principal — Publicação Automática

```text
Commit → Webhook → Sync → Version → Render → Publish → Cache → UI
```

---

## 🔍 Etapas

---

### 1. Commit

* Desenvolvedor altera `.md`
* Commit é enviado ao repositório

---

### 2. Webhook

* Evento disparado automaticamente
* Backend recebe notificação

---

### 3. Sync

* Executa `git pull`
* Detecta arquivos alterados

---

### 4. Version

* Cria nova versão do documento
* Associa ao commit

---

### 5. Render

* Converte markdown → HTML

---

### 6. Publish

* Atualiza estado para `published`

---

### 7. Cache

* Armazena HTML renderizado
* Evita recomputação

---

### 8. UI

* Usuário acessa documentação
* Conteúdo servido rapidamente

---

## 🛠 Workflow Manual

Executado via CLI:

```bash
php artisan docs:sync {project}
```

### Quando usar

* Falha de webhook
* Reprocessamento manual
* Debug

---

## ⚠️ Workflow de Erro

### Possíveis falhas

* Git pull falha
* Erro de render
* Cache inconsistente

### Ações

1. Verificar logs
2. Executar sync manual
3. Validar fila

---

## 🔄 Workflow de Atualização

```text
Novo Commit → Nova Versão → Re-render → Cache Update
```

---

## 🧠 Observações

* Pipeline é idempotente
* Pode ser executado múltiplas vezes
* Baseado em eventos (evolução futura)

---

## 🚀 Evoluções Futuras

* Processamento assíncrono
* Retry automático
* Dead-letter queue
* Observabilidade (logs + métricas)
