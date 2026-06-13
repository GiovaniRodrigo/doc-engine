# Especificação: Melhorias de UI/UX, Acessibilidade e Responsividade do Frontend

Esta especificação define as melhorias visuais, de usabilidade e acessibilidade para o frontend do motor de documentação.

---

## 1. Objetivo

Elevar a experiência do usuário do motor de documentação através do alinhamento visual, acessibilidade por teclado e leitores de tela, e garantir a responsividade total (especialmente em smartphones), adotando técnicas modernas de design e um sistema de temas baseado em HSL.

---

## 2. Cenários de Uso

### Cenário 1: Navegação Mobile
* **Dado que** o usuário está visualizando a documentação em um smartphone (tela < 768px),
* **Quando** ele acessa qualquer página,
* **Então** ele deve ver um botão de menu (hambúrguer) visível e acessível no cabeçalho.
* **Quando** ele clica no botão de menu,
* **Então** a barra lateral de navegação (sidebar) deve se expandir suavemente como uma gaveta (drawer), exibindo toda a estrutura de tópicos da documentação.
* **E** o botão de menu deve atualizar seu estado semântico (`aria-expanded="true"`) para tecnologias assistivas.

### Cenário 2: Navegação por Teclado (Acessibilidade)
* **Dado que** um usuário navega na documentação utilizando a tecla `Tab`,
* **Quando** ele foca em campos de formulário, botões ou links,
* **Então** o elemento focado deve apresentar um anel de foco customizado, vibrante e de alto contraste.
* **E** submenus dinâmicos devem possuir marcação ARIA apropriada para indicar seu estado expandido ou recolhido.

### Cenário 3: Tabelas Responsivas
* **Dado que** um documento Markdown contém uma tabela com muitas colunas que excede a largura da tela móvel,
* **Quando** o usuário visualiza essa página em um dispositivo móvel,
* **Então** a tabela deve ficar contida em um wrapper com rolagem horizontal suave, sem quebrar ou estourar a largura do layout principal da página.

---

## 3. Requisitos Funcionais

### R1. Reestruturação de Cores (Themes)
* Substituir os valores hexadecimais brutos de cores em `themes.css` por um sistema unificado baseado em **HSL cromático** (com matiz baseado no azul principal).
* Centralizar as definições de cores no `:root` e redefinir as variáveis para o tema escuro sob a classe `.dark` ou preferência de sistema, reduzindo regras repetidas nas classes individuais.

### R2. Menu Hambúrguer (Mobile) e Sidebar Drawer
* Adicionar um botão de menu hambúrguer no cabeçalho móvel (`.docs-mobile-header`).
* Implementar a lógica JavaScript/CSS necessária para abrir e fechar a sidebar em telas móveis com transição suave (`transition` de opacidade e transformação).
* Garantir atributos de acessibilidade no botão (`aria-expanded`, `aria-controls` e `aria-label`).
* Garantir que, ao clicar fora da sidebar ou pressionar `Esc` com a sidebar aberta no mobile, ela seja fechada.

### R3. Refinamento Estético e Polimento
* **Glassmorphism:** Aplicar efeitos de desfoque translúcido (`backdrop-filter: blur()`) com fundo semitransparente na sidebar e no cabeçalho móvel.
* **Sombras Cromáticas:** Substituir sombras pretas secas por sombras suaves com opacidade e tonalidade cromática (HSL do tema).
* **Transições:** Padronizar transições suaves (`transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1)`) para todos os estados de `:hover`, `:active`, e `:focus-visible` em links, botões, inputs e breadcrumbs.

### R4. Acessibilidade (a11y)
* **Anéis de Foco:** Implementar contorno/sombra de foco customizada de alto contraste para `:focus-visible` em botões, links de navegação, inputs e textareas.
* **Marcação de Submenus:** Ajustar o componente de menu para suportar atributos semânticos para leitores de tela.
* **Tabelas responsivas:** Ajustar o wrapper de tabelas renderizadas para permitir rolagem horizontal sem estourar o layout do artigo.

### R5. Eliminação de Duplicação de Layouts
* Resolver a redundância entre `base.blade.php` e `layout.blade.php` de forma limpa, fazendo com que uma view reaproveite a outra ou definindo um único ponto de verdade sem quebrar compatibilidade retroativa.

---

## 4. Critérios de Sucesso

1. A barra lateral de navegação é acessível em dispositivos móveis (< 768px) e permite a navegação entre todas as páginas.
2. Não existem cores hexadecimais brutas para controle de tema em `themes.css`.
3. Todos os elementos interativos possuem transições visuais fluidas e anéis de foco personalizados.
4. Tabelas largas não quebram o layout mobile e podem ser navegadas via rolagem horizontal.
5. Todos os testes unitários e de feature atuais e novos continuam passando com 100% de sucesso.
