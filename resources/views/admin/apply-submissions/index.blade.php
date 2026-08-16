<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aanvraag inzendingen</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ddd; padding: .6rem; text-align: left; vertical-align: top; }
        th { background: #f5f5f5; }
        a.button { display: inline-block; padding: .5rem .8rem; border: 1px solid #ccc; background: #fff; text-decoration: none; cursor: pointer; }
        .top { display: flex; justify-content: space-between; align-items: center; gap: .75rem; flex-wrap: wrap; }
        .controls { display: flex; gap: .5rem; align-items: center; }
        .msg { max-width: 420px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="top">
        <h1>Aanvraag inzendingen</h1>
        <div class="controls">
            <a class="button" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="button" href="{{ route('admin.apply-submissions.export') }}">CSV export</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                @csrf
                <button type="submit" style="padding: .5rem .8rem; border: 1px solid #ccc; background: #fff; cursor: pointer;">Uitloggen</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Email</th>
                <th>Telefoon</th>
                <th>Traject</th>
                <th>Bericht</th>
                <th>Datum</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($submissions as $submission)
                <tr>
                    <td>{{ $submission->id }}</td>
                    <td>{{ $submission->first_name }} {{ $submission->last_name }}</td>
                    <td>{{ $submission->email }}</td>
                    <td>{{ $submission->phone }}</td>
                    <td>{{ $submission->trajectory }}</td>
                    <td class="msg">{{ $submission->message }}</td>
                    <td>{{ $submission->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Nog geen inzendingen.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $submissions->links() }}
    </div>
</body>
</html>
