@extends('layouts.app')

@section('title', 'Riwayat Submisi')

@section('content')
    @include('partials.navbar')

    <main class="container">
        <div class="page-head">
            <h1>Riwayat Submisi</h1>
            <p>Semua kode yang pernah kamu kirim beserta hasil penilaiannya.</p>
        </div>

        <section class="card list-card">
            @if ($submissions->isEmpty())
                <p class="empty">Belum ada submisi. <a class="link" href="{{ route('problems.index') }}">Coba kerjakan soal pertama →</a></p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Soal</th>
                            <th class="hide-sm">Bahasa</th>
                            <th>Verdict</th>
                            <th class="col-num hide-sm">Tes</th>
                            <th class="col-num hide-sm">Waktu eksekusi</th>
                            <th class="col-num">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submissions as $s)
                            <tr class="clickable" onclick="location.href='{{ route('problems.show', $s->problem) }}'">
                                <td class="muted nowrap">{{ $s->created_at->locale('id')->translatedFormat('d M, H:i') }}</td>
                                <td><span class="row-title">{{ $s->problem->title }}</span></td>
                                <td class="muted hide-sm">{{ \App\Http\Controllers\ProblemController::LANGUAGES[$s->language] ?? $s->language }}</td>
                                <td><span class="verdict verdict-{{ $s->verdict }}">{{ $s->verdictLabel() }}</span></td>
                                <td class="col-num muted hide-sm">{{ $s->passed }}/{{ $s->total }}</td>
                                <td class="col-num muted hide-sm">{{ $s->time_ms }} ms</td>
                                <td class="col-num"><span @class(['score-chip', 'full' => $s->score === 100, 'part' => $s->score < 100])>{{ $s->score }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pager">
                    @if ($submissions->previousPageUrl())<a class="btn" href="{{ $submissions->previousPageUrl() }}">← Sebelumnya</a>@endif
                    <span class="muted">Halaman {{ $submissions->currentPage() }} dari {{ $submissions->lastPage() }}</span>
                    @if ($submissions->nextPageUrl())<a class="btn" href="{{ $submissions->nextPageUrl() }}">Berikutnya →</a>@endif
                </div>
            @endif
        </section>
    </main>
@endsection
