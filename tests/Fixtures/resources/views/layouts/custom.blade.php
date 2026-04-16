<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Teste</title>
</head>
<body data-test-layout="custom">
    <aside>
        Sidebar items: {{ count($sidebar ?? []) }}
    </aside>

    <main>
        @yield('content')
    </main>
</body>
</html>
