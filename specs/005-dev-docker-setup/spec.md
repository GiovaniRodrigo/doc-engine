# Especificação: Ambiente de Desenvolvimento com Docker Compose

## Objetivo
Modificar a configuração do `docker-compose.yml` para viabilizar um ambiente de desenvolvimento local interativo, onde as alterações no código da biblioteca no host sejam refletidas automaticamente no container, e a aplicação esteja acessível pelo navegador do host executando o servidor de desenvolvimento.

## Cenários de Uso
* **Dado que** o desenvolvedor está alterando arquivos de código no diretório da biblioteca (`src/`),
* **Quando** ele inicia o container com `docker compose up`,
* **Então** o container deve executar o comando `composer run dev` na pasta da aplicação de testes Laravel e ficar acessível no navegador do host em `http://localhost:8000`.
* **E** as alterações feitas nos arquivos locais (`src/`) devem ser refletidas imediatamente dentro do container sem necessidade de rebuild.

## Requisitos Funcionais / Infraestrutura
1. O container do `docker-compose.yml` deve usar os volumes apropriados para mapear o código do repositório local (`.`) para `/app` no container.
2. Os diretórios gerenciados por gerenciadores de dependência (`vendor`, `tests/laravel/vendor`, `tests/laravel/node_modules`) que são gerados durante a build do Dockerfile não devem ser sobrescritos por pastas vazias ou incompatíveis do host.
3. O comando padrão do container no `docker-compose.yml` deve ser configurado para rodar `composer run dev` no diretório `/app/tests/laravel`.
4. Os servidores de desenvolvimento do Laravel (Artisan serve) e do Vite (npm run dev) devem ser expostos nas portas adequadas (`8000` e `5173` respectivamente) e configurados para escutar em `0.0.0.0` para permitir conexões externas a partir do host.

## Critérios de Sucesso
* Executar `docker compose up` inicia com sucesso o servidor de desenvolvimento do Laravel e do Vite.
* A URL `http://localhost:8000` é acessível via navegador do host.
* Alterações de código nos arquivos da biblioteca no host são imediatamente sincronizadas com o container.
