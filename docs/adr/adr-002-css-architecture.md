
# ADR 002 — Arquitetura de CSS para a Documentação

**Autor:** Giovani Fernandes  
**Data:** 2026-04-15  
**Status:** Aceito  

---

## Contexto

A documentação do sistema é renderizada a partir de arquivos Markdown, convertidos em HTML por um renderer (CommonMark).

Esse cenário gera desafios específicos:

* O HTML gerado não é totalmente controlado
* Necessidade de padronização visual entre documentos
* Risco de conflito com estilos globais da aplicação
* Necessidade de escalabilidade para futuras evoluções (temas, componentes, plugins)

Alternativas consideradas:

1. **Uso de CSS global da aplicação**

   * Simples de implementar
   * Alto risco de conflitos e inconsistência visual

2. **Uso de Tailwind diretamente no HTML**

   * Limitação: Markdown não gera classes utilitárias
   * Dificulta controle fino da estilização

3. **CSS-in-JS**

   * Complexidade desnecessária para o contexto
   * Aumenta acoplamento com frontend

---

## Decisão

Adotar uma arquitetura de CSS baseada em isolamento e escopo, com as seguintes diretrizes:

* Todo conteúdo da documentação deve estar contido em um wrapper `.docs-article`
* Todo CSS deve ser escopado com prefixo `docs-`
* Separação de estilos por responsabilidade (layout, tipografia, componentes)
* Estilização baseada em elementos gerados pelo Markdown
* Uso de CSS Grid para layout
* Suporte a Dark Mode via `.dark`
* Responsividade obrigatória

---

## Exemplos de Implementação (Granular)

---

### 📁 Estrutura de arquivos

```text
resources/css/docs/
 ├── layout.css
 ├── sidebar.css
 ├── article.css
 ├── components.css
 └── themes.css
```

---

## 🧱 Layout geral

```css
.docs-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  min-height: 100vh;
}
```

---

## 📚 Sidebar (Navegação)

### Estrutura HTML

```html
<aside class="docs-sidebar">
  <nav class="docs-nav">
    <ul class="docs-nav-list">
      <li class="docs-nav-item">
        <a class="docs-nav-link active">Introdução</a>
      </li>

      <li class="docs-nav-group">
        <span class="docs-nav-group-title">Arquitetura</span>

        <ul>
          <li class="docs-nav-item">
            <a class="docs-nav-link">DDD</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</aside>
```

---

### CSS da Sidebar

```css
.docs-sidebar {
  background: #0f172a;
  padding: 16px;
  overflow-y: auto;
}

.docs-nav-list {
  list-style: none;
  padding: 0;
}

.docs-nav-item {
  margin-bottom: 4px;
}

.docs-nav-link {
  display: block;
  padding: 8px 12px;
  border-radius: 6px;
  color: #cbd5f5;
  text-decoration: none;
}

.docs-nav-link:hover {
  background: #1e293b;
}

.docs-nav-link.active {
  background: #2563eb;
  color: #fff;
}
```

---

### Grupos de navegação

```css
.docs-nav-group-title {
  font-size: 12px;
  text-transform: uppercase;
  color: #94a3b8;
  margin: 16px 0 8px;
}
```

---

## 📄 Article (Conteúdo)

### Estrutura HTML

```html
<main class="docs-content">
  <article class="docs-article">
    <h1>Título</h1>
    <p>Texto...</p>
  </article>
</main>
```

---

### Container

```css
.docs-content {
  padding: 40px;
}

.docs-article {
  max-width: 900px;
  margin: 0 auto;
  line-height: 1.7;
}
```

---

### Tipografia granular

```css
.docs-article h1 {
  font-size: 2rem;
  margin-bottom: 16px;
}

.docs-article h2 {
  font-size: 1.5rem;
  margin-top: 32px;
}

.docs-article h3 {
  margin-top: 24px;
}

.docs-article p {
  margin-bottom: 16px;
}

.docs-article blockquote {
  border-left: 4px solid #2563eb;
  padding-left: 16px;
  color: #475569;
}
```

---

### Código

```css
.docs-article pre {
  background: #0f172a;
  color: #e2e8f0;
  padding: 16px;
  border-radius: 8px;
  overflow-x: auto;
}

.docs-article code {
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
}
```

---

### Tabelas

```css
.docs-article table {
  width: 100%;
  margin: 24px 0;
}

.docs-article th {
  background: #f8fafc;
  text-align: left;
}
```

---

## 🧩 Componentes adicionais

---

### Callout (alertas)

```html
<div class="docs-callout docs-callout-info">
  Informação importante
</div>
```

```css
.docs-callout {
  padding: 16px;
  border-radius: 8px;
  margin: 16px 0;
}

.docs-callout-info {
  background: #e0f2fe;
  border-left: 4px solid #0284c7;
}
```

---

### Breadcrumb

```html
<nav class="docs-breadcrumb">
  <a>Home</a> / <a>Docs</a> / <span>CSS</span>
</nav>
```

```css
.docs-breadcrumb {
  font-size: 14px;
  margin-bottom: 16px;
}
```

---

## 🌙 Dark Mode

```css
.dark .docs-sidebar {
  background: #020617;
}

.dark .docs-article {
  color: #e2e8f0;
}
```

---

## 📱 Responsividade

```css
@media (max-width: 768px) {
  .docs-layout {
    grid-template-columns: 1fr;
  }

  .docs-sidebar {
    display: none;
  }
}
```

---

## ⚙️ Integração (Laravel Blade)

```php
<div class="docs-layout">
  <aside class="docs-sidebar">
    {{-- árvore de navegação --}}
  </aside>

  <main class="docs-content">
    <article class="docs-article">
      {!! $html !!}
    </article>
  </main>
</div>
```

---

## Consequências

### Prós

* Estrutura altamente organizada
* Fácil evolução (componentes, temas, plugins)
* Navegação clara e reutilizável
* Baixo acoplamento com backend

### Contras

* Mais arquivos para gerenciar
* Requer padronização rigorosa

---
