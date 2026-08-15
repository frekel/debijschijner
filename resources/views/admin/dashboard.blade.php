<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 2rem; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .dashboard-card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-decoration: none; color: inherit; transition: all 0.3s; }
        .dashboard-card:hover { transform: translateY(-4px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .dashboard-card h2 { margin: 0 0 0.5rem 0; color: #333; }
        .dashboard-card p { margin: 0; color: #666; font-size: 0.9rem; }
        .dashboard-card .icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .logout-button { display: inline-block; margin-top: 2rem; padding: .5rem .8rem; border: 1px solid #ccc; background: #fff; text-decoration: none; cursor: pointer; border-radius: 4px; }
        .logout-button:hover { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-grid">
            <a href="{{ route('admin.pages.index') }}" class="dashboard-card">
                <div class="icon">📄</div>
                <h2>Pagina beheer</h2>
                <p>Beheer, bewerk en maak pagina's aan</p>
            </a>

            <a href="{{ route('admin.contact-submissions.index') }}" class="dashboard-card">
                <div class="icon">📧</div>
                <h2>Contact inzendingen</h2>
                <p>Bekijk en exporteer contactformulier inzendingen</p>
            </a>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="logout-button">Uitloggen</button>
        </form>
    </div>
</body>
</html>
