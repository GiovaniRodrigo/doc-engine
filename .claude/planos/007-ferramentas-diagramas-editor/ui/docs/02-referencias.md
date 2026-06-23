# Referências Populares

## Tabela de Referências

| Referência | URL | Popularidade | Aplicabilidade à demanda 007 |
|---|---|---|---|
| Mermaid Live Editor | https://mermaid.live | 2.3k ⭐ GitHub (live-editor repo) | Layout split code↔preview; sync scroll; erro de sintaxe inline |
| TOAST UI Editor | https://github.com/nhn/tui.editor | 17k ⭐ GitHub | Toolbar extensível com modo Preview; plugin UML; botão toggle view |
| VS Code Markdown Preview | https://code.visualstudio.com | 165k ⭐ GitHub | Padrão de split lateral (Ctrl+K V); scroll sincronizado; preview ao vivo |
| Mermaid Flow | https://www.mermaidflow.app | produto comercial, >50k usuários | Layout toolbar horizontal + canvas de diagrama; painel de código colapsável |
| CKEditor 5 | https://ckeditor.com/docs/ckeditor5 | 9.7k ⭐ GitHub | Balloon toolbar sobre elemento selecionado; ações contextuais sem poluir a UI |
| Typora | https://typora.io | >5M usuários | Preview inline on-blur: bloco Mermaid some e SVG aparece no lugar |
| GitLab Docs | https://docs.gitlab.com | produto com >30M usuários | Diagrama renderizado com controles hover (copy, zoom, fullscreen) |
| Mermaid Chart | https://docs.mermaidchart.com | produto comercial | Toolbar de diagrama com tipo, direção, tema e configuração rápida |

## Padrões de Interação de Alta Adoção

### Padrão 1 — Split view code↔preview (VS Code / Mermaid Live Editor)
- Painel esquerdo: editor de texto com syntax highlighting no bloco Mermaid
- Painel direito: SVG renderizado em tempo real
- Divisor arrastável (`resize: horizontal`) para alocar mais espaço
- Botão toggle para esconder/exibir o preview

### Padrão 2 — Preview inline on-blur (Typora)
- Enquanto cursor está dentro do bloco ` ```mermaid ```, mostra o código
- Ao clicar fora, o bloco colapsa e exibe o SVG no lugar
- Clicar no SVG restaura o código para edição
- Zero mudança de contexto: não precisa de split

### Padrão 3 — Toolbar contextual flutuante (CKEditor 5 / Notion)
- Mini-toolbar aparece acima do bloco Mermaid quando o cursor está dentro
- Ações: `Renderizar` | `Copiar SVG` | `Ampliar` | `Editar tipo`
- Posicionamento: `position: sticky` no topo do bloco, não no viewport

### Padrão 4 — Controles hover no diagrama renderizado (GitLab / GitHub)
- Diagrama renderizado na view: `<div class="mermaid">` com SVG
- Overlay de controles aparece no hover: zoom+, zoom-, fullscreen, copiar PNG
- Não interfere com o fluxo de leitura quando em repouso

### Padrão 5 — Indicador de erro de sintaxe inline (Mermaid Live Editor)
- Badge vermelho abaixo do bloco Mermaid com a mensagem do erro
- Linha do erro destacada com fundo vermelho no editor
- Mensagem compacta, não um modal
