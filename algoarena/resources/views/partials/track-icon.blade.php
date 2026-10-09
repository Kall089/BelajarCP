{{-- Ikon track. Variabel: $key (kunci track), $size (opsional, default 24) --}}
@php($s = $size ?? 24)
<svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($key)
        @case('graph')
            <circle cx="5" cy="6" r="2.5" /><circle cx="19" cy="6" r="2.5" /><circle cx="12" cy="18" r="2.5" /><path d="M7.5 6h9M6.3 8.2l4.4 7.6M17.7 8.2l-4.4 7.6" />
            @break
        @case('dp')
            <rect x="3" y="3" width="7" height="7" rx="1.5" /><rect x="14" y="3" width="7" height="7" rx="1.5" /><rect x="3" y="14" width="7" height="7" rx="1.5" /><rect x="14" y="14" width="7" height="7" rx="1.5" /><path d="M10 6.5h4M17.5 10v4" />
            @break
        @case('fondasi')
            <path d="M4 20h16M6 20V10M12 20V6M18 20v-7" /><path d="M3 10l9-6 9 6" />
            @break
        @case('array')
            <rect x="2.5" y="8" width="5" height="8" rx="1" /><rect x="9.5" y="8" width="5" height="8" rx="1" /><rect x="16.5" y="8" width="5" height="8" rx="1" /><path d="M5 19.5h14" />
            @break
        @case('sorting')
            <path d="M4 18h3M4 13h7M4 8h11M4 3h15" /><path d="M18 9v12M15 18l3 3 3-3" />
            @break
        @case('rekursi')
            <path d="M12 3v5M12 8l-5 5M12 8l5 5M7 13l-3 4M7 13l3 4M17 13l-3 4M17 13l3 4" /><circle cx="12" cy="3" r="1" />
            @break
        @case('greedy')
            <path d="M12 2l2.6 6.2L21 9l-5 4.4L17.5 20 12 16.7 6.5 20 8 13.4 3 9l6.4-.8z" />
            @break
        @case('matematika')
            <path d="M5 5h14M5 5l7 7-7 7h14" />
            @break
        @case('struktur-data')
            <rect x="9" y="2.5" width="6" height="5" rx="1" /><rect x="3" y="16.5" width="6" height="5" rx="1" /><rect x="15" y="16.5" width="6" height="5" rx="1" /><path d="M12 7.5V12M6 16.5V12h12v4.5" />
            @break
        @case('pohon')
            <circle cx="12" cy="4" r="2" /><circle cx="6" cy="12" r="2" /><circle cx="18" cy="12" r="2" /><circle cx="3.5" cy="20" r="1.6" /><circle cx="9" cy="20" r="1.6" /><path d="M10.6 5.4 7.4 10.6M13.4 5.4l3.2 5.2M5.2 13.8 4.2 18.4M6.9 13.8l1.4 4.6" />
            @break
        @case('string')
            <path d="M4 7V5h16v2M9 19h6M12 5v14" />
            @break
        @case('geometri')
            <path d="M12 3 21 19H3z" /><circle cx="12" cy="13.5" r="2.5" />
            @break
        @case('permainan')
            <rect x="3" y="3" width="18" height="18" rx="3" /><circle cx="8" cy="8" r="1.4" fill="currentColor" /><circle cx="16" cy="16" r="1.4" fill="currentColor" /><circle cx="12" cy="12" r="1.4" fill="currentColor" />
            @break
        @case('kontes')
            <circle cx="12" cy="13" r="8" /><path d="M12 9v4l2.5 2.5M9 2h6" />
            @break
        @case('latihan')
            <path d="M9 11l3 3 8-8" /><path d="M20 12v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9" />
            @break
        @default
            <circle cx="12" cy="12" r="8" />
    @endswitch
</svg>
