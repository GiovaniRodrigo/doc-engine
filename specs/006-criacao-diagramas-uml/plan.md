# Plano de Implementação: Criação de Diagramas UML no Editor

Este plano detalha as alterações necessárias para implementar o menu dropdown de seleção de diagramas UML no editor.

---

## 1. Arquivos a serem Criados/Editados

### 1.1. Views e CSS
*   **[resources/views/edit.blade.php](file:///home/giovani/Documents/projects/doc-engine/resources/views/edit.blade.php):**
    *   Substituir o botão simples de diagrama por uma estrutura de botão com menu dropdown M3.
    *   Atualizar a função `insertMarkdown(type)` para aceitar um segundo argumento opcional `diagramType` (ex: `flowchart`, `sequence`, `class`, `usecase`, `state`), mantendo retrocompatibilidade.
    *   Implementar código JS para toggle do dropdown, fechar ao clicar fora ou ao pressionar `Escape`.
*   **[resources/css/docs/components.css](file:///home/giovani/Documents/projects/doc-engine/resources/css/docs/components.css):**
    *   Adicionar estilos CSS para a estrutura do dropdown de diagramas (posicionamento absoluto, animação de fade/scale, estilo dos itens de lista e ícones do menu M3).
*   **[resources/lang/pt/messages.php](file:///home/giovani/Documents/projects/doc-engine/resources/lang/pt/messages.php) & [resources/lang/en/messages.php](file:///home/giovani/Documents/projects/doc-engine/resources/lang/en/messages.php):**
    *   Adicionar chaves de internacionalização para os tipos de diagramas (ex: `flowchart`, `sequence_diagram`, `class_diagram`, `usecase_diagram`, `state_diagram`).

### 1.2. Testes
*   **[tests/Feature/FormattingToolsTest.php](file:///home/giovani/Documents/projects/doc-engine/tests/Feature/FormattingToolsTest.php):**
    *   Adicionar asserções para certificar que o menu de diagramas e seus links de inserção para os diferentes tipos de diagramas UML estão presentes no HTML retornado.

---

## 2. Estratégia Técnica e Regras

### 2.1. Estrutura de HTML do Dropdown M3
A estrutura do botão do diagrama e do menu flutuante será:
```html
<div class="docs-dropdown-container">
    <button type="button" id="docs-diagram-btn" class="docs-toolbar-btn docs-toolbar-btn-accent">
        ...
        <span>Diagrama</span>
    </button>
    <div id="docs-diagram-menu" class="docs-dropdown-menu" hidden>
        <button type="button" onclick="insertMarkdown('mermaid', 'flowchart')">Fluxograma</button>
        <button type="button" onclick="insertMarkdown('mermaid', 'sequence')">Diagrama de Sequência</button>
        ...
    </div>
</div>
```

### 2.2. Retrocompatibilidade no Script do Editor
A assinatura da função `insertMarkdown` em `edit.blade.php` será alterada para:
```javascript
window.insertMarkdown = function(type, diagramSubtype = 'flowchart') {
    // ...
    // Se type === 'mermaid', seleciona o prefix/suffix/placeholder com base no diagramSubtype
}
```
Isso garante que chamadas antigas sem o segundo argumento herdem o tipo `flowchart` de forma segura.
