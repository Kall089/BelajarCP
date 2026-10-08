@extends('layouts.app')

@section('title', 'Peta Belajar')

@php
    $diffClass = ['Mudah' => 'easy', 'Sedang' => 'medium', 'Sulit' => 'hard'];
    $gridVals = [1, 1, 1, 1, 1, 1, 2, 3, 4, 5, 1, 3, 6, 10, 15];
@endphp

@section('content')
    @include('partials.navbar')

    <main class="container">
        @if (session('welcome'))
            <div class="flash">Akun <strong>{{ auth()->user()->username }}</strong> berhasil dibuat. Mulai dari materi pertama, ya!</div>
        @endif

        <section class="hero">
            <div>
                <p class="eyebrow">Halo, {{ auth()->user()->username }}</p>
                <h1>Pahami algoritmanya,<br><span class="grad-text">lalu taklukkan soalnya.</span></h1>
                <p class="lead">
                    Setiap materi dimulai dengan teori dan <strong>visualisasi interaktif</strong> yang bisa kamu putar langkah demi langkah.
                    Setelah paham caranya, uji dirimu di soal competitive programming yang mirip.
                </p>
                <div class="hero-actions">
                    @if ($nextLesson)
                        <a href="{{ route('lessons.show', $nextLesson) }}" class="btn btn-primary btn-lg">
                            {{ $stats['lessons'] ? 'Lanjutkan' : 'Mulai' }}: {{ $nextLesson->title }} →
                        </a>
                    @else
                        <a href="{{ route('problems.index') }}" class="btn btn-primary btn-lg">Semua materi selesai, lanjut berlatih →</a>
                    @endif
                    <a href="{{ route('problems.index') }}" class="btn btn-outline btn-lg">Bank Soal</a>
                </div>
                <div class="hero-stats">
                    <div><b>{{ $stats['lessons'] }}<span class="faint">/{{ $stats['totalLessons'] }}</span></b><small>Materi selesai</small></div>
                    <div><b>{{ $stats['solved'] }}<span class="faint">/{{ $stats['totalProblems'] }}</span></b><small>Soal Accepted</small></div>
                    <div><b>{{ $stats['submissions'] }}</b><small>Total submisi</small></div>
                </div>
            </div>

            <div class="hero-visual hide-sm">
                @include('partials.mini-graph')
                <div class="hv-label">
                    <span class="pill pill-graph">BFS</span>
                    <span class="pill pill-dp">DP grid</span>
                </div>
                <div class="hv-grid">
                    @foreach ($gridVals as $i => $v)
                        <span style="--d: {{ ($i % 5 + intdiv($i, 5)) * 0.35 }}s">{{ $v }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        @foreach ($tracks as $track)
            @php
                $pct = $track['lessons']->count() ? $track['lessonsDone'] / $track['lessons']->count() * 100 : 0;
                $firstOpen = $track['lessons']->first(fn ($l) => ! $completed->has($l->id));
            @endphp
            <section class="track track-{{ $track['key'] }}">
                <div class="track-head">
                    <div class="track-title">
                        <span class="track-icon">
                            @if ($track['key'] === 'graph')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="5" cy="6" r="2.5" /><circle cx="19" cy="6" r="2.5" /><circle cx="12" cy="18" r="2.5" /><path d="M7.5 6h9M6.3 8.2l4.4 7.6M17.7 8.2l-4.4 7.6" /></svg>
                            @elseif ($track['key'] === 'struktur-data')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="3" width="6" height="5" rx="1.2" /><rect x="3" y="15" width="6" height="5" rx="1.2" /><rect x="15" y="15" width="6" height="5" rx="1.2" /><path d="M12 8v3M6 15v-4h12v4" /></svg>
                            @elseif ($track['key'] === 'matematika')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 7h6M8 4v6M14 7h5M5 17h6M14 15.5h5M14 18.5h5" /><path d="M5.5 14.5l5 5M10.5 14.5l-5 5" /></svg>
                            @elseif ($track['key'] === 'string')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 18l4-12 4 12M5.5 14h5" /><path d="M14 10h3.5a2 2 0 0 1 0 4H14V7h3a2 2 0 0 1 0 3M14 14h4a2 2 0 0 1 0 4h-4z" /></svg>
                            @elseif ($track['key'] === 'fondasi')
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16M6 20V10M12 20V6M18 20v-7" /></svg>
                            @else
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /><path d="M10 6.5h4M17.5 10v4" /></svg>
                            @endif
                        </span>
                        <div>
                            <h2>{{ $track['name'] }}</h2>
                            <p>{{ $track['tagline'] }}</p>
                        </div>
                    </div>
                    <div class="track-progress">
                        <div class="track-progress-text">
                            <span><b>{{ $track['lessonsDone'] }}</b>/{{ $track['lessons']->count() }} materi</span>
                            <span><b>{{ $track['problemsSolved'] }}</b>/{{ $track['problemsTotal'] }} soal AC</span>
                        </div>
                        <div class="bar"><i style="width: {{ $pct }}%"></i></div>
                    </div>
                </div>

                <div class="roadmap">
                    @foreach ($track['lessons'] as $lesson)
                        @php $isDone = $completed->has($lesson->id); @endphp
                        <article @class(['lesson-card', 'done' => $isDone, 'next' => $firstOpen && $firstOpen->id === $lesson->id])>
                            <span class="node"></span>
                            <div>
                                <h3><a href="{{ route('lessons.show', $lesson) }}">{{ $lesson->title }}</a></h3>
                                <p>{{ $lesson->summary }}</p>
                                <div class="lesson-meta">
                                    <span class="pill">⏱ {{ $lesson->minutes }} menit</span>
                                    @foreach ($lesson->problems as $p)
                                        @php $s = $best[$p->id] ?? null; @endphp
                                        <a class="lesson-problem" href="{{ route('problems.show', $p) }}" title="{{ $p->difficulty }}">
                                            <span @class(['status-dot', 'done' => $s === 100, 'part' => $s !== null && $s < 100])></span>
                                            {{ $p->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="lesson-cta">
                                <a href="{{ route('lessons.show', $lesson) }}" @class(['btn', 'btn-primary' => $firstOpen && $firstOpen->id === $lesson->id, 'btn-outline' => ! ($firstOpen && $firstOpen->id === $lesson->id)])>
                                    {{ $isDone ? 'Ulas lagi' : 'Pelajari' }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </main>
@endsection
