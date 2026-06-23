# Princípios de Design Aplicados — Demanda 007

Telas: Editor (`/docs/:slug/edit`) e Visualização (`/docs/:slug`)
Componentes centrais: toolbar de diagramas, preview split/inline, diagrama renderizado com controles

---

## 1. Início Óbvio

**Editor:**
- Botão "Diagrama" na toolbar permanece o ponto de entrada principal para inserção — já implementado com cor accent diferenciada.
- Novo: botão toggle "👁 Preview" no lado direito da toolbar abre o painel de preview do diagrama inserido. É o CTA mais importante após a inserção.
- O painel de preview exibe imediatamente o diagrama do bloco mais próximo do cursor — sem precisar salvar ou navegar.

**Visualização:**
- Diagramas renderizados aparecem centralizados com borda suave, diferenciando-os do texto corrido. Tornam o conteúdo visual óbvio sem elementos extras.
- Overlay de controles (zoom, copy) aparece no hover sem poluir o estado de repouso.

---

## 2. Reversão Clara

**Editor:**
- O painel de preview é apenas leitura — nenhuma ação no preview altera o código. Fica claro que editar = textarea, visualizar = painel.
- Fechar o preview (botão ✕ ou toggle) é instantâneo e reversível — sem confirmação necessária.
- Erros de sintaxe Mermaid exibidos no preview nunca alteram o conteúdo do editor — apenas informam.

**Visualização:**
- Modal de fullscreen do diagrama tem botão ✕ explícito e fecha com `Escape`. Não há ações destrutivas possíveis na visualização.

---

## 3. Lógica Consistente

- O botão toggle de preview sempre fica na **extrema direita** da toolbar, em todas as telas do editor.
- Todos os controles hover do diagrama renderizado (zoom+, zoom-, copy, fullscreen) usam o mesmo padrão de ícone SVG 16px + tooltip nativo (`title`).
- O estado do painel de preview (aberto/fechado) é mantido na sessão — se o usuário abriu ao editar um diagrama, permanece aberto ao navegar entre documentos.
- `aria-expanded` no botão toggle reflete o estado do painel (padrão ARIA já usado no dropdown do diagrama).

---

## 4. Observar Convenções

- **Split view**: divisor vertical arrastável entre textarea e preview — convenção estabelecida por VS Code, Mermaid Live Editor, CodePen.
- **Ícone de preview**: ícone de olho (`👁`) é universalmente reconhecido para "visualizar" em editores (VS Code, GitHub, GitLab).
- **Ícone fullscreen**: ícone de 4 setas divergentes — convenção universal (YouTube, Google Docs, GitHub).
- **Erro de sintaxe**: fundo vermelho translúcido + texto da mensagem de erro — padrão de lint em IDEs.
- **Copiar**: ícone de clipboard — convenção universal para "copiar para área de transferência".

---

## 5. Feedback e Marcos

**Editor — estados do preview:**
- **Loading**: skeleton animado no painel de preview enquanto Mermaid.js processa o SVG
- **Sucesso**: SVG renderizado com transição `fade-in` suave (200ms)
- **Erro de sintaxe**: badge vermelho com mensagem compacta abaixo do SVG (ou no lugar, se falhar totalmente)
- **Sem diagrama no cursor**: mensagem "Posicione o cursor dentro de um bloco Mermaid" no painel vazio

**Visualização — controles do diagrama:**
- Hover no diagrama: overlay com 4 botões aparece (fade-in 150ms)
- Clique em "Copiar": botão muda para "✓ Copiado!" por 2 segundos (feedback visual de confirmação)
- Fullscreen: modal abre com animação `scale(0.9→1)` — confirma que a ação foi registrada

---

## 6. Proximidade e Adaptação

- Painel de preview **adjacente ao textarea** (split lateral), não numa aba separada — proximidade física reforça a relação código↔resultado.
- Controles do diagrama renderizado (zoom, copy) surgem **sobre o próprio diagrama**, não em barra separada.
- Em mobile (`max-width: 768px`): split view colapsa para view única (apenas editor ou preview), com botão toggle para alternar. Não há split em telas estreitas.
- Largura padrão do painel de preview: 40% do editor, ajustável com divisor.

---

## 7. Interface é Conteúdo

- O painel de preview não tem header, título nem decoração — apenas o SVG renderizado e o badge de erro se houver.
- Os controles do diagrama renderizado são invisíveis em repouso — surgem só no hover, mantendo o foco no conteúdo.
- O divisor entre os painéis é uma linha fina de 1px (`--docs-border`) — sem handle decorativo.
- A toolbar não cresce: o botão toggle de preview usa `margin-left: auto` para se afastar da faixa de formatação sem adicionar elementos.

---

## 8. Princípios Gerais de Design Visual

- **Torne o assunto óbvio**: o painel de preview exibe o diagrama sem label "Preview" — o SVG fala por si.
- **Forma e conteúdo integrados**: estado de erro usa vermelho (`--docs-error-*`) e estado de sucesso usa fundo neutro — sem ambiguidade.
- **Metáforas para conceitos novos**: o divisor arrastável tem cursor `col-resize` — metáfora de redimensionamento de coluna que usuários de IDEs já conhecem.
- **Visualização de dados adequada**: o diagrama Mermaid é renderizado em SVG escalável — sem pixelização em zoom.

---

## 9. Matriz de Decisão de Design

| Decisão de design | Início Óbvio | Reversão Clara | Consistência | Convenção | Feedback | Proximidade | Conteúdo > Deco. |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| Botão toggle preview na extrema direita da toolbar | ✓ | — | ✓ | ✓ | ✓ | — | ✓ |
| Split view lateral (textarea ↔ preview) | ✓ | ✓ | ✓ | ✓ | — | ✓ | ✓ |
| Divisor arrastável sem handle decorativo | — | — | ✓ | ✓ | — | ✓ | ✓ |
| Skeleton no preview durante render | — | — | ✓ | ✓ | ✓ | — | ✓ |
| Badge de erro inline abaixo do SVG | — | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Controles hover sobre diagrama renderizado | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Feedback "✓ Copiado!" no botão copy | — | — | ✓ | ✓ | ✓ | — | ✓ |
| Modal fullscreen com scale+fade | — | ✓ | ✓ | ✓ | ✓ | — | ✓ |
| Preview colapsado em mobile | — | — | ✓ | ✓ | — | ✓ | ✓ |
