<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login · De Bijschijner</title>
    <style>
        :root {
            --bg: #f7f6f2;
            --card: #ffffff;
            --text: #1b1b18;
            --muted: #66645f;
            --line: #e8e3d8;
            --accent: #E17000;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: radial-gradient(circle at 20% 0%, rgba(225, 112, 0, 0.12) 0%, var(--bg) 45%);
            color: var(--text);
        }

        .card {
            width: 100%;
            max-width: 420px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: var(--card);
            box-shadow: 0 20px 60px rgba(27, 27, 24, 0.08);
            padding: 28px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            letter-spacing: -0.02em;
        }

        p {
            margin: 0 0 20px;
            color: var(--muted);
        }

        form {
            display: grid;
            gap: 12px;
        }

        label {
            font-size: 14px;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
        }

        button {
            margin-top: 4px;
            border: 0;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-weight: 700;
            padding: 12px;
            cursor: pointer;
        }

        .error {
            margin-top: 12px;
            border: 1px solid #f7c8c8;
            background: #fff4f4;
            color: #941919;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .back {
            display: inline-block;
            margin-top: 16px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Admin Login</h1>
        <p>Sign in to manage the website.</p>

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div>
                <label for="username">Username or email</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button type="submit">Sign in</button>
        </form>

        @if ($errors->any())
            <div class="error">{{ $errors->first('username') }}</div>
        @endif

        <a class="back" href="/">← Back to website</a>
    </main>
</body>
</html>
