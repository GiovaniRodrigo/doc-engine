<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Documentacao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://cdn.tailwindcss.com"></script>

    @if (config('documentation-engine.css'))
        <link rel="stylesheet" href="{{ config('documentation-engine.css') }}">
    @endif
</head>

<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <aside class="w-72 shrink-0 overflow-y-auto border-r bg-white p-6">
            <h2 class="mb-6 text-xl font-semibold">Docs</h2>

            @include('documentation-engine::sidebar-node', ['nodes' => $sidebar])
        </aside>

        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-4xl p-10">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
