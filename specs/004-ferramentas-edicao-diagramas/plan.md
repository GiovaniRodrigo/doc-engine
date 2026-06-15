# Plano de Implementação: Barra de Ferramentas de Formatação e Diagramas (Mermaid)

Este plano descreve as alterações técnicas necessárias para implementar a barra de ferramentas no editor e o suporte a diagramas com Mermaid.js.

---

## 1. Arquivos a serem Criados/Editados

### 1.1. Backend e Views (Blade & JS)
*   **[resources/views/edit.blade.php](file:///home/giovani/Documents/projects/doc-engine/resources/views/edit.blade.php):**
    *   Inserir o HTML da barra de atalhos `.docs-editor-toolbar` logo acima da textarea `#content`.
    *   Implementar a função JavaScript `insertMarkdown(type)` para manipular o valor e a posição do cursor (setSelectionRange) da textarea.
*   **[resources/views/show.blade.php](file:///home/giovani/Documents/projects/doc-engine/resources/views/show.blade.php):**
    *   Adicionar script para detecção dinâmica de blocos `<code class="language-mermaid">`.
    *   Caso identificados, carregar o Mermaid.js assincronamente e inicializá-lo com o tema correto baseado no estado de tema ativo do usuário.
*   **[tests/Feature/FormattingToolsTest.php](file:///home/giovani/Documents/projects/doc-engine/tests/Feature/FormattingToolsTest.php):**
    *   Testes de feature para certificar a presença visual dos botões no editor e que as visualizações renderizam os blocos estruturais de diagramas.

### 1.2. Frontend (CSS)
*   **[resources/css/docs/components.css](file:///home/giovani/Documents/projects/doc-engine/resources/css/docs/components.css):**
    *   Estilizar a barra de ferramentas do editor (`.docs-editor-toolbar`, `.docs-toolbar-btn`) com cantos de `8px` integrando-se na parte superior do editor.
    *   Ajustar a borda superior do editor `.docs-textarea-editor` para `0` para acoplar harmoniosamente com a barra.

---

## 2. Abordagem Técnica e Otimizações

### 2.1. Manipulação Sem Regressão da Seleção do Editor
*   O script `insertMarkdown` irá:
    1.  Armazenar as posições iniciais e finais da seleção (`selectionStart` / `selectionEnd`).
    2.  Dividir o valor do input em: prefixo, seleção atual (se houver) e sufixo.
    3.  Construir a string de substituição aplicando a formatação desejada (ex: `**` ao redor da seleção).
    4.  Substituir o valor no textarea.
    5.  Colocar o cursor de volta focando na palavra inserida/editada para manter o fluxo fluído de digitação do usuário.

### 2.2. Otimização de Carregamento do Mermaid.js (Lazy Loading)
*   Para não penalizar o carregamento de páginas comuns que não usam diagramas, o script em `show.blade.php` só fará o append da tag `<script>` do CDN do Mermaid se e somente se houver pelo menos um bloco `.language-mermaid` na página.
*   O tema será inicializado como `'dark'` se `document.documentElement.classList.contains('dark')` for verdadeiro, mantendo harmonia com o tema selecionado no portal.
