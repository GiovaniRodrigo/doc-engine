# ADR 002: Arquitetura de CSS para a Documentação

Author: Giovani Fernandes

---

## Status

Aceita

---

## Data

2026-04-15

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
* Estilização baseada em elementos gerados pelo Markdown (h1, p, ul, pre, etc.)
* Uso de CSS Grid para estrutura de layout
* Suporte a Dark Mode via classe global `.dark`
* Responsividade obrigatória com colapso da sidebar em telas menores

---

## Exemplos de Implementação

### 📁 Estrutura de arquivos CSS

```text
resources/css/docs/
 ├── layout.css
 ├── typography.css
 ├── components.css
 └── themes.css
```

---

### 🧱 Layout base

```css
.docs-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  min-height: 100vh;
}

.docs-sidebar {
  background: #0f172a;
  color: #e2e8f0;
  padding: 16px;
}

.docs-content {
  padding: 40px;
}
```

---

### 📄 Wrapper do conteúdo Markdown

```html
<div class="docs-layout">
  <aside class="docs-sidebar"></aside>

  <main class="docs-content">
    <article class="docs-article">
      <!-- HTML renderizado -->
    </article>
  </main>
</div>
```

---

### 🔠 Tipografia (Markdown)

```css
.docs-article h1 {
  font-size: 2rem;
  margin-bottom: 16px;
}

.docs-article h2 {
  margin-top: 32px;
}

.docs-article p {
  margin-bottom: 16px;
}
```

---

### 🧾 Código

```css
.docs-article pre {
  background: #0f172a;
  color: #e2e8f0;
  padding: 16px;
  border-radius: 8px;
}

.docs-article code {
  background: #f1f5f9;
  padding: 2px 6px;
}
```

---

### 🌙 Dark Mode

```css
.dark .docs-content {
  background: #020617;
}

.dark .docs-article {
  color: #e2e8f0;
}
```

---

### 📱 Responsividade

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

### ⚙️ Importação no Laravel (Vite)

```js
import './docs/layout.css'
import './docs/typography.css'
import './docs/components.css'
import './docs/themes.css'
```

---

### 🧪 Uso no Blade

```php
@extends('layouts.app')

@section('content')
<div class="docs-layout">
    <aside class="docs-sidebar">
        {{-- navegação --}}
    </aside>

    <main class="docs-content">
        <article class="docs-article">
            {!! $html !!}
        </article>
    </main>
</div>
@endsection
```

---

## Consequências

### Prós

* Isolamento completo do CSS da documentação
* Redução de conflitos com estilos globais
* Melhor organização e manutenção do código
* Escalabilidade para temas e componentes futuros
* Compatibilidade com qualquer renderer Markdown

### Contras

* Necessidade de disciplina na aplicação dos padrões
* Possível duplicação de estilos se não houver padronização
* Não reaproveita automaticamente estilos globais existentes

---
