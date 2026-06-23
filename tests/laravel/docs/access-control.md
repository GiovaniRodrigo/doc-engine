# Access Control

This guide explains how to protect Documentation Engine routes using Laravel middleware.

## Reading Routes

By default, anyone can view the documentation. To restrict reading to authenticated users:

```env
DOC_ENGINE_MIDDLEWARE=web,auth
```

## Editing Routes

To restrict editing, publishing, and AI features to administrators:

```env
DOC_ENGINE_MIDDLEWARE=web,auth
DOC_ENGINE_EDIT_MIDDLEWARE=auth,role:admin
```

## Role Middleware

Create a `CheckRole` middleware to validate user roles:

```php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (! $request->user() || ! $request->user()->hasRole($role)) {
        abort(403, 'Forbidden.');
    }

    return $next($request);
}
```

Register the alias in `bootstrap/app.php`:

```php
$middleware->alias(['role' => CheckRole::class]);
```

## Using Laravel Gates

Define a gate in your `AppServiceProvider`:

```php
Gate::define('manage-docs', fn(User $user) => $user->is_admin);
```

Then configure the middleware:

```env
DOC_ENGINE_EDIT_MIDDLEWARE=auth,can:manage-docs
```
