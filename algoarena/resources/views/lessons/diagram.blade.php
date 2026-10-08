{{--
    Diagram graph statis (SVG) dari spesifikasi ringkas.
    Variabel:
      $w, $h      : ukuran viewBox (default 520 × 220)
      $nodes      : [[id, x, y, kelas?, label?], ...]   kelas: cur | vis | cyan | green | red
      $edges      : [[u, v, kelas?, bobot?, lengkung?], ...]   kelas: hl | ok | bad | cy
      $directed   : (opsional) true untuk panah
      $badges     : (opsional) [id => teks] label kecil di atas simpul
      $notes      : (opsional) [[x, y, teks, kelas?], ...] teks bebas
      $caption    : (opsional) keterangan di bawah gambar
      $bare       : (opsional) true = hanya <svg>, tanpa <figure>
--}}
@php
    $dgW = $w ?? 520;
    $dgH = $h ?? 220;
    $dgR = 18;
    $dgDirected = $directed ?? false;
    $dgPos = [];
    foreach ($nodes as $n) {
        $dgPos[$n[0]] = [$n[1], $n[2]];
    }
    $dgId = 'dg'.substr(md5(json_encode([$nodes, $edges])), 0, 6);
    $dgColors = ['' => '#3d4864', 'hl' => '#8b5cf6', 'ok' => '#22c55e', 'bad' => '#ef4444', 'cy' => '#22d3ee'];
@endphp
@unless ($bare ?? false)
    <figure class="diagram">
@endunless
<svg viewBox="0 0 {{ $dgW }} {{ $dgH }}" role="img" aria-label="{{ $caption ?? 'Diagram graph' }}">
    <defs>
        <radialGradient id="dg-node" cx="38%" cy="30%" r="78%">
            <stop offset="0%" style="stop-color: var(--node-a)" />
            <stop offset="100%" style="stop-color: var(--node-b)" />
        </radialGradient>
        @foreach ($dgColors as $cls => $color)
            <marker id="{{ $dgId }}-a{{ $cls }}" viewBox="0 0 10 10" refX="8.5" refY="5" markerWidth="6.5" markerHeight="6.5" orient="auto-start-reverse">
                <path d="M0 0 10 5 0 10z" style="fill: {{ $cls === '' ? 'var(--edge)' : $color }}" />
            </marker>
        @endforeach
    </defs>
    @foreach ($edges as $e)
        @php
            [$ax, $ay] = $dgPos[$e[0]];
            [$bx, $by] = $dgPos[$e[1]];
            $dx = $bx - $ax;
            $dy = $by - $ay;
            $len = max(1, sqrt($dx * $dx + $dy * $dy));
            $nx = -$dy / $len;
            $ny = $dx / $len;
            $curve = $e[4] ?? 0;
            $cx = ($ax + $bx) / 2 + $nx * $curve;
            $cy = ($ay + $by) / 2 + $ny * $curve;
            $sd = max(1, sqrt(($cx - $ax) ** 2 + ($cy - $ay) ** 2));
            $ed = max(1, sqrt(($bx - $cx) ** 2 + ($by - $cy) ** 2));
            $trim = $dgDirected ? $dgR + 4 : $dgR;
            $sx = $ax + ($cx - $ax) / $sd * $dgR;
            $sy = $ay + ($cy - $ay) / $sd * $dgR;
            $tx = $bx - ($bx - $cx) / $ed * $trim;
            $ty = $by - ($by - $cy) / $ed * $trim;
            $mx = 0.25 * $sx + 0.5 * $cx + 0.25 * $tx + ($curve ? 0 : $nx * 12);
            $my = 0.25 * $sy + 0.5 * $cy + 0.25 * $ty + ($curve ? 0 : $ny * 12);
            $cls = $e[2] ?? '';
        @endphp
        <path class="dg-edge {{ $cls }}" d="M{{ round($sx, 1) }} {{ round($sy, 1) }} Q{{ round($cx, 1) }} {{ round($cy, 1) }} {{ round($tx, 1) }} {{ round($ty, 1) }}" @if ($dgDirected) marker-end="url(#{{ $dgId }}-a{{ $cls }})" @endif />
        @if (isset($e[3]) && $e[3] !== null && $e[3] !== '')
            <text class="dg-w" x="{{ round($mx, 1) }}" y="{{ round($my, 1) }}">{{ $e[3] }}</text>
        @endif
    @endforeach
    @foreach ($nodes as $n)
        <g class="dg-node {{ $n[3] ?? '' }}" transform="translate({{ $n[1] }} {{ $n[2] }})">
            <circle r="{{ $dgR }}" />
            <text>{{ $n[4] ?? $n[0] }}</text>
        </g>
        @isset($badges[$n[0]])
            <text class="dg-small" x="{{ $n[1] }}" y="{{ $n[2] - $dgR - 8 }}" style="fill: var(--hl-text); font-family: var(--mono); font-weight: 700">{{ $badges[$n[0]] }}</text>
        @endisset
    @endforeach
    @foreach ($notes ?? [] as $note)
        <text class="dg-small {{ $note[3] ?? '' }}" x="{{ $note[0] }}" y="{{ $note[1] }}">{{ $note[2] }}</text>
    @endforeach
</svg>
@unless ($bare ?? false)
    @isset($caption)
        <figcaption>{!! $caption !!}</figcaption>
    @endisset
    </figure>
@endunless
