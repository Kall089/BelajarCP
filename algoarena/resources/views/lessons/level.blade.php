{{-- Pembatas level di dalam materi. Variabel: $n (1-3), $title, $desc --}}
@php
    $lv = \App\Models\Lesson::LEVELS[$n];
    $lvId = ['', 'dasar', 'menengah', 'lanjut'][$n];
@endphp
<div class="level-band lvl-band-{{ $n }}" id="{{ $lvId }}" data-toc-group="{{ $lv['name'] }}" data-level="{{ $n }}">
    <span class="lb-icon">{{ $lv['icon'] }}</span>
    <div>
        <b>Level {{ $lv['name'] }}: {{ $title }}</b>
        <span>{!! $desc !!}</span>
    </div>
</div>
