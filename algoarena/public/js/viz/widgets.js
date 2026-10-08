/* Widget interaktif untuk materi Fondasi: penjelajah kompleksitas & balapan pencarian */
(() => {
    "use strict";

    const V = window.Viz;

    // ════════════════════════════ Penjelajah kompleksitas (Big-O) ════════════════════════════
    const SUP = { "-": "⁻", 0: "⁰", 1: "¹", 2: "²", 3: "³", 4: "⁴", 5: "⁵", 6: "⁶", 7: "⁷", 8: "⁸", 9: "⁹" };
    const sup = (n) =>
        String(n)
            .split("")
            .map((c) => SUP[c] ?? c)
            .join("");
    const fmtInt = (x) => Math.round(x).toLocaleString("id-ID");

    function log10Factorial(n) {
        if (n < 2) return 0;
        if (n <= 170) {
            let s = 0;
            for (let k = 2; k <= n; k++) s += Math.log10(k);
            return s;
        }
        // Rumus Stirling
        return (n * Math.log(n) - n + 0.5 * Math.log(2 * Math.PI * n)) / Math.LN10;
    }

    const log2 = (n) => Math.log2(Math.max(2, n));
    const CX = [
        { label: "O(1)", eg: "akses array, rumus langsung", color: "#22c55e", f: () => 0 },
        { label: "O(log N)", eg: "binary search, pangkat cepat", color: "#2dd4bf", f: (n) => Math.log10(log2(n)) },
        { label: "O(N)", eg: "satu perulangan, BFS / DFS", color: "#22d3ee", f: (n) => Math.log10(n) },
        { label: "O(N log N)", eg: "sorting, Dijkstra dengan heap", color: "#818cf8", f: (n) => Math.log10(n) + Math.log10(log2(n)) },
        { label: "O(N²)", eg: "dua loop bersarang, DP 2D", color: "#a78bfa", f: (n) => 2 * Math.log10(n) },
        { label: "O(N³)", eg: "tiga loop bersarang", color: "#f472b6", f: (n) => 3 * Math.log10(n) },
        { label: "O(2ᴺ)", eg: "mencoba semua subset", color: "#fb923c", f: (n) => n * Math.log10(2) },
        { label: "O(N!)", eg: "mencoba semua permutasi", color: "#ef4444", f: (n) => log10Factorial(n) },
    ];

    function fmtOps(lg) {
        if (lg < 6) return fmtInt(Math.pow(10, lg));
        const e = Math.floor(lg);
        const m = Math.pow(10, lg - e);
        if (e > 99) return `10${sup(e)}`;
        return `${m.toFixed(1).replace(".", ",")} × 10${sup(e)}`;
    }

    function fmtTime(lg) {
        const lgSec = lg - 8; // asumsi 10^8 operasi per detik
        if (lgSec > 17.6) return "lebih lama dari umur alam semesta 🌌";
        const s = Math.pow(10, lgSec);
        if (s < 0.001) return "< 1 milidetik";
        if (s < 1) return `${Math.round(s * 1000)} milidetik`;
        if (s < 60) return `${s.toFixed(s < 10 ? 1 : 0).replace(".", ",")} detik`;
        if (s < 3600) return `${Math.round(s / 60)} menit`;
        if (s < 86400) return `${Math.round(s / 3600)} jam`;
        if (s < 3.15e7) return `${Math.round(s / 86400)} hari`;
        const years = s / 3.15e7;
        if (years < 1e6) return `${fmtInt(years)} tahun`;
        return `≈ 10${sup(Math.floor(Math.log10(years)))} tahun`;
    }

    function status(lg) {
        if (lg <= 8) return ["ok", "✅ Aman"];
        if (lg <= 8.7) return ["warn", "⚠️ Mepet"];
        return ["bad", "❌ TLE"];
    }

    V.register("bigo", (root) => {
        root.innerHTML = `
            <div class="viz-head">
                <div class="viz-title"><i class="live"></i><span>Penjelajah Kompleksitas</span></div>
                <div class="viz-controls-top">
                    <span class="viz-input">N = <b class="mono" data-nval>1.000</b></span>
                    <div class="segmented" data-presets>
                        ${[1, 2, 3, 4, 5, 6].map((e) => `<button data-e="${e}">10${sup(e)}</button>`).join("")}
                    </div>
                </div>
            </div>
            <div class="bigo-body">
                <div class="bigo-left">
                    <div class="bigo-slider">
                        <input type="range" min="0" max="7" step="0.01" value="3" data-slider aria-label="Nilai N (skala logaritma)">
                        <div class="bigo-scale">${Array.from({ length: 8 }, (_, e) => `<span>10${sup(e)}</span>`).join("")}</div>
                    </div>
                    <div class="bigo-chart" data-chart></div>
                </div>
                <div class="bigo-table" data-table></div>
            </div>
            <div class="viz-caption">
                <span class="badge">Aturan praktis</span>
                <div class="text">Komputer judge kira-kira mengerjakan <b>10${sup(8)} operasi sederhana per detik</b>. Hitung perkiraan operasi algoritmamu dari batasan N, lalu bandingkan dengan angka itu <b>sebelum</b> mulai menulis kode.</div>
            </div>`;

        const slider = root.querySelector("[data-slider]");
        const chart = root.querySelector("[data-chart]");
        const table = root.querySelector("[data-table]");
        const nval = root.querySelector("[data-nval]");
        const W = 560;
        const H = 300;
        const PAD = { l: 44, r: 14, t: 14, b: 30 };
        const xMax = 7;
        const yMax = 14;
        const X = (lgN) => PAD.l + (lgN / xMax) * (W - PAD.l - PAD.r);
        const Y = (lg) => H - PAD.b - (Math.min(lg, yMax) / yMax) * (H - PAD.t - PAD.b);

        // Gambar kurva sekali (tidak bergantung N)
        let curves = "";
        for (const c of CX) {
            const pts = [];
            for (let k = 0; k <= 280; k++) {
                const lgN = (k / 280) * xMax;
                const lg = c.f(Math.pow(10, lgN));
                pts.push(`${X(lgN).toFixed(1)},${Y(lg).toFixed(1)}`);
                if (lg > yMax) break;
            }
            curves += `<polyline class="bigo-curve" points="${pts.join(" ")}" stroke="${c.color}"/>`;
        }
        let grid = "";
        for (let e = 0; e <= xMax; e++) grid += `<line class="bigo-grid" x1="${X(e)}" y1="${PAD.t}" x2="${X(e)}" y2="${H - PAD.b}"/><text class="bigo-ax" x="${X(e)}" y="${H - 10}">10${sup(e)}</text>`;
        for (let e = 0; e <= yMax; e += 2) grid += `<line class="bigo-grid" x1="${PAD.l}" y1="${Y(e)}" x2="${W - PAD.r}" y2="${Y(e)}"/><text class="bigo-ay" x="${PAD.l - 6}" y="${Y(e) + 4}">10${sup(e)}</text>`;

        function render() {
            const lgN = +slider.value;
            const n = Math.max(1, Math.round(Math.pow(10, lgN)));
            nval.textContent = fmtInt(n);
            root.querySelectorAll("[data-presets] button").forEach((b) => b.classList.toggle("active", Math.abs(+b.dataset.e - lgN) < 0.005));
            const values = CX.map((c) => ({ ...c, lg: c.f(n) }));
            let dots = "";
            values.forEach((c) => {
                if (c.lg <= yMax) dots += `<circle class="bigo-dot" cx="${X(Math.log10(n))}" cy="${Y(c.lg)}" r="5" fill="${c.color}"/>`;
            });
            chart.innerHTML = `
                <svg viewBox="0 0 ${W} ${H}" class="bigo-svg">
                    <rect class="bigo-tle" x="${PAD.l}" y="${PAD.t}" width="${W - PAD.l - PAD.r}" height="${Y(8) - PAD.t}"/>
                    ${grid}
                    <text class="bigo-tle-label" x="${W - PAD.r - 6}" y="${PAD.t + 16}">zona TLE (&gt; 1 detik)</text>
                    <line class="bigo-limit" x1="${PAD.l}" y1="${Y(8)}" x2="${W - PAD.r}" y2="${Y(8)}"/>
                    <text class="bigo-limit-label" x="${PAD.l + 6}" y="${Y(8) - 6}">10⁸ operasi ≈ 1 detik</text>
                    ${curves}
                    <line class="bigo-now" x1="${X(Math.log10(n))}" y1="${PAD.t}" x2="${X(Math.log10(n))}" y2="${H - PAD.b}"/>
                    ${dots}
                    <text class="bigo-axis-title" x="${W / 2}" y="${H - 0}">ukuran input N</text>
                </svg>`;
            table.innerHTML = `
                <div class="bigo-row bigo-row-head"><span>Kompleksitas</span><span>Operasi untuk N = ${fmtInt(n)}</span><span>Perkiraan waktu</span></div>
                ${values
                    .map((c) => {
                        const [cls, label] = status(c.lg);
                        const bar = Math.max(2, Math.min(100, (c.lg / yMax) * 100));
                        return `<div class="bigo-row ${cls}">
                            <span><b style="color:${c.color}">${c.label}</b><small>${c.eg}</small></span>
                            <span class="mono">${fmtOps(c.lg)}<i class="bigo-bar"><i style="width:${bar}%;background:${c.color}"></i></i></span>
                            <span>${fmtTime(c.lg)}<em class="bigo-status ${cls}">${label}</em></span>
                        </div>`;
                    })
                    .join("")}`;
        }

        slider.addEventListener("input", render);
        root.querySelectorAll("[data-presets] button").forEach((b) =>
            b.addEventListener("click", () => {
                slider.value = b.dataset.e;
                render();
            }),
        );
        render();
    });

    // ════════════════════════════ Balapan: pencarian linear vs biner ════════════════════════════
    V.register("search-race", (root) => {
        const sh = V.shell(root, {
            title: "Balapan Pencarian: Linear O(N) vs Biner O(log N)",
            controls: `
                <label class="viz-input">N <select data-n><option>16</option><option selected>24</option><option>32</option></select></label>
                <label class="viz-input">Cari <select data-target></select></label>
                <button class="btn btn-sm" data-random>🎲 Array baru</button>`,
            legend: [
                ["Sedang diperiksa", "#f59e0b", "#f59e0b"],
                ["Masih mungkin (rentang biner)", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Sudah dibuang", "#3f4a64"],
                ["Ketemu!", "#22c55e", "rgba(34,197,94,.2)"],
            ],
        });
        const nSel = sh.head.querySelector("[data-n]");
        const tSel = sh.head.querySelector("[data-target]");
        const counter = V.htmlPanel(sh.side, "Jumlah langkah");
        const pseudo = V.codePanel(
            sh.side,
            `
int lo = 0, hi = n - 1;                  //@0
while (lo <= hi) {                       //@1
    int mid = (lo + hi) / 2;             //@2
    if (a[mid] == target) return mid;    //@3
    if (a[mid] < target) lo = mid + 1;   //@4
    else hi = mid - 1;                   //@5
}
return -1;  // tidak ditemukan`,
            "Kode C++: pencarian biner",
        );
        const watch = V.watchPanel(sh.side, "Variabel biner");
        let arr = [];
        let table = null;

        function makeArray(n) {
            const a = [];
            let x = V.rand(1, 4);
            for (let i = 0; i < n; i++) {
                a.push(x);
                x += V.rand(1, 5);
            }
            return a;
        }

        function refreshTargets(prefer) {
            tSel.innerHTML = arr.map((v, i) => `<option value="${i}">${v}</option>`).join("");
            tSel.value = String(prefer ?? Math.floor(arr.length * 0.8));
        }

        function build() {
            const n = arr.length;
            const ti = +tSel.value;
            const target = arr[ti];
            table = V.tableView(sh.stage, {
                rows: 2,
                cols: n,
                rowHead: ["Linear", "Biner"],
                colHead: Array.from({ length: n }, (_, i) => i),
                cell: 34,
            });
            const frames = [];
            let lin = 0;
            let linFound = false;
            let lo = 0;
            let hi = n - 1;
            let mid = -1;
            let binSteps = 0;
            let binFound = false;
            let linSteps = 0;
            const snap = (text, extra = {}) => {
                const cls = {};
                for (let i = 0; i < n; i++) {
                    if (linFound && i === ti) cls[`0,${i}`] = "path";
                    else if (i < lin) cls[`0,${i}`] = "gone";
                    if (binFound && i === mid) cls[`1,${i}`] = "path";
                    else if (i < lo || i > hi) cls[`1,${i}`] = "gone";
                    else cls[`1,${i}`] = "range";
                }
                if (extra.linCur !== undefined && !linFound) cls[`0,${extra.linCur}`] = "current";
                if (extra.binCur !== undefined && !binFound) cls[`1,${extra.binCur}`] = "current";
                frames.push({
                    text,
                    line: extra.line ?? -1,
                    vals: [arr, arr],
                    cls,
                    hlCol: ti,
                    counter: `<div class="counter-box"><div><b>${linSteps}</b><small>langkah linear</small></div><div><b style="color:var(--cyan)">${binSteps}</b><small>langkah biner</small></div></div>`,
                    watch: [
                        ["lo", lo],
                        ["hi", hi],
                        ["mid", mid < 0 ? "–" : mid],
                        ["a[mid]", mid < 0 ? "–" : arr[mid]],
                        ["target", target],
                    ],
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };

            snap(`Array terurut berisi <b>${n}</b> angka. Kita cari <b>${target}</b> (ada di indeks ${ti}) dengan dua cara sekaligus: <b>linear</b> memeriksa satu per satu dari kiri, <b>biner</b> selalu memeriksa bagian tengah.`, { line: 0 });
            let pendingDir = null;
            while (!linFound) {
                // Pencarian linear: periksa satu elemen
                linSteps++;
                const linCur = lin;
                if (arr[lin] === target) linFound = true;
                else lin++;
                // Pencarian biner: satu iterasi
                let binText = "";
                let line = -1;
                let ask;
                if (!binFound && lo <= hi) {
                    if (pendingDir) {
                        ask = pendingDir;
                        pendingDir = null;
                    }
                    binSteps++;
                    mid = Math.floor((lo + hi) / 2);
                    line = 2;
                    if (arr[mid] === target) {
                        binFound = true;
                        binText = ` Biner: mid = ${mid}, a[mid] = ${arr[mid]} → <b>ketemu</b> dalam ${binSteps} langkah! 🎯`;
                        line = 3;
                    } else if (arr[mid] < target) {
                        binText = ` Biner: mid = ${mid}, a[mid] = ${arr[mid]} &lt; ${target} → buang separuh kiri.`;
                        pendingDir = { type: "choice", options: ["⬅️ Separuh kiri", "➡️ Separuh kanan"], answer: 1, prompt: `a[mid] = ${arr[mid]} dan target = ${target}. Di separuh mana pencarian biner berlanjut?`, hint: "Array terurut: jika a[mid] lebih kecil dari target, target pasti ada di kanan." };
                        lo = mid + 1;
                        line = 4;
                    } else {
                        binText = ` Biner: mid = ${mid}, a[mid] = ${arr[mid]} &gt; ${target} → buang separuh kanan.`;
                        pendingDir = { type: "choice", options: ["⬅️ Separuh kiri", "➡️ Separuh kanan"], answer: 0, prompt: `a[mid] = ${arr[mid]} dan target = ${target}. Di separuh mana pencarian biner berlanjut?`, hint: "Array terurut: jika a[mid] lebih besar dari target, target pasti ada di kiri." };
                        hi = mid - 1;
                        line = 5;
                    }
                }
                const linText = linFound ? `Linear: a[${linCur}] = ${arr[linCur]} → <b>ketemu</b> setelah ${linSteps} langkah.` : `Linear: a[${linCur}] = ${arr[linCur]} ≠ ${target}, lanjut ke kanan.`;
                snap(linText + binText, { linCur, binCur: mid, line, mark: binFound && binText ? "take" : undefined, ask });
            }
            snap(
                `🏁 Linear butuh <b>${linSteps}</b> langkah, biner hanya <b>${binSteps}</b>. Untuk N = 1 000 000, linear bisa butuh sejuta langkah, sedangkan biner cukup ±20 (karena 2²⁰ ≈ 1 juta). Inilah beda O(N) dan O(log N)!`,
                { mark: "done" },
            );
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            table.update(f);
            counter.set(f.counter);
            pseudo.set(f.line);
            watch.set(f.watch, f.masked);
        });
        const regenerate = () => {
            arr = makeArray(+nSel.value);
            refreshTargets();
            build();
        };
        nSel.onchange = regenerate;
        tSel.onchange = build;
        sh.head.querySelector("[data-random]").onclick = regenerate;
        regenerate();
    });
})();
