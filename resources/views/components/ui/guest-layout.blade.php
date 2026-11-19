@props(['title'])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Aura CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="crm-auth-shell">
        <div class="crm-auth-panel">
            <div class="crm-auth-brand">
                <span class="crm-sidebar__logo-mark" aria-hidden="true">A</span>
                <span>Aura CRM</span>
            </div>
            <x-ui.card>
                <h1 class="crm-card__title">{{ $title }}</h1>
                @if (session('status'))
                    <p role="status">{{ session('status') }}</p>
                @endif
                {{ $slot }}
            </x-ui.card>
        </div>
    </main>
    <crm-toast id="crm-toaster"></crm-toast>
</body>
</html>
