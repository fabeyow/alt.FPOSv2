<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | alt.FPOS</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; }
        nav { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; background: #1e293b; }
        nav a { color: #e2e8f0; text-decoration: none; margin-left: 1.5rem; }
        nav a:hover, nav a.active { color: #38bdf8; }
        .brand { font-weight: 700; font-size: 1.2rem; margin: 0; }
        main { max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        .hero { text-align: center; padding: 3rem 0; }
        .btn { display: inline-block; padding: .7rem 1.4rem; margin: .3rem; background: #38bdf8; color: #0f172a; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; }
        .card { background: #1e293b; padding: 1.5rem; border-radius: 12px; }
        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 12px; overflow: hidden; }
        th, td { padding: .8rem 1rem; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #334155; }
        footer { text-align: center; padding: 2rem; color: #94a3b8; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}" class="brand">⚡ alt.FPOS</a>
        <div>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            <a href="{{ route('customers') }}" class="{{ request()->routeIs('customers') ? 'active' : '' }}">Customers</a>
            <a href="{{ route('users') }}" class="{{ request()->routeIs('users') ? 'active' : '' }}">Users</a>
        </div>
    </nav>
    <main>@yield('content')</main>
    <footer>© {{ date('Y') }} alt.FPOS — Built with Laravel {{ app()->version() }}</footer>
</body>
</html>