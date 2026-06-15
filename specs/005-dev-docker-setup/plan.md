# Plano de Implementação: Ambiente de Desenvolvimento com Docker Compose

## Arquivos a Modificar
1. [docker-compose.yml](file:///home/giovani/Documents/projects/doc-engine/docker-compose.yml)
2. [tests/laravel/vite.config.js](file:///home/giovani/Documents/projects/doc-engine/tests/laravel/vite.config.js)

## Detalhes Técnicos

### 1. docker-compose.yml
* **Volumes**:
  * Mapear `.` (host) para `/app` (container) para sincronização em tempo real.
  * Preservar `/app/vendor`, `/app/tests/laravel/vendor` e `/app/tests/laravel/node_modules` usando volumes anônimos do Docker. Isso evita que as dependências instaladas no container sejam sobrescritas pela estrutura do host.
  * Preservar o volume existente para os screenshots: `./tests/laravel/tests/Browser/screenshots:/app/tests/laravel/tests/Browser/screenshots`.
* **Portas**:
  * Expor porta `8000` para a aplicação Laravel.
  * Expor porta `5173` para o Vite dev server (necessário para HMR).
* **Variáveis de Ambiente**:
  * Definir `SERVER_HOST=0.0.0.0` para que o comando `php artisan serve` escute em todas as interfaces.
* **Comando e Diretório de Trabalho**:
  * Definir `working_dir: /app/tests/laravel`.
  * Definir `command: composer run dev`.

### 2. tests/laravel/vite.config.js
* Adicionar `host: '0.0.0.0'` em `server` para que o servidor Vite escute em todas as interfaces de rede do container.
