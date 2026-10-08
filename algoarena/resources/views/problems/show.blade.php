@extends('layouts.app')

@section('title', $problem->title)
@section('body-class', 'page-workspace')

@php
    $diffClass = ['Mudah' => 'easy', 'Sedang' => 'medium', 'Sulit' => 'hard'];
    $langs = \App\Http\Controllers\ProblemController::LANGUAGES;
    $hints = $problem->hints ?? [];
    $hasSubmitted = $submissions->isNotEmpty();
@endphp

@push('head')
@endpush

@section('content')
    <header class="topbar">
        <div class="topbar-left">
            @include('partials.logo')
            <span class="divider hide-sm"></span>
            <a class="btn btn-ghost hide-sm" href="{{ route('problems.index') }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
                Bank Soal
            </a>
            <a @class(['btn', 'btn-icon', 'disabled' => ! $prev]) href="{{ $prev ? route('problems.show', $prev->slug) : '#' }}" title="{{ $prev?->title ?? 'Soal sebelumnya' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m15 18-6-6 6-6" /></svg>
            </a>
            <a @class(['btn', 'btn-icon', 'disabled' => ! $next]) href="{{ $next ? route('problems.show', $next->slug) : '#' }}" title="{{ $next?->title ?? 'Soal berikutnya' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m9 18 6-6-6-6" /></svg>
            </a>
        </div>

        <div class="topbar-center">
            <button class="btn" data-run title="Jalankan tes contoh (Ctrl + ')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M7 4v16l13-8z" /></svg>
                <span class="hide-sm">Jalankan</span>
            </button>
            <button class="btn btn-success" data-submit title="Kirim ke judge (Ctrl + Enter)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 16V4m0 0-5 5m5-5 5 5M5 20h14" /></svg>
                Submit
            </button>
        </div>

        <div class="topbar-right">
            <span class="pill mono hide-sm" data-stopwatch title="Waktu pengerjaan">⏱ 00:00</span>
            @include('partials.user-menu')
        </div>
    </header>

    <main class="workspace" data-workspace>
        {{-- ───────────── Panel kiri ───────────── --}}
        <section class="pane pane-left" data-pane-left>
            <nav class="pane-tabs">
                <button class="tab active" data-tab="soal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" /><path d="M14 3v6h6" /></svg>
                    Soal
                </button>
                <button class="tab" data-tab="editorial">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z" /></svg>
                    Pembahasan
                </button>
                <button class="tab" data-tab="submisi">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>
                    Submisi <span class="count" data-sub-count>{{ $submissions->count() }}</span>
                </button>
            </nav>

            <div class="pane-body">
                {{-- Soal --}}
                <article class="tab-panel active" data-panel="soal">
                    <h1 class="problem-title">
                        {{ $problem->title }}
                        @if ($best === 100)
                            <span class="pill" style="color: var(--green); background: var(--green-soft)">✓ Accepted</span>
                        @elseif ($submissions->isNotEmpty())
                            <span class="score-chip part">{{ $best }}</span>
                        @endif
                    </h1>
                    <div class="problem-meta">
                        <span class="pill diff-pill {{ $diffClass[$problem->difficulty] }}">{{ $problem->difficulty }}</span>
                        @foreach ($problem->tags as $tag)
                            <span class="pill">{{ $tag }}</span>
                        @endforeach
                        <span class="pill mono" title="Batas waktu per tes">⏱ {{ $problem->timeLimitFor('cpp') / 1000 }} dtk · Python {{ $problem->timeLimitFor('python') / 1000 }} dtk</span>
                    </div>

                    <div class="statement">
                        {!! $problem->statement !!}
                        <h3>Format Masukan</h3>
                        {!! $problem->input_format !!}
                        <h3>Format Keluaran</h3>
                        {!! $problem->output_format !!}
                        <h3>Batasan</h3>
                        {!! $problem->constraints !!}

                        @foreach ($problem->samples as $i => $sample)
                            <div class="sample">
                                <div class="sample-head">Contoh {{ $i + 1 }}</div>
                                <div class="sample-io">
                                    <div>
                                        <small>Masukan <button class="copy-btn" data-copy-text="{{ $sample->input }}">salin</button></small>
                                        <pre>{{ rtrim($sample->input) }}</pre>
                                    </div>
                                    <div>
                                        <small>Keluaran</small>
                                        <pre>{{ $sample->output }}</pre>
                                    </div>
                                </div>
                                @if ($sample->explanation)
                                    <div class="sample-note">{!! $sample->explanation !!}</div>
                                @endif
                                @if ($problem->sample_visual)
                                    <details class="sample-visual" data-sample-visual="{{ $i }}">
                                        <summary>👁 Lihat contoh ini sebagai gambar</summary>
                                        <div class="sample-visual-body"></div>
                                    </details>
                                @endif
                            </div>
                        @endforeach

                        @if ($hints)
                            <h3>Petunjuk</h3>
                            <p class="muted" style="margin-top: -4px">Buka satu per satu hanya jika kamu buntu. Coba pikirkan sendiri dulu!</p>
                            <div class="hint-list">
                                @foreach ($hints as $h => $hint)
                                    <details class="hint-item">
                                        <summary><span class="hint-num">{{ $h + 1 }}</span> Petunjuk {{ $h + 1 }}{{ $h === count($hints) - 1 ? ' (hampir jawaban)' : '' }}</summary>
                                        <p>{!! $hint !!}</p>
                                    </details>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($problem->lesson)
                        <a class="lesson-link" href="{{ route('lessons.show', $problem->lesson) }}">
                            <span style="font-size: 22px">🎬</span>
                            <div>
                                <b>Lupa caranya? Pelajari lagi: {{ $problem->lesson->title }}</b>
                                <small>Lengkap dengan visualisasi langkah demi langkah</small>
                            </div>
                        </a>
                    @endif
                </article>

                {{-- Pembahasan --}}
                <article class="tab-panel" data-panel="editorial">
                    <div class="locked" data-editorial-locked @if ($hasSubmitted) hidden @endif>
                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="11" width="16" height="10" rx="2" /><path d="M8 11V7a4 4 0 0 1 8 0v4" /></svg>
                        <h3>Pembahasan masih terkunci</h3>
                        <p>Coba dulu! Pembahasan dan kode solusi terbuka setelah kamu melakukan submit pertama.</p>
                    </div>
                    <div data-editorial @if (! $hasSubmitted) hidden @endif>
                        @if ($hasSubmitted)
                            @include('problems.editorial')
                        @endif
                    </div>
                </article>

                {{-- Submisi --}}
                <article class="tab-panel" data-panel="submisi">
                    <div class="sub-list" data-sub-list>
                        @forelse ($submissions as $s)
                            <div class="sub-item">
                                <div>
                                    <span class="verdict verdict-{{ $s->verdict }}">{{ $s->verdictLabel() }}</span>
                                    <small>{{ $s->created_at->locale('id')->translatedFormat('d M, H:i') }} · {{ $langs[$s->language] ?? $s->language }} · {{ $s->passed }}/{{ $s->total }} tes · {{ $s->time_ms }} ms</small>
                                </div>
                                <span @class(['score-chip', 'full' => $s->score === 100, 'part' => $s->score < 100])>{{ $s->score }}</span>
                                <button class="btn btn-sm btn-ghost" data-load-sub="{{ route('submissions.show', $s) }}">Lihat kode</button>
                            </div>
                        @empty
                            <p class="empty" data-sub-empty>Belum ada submisi untuk soal ini.</p>
                        @endforelse
                    </div>
                </article>
            </div>
        </section>

        <div class="gutter gutter-x" data-gutter-x><span></span></div>

        {{-- ───────────── Panel kanan ───────────── --}}
        <section class="pane-right" data-pane-right>
            <div class="pane editor-pane">
                <div class="pane-tabs editor-head">
                    <span class="tab active static">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2.4"><path d="m16 18 6-6-6-6M8 6l-6 6 6 6" /></svg>
                        Kode
                    </span>
                    <div class="editor-tools">
                        <select class="lang-select" data-lang-select aria-label="Bahasa">
                            @foreach ($languages as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-icon" data-reset-code title="Kembalikan kode awal">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" /><path d="M3 3v5h5" /></svg>
                        </button>
                    </div>
                </div>
                <div class="editor-host">
                    <div class="editor-loading" data-editor-loading>Memuat editor…</div>
                    <div class="monaco" data-editor></div>
                </div>
                <div class="editor-status">
                    <span class="runtime-status" data-runtime><i></i><span>JavaScript siap</span></span>
                    <span class="hide-sm">Ctrl + ' Jalankan · Ctrl + Enter Submit · <span data-lang-hint></span></span>
                </div>
            </div>

            <div class="gutter gutter-y" data-gutter-y><span></span></div>

            <div class="pane console-pane" data-console>
                <nav class="pane-tabs">
                    <button class="tab active" data-ctab="samples">Tes Contoh</button>
                    <button class="tab" data-ctab="custom">Input Sendiri</button>
                    <button class="tab" data-ctab="result">Hasil Submit</button>
                </nav>
                <div class="console-body">
                    <div class="ctab-panel active" data-cpanel="samples">
                        <div data-sample-view>
                            <p class="muted" style="margin: 0">Tekan <b>Jalankan</b> untuk menguji kodemu dengan tes contoh.</p>
                        </div>
                    </div>
                    <div class="ctab-panel" data-cpanel="custom">
                        <div class="io-block">
                            <small>Input</small>
                            <textarea class="mono" data-custom-input spellcheck="false">{{ rtrim($problem->samples->first()?->input ?? '') }}</textarea>
                        </div>
                        <button class="btn btn-sm" data-run-custom>▶ Jalankan dengan input ini</button>
                        <div class="io-block" style="margin-top: 12px" data-custom-out-wrap hidden>
                            <small data-custom-label>Output</small>
                            <pre data-custom-out></pre>
                        </div>
                    </div>
                    <div class="ctab-panel" data-cpanel="result">
                        <div data-result-view>
                            <p class="muted" style="margin: 0">Hasil penilaian semua tes (termasuk tes tersembunyi) akan muncul di sini setelah <b>Submit</b>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal-backdrop" data-modal hidden>
        <div class="modal">
            <div class="modal-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5" /></svg>
            </div>
            <h2>Accepted! 🎉</h2>
            <p data-modal-text>Semua tes lolos.</p>
            <div class="modal-actions">
                <button class="btn btn-outline" data-modal-editorial>Lihat Pembahasan</button>
                @if ($next)
                    <a class="btn btn-primary" href="{{ route('problems.show', $next->slug) }}">Soal Berikutnya →</a>
                @else
                    <a class="btn btn-primary" href="{{ route('problems.index') }}">Kembali ke Bank Soal</a>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.PROBLEM = {{ Js::from($config) }};
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.52.2/min/vs/loader.min.js"></script>
    <script src="{{ asset('js/lesson.js') }}?v={{ filemtime(public_path('js/lesson.js')) }}"></script>
    @if ($problem->sample_visual)
        <script src="{{ asset('js/viz/core.js') }}?v={{ filemtime(public_path('js/viz/core.js')) }}"></script>
        <script src="{{ asset('js/sample-visual.js') }}?v={{ filemtime(public_path('js/sample-visual.js')) }}"></script>
    @endif
    <script src="{{ asset('js/workspace.js') }}?v={{ filemtime(public_path('js/workspace.js')) }}"></script>
@endpush
