{{-- Blok kode dengan tab bahasa. Variabel: $cpp (opsional), $js, $py (opsional) --}}
@php
    $codeTabs = array_filter(
        ['cpp' => ['C++', $cpp ?? null], 'javascript' => ['JavaScript', $js ?? null], 'python' => ['Python', $py ?? null]],
        fn ($tab) => $tab[1] !== null,
    );
    $firstTab = array_key_first($codeTabs);
@endphp
<div class="code-tabs" data-code-tabs>
    <div class="code-tabs-head">
        @foreach ($codeTabs as $key => [$label])
            <button @class(['code-tab', 'active' => $key === $firstTab]) data-lang="{{ $key }}">{{ $label }}</button>
        @endforeach
        <button class="btn btn-ghost btn-sm code-copy" data-copy>Salin</button>
    </div>
    @foreach ($codeTabs as $key => [, $code])
        <pre data-lang="{{ $key }}" @if ($key !== $firstTab) hidden @endif><code class="language-{{ $key }}">{{ $code }}</code></pre>
    @endforeach
</div>
