# Plano de Implementação: Melhorias de UI/UX, Acessibilidade e Responsividade

Este documento descreve a estratégia técnica para a implementação dos requisitos de melhoria visual e acessibilidade.

---

## 1. Estratégia Técnica

### A. Temas e Cores (Themes.css)
* Definiremos variáveis de cores baseadas em HSL no `:root` usando um Hue principal (ex: 220, azul).
* Criaremos uma escala de cinza cromática (adicionando saturação leve baseada no Hue 220) para tons neutros e superfícies.
* As definições de Dark Mode serão feitas sobrescrevendo as variáveis CSS dentro de `.dark` (e, opcionalmente, `@media (prefers-color-scheme: dark)`), eliminando a necessidade de repetir definições de propriedades em múltiplos seletores.

### B. Menu Hambúrguer & Gaveta da Sidebar (Layout, Sidebar e Default Layout)
* No layout padrão (`layouts/default.blade.php`), inseriremos o botão do menu hambúrguer (com `aria-expanded="false"`, `aria-controls="docs-sidebar"` e `aria-label="Abrir menu de navegação"`).
* Adicionaremos um bloco inline `<script>` leve no final do layout para gerenciar as classes CSS do menu móvel, o controle de foco por teclado, e fechar ao pressionar `Esc` ou clicar fora.
* No `layout.css`, adicionaremos transições e posições da sidebar para quando estiver em visualização mobile: usaremos `transform: translateX(-100%)` e `opacity: 0` por padrão em mobile, e aplicaremos uma classe `.active` ou `.open` para exibi-la usando `transform: translateX(0)` e `opacity: 1`.

### C. Glassmorphism e Sombras
* Utilizaremos `backdrop-filter: blur(12px)` e fundos com opacidade (ex: `hsla(...)`) para a sidebar e cabeçalhos fixos.
* As sombras serão redefinidas no `:root` como sombras suaves utilizando opacidades de HSL correspondentes ao tema.

### D. Acessibilidade (a11y)
* **Focus Visible:** Definiremos uma borda e contorno de foco no `:focus-visible` para todos os botões, inputs, links do menu, breadcrumbs e links de paginação em `components.css` e `layout.css`.
* **Tabelas Responsivas:** O processador Markdown gera tabelas como `<table>`. Para torná-las responsivas sem precisar alterar o parseador Markdown, podemos estilizar `table` para ter `display: block` e `overflow-x: auto` em telas menores, ou envolver as tabelas na renderização (mas a forma mais limpa é no CSS com um wrapper ou aplicando comportamento de rolagem diretamente no elemento da tabela se ela puder rolar, ou adicionando regras no `article.css`).

### E. Resolução da Duplicação
* Faremos com que `resources/views/layout.blade.php` simplesmente inclua `resources/views/base.blade.php` para eliminar o código duplicado de forma limpa.

---

## 2. Arquivos Envolvidos

* **Modificados:**
  * `resources/css/docs/themes.css`
  * `resources/css/docs/layout.css`
  * `resources/css/docs/sidebar.css`
  * `resources/css/docs/components.css`
  * `resources/views/layouts/default.blade.php`
  * `resources/views/layout.blade.php`
  * `resources/views/base.blade.php`
  * `resources/views/sidebar-node.blade.php`
* **Criados (Testes):**
  * `tests/Feature/FrontendUiAccessibilityTest.php`
