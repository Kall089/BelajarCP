<h2 class="problem-title" style="font-size: 20px">Pembahasan</h2>
<div class="statement">{!! $problem->editorial !!}</div>
<h3 class="statement" style="margin-top: 22px">Kode Solusi</h3>
@include('lessons.code', [
    'cpp' => $problem->solutions['cpp'] ?? null,
    'js' => $problem->solutions['javascript'] ?? null,
    'py' => $problem->solutions['python'] ?? null,
])
