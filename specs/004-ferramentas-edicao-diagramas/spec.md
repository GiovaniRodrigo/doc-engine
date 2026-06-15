# Especificação: Barra de Ferramentas de Formatação e Suporte a Diagramas (Mermaid)

Esta especificação define os requisitos e cenários de uso para uma barra de ferramentas de edição na página de escrita de documentos e o suporte para exibição visual de diagramas e formas usando a biblioteca Mermaid.js.

---

## 1. Objetivo

Facilitar a criação e formatação de conteúdos Markdown no editor de documentos, fornecendo uma barra de ferramentas com botões de atalho (negrito, itálico, títulos, listas, tabelas, links). Além disso, dar suporte nativo para criação de diagramas e formas (fluxogramas, sequências, diagramas de classe) usando a sintaxe Mermaid.js, renderizando-os dinamicamente no leitor de artigos.

---

## 2. Cenários de Uso

### Cenário 1: Uso da Barra de Ferramentas de Edição
* **Dado que** o usuário está na página de edição de um documento (`/docs/guia/edit`),
* **Quando** ele clica no botão de atalho "B" (Negrito) sem selecionar nenhum texto,
* **Então** o editor deve inserir a marcação `**texto**` na posição do cursor e focar o cursor sobre a palavra "texto".
* **E quando** ele seleciona uma palavra existente (ex: "Instalação") e clica no botão "B",
* **Então** o texto selecionado deve ser envolto com asteriscos duplo (`**Instalação**`).

### Cenário 2: Inserção de Diagramas e Formas (Mermaid)
* **Dado que** o usuário deseja desenhar um fluxograma de processos,
* **Quando** ele clica no botão de atalho "Diagrama" na barra de ferramentas,
* **Então** o editor deve inserir um bloco de código estruturado com a sintaxe padrão do Mermaid:
  ```
  ```mermaid
  graph TD
      A[Início] --> B(Processo)
  ```
  ```

### Cenário 3: Renderização Visual do Diagrama no Leitor
* **Dado que** o documento foi salvo contendo um bloco de código com a linguagem `mermaid`,
* **Quando** qualquer usuário acessa a página de leitura deste documento (`/docs/guia`),
* **Então** o sistema deve detectar o bloco de código do Mermaid e carregá-lo visualmente como um diagrama gerado via SVG na página, ocultando o código fonte bruto.
* **E** o diagrama deve respeitar o tema atual (tema escuro gera diagrama escuro, tema claro gera diagrama claro).
* **E** a biblioteca do Mermaid deve ser carregada de forma assíncrona (lazy-load) apenas se a página contiver pelo menos um diagrama, poupando rede.

---

## 3. Requisitos Funcionais

### R1. Barra de Ferramentas de Edição (Toolbar)
* Criar um componente de barra de ferramentas acima da textarea do editor em `edit.blade.php`.
* A barra deve conter botões com títulos claros e ícones M3 para os seguintes formatos Markdown:
  * Negrito (`**text**`)
  * Itálico (`*text*`)
  * Título H2 (`\n# text\n`)
  * Bloco de Código (```\ncode\n```)
  * Link (`[text](url)`)
  * Lista Não Ordenada (`\n- item`)
  * Tabela (`| Col 1 | Col 2 | ...`)
  * Diagrama Mermaid (bloco de código com a sintaxe básica `graph TD`)
* Implementar a lógica JS de inserção mantendo o foco no editor e posicionando o cursor de forma inteligente caso nenhuma seleção esteja ativa.

### R2. Renderização de Diagramas Mermaid
* Adicionar script assíncrono na página de leitura de documentos (`show.blade.php`) que identifique elementos `<pre><code class="language-mermaid">`.
* Se detectados, o script deve substituir o elemento `pre/code` por um `<div class="mermaid">` contendo o código bruto e carregar dinamicamente a biblioteca Mermaid.js via CDN.
* Inicializar o Mermaid definindo o tema dinâmico: `'dark'` se a classe do HTML for `.dark`, ou `'default'` se for tema claro.

### R3. Estilização Visual (M3)
* Integrar visualmente a barra de ferramentas com a textarea: a barra de ferramentas deve possuir bordas arredondadas nos cantos superiores e a textarea nos cantos inferiores, aglutinando-se em um único painel.
* Estilizar os botões da barra de ferramentas com estados hover (`:hover`), ativo (`:active`) e foco visível (`:focus-visible`).

---

## 4. Critérios de Sucesso

1. A barra de ferramentas de edição insere corretamente todas as formatações e blocos de código na textarea de edição.
2. Blocos de código da linguagem `mermaid` são renderizados como diagramas visuais SVG interativos na leitura do documento.
3. Se o documento não possuir blocos `mermaid`, a biblioteca do Mermaid.js não é baixada do CDN (otimização de rede).
4. O diagrama adapta-se corretamente ao tema Claro/Escuro do portal.
5. Todos os testes automatizados (atuais e novos) continuam passando com sucesso.
