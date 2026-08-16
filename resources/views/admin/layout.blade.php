<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-gray-900">Admin Panel</a>
                <div class="flex gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-900">Pagina's</a>
                    <a href="{{ route('admin.contact-submissions.index') }}" class="text-gray-600 hover:text-gray-900">Contact</a>
                    <a href="{{ route('admin.apply-submissions.index') }}" class="text-gray-600 hover:text-gray-900">Aanvragen</a>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium">Uitloggen</button>
            </form>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-600 text-sm">
            <p>&copy; {{ now()->year }} De Bijschijner. Alle rechten voorbehouden.</p>
        </div>
    </footer>
</body>
</html>
