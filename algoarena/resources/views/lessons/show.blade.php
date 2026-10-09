@extends('layouts.app')

@section('title', $lesson->title)

@php($diffClass = ['Mudah' => 'easy', 'Sedang' => 'medium', 'Sulit' => 'hard'])

@push('head')
@endpush

@section('content')
    @include('partials.navbar')

    <div class="lesson-layout">
        <aside class="lesson-aside">
            <p class="aside-title">Di halaman ini</p>
            <ul class="toc" data-toc></ul>

            <p class="aside-title">Track {{ $lesson->trackName() }}</p>
            <ul class="track-nav">
                @foreach ($trackLessons as $l)
                    <li>
                        <a href="{{ route('lessons.show', $l->slug) }}" @class(['current' => $l->id === $lesson->id])>
                            <span @class(['status-dot', 'done' => $completed->has($l->id)])></span>
                            {{ $l->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <main class="lesson-main">
            <header class="lesson-hero">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Peta Belajar</a>
                    <span>/</span>
                    <span>{{ $lesson->trackName() }}</span>
                </div>
                <h1>{{ $lesson->title }}</h1>
                <p class="subtitle">{{ $lesson->subtitle }}</p>
                <div class="meta">
                    <span class="pill pill-{{ $lesson->track }}">{{ $lesson->trackName() }}</span>
                    <span class="lvl {{ $lesson->levelInfo()['class'] }}">Materi {{ $lesson->levelInfo()['name'] }}</span>
                    <span class="pill">⏱ {{ $lesson->minutes }} menit</span>
                    <span class="pill">{{ $lesson->problems->count() }} soal latihan</span>
                    @if ($done)
                        <span class="pill" style="color: var(--green); background: var(--green-soft)">✓ Selesai</span>
                    @endif
                </div>
                <nav class="level-rail" aria-label="Tingkatan isi materi" data-level-rail>
                    <a href="#dasar"><span class="lvl lvl-1">Dasar</span> konsep, contoh &amp; visualisasi</a>
                    <a href="#menengah"><span class="lvl lvl-2">Menengah</span> kode C++ baris demi baris</a>
                    <a href="#lanjut"><span class="lvl lvl-3">Lanjut</span> teknik untuk soal sulit</a>
                </nav>
            </header>

            @includeFirst(['lessons.content.'.$lesson->slug, 'lessons.content._soon'])

            <section class="lesson-section" id="latihan" data-toc="Latihan Soal" data-level="0">
                <h2><span class="sec-icon">🏁</span> Latihan Soal</h2>
                <p class="prose">Sudah paham caranya? Saatnya membuktikan! Soal-soal ini memakai teknik yang sama dengan materi di atas.</p>
                <div class="practice-grid">
                    @foreach ($lesson->problems as $p)
                        @php($s = $best[$p->id] ?? null)
                        <a href="{{ route('problems.show', $p) }}" class="practice-card">
                            <div class="row">
                                <span class="diff {{ $diffClass[$p->difficulty] }}">{{ $p->difficulty }}</span>
                                @if ($s !== null)
                                    <span @class(['score-chip', 'full' => $s === 100, 'part' => $s < 100])>{{ $s }}</span>
                                @endif
                            </div>
                            <h3>{{ $p->title }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($p->statement), 110) }}</p>
                            <span class="link">Kerjakan soal →</span>
                        </a>
                    @endforeach
                </div>

                <div @class(['complete-box', 'is-done' => $done]) data-complete-box>
                    <div style="flex: 1">
                        <b data-complete-title>{{ $done ? 'Materi ini sudah kamu selesaikan 🎉' : 'Sudah memahami materi ini?' }}</b>
                        <div class="muted" data-complete-sub>
                            {{ $done ? 'Kamu tetap bisa mengulasnya kapan saja.' : 'Tandai selesai agar progresmu tercatat di peta belajar.' }}
                        </div>
                    </div>
                    <button class="btn {{ $done ? 'btn-outline' : 'btn-primary' }}" data-complete
                        data-url="{{ route('lessons.complete', $lesson) }}" data-done="{{ $done ? 1 : 0 }}">
                        {{ $done ? 'Batalkan' : 'Tandai Selesai' }}
                    </button>
                </div>
            </section>

            <nav class="lesson-footer">
                @if ($prev)
                    <a class="nav-lesson" href="{{ route('lessons.show', $prev->slug) }}"><small>← Sebelumnya</small><b>{{ $prev->title }}</b></a>
                @else
                    <span></span>
                @endif
                @if ($next)
                    <a class="nav-lesson next" href="{{ route('lessons.show', $next->slug) }}"><small>Berikutnya →</small><b>{{ $next->title }}</b></a>
                @endif
            </nav>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="{{ asset('js/viz/core.js') }}?v={{ filemtime(public_path('js/viz/core.js')) }}"></script>
    <script src="{{ asset('js/viz/graph-algos.js') }}?v={{ filemtime(public_path('js/viz/graph-algos.js')) }}"></script>
    <script src="{{ asset('js/viz/dp-algos.js') }}?v={{ filemtime(public_path('js/viz/dp-algos.js')) }}"></script>
    {{-- kit.js: komponen visual bersama; t-{track}.js: visualisasi khusus track materi ini --}}
    @foreach (['js/viz/widgets.js', 'js/viz/graph-advanced.js', 'js/viz/graph-more.js', 'js/viz/dp-advanced.js', 'js/viz/dp-more.js', 'js/viz/fondasi.js', 'js/viz/kit.js', 'js/viz/t-'.$lesson->track.'.js', 'js/walkthrough.js'] as $script)
        @if (is_file(public_path($script)))
            <script src="{{ asset($script) }}?v={{ filemtime(public_path($script)) }}"></script>
        @endif
    @endforeach
    <script src="{{ asset('js/lesson.js') }}?v={{ filemtime(public_path('js/lesson.js')) }}"></script>
@endpush
