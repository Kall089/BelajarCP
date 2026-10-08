@extends('layouts.app')

@section('title', 'Bank Soal')

@php($diffClass = ['Mudah' => 'easy', 'Sedang' => 'medium', 'Sulit' => 'hard'])

@section('content')
    @include('partials.navbar')

    <main class="container">
        <div class="page-head">
            <h1>Bank Soal</h1>
            <p>{{ $problems->count() }} soal competitive programming. Kerjakan dalam JavaScript atau Python, dan kode dinilai otomatis.</p>
        </div>

        <section class="card list-card">
            <div class="list-toolbar">
                <div class="chips">
                    <button class="chip active" data-filter="all">Semua</button>
                    <button class="chip" data-filter="graph">Graph</button>
                    <button class="chip" data-filter="dp">Dynamic Programming</button>
                    <button class="chip" data-filter="todo">Belum AC</button>
                </div>
                <input type="search" class="search" placeholder="Cari judul atau topik..." data-search>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th class="col-status">Status</th>
                        <th>Soal</th>
                        <th class="hide-md">Materi</th>
                        <th class="col-num">Kesulitan</th>
                        <th class="col-num hide-sm">Diselesaikan</th>
                        <th class="col-num">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($problems as $p)
                        @php($s = $best[$p->id] ?? null)
                        <tr class="clickable" data-row data-track="{{ $p->lesson?->track }}" data-done="{{ $s === 100 ? 1 : 0 }}"
                            data-search-text="{{ strtolower($p->title.' '.implode(' ', $p->tags).' '.$p->lesson?->title) }}"
                            onclick="location.href='{{ route('problems.show', $p) }}'">
                            <td class="col-status"><span @class(['status-dot', 'done' => $s === 100, 'part' => $s !== null && $s < 100])></span></td>
                            <td>
                                <a href="{{ route('problems.show', $p) }}" class="row-title">{{ $p->title }}</a>
                                <span class="row-sub">{{ implode(' · ', $p->tags) }}</span>
                            </td>
                            <td class="hide-md">
                                @if ($p->lesson)
                                    <span @class(['pill', 'pill-graph' => $p->lesson->track === 'graph', 'pill-dp' => $p->lesson->track === 'dp'])>{{ $p->lesson->title }}</span>
                                @endif
                            </td>
                            <td class="col-num"><span class="diff {{ $diffClass[$p->difficulty] }}">{{ $p->difficulty }}</span></td>
                            <td class="col-num muted hide-sm">{{ $solvers[$p->id] ?? 0 }} orang</td>
                            <td class="col-num">
                                @if ($s !== null)
                                    <span @class(['score-chip', 'full' => $s === 100, 'part' => $s < 100])>{{ $s }}</span>
                                @else
                                    <span class="faint">–</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="empty" data-empty hidden>Tidak ada soal yang cocok.</p>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        (() => {
            const rows = [...document.querySelectorAll("[data-row]")];
            const search = document.querySelector("[data-search]");
            let filter = "all";

            const apply = () => {
                const q = search.value.trim().toLowerCase();
                let visible = 0;
                rows.forEach((row) => {
                    const okFilter =
                        filter === "all" ||
                        (filter === "todo" ? row.dataset.done === "0" : row.dataset.track === filter);
                    const show = okFilter && row.dataset.searchText.includes(q);
                    row.hidden = !show;
                    if (show) visible++;
                });
                document.querySelector("[data-empty]").hidden = visible > 0;
            };

            document.querySelectorAll("[data-filter]").forEach((chip) =>
                chip.addEventListener("click", () => {
                    document.querySelectorAll("[data-filter]").forEach((c) => c.classList.toggle("active", c === chip));
                    filter = chip.dataset.filter;
                    apply();
                }),
            );
            search.addEventListener("input", apply);
        })();
    </script>
@endpush
