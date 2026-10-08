<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <script>
        (function () {
            var t = 'light';
            try { t = localStorage.getItem('aa-theme') || 'light'; } catch (e) {}
            document.documentElement.dataset.theme = t;
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AlgoArena') · AlgoArena</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='1' y2='1'%3E%3Cstop offset='0' stop-color='%2322d3ee'/%3E%3Cstop offset='1' stop-color='%238b5cf6'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='32' height='32' rx='9' fill='url(%23g)'/%3E%3Cpath d='M9 22 16 9l7 13' stroke='white' stroke-width='2.4' fill='none'/%3E%3Ccircle cx='9' cy='22' r='3' fill='white'/%3E%3Ccircle cx='16' cy='9' r='3' fill='white'/%3E%3Ccircle cx='23' cy='22' r='3' fill='white'/%3E%3C/svg%3E">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
    @stack('head')
</head>
<body class="@yield('body-class')">
    @yield('content')
    <div class="toast" data-toast hidden></div>
    <script>
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-theme-toggle]');
            if (!btn) return;
            var next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;
            try { localStorage.setItem('aa-theme', next); } catch (err) {}
            document.dispatchEvent(new CustomEvent('aa:theme', { detail: next }));
        });
    </script>
    @stack('scripts')
</body>
</html>
