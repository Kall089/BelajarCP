@extends('auth.layout')

@section('title', 'Masuk')

@section('form')
    <h2>Selamat datang kembali 👋</h2>
    <p class="muted">Masuk untuk melanjutkan perjalanan algoritmamu.</p>

    <form method="POST" action="{{ url('/login') }}" class="auth-form" novalidate>
        @csrf

        <label class="field">
            <span>Username</span>
            <input
                type="text"
                name="username"
                value="{{ old('username') }}"
                placeholder="contoh: budi_rpl"
                autocomplete="username"
                autofocus
                required
                @class(['invalid' => $errors->has('username')])
            >
            @error('username')
                <small class="error">{{ $message }}</small>
            @enderror
        </label>

        <label class="field">
            <span>Password</span>
            <span class="password-wrap">
                <input
                    type="password"
                    name="password"
                    placeholder="••••••"
                    autocomplete="current-password"
                    required
                    @class(['invalid' => $errors->has('password')])
                >
                <button type="button" class="toggle-pw" data-toggle-pw>Lihat</button>
            </span>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror
        </label>

        <label class="check">
            <input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Masuk</button>
    </form>

    <p class="auth-switch">
        Belum punya akun? <a href="{{ route('register') }}" class="link">Daftar sekarang</a>
    </p>
@endsection

@include('auth.toggle-script')
