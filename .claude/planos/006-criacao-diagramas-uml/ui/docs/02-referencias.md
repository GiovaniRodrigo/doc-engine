# Referências Populares

## Tabela de Referências

| Referência | URL | Popularidade | Aplicabilidade à demanda 006 |
|---|---|---|---|
| Mermaid Live Editor | https://mermaid.live | 73k+ ⭐ GitHub (mermaid-js/mermaid) | Padrão de inserção de código Mermaid; estrutura dos templates de diagrama |
| Mermaid Chart Visual Editor | https://docs.mermaidchart.com/blog/posts/gui-for-editing-mermaid-class-diagrams | >100k usuários | Toolbar contextual por tipo de diagrama; ícones semânticos por ação |
| GitBook Editor | https://www.gitbook.com | >500k usuários registrados | Padrão de menu de seleção de tipo de bloco de conteúdo (slash-command) |
| Notion Editor | https://notion.com | ~100M usuários ativos | Dropdown de inserção de bloco com ícone + label + descrição curta por item |
| Material Design 3 | https://m3.material.io/components/toolbars/guidelines | Adotado em bilhões de dispositivos Android | Toolbar docked, overflow menu, FAB menu, surface container elevation |
| Material 3 Expressive FAB Menu | https://medium.com/@renaud.mathieu/discovering-material-3-expressive-fab-menu-ecfae766a946 | Sistema de design do Google (2025) | FAB expandindo em menu de ações relacionadas; animação de abertura scale+fade |
| draw.io / diagrams.net | https://www.drawio.com/blog/mermaid-diagrams | >20M usuários mensais | Menu de seleção de tipo de diagrama com ícones representativos da estrutura visual |
| Miro UML Diagrams | https://miro.com/diagramming/uml-diagram | >60M usuários | Paleta de tipos UML com ícones diferenciados por categoria |

## Padrões de Interação de Alta Adoção

### Padrão 1 — Dropdown com ícone + label (Notion / GitBook)
Cada opção do menu contém:
- Ícone à esquerda representando visualmente o tipo (mini-diagrama, linhas, caixas)
- Label em negrito com o nome do tipo
- Descrição curta opcional em texto secundário

### Padrão 2 — Posicionamento do dropdown abaixo do botão acionador (M3)
- `position: absolute; top: 100%; right: 0;` relativo ao container do botão
- `z-index` elevado (100+) para sobrepor o editor
- Fecha ao clicar fora ou pressionar `Escape` (padrão universal)

### Padrão 3 — Animação de abertura (M3 / draw.io)
- `transform: scale(0.95) → scale(1)` + `opacity: 0 → 1`
- `transform-origin: top right` (alinhado ao botão acionador)
- Duração: 150–200ms com easing cúbico suave

### Padrão 4 — Seleção com feedback visual imediato (Mermaid Chart)
- Hover: fundo de superfície muted + texto accent
- Active: leve `scale(0.98)` no item
- Após seleção: inserção imediata + foco devolvido ao editor
