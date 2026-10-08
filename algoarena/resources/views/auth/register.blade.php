@extends('auth.layout')

@section('title', 'Daftar')

@section('form')
    <h2>Buat akun baru</h2>
    <p class="muted">Cukup username dan password, langsung bisa belajar.</p>

    <form method="POST" action="{{ url('/register') }}" class="auth-form" novalidate>
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
                minlength="3"
                maxlength="30"
                @class(['invalid' => $errors->has('username')])
            >
            @error('username')
                <small class="error">{{ $message }}</small>
            @else
                <small class="muted">3–30 karakter: huruf, angka, - atau _</small>
            @enderror
        </label>

        <label class="field">
            <span>Password</span>
            <span class="password-wrap">
                <input
                    type="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    autocomplete="new-password"
                    required
                    minlength="6"
                    @class(['invalid' => $errors->has('password')])
                >
                <button type="button" class="toggle-pw" data-toggle-pw>Lihat</button>
            </span>
            @error('password')
                <small class="error">{{ $message }}</small>
            @enderror
        </label>

        <label class="field">
            <span>Ulangi Password</span>
            <input type="password" name="password_confirmation" placeholder="Ketik ulang password" autocomplete="new-password" required>
        </label>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Daftar &amp; Mulai Belajar</button>
    </form>

    <p class="auth-switch">
        Sudah punya akun? <a href="{{ route('login') }}" class="link">Masuk di sini</a>
    </p>
@endsection

@include('auth.toggle-script')
