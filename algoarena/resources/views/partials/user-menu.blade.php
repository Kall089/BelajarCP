@php($u = auth()->user())
<details class="user-menu">
    <summary title="Akun">
        <span class="avatar">{{ strtoupper(mb_substr($u->username, 0, 1)) }}</span>
        <span class="hide-sm">{{ $u->username }}</span>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6" /></svg>
    </summary>
    <div class="user-dropdown">
        <div class="user-dropdown-head">
            <span class="avatar lg">{{ strtoupper(mb_substr($u->username, 0, 1)) }}</span>
            <div>
                <strong>{{ $u->username }}</strong>
                <small>Bergabung {{ $u->created_at->locale('id')->translatedFormat('d M Y') }}</small>
            </div>
        </div>
        <a href="{{ route('home') }}">Peta Belajar</a>
        <a href="{{ route('problems.index') }}">Bank Soal</a>
        <a href="{{ route('history') }}">Riwayat Submisi</a>
        <a href="{{ route('leaderboard') }}">Peringkat</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout">Keluar</button>
        </form>
    </div>
</details>
