# Controle de Acesso no Laravel

Este guia orienta os desenvolvedores sobre como implementar controle de acesso (autenticação e autorização) no Laravel, com foco específico em como proteger as rotas da biblioteca **Documentation Engine**.

---

## 1. Visão Geral do Controle de Acesso no Laravel

O Laravel oferece três mecanismos principais para gerenciar quem pode acessar recursos da aplicação:

1. **Middleware de Rotas**: Filtra requisições HTTP antes de chegarem aos controladores (ex: garantir que o usuário está logado).
2. **Gates (Portões)**: Closures simples usadas para determinar se um usuário está autorizado a realizar uma determinada ação.
3. **Policies (Políticas)**: Classes que organizam a lógica de autorização para um modelo de dados específico (ex: determinar quem pode editar um `Document`).

---

## 2. Aplicando Controle de Acesso ao Documentation Engine

A biblioteca **Documentation Engine** disponibiliza rotas para leitura (`/docs`), busca, edição (`/docs/{slug}/edit`), publicação e recursos de IA. É possível proteger essas rotas facilmente através de variáveis de ambiente no arquivo `.env` ou customizando o arquivo publicado [config/documentation-engine.php](file:///home/giovani/Documents/projects/laravel/config/documentation-engine.php).

### Proteger a Leitura da Documentação

Por padrão, qualquer visitante pode visualizar a documentação pública. Para restringir a leitura apenas a usuários autenticados, configure a variável `DOC_ENGINE_MIDDLEWARE` no seu arquivo `.env`:

```env
# Apenas usuários logados via autenticação padrão do Laravel podem visualizar a documentação
DOC_ENGINE_MIDDLEWARE=web,auth
```

### Proteger a Edição e Funcionalidades de IA

Por padrão, a edição e os endpoints de geração por IA utilizam os mesmos middlewares de leitura. Para restringir essas ações administrativas (criar rascunhos, comparar versões, publicar e usar assistentes de IA) a um grupo restrito, defina a variável `DOC_ENGINE_EDIT_MIDDLEWARE`:

```env
# Permite leitura para qualquer usuário logado, mas restringe a edição a administradores
DOC_ENGINE_MIDDLEWARE=web,auth
DOC_ENGINE_EDIT_MIDDLEWARE=auth,role:admin
```

---

## 3. Exemplo Prático: Criando um Middleware de Nível de Acesso (Roles)

Caso a sua aplicação necessite de um controle baseado em níveis (ex: `admin`, `editor`, `reader`), siga o passo a passo abaixo para criar e registrar um middleware customizado.

### Passo 1: Criar o Middleware
Crie o arquivo do middleware utilizando o comando Artisan:

```bash
php artisan make:middleware CheckRole
```

Edite o arquivo criado em `app/Http/Middleware/CheckRole.php` com a seguinte lógica:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Trata a requisição de entrada.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Certifique-se de que o usuário está logado e possui a role necessária
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            abort(403, 'Você não tem permissão para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}
```

> [!NOTE]
> Certifique-se de que o modelo `User` possui o método `hasRole(string $role)` implementado para que a validação funcione corretamente.

### Passo 2: Registrar o Middleware no Laravel
No Laravel 11+, o registro de middleware é centralizado no arquivo [bootstrap/app.php](file:///home/giovani/Documents/projects/laravel/bootstrap/app.php). Registre o alias do seu middleware na seção `withMiddleware`:

```php
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    // ...
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    // ...
```

### Passo 3: Configurar na Documentação
Com o middleware registrado com o alias `role`, basta atualizar o seu `.env`:

```env
# Apenas usuários autenticados com o papel 'admin' podem editar a documentação
DOC_ENGINE_EDIT_MIDDLEWARE=role:admin
```

---

## 4. Autorização Avançada usando Gates

Caso prefira delegar a verificação de permissões da documentação para um [Gate] do Laravel, você pode criar um middleware específico ou utilizá-lo diretamente nos controladores do seu projeto host.

### Definir o Gate
Defina o Gate no método `boot` do seu `AppServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;
use App\Models\User;

public function boot(): void
{
    Gate::define('manage-docs', function (User $user) {
        return $user->is_admin || $user->department === 'Engineering';
    });
}
```

### Utilizar o Gate no Middleware do Documentation Engine
Como a biblioteca aceita qualquer middleware registrado no Laravel, você pode usar o middleware nativo `can` do Laravel para aplicar o Gate diretamente nas configurações:

```env
# Bloqueia as ações de edição a menos que o usuário passe na validação do Gate 'manage-docs'
DOC_ENGINE_EDIT_MIDDLEWARE=auth,can:manage-docs
```
