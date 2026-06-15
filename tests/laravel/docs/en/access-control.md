# Access Control in Laravel

This guide instructs developers on how to implement access control (authentication and authorization) in Laravel, with a specific focus on protecting the routes of the **Documentation Engine** library.

---

## 1. Access Control Overview in Laravel

Laravel provides three main mechanisms to manage who can access application resources:

1. **Route Middleware**: Filters HTTP requests before they reach controllers (e.g., ensuring the user is logged in).
2. **Gates**: Simple closures used to determine if a user is authorized to perform a specific action.
3. **Policies**: Classes that organize authorization logic for a specific data model (e.g., determining who can edit a `Document`).

---

## 2. Applying Access Control to Documentation Engine

The **Documentation Engine** library provides routes for reading (`/docs`), searching, editing (`/docs/{slug}/edit`), publishing, and AI features. You can easily protect these routes using environment variables in the `.env` file or by customizing the published [config/documentation-engine.php](file:///home/giovani/Documents/projects/laravel/config/documentation-engine.php) file.

### Restricting Documentation Reading

By default, any visitor can view the public documentation. To restrict viewing only to authenticated users, configure the `DOC_ENGINE_MIDDLEWARE` variable in your `.env` file:

```env
# Only users logged in via standard Laravel authentication can view documentation
DOC_ENGINE_MIDDLEWARE=web,auth
```

### Restricting Editing and AI Features

By default, editing and AI generation endpoints use the same middleware as reading. To restrict these administrative actions (creating drafts, comparing versions, publishing, and using AI assistants) to a limited group, define the `DOC_ENGINE_EDIT_MIDDLEWARE` variable:

```env
# Allows reading for any logged-in user, but restricts editing to administrators
DOC_ENGINE_MIDDLEWARE=web,auth
DOC_ENGINE_EDIT_MIDDLEWARE=auth,role:admin
```

---

## 3. Practical Example: Creating a Role-Based Access Control Middleware

If your application requires role-based control (e.g., `admin`, `editor`, `reader`), follow the steps below to create and register a custom middleware.

### Step 1: Create the Middleware
Create the middleware file using the Artisan command:

```bash
php artisan make:middleware CheckRole
```

Edit the created file at `app/Http/Middleware/CheckRole.php` with the following logic:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Ensure the user is logged in and has the required role
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            abort(403, 'You do not have permission to access this feature.');
        }

        return $next($request);
    }
}
```

> [!NOTE]
> Make sure the `User` model implements the `hasRole(string $role)` method for this validation to work correctly.

### Step 2: Register Middleware in Laravel
In Laravel 11+, middleware registration is centralized in the [bootstrap/app.php](file:///home/giovani/Documents/projects/laravel/bootstrap/app.php) file. Register your middleware alias in the `withMiddleware` section:

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

### Step 3: Configure in Documentation Engine
Once the middleware is registered with the alias `role`, update your `.env` file:

```env
# Only authenticated users with the 'admin' role can edit the documentation
DOC_ENGINE_EDIT_MIDDLEWARE=role:admin
```

---

## 4. Advanced Authorization using Gates

If you prefer to delegate documentation permission checks to a Laravel [Gate], you can create a specific middleware or use it directly in your host project's controllers.

### Define the Gate
Define the Gate in the `boot` method of your `AppServiceProvider`:

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

### Use the Gate in Documentation Engine Middleware
Since the library accepts any middleware registered in Laravel, you can use Laravel's native `can` middleware directly in your configuration:

```env
# Blocks edit actions unless the user passes the 'manage-docs' Gate validation
DOC_ENGINE_EDIT_MIDDLEWARE=auth,can:manage-docs
```
