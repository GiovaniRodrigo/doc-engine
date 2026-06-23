# Contexto do Projeto

## Domínio

**Documentation Engine** — motor de documentação técnica em Laravel com editor Markdown, versionamento Git e renderização de diagramas Mermaid.js. A demanda 007 foca na **experiência completa de criação e visualização de diagramas** dentro do editor e da tela de leitura:

- Editor (`/docs/:slug/edit`): barra de ferramentas com botões de formatação + dropdown de tipos UML
- Visualização (`/docs/:slug`): artigo renderizado em HTML com blocos Mermaid convertidos em SVG
- Lacuna atual: não há preview ao vivo do diagrama no editor, nem controles sobre o diagrama renderizado (zoom, copiar SVG, fullscreen)

## Público-Alvo

- **Perfil**: Desenvolvedores de software, arquitetos, tech writers
- **Nível técnico**: Avançado — familiarizados com Markdown e sintaxe Mermaid
- **Contexto de uso**: Desktop, monitores grandes (≥1280px), dentro de app Laravel
- **Dor principal**: Ciclo lento de feedback — editar Mermaid, salvar rascunho, abrir outra aba para ver o diagrama renderizado

## Referências Visuais Encontradas

1. **Mermaid Live Editor** (mermaid.live) — editor oficial com painel dividido código ↔ preview sincronizado. Repositório `mermaid-js/mermaid-live-editor` com **2.3k ⭐ GitHub**, produto com >100k usuários. Principal referência de UX para split view.
2. **TOAST UI Editor** (github.com/nhn/tui.editor) — editor Markdown com toolbar e plugin de renderização UML inline. **17k ⭐ GitHub**. Padrão de toolbar extensível com botão de modo "Preview".
3. **VS Code Markdown Preview** — padrão de split editor (Ctrl+K V) com scroll sincronizado. Produto com **165k ⭐ GitHub**. Referência de UX para preview lateral em ferramentas para dev.
4. **Mermaid Flow** (mermaidflow.app) — editor visual drag & drop de diagramas Mermaid com painel de código ao lado. Produto comercial. Referência de layout toolbar+canvas.
5. **CKEditor 5** (ckeditor.com) — toolbar balloon que aparece sobre o elemento selecionado. **9.7k ⭐ GitHub**. Referência de toolbars contextuais não intrusivas.
6. **Typora** — editor Markdown com renderização inline (sem split), o diagrama surge no lugar do bloco de código ao perder o foco. >5M usuários. Referência de preview implícito.

## Tendências Identificadas

1. **Split view sincronizado** — código à esquerda, preview à direita, scroll sincronizado. Padrão dominante em 2025 para editores com renderização complexa (Mermaid, LaTeX, MathJax).
2. **Preview inline / on-blur** — ao perder foco no bloco Mermaid, o textarea colapsa e exibe o SVG renderizado no lugar (Typora-style). Tendência crescente em editores WYSIWYG.
3. **Toolbar flutuante contextual** — quando o cursor está dentro de um bloco Mermaid, uma mini-toolbar aparece acima com ações específicas: "Renderizar", "Copiar SVG", "Ampliar". Padrão CKEditor 5 / Notion.
4. **Diagrama interativo na leitura** — diagrama renderizado com botões de hover: zoom in/out, copiar como imagem, expandir em modal fullscreen. Padrão adotado no GitLab Docs e README GitHub.
5. **Feedback de erro de sintaxe** — mensagem de erro inline ao lado do diagrama (não um alert genérico), com linha específica do erro destacada. Padrão do Mermaid Live Editor e VS Code.
