@extends('layouts.app')

@section('body-class', 'page-auth')

@section('content')
    <main class="auth-wrap">
        <section class="auth-side">
            @include('partials.logo')
            <h1>Kuasai algoritma<br><span class="grad-text">lewat visual.</span></h1>
            <p>
                Pelajari Graph dan Dynamic Programming dengan animasi langkah demi langkah,
                lalu uji pemahamanmu di soal competitive programming yang dinilai otomatis.
            </p>
            <div class="auth-visual">@include('partials.mini-graph')</div>
        </section>

        <section class="auth-card card">
            @yield('form')
        </section>
    </main>
@endsection
