# Contexto do Projeto

## Domínio

**Documentation Engine** é um pacote Laravel que oferece um motor de documentação técnica baseado em Markdown. Funcionalidades principais:

- Armazenamento versionado de documentos via Git (commits como histórico)
- Editor de Markdown com barra de ferramentas de formatação
- Integração com IA (OpenAI / Gemini) para geração e sumarização de conteúdo
- Colaboração em tempo real (presença de editores simultâneos)
- Suporte multilíngue (PT-BR / EN)
- Renderização de diagramas Mermaid.js embutidos no conteúdo

A demanda **006** expande o botão "Diagrama" da barra de ferramentas, adicionando um menu dropdown que permite inserir cinco tipos de diagramas UML com templates Mermaid pré-configurados.

## Público-Alvo

- **Perfil principal**: Desenvolvedores de software, arquitetos de sistemas, tech writers
- **Nível técnico**: Avançado — usuários familiarizados com Markdown, Git e terminologia UML
- **Contexto de uso**: Ambiente de trabalho desktop (monitor), dentro de uma aplicação Laravel existente
- **Motivação de uso**: Documentar arquitetura, fluxos e contratos de sistema de forma ágil, sem abandonar o editor

## Referências Visuais Encontradas

1. **Mermaid Live Editor** (mermaid.live) — editor oficial do Mermaid.js com preview ao vivo. UI minimalista com painel dividido (código | diagrama). Repositório com 73k+ ⭐ no GitHub.
2. **Mermaid Chart Visual Editor** — ferramenta GUI para edição de diagramas de classe Mermaid. Toolbar com ações contextuais: direção, tema, classes. Produto comercial com >100k usuários.
3. **GitBook Editor** (gitbook.com) — plataforma de documentação com editor de bloco. Interface limpa com slash-command `/` para inserção de conteúdo especial. Referência de mercado com >500k usuários.
4. **Notion Editor** (notion.com) — workspace de documentação com editor de bloco e dropdown de inserção de conteúdo. ~100M usuários ativos. Padrão de mercado para menus de seleção de tipo de bloco.
5. **Material Design 3 Expressive** (m3.material.io) — sistema de design do Google. FAB Menu formalizado, floating toolbars, overflow menus. Adotado por bilhões de dispositivos Android.
6. **draw.io / diagrams.net** — ferramenta de diagramação com toolbar e menu de inserção de tipo de diagrama. >20M usuários mensais.

## Tendências Identificadas

1. **Menus de inserção por categoria** — GitBook e Notion popularizaram o padrão de seleção de tipo de conteúdo via dropdown. Usuários esperam selecionar "o que" criar antes de "como" criar.
2. **Ícones semânticos por tipo de diagrama** — cada tipo de diagrama (sequência, classe, estado) tem um ícone visual reconhecível. Ferramentas como draw.io e Mermaid Chart usam ícones que refletem a estrutura do diagrama.
3. **Toolbars compactas com overflow** — Material 3 Expressive formaliza toolbar com menu de overflow. Ações menos frequentes ficam no menu, as mais frequentes ficam expostas.
4. **Feedback imediato de inserção** — ao selecionar um tipo de diagrama, o template é inserido e o cursor é posicionado no ponto de edição mais relevante. Experiência de "zero friction insertion".
5. **Surface containers com sombra suave** — dropdowns usam `box-shadow` leve e `border-radius` generoso (12-16px no M3), com fundo de superfície opaco para legibilidade.
