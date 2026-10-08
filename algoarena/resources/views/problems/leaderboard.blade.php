@extends('layouts.app')

@section('title', 'Peringkat')

@section('content')
    @include('partials.navbar')

    <main class="container">
        <div class="page-head">
            <h1>Peringkat</h1>
            <p>Poin = jumlah nilai terbaik di setiap soal (maksimal {{ number_format($totalProblems * 100, 0, ',', '.') }} poin).</p>
        </div>

        <section class="card list-card">
            @if ($rows->isEmpty())
                <p class="empty">Belum ada yang mengirim jawaban. Jadilah yang pertama!</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th class="col-status">#</th>
                            <th>Username</th>
                            <th class="col-num hide-sm">Accepted</th>
                            <th class="col-num hide-sm">Dicoba</th>
                            <th class="col-num hide-md">Materi</th>
                            <th class="col-num">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $i => $row)
                            <tr @class(['me' => $row->id === auth()->id()])>
                                <td class="col-status"><span class="rank rank-{{ $i + 1 }}">{{ $i + 1 }}</span></td>
                                <td>
                                    <span class="lb-user">
                                        <span class="avatar">{{ strtoupper(mb_substr($row->username, 0, 1)) }}</span>
                                        {{ $row->username }}
                                        @if ($row->id === auth()->id())<span class="pill">Kamu</span>@endif
                                    </span>
                                </td>
                                <td class="col-num hide-sm">{{ $row->solved }}/{{ $totalProblems }}</td>
                                <td class="col-num muted hide-sm">{{ $row->attempted }}</td>
                                <td class="col-num muted hide-md">{{ $lessonsDone[$row->id] ?? 0 }}</td>
                                <td class="col-num"><span class="points">{{ number_format($row->points, 0, ',', '.') }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>
@endsection
