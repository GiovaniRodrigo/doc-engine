# Princípios de Design Aplicados — Demanda 006

Tela: Editor de Documento (`/docs/:slug/edit`)
Componente central: Dropdown de seleção de tipo de diagrama UML na barra de ferramentas

---

## 1. Início Óbvio

**Como aplicar nesta tela:**
- O botão "Diagrama" na toolbar deve ser o ponto de entrada visual mais distinto da barra para inserção de diagramas. Ele usa `docs-toolbar-btn-accent` (fundo `--docs-accent-container`, texto `--docs-accent-on-container`) para se destacar dos demais botões cinzas.
- O dropdown deve abrir imediatamente ao clique, sem etapas intermediárias.
- O primeiro item do menu (Fluxograma) é o tipo mais comum — posicionado no topo como sugestão padrão.
- O ícone de setas para baixo (`▾`) ao lado do label "Diagrama" sinaliza inequivocamente que há um submenu.

---

## 2. Reversão Clara

**Como aplicar nesta tela:**
- A inserção do template no editor é uma ação reversível via `Ctrl+Z` (undo nativo do textarea).
- Fechar o dropdown sem selecionar nenhum item (clique fora ou `Escape`) cancela a ação sem efeitos colaterais.
- O botão "Cancelar" na área de ações do formulário principal (`docs-form-actions`) permite abandonar todas as edições da sessão.
- Não há ações destrutivas no dropdown — apenas inserção aditiva.

---

## 3. Lógica Consistente

**Como aplicar nesta tela:**
- Todos os itens do dropdown seguem a mesma estrutura visual: `ícone (12px) + label`.
- O comportamento de hover/focus dos itens do dropdown replica o padrão dos botões da toolbar (`fundo surface-muted, texto accent`).
- O dropdown fecha após qualquer seleção — comportamento idêntico ao esperado em qualquer select/menu da aplicação.
- A função `insertMarkdown()` já usada pelos outros botões é reutilizada para os itens do dropdown, mantendo consistência de comportamento.

---

## 4. Observar Convenções

**Como aplicar nesta tela:**
- Ícones SVG de cada tipo de diagrama usam formas que evocam a estrutura visual do diagrama:
  - Fluxograma: seta direcional com losango de decisão
  - Sequência: linhas verticais paralelas com seta horizontal (atores e mensagem)
  - Classes: retângulo com linha horizontal (cabeçalho de classe)
  - Casos de Uso: círculo com ator stick figure
  - Estado: círculo preenchido (estado inicial UML)
- O botão acionador usa `aria-expanded` e `aria-haspopup="true"` — convenção ARIA para menus.
- Fechar com `Escape` é o padrão universal de dropdowns (W3C ARIA Authoring Practices).

---

## 5. Feedback e Marcos

**Como aplicar nesta tela:**
- **Hover no item**: mudança imediata de cor de fundo e texto — feedback visual de "selecionável".
- **Após inserção**: o template aparece instantaneamente no textarea e o cursor é posicionado no placeholder editável.
- **Estado do botão acionador**: `aria-expanded="true"` quando aberto (acessibilidade), podendo ser usado para estilizar o botão como "ativo".
- **Animação do dropdown**: `scale(0.95→1) + opacity(0→1)` em 200ms sinaliza que o menu está "surgindo" — confirma que a ação de clique foi registrada.
- **Empty state**: não aplicável (o dropdown sempre tem os 5 tipos disponíveis).

---

## 6. Proximidade e Adaptação

**Como aplicar nesta tela:**
- O dropdown posiciona-se imediatamente abaixo e alinhado à direita do botão "Diagrama" — proximidade física com o acionador.
- O botão "Diagrama" está no extremo direito da toolbar (`margin-left: auto` no container), separado visualmente dos botões de formatação simples, agrupando-o conceitualmente com "ações de inserção de bloco complexo".
- Em mobile (`max-width: 480px`): a toolbar quebra linha (`flex-wrap: wrap`) e o dropdown mantém `min-width: 220px` para legibilidade dos itens.

---

## 7. Interface é Conteúdo

**Como aplicar nesta tela:**
- O dropdown não tem decoração supérflua: sem ícones de check, sem badges, sem bordas decorativas nos itens.
- A descrição dos tipos de diagrama fica no `title` do botão acionador e nos `title` dos itens — visível apenas no hover (tooltip nativo), sem ocupar espaço visual permanente.
- A toolbar é compacta (height: 40px nos botões) para maximizar a área do editor Markdown.

---

## 8. Princípios Gerais de Design Visual

- **Torne o assunto óbvio**: o botão "Diagrama" com ícone de camadas (layers SVG) e label explícito identifica imediatamente a finalidade da ação.
- **Forma e conteúdo integrados**: o acento cromático do botão (cor primária da paleta M3) diferencia "ação de inserção de bloco" das "ações de formatação inline" (cinzas).
- **Metáforas para conceitos novos**: ícones SVG que reproduzem a forma visual do diagrama (não ícones genéricos) ajudam o usuário a reconhecer o tipo antes de clicar.

---

## 9. Matriz de Decisão de Design

| Decisão de design | Início Óbvio | Reversão Clara | Consistência | Convenção | Feedback | Proximidade | Conteúdo > Deco. |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| Botão "Diagrama" com acento cromático | ✓ | — | ✓ | ✓ | — | — | ✓ |
| Dropdown posicionado abaixo-direita do botão | ✓ | — | ✓ | ✓ | — | ✓ | ✓ |
| Ícone SVG semântico por tipo de diagrama | ✓ | — | ✓ | ✓ | — | — | ✓ |
| Animação scale+fade na abertura | — | — | ✓ | ✓ | ✓ | — | ✓ |
| Fechar com Escape / clique fora | — | ✓ | ✓ | ✓ | ✓ | — | — |
| Cursor posicionado no placeholder após inserção | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Hover com fundo surface-muted + texto accent | — | — | ✓ | ✓ | ✓ | — | ✓ |
