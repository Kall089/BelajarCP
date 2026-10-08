{{--
    Kode C++ lengkap + penjelasan baris demi baris.
    Variabel:
      $title  : judul program
      $steps  : [[judul, potongan kode, penjelasan HTML], ...]
                (atau ['title' => ..., 'code' => ..., 'text' => ...])
                Potongan kode digabung berurutan menjadi satu program utuh,
                sehingga nomor baris setiap langkah selalu cocok dengan kodenya.
      $intro  : (opsional) kalimat pembuka, HTML
      $sample : (opsional) ['input' => ..., 'output' => ...]
      $js,$py : (opsional) versi bahasa lain
--}}
@php
    $steps = array_map(fn ($s) => isset($s['code']) ? $s : ['title' => $s[0], 'code' => $s[1], 'text' => $s[2]], $steps);
    $wtLines = [];
    $wtRanges = [];
    foreach ($steps as $wtStep) {
        $wtChunk = explode("\n", rtrim($wtStep['code'], "\n"));
        // Baris kosong di awal potongan hanya pemisah: tidak ikut disorot
        $wtSkip = 0;
        while ($wtSkip < count($wtChunk) - 1 && trim($wtChunk[$wtSkip]) === '') {
            $wtSkip++;
        }
        $wtFrom = count($wtLines) + 1 + $wtSkip;
        foreach ($wtChunk as $wtLine) {
            $wtLines[] = $wtLine;
        }
        $wtRanges[] = [$wtFrom, count($wtLines)];
    }
@endphp
<div class="walkthrough" data-walkthrough data-mode="step" tabindex="-1">
    <div class="wt-head">
        <div class="wt-title">
            <span class="wt-lang">C++</span>
            <div>
                <b>{{ $title }}</b>
                <small>{{ count($steps) }} langkah · {{ count($wtLines) }} baris · klik baris kode atau gunakan tombol ← →</small>
            </div>
        </div>
        <div class="wt-tools">
            <div class="wt-mode" role="group" aria-label="Cara membaca">
                <button type="button" class="active" data-wt-mode="step">Satu per satu</button>
                <button type="button" data-wt-mode="all">Baca berurutan</button>
            </div>
            <button type="button" class="btn btn-sm btn-ghost" data-wt-copy>Salin kode</button>
            <button type="button" class="btn btn-sm btn-ghost fullcode-btn" data-fullcode>Layar penuh</button>
        </div>
    </div>
    @isset($intro)
        <p class="wt-intro">{!! $intro !!}</p>
    @endisset
    <div class="wt-body">
        <div class="wt-code" data-wt-code-wrap>
            <pre><code class="language-cpp" data-wt-code>{{ implode("\n", $wtLines) }}</code></pre>
        </div>
        <div class="wt-explain">
            <div class="wt-progress" aria-hidden="true"><i data-wt-bar></i></div>
            <ol class="wt-steps" data-wt-steps>
                @foreach ($steps as $i => $wtStep)
                    <li class="wt-step" data-from="{{ $wtRanges[$i][0] }}" data-to="{{ $wtRanges[$i][1] }}">
                        <div class="wt-step-head">
                            <span class="wt-num">{{ $i + 1 }}</span>
                            <b>{{ $wtStep['title'] }}</b>
                            <span class="wt-lines">{{ $wtRanges[$i][0] === $wtRanges[$i][1] ? 'baris '.$wtRanges[$i][0] : 'baris '.$wtRanges[$i][0].'–'.$wtRanges[$i][1] }}</span>
                        </div>
                        <div class="wt-text">{!! $wtStep['text'] !!}</div>
                    </li>
                @endforeach
            </ol>
            <div class="wt-nav">
                <button type="button" class="btn btn-sm" data-wt-prev>← Sebelumnya</button>
                <span class="wt-counter" data-wt-counter>Langkah 1 dari {{ count($steps) }}</span>
                <button type="button" class="btn btn-sm btn-primary" data-wt-next>Berikutnya →</button>
            </div>
        </div>
    </div>
    @isset($sample)
        <div class="wt-sample">
            <div><small>Contoh input</small><pre>{{ rtrim($sample['input']) }}</pre></div>
            <div><small>Output program</small><pre>{{ rtrim($sample['output']) }}</pre></div>
        </div>
    @endisset
    @if (isset($js) || isset($py))
        <details class="wt-alt">
            <summary>Lihat program yang sama dalam JavaScript / Python</summary>
            @include('lessons.code', ['cpp' => null, 'js' => $js ?? null, 'py' => $py ?? null])
        </details>
    @endif
</div>
