# Plano de Implementação: Suporte Multilíngue para Documentação

Este plano detalha as alterações técnicas necessárias para dar suporte a estruturas de pastas multilíngues no pacote `doc-engine`.

---

## 1. Arquivos a serem Criados/Editados

### 1.1. Backend (Laravel)
*   **[src/Http/DocumentationController.php](file:///home/giovani/Documents/projects/doc-engine/src/Http/DocumentationController.php):**
    *   Atualizar `normalizeSlug` para converter barras (`/` e `\`) em pontos (`.`).
    *   Adicionar lógica para persistência e detecção de idioma na sessão.
    *   Filtrar os documentos no método `index` com base no idioma selecionado.
    *   No método `show`, identificar traduções disponíveis para o documento atual (mesmo sufixo pós-idioma) e passar a lista para o template.
*   **[src/Application/Services/SidebarBuilder.php](file:///home/giovani/Documents/projects/doc-engine/src/Application/Services/SidebarBuilder.php):**
    *   Modificar a assinatura do método `build(array $slugs, ?string $selectedLanguage = null)` para aceitar a linguagem ativa.
    *   Filtrar os itens por idioma e remover a pasta raiz do idioma do menu lateral, deixando apenas a estrutura interna dos documentos.
*   **[tests/Feature/MultilingualSupportTest.php](file:///home/giovani/Documents/projects/doc-engine/tests/Feature/MultilingualSupportTest.php):**
    *   Criar testes de feature para validar a identificação de idiomas, filtro da sidebar, redirecionamentos diretos e normalização de caminhos com barra.

### 1.2. Frontend (Material Design 3 & JS)
*   **[resources/views/layouts/default.blade.php](file:///home/giovani/Documents/projects/doc-engine/resources/views/layouts/default.blade.php):**
    *   Inserir o seletor de idiomas (dropdown) na barra lateral de navegação.
    *   Adicionar a lógica JavaScript para lidar com a alternância de idiomas e redirecionamento de traduções.
*   **[resources/css/docs/components.css](file:///home/giovani/Documents/projects/doc-engine/resources/css/docs/components.css):**
    *   Estilizar o dropdown de seleção de idiomas no padrão M3 (cantos arredondados, fundo de superfície e efeito de foco).

---

## 2. Abordagem Técnica e Regras de Negócio

### 2.1. Detecção de Idiomas
*   Um slug é considerado multilíngue se contiver um ponto e o primeiro segmento bater com a regex `^[a-z]{2}(?:[._-][a-z]{2})?$` (ex: `pt`, `en`, `pt-br`).
*   Construiremos um mapa dinâmico de idiomas disponíveis a partir de todos os slugs registrados no banco.
*   Os nomes amigáveis serão traduzidos se coincidirem com chaves conhecidas (`pt` -> `Português`, `en` -> `English`, etc.), caindo de volta para a sigla em letras maiúsculas.

### 2.2. Resolução de URLs
*   Se o usuário acessar `/docs/pt/instalacao`, o controlador traduzirá o slug para `pt.instalacao` antes de consultar o repositório.
*   Isso garante compatibilidade retroativa e facilidade de escrita de links internos no formato Markdown padrão (ex: `[Instalação](./pt/instalacao)` ou `[Instalação](pt/instalacao.md)`).

### 2.3. Lógica de Redirecionamento de Traduções
*   Quando o usuário muda de idioma na tela de leitura de um documento, a interface verifica um objeto JSON contendo as traduções disponíveis para a página corrente:
    ```json
    {
        "en": "http://localhost/docs/en.guia-instalacao",
        "es": "http://localhost/docs/es.guia-instalacao"
    }
    ```
*   Se o idioma selecionado estiver presente no mapa, o usuário é redirecionado diretamente para a tradução. Caso contrário, ele é redirecionado para a home filtrada no idioma (`/docs?lang=code`).
