# Onboarding

## Requisitos

Certifique-se de ter instalado:

* PHP 8.3
* Composer

---

## Setup

Clone o repositório e instale as dependências:

```bash
git clone <repo-url>
cd project
composer install
```

---

## Configuração

Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

---

## Subir o ambiente

Inicie os containers:

```bash
docker-compose up -d
```

---

## Sincronizar documentação

Execute o comando para importar e processar a documentação:

```bash
php artisan docs:sync <project>
```

---

## Acessar aplicação

Abra no navegador:

```text
http://localhost/docs/<project>
```

---

## Observações

* Certifique-se de que o diretório `docs/<project>` existe
* O repositório de documentação deve estar acessível localmente
* Caso utilize filas, execute:

```bash
php artisan queue:work
```
