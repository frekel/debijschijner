<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe pagina</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        label { display: block; margin-top: 1rem; font-weight: bold; }
        input[type=text], textarea { width: 100%; padding: .5rem; }
        textarea { min-height: 380px; font-family: monospace; }
        .actions { margin-top: 1rem; display: flex; gap: .5rem; }
        a.button, button { display: inline-block; padding: .5rem .8rem; border: 1px solid #ccc; background: #fff; text-decoration: none; cursor: pointer; }
        .errors { background: #fee; border: 1px solid #f99; padding: .6rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <h1>Nieuwe pagina</h1>
    <a class="button" href="{{ route('admin.pages.index') }}">Terug</a>

    @if ($errors->any())
        <div class="errors">
            <strong>Validatiefouten:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pages.store') }}">
        @csrf

        <label for="slug">Slug (gebruik `home` voor de voorpagina)</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug') }}" required>

        <label for="title">Titel</label>
        <input id="title" name="title" type="text" value="{{ old('title') }}" required>

        <label for="html">HTML</label>
        <textarea id="html" name="html" required>{{ old('html') }}</textarea>

        <label>
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
            Gepubliceerd
        </label>

        <div class="actions">
            <button type="submit">Opslaan</button>
        </div>
    </form>
</body>
</html>
