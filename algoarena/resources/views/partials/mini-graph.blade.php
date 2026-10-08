{{-- Ilustrasi graph beranimasi: gelombang BFS menyebar dari simpul 1 --}}
@php
    $nodes = [1 => [70, 70], 2 => [190, 50], 3 => [130, 160], 4 => [290, 130], 5 => [235, 245], 6 => [385, 50], 7 => [440, 150], 8 => [90, 270]];
    $edges = [[1, 2], [1, 3], [2, 4], [3, 4], [3, 8], [4, 5], [4, 6], [5, 7], [6, 7], [3, 5]];
    $level = [1 => 0, 2 => 1, 3 => 1, 4 => 2, 8 => 2, 5 => 2, 6 => 3, 7 => 3];
@endphp
<svg viewBox="0 0 470 320" aria-hidden="true">
    @foreach ($edges as [$a, $b])
        <line class="hv-edge" x1="{{ $nodes[$a][0] }}" y1="{{ $nodes[$a][1] }}" x2="{{ $nodes[$b][0] }}" y2="{{ $nodes[$b][1] }}" />
        <line class="hv-edge lit" style="animation-delay: {{ max($level[$a], $level[$b]) * 0.6 }}s" x1="{{ $nodes[$a][0] }}" y1="{{ $nodes[$a][1] }}" x2="{{ $nodes[$b][0] }}" y2="{{ $nodes[$b][1] }}" />
    @endforeach
    @foreach ($nodes as $id => [$x, $y])
        <g class="hv-node wave" style="--d: {{ $level[$id] * 0.6 }}s">
            <circle cx="{{ $x }}" cy="{{ $y }}" r="19" />
            <text x="{{ $x }}" y="{{ $y }}">{{ $id }}</text>
        </g>
    @endforeach
</svg>
