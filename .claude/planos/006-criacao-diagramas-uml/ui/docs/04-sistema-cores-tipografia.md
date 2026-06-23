# Sistema de Cores e Tipografia

Base: Material Design 3 conforme já implementado no projeto (`--docs-*` CSS custom properties).

---

## Paleta de Cores

### Primária (Cyan M3 — confirmada nos avatars de colaboração)
A cor `#006874` aparece no avatar `docs-user-avatar-1` e é consistente com o `--docs-accent` usado no projeto.

| Token | Valor (referência M3 Cyan) | Uso |
|---|---|---|
| `--docs-accent` | `#006874` | Botões primários, bordas de foco, links, FAB |
| `--docs-accent-strong` | `#004F58` | Hover de botões primários |
| `--docs-accent-container` | `#9EEFFD` (light) / `#004F58` (dark) | Fundo do botão "Diagrama" (accent) |
| `--docs-accent-on-container` | `#001F24` (light) / `#9EEFFD` (dark) | Texto no botão "Diagrama" |
| `--docs-accent-text` | `#FFFFFF` | Texto sobre fundo accent sólido |

**Referência de mercado**: cyan-teal (`#006874`) é a cor primária da paleta "Cyan" do Material Theme Builder, usada em produtos como Google Meet e aplicações de produtividade.

### Superfícies (hierarquia M3)

| Token | Uso |
|---|---|
| `--docs-page-background` | Fundo da página (nível 0) |
| `--docs-surface` | Cards, painéis, dropdown menu (nível 1) |
| `--docs-surface-muted` | Campos de input, toolbar background (nível 2) |
| `--docs-surface-container` | Collaboration container, toasts (nível 3) |

### Semântica

| Cor | Token | Referência |
|---|---|---|
| Sucesso | `--docs-success-*` (bg, border, text) | Verde M3 `#386A20` (avatar-2) |
| Erro | `--docs-error-*` (bg, border, text) | Terracotta M3 `#A63E2B` (avatar-3) |
| Info | `--docs-info-*` (bg, border, text) | Azul M3 informativo |
| Aviso | — | Não aplicado na demanda 006 |

### Específicas do Dropdown UML

| Elemento | Cor recomendada | Justificativa |
|---|---|---|
| Fundo do menu | `--docs-surface` | Superfície elevada (M3 elevation level 2) |
| Borda do menu | `--docs-border` | Consistente com todos os cards/panels |
| Hover do item | `--docs-surface-muted` bg + `--docs-accent` text | Padrão já usado em `.docs-dropdown-item:hover` |
| Foco do item | `inset box-shadow 2px --docs-accent` | Acessibilidade — padrão do projeto |
| Ícone do item | `currentColor` (herda do texto) | Ícones mudam de cor junto com o texto no hover |

---

## Tipografia

### Famílias (já definidas no projeto)

| Função | Família | Popularidade |
|---|---|---|
| Interface (labels, botões, menus) | Herança do sistema (`font: inherit` no CSS) | Respeita a fonte do host Laravel |
| Editor de código/Markdown | `"SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace` | Stack mono padrão do GitHub/VS Code |

**Nota**: O projeto usa `font: inherit` em todos os elementos de formulário, delegando a tipografia ao `<body>` da aplicação host. Esta é a abordagem correta para um pacote Laravel.

### Escala Tipográfica (demanda 006 — toolbar e dropdown)

| Elemento | Tamanho | Peso | Cor token |
|---|---|---|---|
| Label do botão "Diagrama" | `0.8rem` | `700` | `--docs-accent-on-container` |
| Label dos itens do dropdown | `0.88rem` | `500` | `--docs-text` |
| Subtítulo do grupo (se aplicável) | `0.72rem` | `700`, uppercase | `--docs-text-muted` |
| Título do panel AI (referência) | `0.85rem` | `700`, uppercase, letter-spacing 0.08em | `--docs-heading` |

---

## Espaçamento (Grid)

| Token | Valor | Uso |
|---|---|---|
| Base unit | `8px` | Múltiplos de 8 em todos os espaçamentos |
| `--docs-radius-sm` | `~4-6px` | Botões da toolbar, foco outline |
| `--docs-radius-md` | `12px` | Campos, alerts, dropdown items |
| `--docs-radius-lg` | `28px` | Panels, chat card (M3 Extra Large) |
| Padding dos itens do dropdown | `10px 16px` | Área de toque mínima ~40px (WCAG AA) |
| Gap entre ícone e label no item | `10px` | Alinhamento visual confortável |
| `min-width` do dropdown | `220px` | Garante legibilidade dos labels mais longos |

---

## Sombras (Elevação M3)

| Token | Uso no dropdown |
|---|---|
| `--docs-shadow-lg` | Menu dropdown — elevação máxima (sobre o conteúdo) |
| `--docs-shadow-md` | Panels — elevação intermediária |
| `--docs-shadow-1` | User chips — elevação mínima |

---

## Animações

| Propriedade | Valor | Uso |
|---|---|---|
| `--docs-transition-fast` | `~150ms ease` | Hover de botões, cor de bordas |
| `--docs-transition-normal` | `~200ms ease` | Animação do dropdown (scale+fade) |
| Keyframe dropdown | `scale(0.95)→scale(1), opacity(0→1)` | `.docsDropdownFadeIn` já implementado |
