# Sistema de Cores e Tipografia — Demanda 007

Base: Material Design 3 (`--docs-*` CSS custom properties) já implementado no projeto.

---

## Paleta de Cores

### Primária (Cyan M3 — `--docs-accent`)
Confirmada como `#006874` (avatar `.docs-user-avatar-1` no CSS de colaboração).

| Token | Valor | Uso na demanda 007 |
|---|---|---|
| `--docs-accent` | `#006874` | Botão toggle preview, divisor ativo, borda de foco |
| `--docs-accent-strong` | `#004F58` | Hover do botão toggle |
| `--docs-accent-container` | `#9EEFFD` | Fundo do botão "Diagrama" na toolbar |

### Superfícies — hierarquia M3

| Token | Uso nos novos componentes |
|---|---|
| `--docs-surface` | Fundo do painel de preview; fundo do modal fullscreen |
| `--docs-surface-muted` | Fundo da toolbar; fundo do skeleton loader |
| `--docs-page-background` | Fundo da área de separação entre painéis |

### Semântica — estados do preview

| Estado | Tokens | Valor aproximado |
|---|---|---|
| Erro de sintaxe Mermaid | `--docs-error-bg`, `--docs-error-text`, `--docs-error-border` | bg: #fee2e2, text: #991b1b |
| Sucesso (diagrama OK) | neutro — fundo `--docs-surface` | sem indicador visual especial |
| Loading (skeleton) | gradiente animado `--docs-surface-muted` → `--docs-border` | animação shimmer |

### Novos tokens sugeridos para o painel de preview

```css
/* Divisor entre textarea e preview */
--docs-split-divider: var(--docs-border);
--docs-split-divider-active: var(--docs-accent);

/* Overlay de controles do diagrama */
--docs-diagram-overlay-bg: rgba(0, 0, 0, 0.55);
--docs-diagram-overlay-text: #ffffff;
--docs-diagram-overlay-radius: 8px;
```

---

## Tipografia

### Famílias (herança do projeto)
O projeto usa `font: inherit` em todos os elementos de UI — a tipografia é delegada ao host Laravel. Para o painel de preview e controles:

| Elemento | Família | Tamanho | Peso |
|---|---|---|---|
| Mensagem de erro de sintaxe | `inherit` (sans-serif) | `0.82rem` | `500` |
| Tooltip dos controles hover | `inherit` | nativo do browser via `title` | — |
| Label "sem diagrama" no preview vazio | `inherit` | `0.9rem` | `400` |
| Código Mermaid no textarea | `"SFMono-Regular", Consolas, monospace` | `0.9rem` | `400` |

---

## Espaçamento e Layout

| Medida | Valor | Uso |
|---|---|---|
| Largura padrão do painel de preview | `40%` do container | Split inicial |
| Largura mínima do painel de preview | `240px` | Limite inferior do resize |
| Largura mínima do textarea (split) | `320px` | Limite inferior do resize |
| Padding interno do painel de preview | `16px` | Espaço ao redor do SVG |
| Altura dos controles hover do diagrama | `32px` | Área de clique confortável |
| Gap entre botões nos controles hover | `4px` | Compacto, sem desperdício |
| Raio do overlay de controles | `8px` (`--docs-radius-sm`) | Consistente com o projeto |

---

## Animações

| Transição | Duração / Easing | Uso |
|---|---|---|
| Preview fade-in (SVG surgindo) | `200ms ease` | SVG renderizado com sucesso |
| Skeleton shimmer | `1.5s linear infinite` | Loading do Mermaid |
| Overlay hover controls | `150ms ease` | Fade-in dos controles sobre o diagrama |
| Modal fullscreen open | `200ms cubic-bezier(0.2, 0, 0, 1)` | Scale(0.95→1) + opacity(0→1) |
| Feedback "✓ Copiado!" | `150ms ease` | Mudança de cor/texto do botão copy |

---

## Referência de mercado para o esquema de cores

- **Mermaid Live Editor** usa fundo `#1e1e2e` (dark) com o SVG em branco — padrão "código escuro, preview claro ou neutro"
- **VS Code Preview** usa `--vscode-editor-background` como fundo do preview — mesmo princípio de herdar a cor de superfície do tema
- **Padrão adotado para o doc-engine**: preview com fundo `--docs-surface` (branco no tema light, cinza escuro no dark) — consistente com cards e panels existentes
