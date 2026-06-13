# Especificação: Suporte Multilíngue para Documentação

Esta especificação define os requisitos e o comportamento para identificação automática de pastas multilíngues e fornecimento de uma interface para seleção de idiomas no portal de documentação.

---

## 1. Objetivo

Fornecer suporte nativo a documentações multilíngues. O sistema deve identificar pastas de idiomas (ex: `pt/`, `en/`, `es/`), mapear os documentos correspondentes no banco de dados e oferecer um seletor visual de idiomas. O seletor permitirá alternar globalmente o idioma (filtrando o catálogo e o menu lateral) e alternar diretamente entre traduções de um mesmo documento quando disponíveis.

---

## 2. Cenários de Uso

### Cenário 1: Filtragem Global de Idiomas no Catálogo e Menu
* **Dado que** a documentação possui arquivos em português (`pt/guia.md`) e inglês (`en/guia.md`),
* **Quando** o usuário acessa o catálogo principal (`/docs`) e seleciona "English" no seletor de idiomas,
* **Então** ele deve ver apenas os documentos da pasta `en/` no catálogo.
* **E** o menu lateral de navegação (sidebar) deve exibir apenas os tópicos e links de documentos em inglês, ocultando os de português e sem exibir uma pasta raiz redundante "En".

### Cenário 2: Persistência do Idioma Selecionado
* **Dado que** o usuário selecionou o idioma "Português" no catálogo,
* **Quando** ele clica em um documento ou navega pelas páginas,
* **Então** a escolha do idioma deve persistir (via sessão), mantendo o menu lateral filtrado em português durante toda a navegação.

### Cenário 3: Alternância de Tradução Direta no Artigo
* **Dado que** o usuário está lendo o documento `pt.guia-instalacao` ("Guia de Instalação" em português),
* **Quando** o usuário muda o idioma no seletor para "English" (e o documento correspondente `en.guia-instalacao` existe),
* **Então** o sistema deve redirecioná-lo diretamente para a página do documento traduzido (`/docs/en.guia-instalacao`).

### Cenário 4: Páginas Globais/Sem Prefixo
* **Dado que** existem documentos globais na raiz sem prefixo de idioma (ex: `sobre-nos.md`),
* **Quando** o usuário filtra a documentação por qualquer idioma (ex: português ou inglês),
* **Então** os documentos globais sem prefixo devem continuar visíveis e acessíveis.

---

## 3. Requisitos Funcionais

### R1. Mapeamento e Normalização de Slugs
* Ajustar a normalização de slugs no controlador para tratar barras (`/` e `\`) como pontos (`.`). Isso permite que URLs amigáveis como `/docs/pt/guia` resolvam internamente para o slug `pt.guia`.
* Identificar códigos de idioma a partir do primeiro segmento do slug (ex: se o slug começa com `pt.`, `en.`, `es.`, `pt-br.` etc.). O padrão de detecção deve usar expressões regulares case-insensitive para códigos de 2 letras (ex: `pt`, `en`) ou códigos regionais (ex: `pt-br`, `en-us`).

### R2. Filtro de Sidebar Dinâmico
* Atualizar o `SidebarBuilder` para aceitar um parâmetro de idioma selecionado.
* Se um idioma for selecionado:
  * Filtrar os documentos exibidos para incluir apenas os correspondentes ao idioma selecionado ou documentos globais (sem código de idioma).
  * Ocultar o nó raiz do idioma (evitando pastas "Pt" ou "En" redundantes no topo da árvore) mantendo os slugs corretos para os links dos nós filhos.

### R3. Seletor de Idiomas (UI M3)
* Implementar um componente visual de seleção de idiomas no menu lateral (sidebar) e/ou no catálogo de documentos usando estilos Material Design 3.
* O seletor deve exibir nomes amigáveis para os idiomas identificados (ex: `pt` -> `Português`, `en` -> `English`, `es` -> `Español`).
* Associar ao seletor um mapa de traduções alternativas do documento atual. Se o usuário estiver visualizando um documento e selecionar outro idioma que possua a tradução desse documento (mesmo sufixo pós-idioma, ex: `pt.guia` e `en.guia`), redirecionar diretamente para a tradução correspondente. Caso contrário, redirecionar para a página principal filtrada no novo idioma.

### R4. Persistência de Estado
* Gerenciar a linguagem ativa na sessão do Laravel (`session(['docs_language' => ...])`) e/ou via parâmetro de busca na query string (`?lang=...`).
* Ao carregar qualquer documento, se o seu slug contiver um prefixo de idioma detectado, atualizar automaticamente o idioma ativo na sessão para coincidir com o idioma do documento lendo.

---

## 4. Critérios de Sucesso

1. O seletor de idiomas é exibido de forma proeminente no menu lateral quando existem pastas multilíngues identificadas.
2. Ao selecionar um idioma, o catálogo e a sidebar de navegação são filtrados dinamicamente para mostrar apenas os documentos daquele idioma (e os globais).
3. Ao alternar o idioma em um documento que possui tradução equivalente, o redirecionamento ocorre diretamente para a tradução correspondente.
4. A normalização de URLs aceita barras para identificar slugs com pontos, permitindo `/docs/pt/instalacao` acessar `pt.instalacao`.
5. Todos os testes automatizados da aplicação continuam passando sem falha.
