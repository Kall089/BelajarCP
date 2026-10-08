<header class="topbar">
    <div class="topbar-left">
        @include('partials.logo')
        <nav class="main-nav">
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home', 'lessons.*')])>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 6.5 12 2l10 4.5-10 4.5z" /><path d="M6 8.5V14c0 1.7 2.7 3 6 3s6-1.3 6-3V8.5" /></svg>
                Belajar
            </a>
            <a href="{{ route('problems.index') }}" @class(['active' => request()->routeIs('problems.*')])>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 18 6-6-6-6M8 6l-6 6 6 6" /></svg>
                Soal
            </a>
            <a href="{{ route('history') }}" @class(['active' => request()->routeIs('history')])>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                Riwayat
            </a>
            <a href="{{ route('leaderboard') }}" @class(['active' => request()->routeIs('leaderboard')])>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z" /><path d="M17 5h3v2a3 3 0 0 1-3 3M7 5H4v2a3 3 0 0 0 3 3" /></svg>
                Peringkat
            </a>
        </nav>
    </div>
    <div></div>
    <div class="topbar-right">
        <button type="button" class="theme-toggle" data-theme-toggle title="Ganti tema tampilan">
            <span class="t-light">Tema gelap</span>
            <span class="t-dark">Tema terang</span>
        </button>
        @include('partials.user-menu')
    </div>
</header>
