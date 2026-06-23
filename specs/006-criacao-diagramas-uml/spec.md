# Especificação: Criação de Diagramas UML no Editor

Esta especificação define os requisitos e cenários de uso para expandir o botão "Diagrama" na barra de ferramentas do editor, permitindo ao usuário selecionar e inserir diferentes tipos de diagramas UML (Fluxograma, Sequência, Classes, Casos de Uso e Estado) através de um menu de seleção interativo.

---

## 1. Objetivo

Aprimorar a barra de ferramentas do editor de documentação permitindo a inserção de diagramas UML estruturados em sintaxe Mermaid.js. O botão de atalho "Diagrama" passará a abrir um menu dropdown do Material Design 3 contendo modelos pré-configurados de diagramas (Fluxograma, Diagrama de Sequência, Diagrama de Classes, Casos de Uso e Estado), otimizando o fluxo de trabalho de arquitetura e documentação técnica.

---

## 2. Cenários de Uso

### Cenário 1: Abertura do Menu de Diagramas UML
* **Dado que** o usuário está editando um documento (`/docs/guia/edit`),
* **Quando** ele clica no botão "Diagrama" na barra de ferramentas,
* **Então** o sistema deve exibir um menu dropdown flutuante posicionado abaixo do botão com os tipos de diagramas disponíveis.
* **E** o menu deve fechar automaticamente se o usuário clicar em qualquer área fora dele ou pressionar a tecla `Escape`.

### Cenário 2: Inserção de um Diagrama Específico
* **Dado que** o menu dropdown de diagramas está aberto,
* **Quando** o usuário clica na opção "Diagrama de Sequência",
* **Então** o sistema deve inserir o bloco de código Mermaid estruturado para um diagrama de sequência na posição atual do cursor na textarea do editor.
* **E** o menu dropdown deve ser fechado após a inserção.

---

## 3. Requisitos Funcionais

### R1. Menu Dropdown de Seleção (M3 UI)
* Substituir o comportamento de clique simples do botão "Diagrama" na barra de ferramentas por um acionador de dropdown em `edit.blade.php`.
* Desenhar um menu dropdown com os seguintes itens e atalhos de inserção correspondentes:
  * **Fluxograma:** Template contendo a estrutura `graph TD` com nós de processo e decisão.
  * **Diagrama de Sequência:** Template com atores/participantes e mensagens direcionais (`sequenceDiagram`).
  * **Diagrama de Classes:** Template com definição de atributos e métodos públicos (`classDiagram`).
  * **Diagrama de Casos de Uso:** Template relacionando atores a casos de uso (`usecaseDiagram`).
  * **Diagrama de Estado:** Template com estados de transição (`stateDiagram-v2`).
* O menu deve seguir os padrões visuais M3: cantos arredondados, fundo de superfície (`var(--docs-surface)`), sombra/elevação suave, e posicionamento absoluto alinhado ao botão acionador.

### R2. Templates Mermaid Inseridos
* **Fluxograma:**
  ```mermaid
  graph TD
      A[Início] --> B{Decisão}
      B -- Sim --> C[Resultado 1]
      B -- Não --> D[Resultado 2]
  ```
* **Diagrama de Sequência:**
  ```mermaid
  sequenceDiagram
      Participante A->>Participante B: Pergunta
      Participante B-->>Participante A: Resposta
  ```
* **Diagrama de Classes:**
  ```mermaid
  classDiagram
      class ExemploClasse {
          +String atributo
          +metodoPublico()
      }
  ```
* **Diagrama de Casos de Uso:**
  ```mermaid
  usecaseDiagram
      actor Usuario
      usecase CasoUso as Caso de Uso Exemplo
      Usuario --> CasoUso
  ```
* **Diagrama de Estado:**
  ```mermaid
  stateDiagram-v2
      [*] --> EstadoInicial
      EstadoInicial --> EstadoFinal : Transição
      EstadoFinal --> [*]
  ```

---

## 4. Critérios de Sucesso

1. O botão "Diagrama" abre o menu dropdown flutuante ao ser clicado.
2. Clicar em qualquer opção de diagrama insere o respectivo template Markdown/Mermaid na posição correta do editor e fecha o dropdown.
3. O dropdown fechará ao perder o foco ou ao clicar fora dele.
4. Todos os testes automatizados da suíte continuam passando sem falhas.
