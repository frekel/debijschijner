<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina beheer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: .6rem; text-align: left; }
        th { background: #f5f5f5; }
        a.button, button { display: inline-block; padding: .5rem .8rem; border: 1px solid #ccc; background: #fff; text-decoration: none; cursor: pointer; }
        .top { display: flex; justify-content: space-between; align-items: center; }
        .status { background: #e7f6ea; border: 1px solid #b6e0be; padding: .6rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="top">
        <h1>Pagina beheer</h1>
        <div style="display:flex; gap:.5rem; align-items:center;">
            <a class="button" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="button" href="{{ route('admin.contact-submissions.index') }}">Contact inzendingen</a>
            <a class="button" href="{{ route('admin.apply-submissions.index') }}">Aanvraag inzendingen</a>
            <a class="button" href="{{ route('admin.pages.create') }}">Nieuwe pagina</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit">Uitloggen</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Slug</th>
                <th>Titel</th>
                <th>Publicatie</th>
                <th>Laatst gewijzigd</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pages as $page)
                <tr>
                    <td>{{ $page->id }}</td>
                    <td>{{ $page->slug }}</td>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->is_published ? 'Ja' : 'Nee' }}</td>
                    <td>{{ $page->updated_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <a class="button" href="{{ route('admin.pages.edit', $page) }}">Bewerken</a>
                        <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" style="display:inline" onsubmit="return confirm('Weet je zeker dat je deze pagina wilt verwijderen?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Verwijderen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nog geen pagina's gevonden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
