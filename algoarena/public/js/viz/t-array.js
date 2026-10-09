/* Visualisasi track Teknik Array: prefix sum, difference array, two pointers, Kadane, monotonic stack/deque, kompresi, frekuensi */
(() => {
    "use strict";

    const V = window.Viz;
    const K = window.Kit;

    // ════════════════════════════ Prefix sum 1D & 2D ════════════════════════════
    V.register("prefix-2d", (root) => {
        const G = [
            [3, 1, 4, 1],
            [5, 9, 2, 6],
            [5, 3, 5, 8],
            [9, 7, 9, 3],
        ];
        K.widget(root, {
            title: "Prefix Sum: Bangun Sekali, Jawab Seketika",
            stageClass: "kx-stage",
            practice: true,
            controls: V.segmented("mode", [["1d", "1 dimensi"], ["2d", "2 dimensi"]], "1d"),
            legend: [
                ["Sel yang sedang dihitung", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sel yang dibaca", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Rentang yang ditanya / ditambah", "#22c55e", "rgba(34,197,94,.14)"],
                ["Dikurangi", "#ef4444", "rgba(239,68,68,.12)"],
            ],
            code: `
// 1D: pre[i] = a[0] + ... + a[i-1]
pre[0] = 0;                                     //@0
for (int i = 0; i < n; i++)
    pre[i + 1] = pre[i] + a[i];                 //@1
jumlah(l, r) = pre[r + 1] - pre[l];             //@2
// 2D: P[i][j] = jumlah persegi (0,0)..(i-1,j-1)
P[i][j] = a[i-1][j-1] + P[i-1][j]
        + P[i][j-1] - P[i-1][j-1];              //@3
jumlah = P[r2+1][c2+1] - P[r1][c2+1]
       - P[r2+1][c1] + P[r1][c1];               //@4`,
            watch: "Keadaan",
            build(ui) {
                const frames = [];
                if (K.segVal(ui, "mode") === "1d") {
                    const a = [1, 3, 4, 8, 6, 2];
                    const pre = [0];
                    const view = (aCls, pCls, hide = -1) =>
                        K.cells(a, { label: "a", cls: aCls }) +
                        K.cells(pre.concat(new Array(a.length + 1 - pre.length).fill("")).map((v, i) => (i === hide ? "?" : v)), {
                            label: "pre",
                            cls: Object.assign(Object.fromEntries(pre.map((_, i) => [i, ""]).concat(Array.from({ length: a.length + 1 - pre.length }, (_, k) => [pre.length + k, "empty"]))), pCls),
                        });
                    frames.push({ line: 0, text: "<code>pre[0] = 0</code>: jumlah dari array kosong. Tanpa sel ini, rentang yang dimulai dari indeks 0 tidak bisa dihitung.", html: view({}, { 0: "on" }), watch: [["i", "–"]] });
                    for (let i = 0; i < a.length; i++) {
                        pre.push(pre[i] + a[i]);
                        frames.push({
                            line: 1,
                            text: `pre[${i + 1}] = pre[${i}] + a[${i}] = ${pre[i]} + ${a[i]} = <b>${pre[i + 1]}</b>.`,
                            html: view({ [i]: "in" }, { [i]: "in", [i + 1]: "on" }),
                            htmlMasked: view({ [i]: "in" }, { [i]: "in", [i + 1]: "on" }, i + 1),
                            watch: [["i", i], ["pre[i+1]", pre[i + 1], true]],
                            ask: i === 2 || i === 4 ? { type: "value", prompt: `Berapa pre[${i + 1}]?`, answer: String(pre[i + 1]) } : undefined,
                        });
                    }
                    for (const [l, r] of [
                        [1, 3],
                        [0, 5],
                        [4, 4],
                    ]) {
                        const aCls = {};
                        for (let k = l; k <= r; k++) aCls[k] = "ok";
                        const ans = pre[r + 1] - pre[l];
                        frames.push({
                            line: 2,
                            text: `jumlah a[${l}..${r}] = pre[${r + 1}] − pre[${l}] = ${pre[r + 1]} − ${pre[l]} = <b>${ans}</b>. Satu pengurangan, berapa pun panjang rentangnya.`,
                            html: view(aCls, { [r + 1]: "ok", [l]: "bad" }),
                            watch: [["l", l], ["r", r], ["jawaban", ans, true]],
                            mark: "key",
                            ask: { type: "value", prompt: `Berapa jumlah a[${l}..${r}]?`, answer: String(ans) },
                        });
                    }
                    frames[frames.length - 1].mark = "done";
                    return frames;
                }
                // 2D
                const n = G.length;
                const P = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(null));
                for (let j = 0; j <= n; j++) P[0][j] = 0;
                for (let i = 0; i <= n; i++) P[i][0] = 0;
                const tbl = (cls = {}, aCls = {}, hide) => {
                    const left = K.table(G, { cls: aCls, head: ["", ...G[0].map((_, j) => `j=${j}`)].slice(1) });
                    const rows = P.map((row, i) => row.map((v, j) => (hide && hide[0] === i && hide[1] === j ? "?" : v === null ? "" : v)));
                    const right = K.table(rows, { cls, head: P[0].map((_, j) => `${j}`) });
                    return `<div class="kx-chips" style="gap:28px;align-items:flex-start"><div><div class="kx-note">a (grid)</div>${left}</div><div><div class="kx-note">P (prefix 2D, ukuran (n+1)²)</div>${right}</div></div>`;
                };
                frames.push({ line: 3, text: "Baris 0 dan kolom 0 dari P berisi 0. P[i][j] = jumlah persegi dari sudut kiri atas sampai (i−1, j−1).", html: tbl(), watch: [] });
                for (let i = 1; i <= n; i++)
                    for (let j = 1; j <= n; j++) {
                        P[i][j] = G[i - 1][j - 1] + P[i - 1][j] + P[i][j - 1] - P[i - 1][j - 1];
                        if (i + j > 5 && !(i === n && j === n)) continue; // tampilkan beberapa sel saja
                        frames.push({
                            line: 3,
                            text: `P[${i}][${j}] = a[${i - 1}][${j - 1}] + P[${i - 1}][${j}] + P[${i}][${j - 1}] − P[${i - 1}][${j - 1}] = ${G[i - 1][j - 1]} + ${P[i - 1][j]} + ${P[i][j - 1]} − ${P[i - 1][j - 1]} = <b>${P[i][j]}</b>. Bagian kiri-atas terhitung dua kali, jadi dikurangi sekali.`,
                            html: tbl({ [`${i},${j}`]: "on", [`${i - 1},${j}`]: "in", [`${i},${j - 1}`]: "in", [`${i - 1},${j - 1}`]: "bad" }, { [`${i - 1},${j - 1}`]: "on" }),
                            htmlMasked: tbl({ [`${i - 1},${j}`]: "in", [`${i},${j - 1}`]: "in", [`${i - 1},${j - 1}`]: "bad" }, { [`${i - 1},${j - 1}`]: "on" }, [i, j]),
                            watch: [["i", i], ["j", j], ["P[i][j]", P[i][j], true]],
                            ask: i === 2 && j === 2 ? { type: "value", prompt: "Berapa P[2][2]?", answer: String(P[2][2]) } : undefined,
                        });
                    }
                frames.push({ line: 3, text: "Sel lain diisi dengan rumus yang sama. Tabel P lengkap.", html: tbl(), watch: [] });
                const [r1, c1, r2, c2] = [1, 1, 2, 3];
                const aCls = {};
                for (let i = r1; i <= r2; i++) for (let j = c1; j <= c2; j++) aCls[`${i},${j}`] = "ok";
                const ans = P[r2 + 1][c2 + 1] - P[r1][c2 + 1] - P[r2 + 1][c1] + P[r1][c1];
                frames.push({
                    line: 4,
                    text: `Jumlah persegi (${r1},${c1})–(${r2},${c2}) = P[${r2 + 1}][${c2 + 1}] − P[${r1}][${c2 + 1}] − P[${r2 + 1}][${c1}] + P[${r1}][${c1}] = ${P[r2 + 1][c2 + 1]} − ${P[r1][c2 + 1]} − ${P[r2 + 1][c1]} + ${P[r1][c1]} = <b>${ans}</b>.`,
                    html: tbl({ [`${r2 + 1},${c2 + 1}`]: "ok", [`${r1},${c2 + 1}`]: "bad", [`${r2 + 1},${c1}`]: "bad", [`${r1},${c1}`]: "in" }, aCls),
                    watch: [["jawaban", ans, true]],
                    mark: "done",
                    ask: { type: "value", prompt: "Berapa jumlah persegi hijau?", answer: String(ans) },
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ Difference array ════════════════════════════
    V.register("diff-array", (root) => {
        const OPS = [
            [1, 4, 3],
            [3, 6, 2],
            [0, 2, 5],
            [5, 7, -1],
        ];
        K.widget(root, {
            title: "Difference Array: Update Rentang dalam Dua Sentuhan",
            stageClass: "kx-stage",
            practice: true,
            controls: `<span class="viz-input">update: ${OPS.map(([l, r, v]) => `<code>[${l},${r}] ${v > 0 ? "+" : ""}${v}</code>`).join(" ")}</span>`,
            legend: [
                ["Titik yang disentuh", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Rentang yang terpengaruh", "#22c55e", "rgba(34,197,94,.14)"],
                ["Sedang dijumlahkan", "#22d3ee", "rgba(34,211,238,.16)"],
            ],
            code: `
// tambah v ke a[l..r]: hanya dua sentuhan
d[l] += v;                                   //@0
d[r + 1] -= v;                               //@0
// setelah semua update: prefix sum dari d
long long berjalan = 0;
for (int i = 0; i < n; i++) {
    berjalan += d[i];                        //@1
    a[i] += berjalan;                        //@1
}`,
            watch: "Keadaan",
            build() {
                const n = 8;
                const d = new Array(n + 1).fill(0);
                const frames = [];
                const naive = new Array(n).fill(0);
                const view = (dCls = {}, aRow, aCls = {}, hide = -1) =>
                    K.cells(d.map((v, i) => (i === hide ? "?" : v)), { label: "d", cls: dCls, idx: true }) + (aRow ? K.cells(aRow, { label: "a", cls: aCls }) : "");
                frames.push({ line: -1, text: "Array a berukuran 8, semuanya 0. Kita akan menambah nilai pada empat rentang. Cara polos menyentuh setiap sel di rentang; difference array hanya menyentuh <b>dua</b> sel per update.", html: view({}, naive.slice()), watch: [["sentuhan", 0]] });
                let touches = 0;
                OPS.forEach(([l, r, v], k) => {
                    d[l] += v;
                    d[r + 1] -= v;
                    touches += 2;
                    for (let i = l; i <= r; i++) naive[i] += v;
                    const aCls = {};
                    for (let i = l; i <= r; i++) aCls[i] = "ok";
                    frames.push({
                        line: 0,
                        text: `Tambah ${v} ke a[${l}..${r}]: <code>d[${l}] += ${v}</code> dan <code>d[${r + 1}] −= ${v}</code>. "Mulai menambah ${v} di ${l}, berhenti setelah ${r}."`,
                        html: view({ [l]: "on", [r + 1]: "on" }, null),
                        htmlMasked: view({ [l]: "on", [r + 1]: "on" }, null, r + 1),
                        watch: [["update ke", k + 1], ["sentuhan", touches]],
                        mark: "key",
                        ask: k === 1 ? { type: "value", prompt: `Berapa d[${r + 1}] sekarang?`, answer: String(d[r + 1]) } : undefined,
                    });
                });
                let run = 0;
                const a = new Array(n).fill("");
                for (let i = 0; i < n; i++) {
                    run += d[i];
                    a[i] = run;
                    frames.push({
                        line: 1,
                        text: `berjalan += d[${i}] → ${run}. Inilah total tambahan untuk a[${i}].`,
                        html: view({ [i]: "in" }, a.map((x, j) => (j <= i ? x : "")), Object.fromEntries(a.map((_, j) => [j, j === i ? "on" : j < i ? "" : "empty"]))),
                        watch: [["i", i], ["berjalan", run, true]],
                        ask: i === 4 ? { type: "value", prompt: "Berapa a[4] setelah semua update?", answer: String(run) } : undefined,
                        htmlMasked: i === 4 ? view({ [i]: "in" }, a.map((x, j) => (j < i ? x : j === i ? "?" : "")), {}) : undefined,
                    });
                }
                frames.push({ line: 1, text: `Selesai: hasilnya sama dengan cara polos (${naive.join(", ")}), tetapi setiap update hanya O(1) dan pembacaan akhir O(n).`, html: view({}, a, Object.fromEntries(a.map((_, j) => [j, "ok"]))), watch: [["sentuhan update", touches]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Two pointers: tiga pola ════════════════════════════
    const TP_CODE = {
        pasangan: `
// a terurut menaik; banyak pasangan i<j dengan a[i]+a[j] <= X
int l = 0, r = n - 1;                           //@0
long long cnt = 0;
while (l < r) {
    if (a[l] + a[r] <= X) {                     //@1
        cnt += r - l;   // a[l] cocok dengan a[l+1..r] //@1
        l++;                                    //@1
    } else r--;         // a[r] terlalu besar     //@2
}`,
        terpendek: `
// subarray terpendek dengan jumlah >= S (a[i] >= 0)
int l = 0, best = INF;  long long sum = 0;      //@0
for (int r = 0; r < n; r++) {
    sum += a[r];                    // perlebar  //@1
    while (sum >= S) {                          //@2
        best = min(best, r - l + 1);            //@2
        sum -= a[l++];              // persempit //@3
    }
}`,
        berbeda: `
// subarray terpanjang tanpa nilai kembar
int l = 0, best = 0;                            //@0
for (int r = 0; r < n; r++) {
    cnt[a[r]]++;                                //@1
    while (cnt[a[r]] > 1)        // ada kembar  //@2
        cnt[a[l++]]--;                          //@2
    best = max(best, r - l + 1);                //@3
}`,
    };
    V.register("two-pointer-pair", (root) => {
        K.widget(root, {
            title: "Two Pointers: Tiga Pola",
            stageClass: "kx-stage",
            practice: true,
            controls: V.segmented(
                "mode",
                [
                    ["pasangan", "berlawanan arah"],
                    ["terpendek", "jendela terpendek"],
                    ["berbeda", "jendela tanpa kembar"],
                ],
                "terpendek",
            ),
            legend: [
                ["Jendela / pasangan", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Elemen baru", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Dibuang", "#ef4444", "rgba(239,68,68,.14)"],
                ["Terbaik", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: (ui) => TP_CODE[K.segVal(ui, "mode")],
            watch: "Keadaan",
            build(ui) {
                const mode = K.segVal(ui, "mode");
                const frames = [];
                const push = (line, text, html, watch, extra = {}) => frames.push(Object.assign({ line, text, html, watch }, extra));
                if (mode === "pasangan") {
                    const a = [1, 2, 4, 5, 7, 8, 11];
                    const X = 10;
                    let l = 0;
                    let r = a.length - 1;
                    let cnt = 0;
                    const view = (cls) => K.cells(a, { cls, ptr: { [l]: "l", [r]: "r" }, size: "lg" });
                    push(0, `Array terurut. Hitung pasangan dengan jumlah ≤ <b>${X}</b>. l mulai dari kiri, r dari kanan; keduanya hanya bergerak <b>ke tengah</b>.`, view({ [l]: "in", [r]: "in" }), [["l", l], ["r", r], ["cnt", cnt]]);
                    while (l < r) {
                        const s = a[l] + a[r];
                        if (s <= X) {
                            const cls = {};
                            for (let k = l + 1; k <= r; k++) cls[k] = "ok";
                            cls[l] = "on";
                            push(1, `a[${l}] + a[${r}] = ${s} ≤ ${X}. Karena a terurut, a[${l}] juga cocok dengan <b>semua</b> a[${l + 1}..${r}]: tambah ${r - l} pasangan sekaligus, lalu l++.`, view(cls), [["l", l], ["r", r], ["a[l]+a[r]", s], ["cnt", cnt + r - l, true]], {
                                mark: "key",
                                ask: { type: "value", prompt: `a[l] + a[r] = ${s} ≤ ${X}. Berapa cnt setelah langkah ini?`, answer: String(cnt + r - l) },
                            });
                            cnt += r - l;
                            l++;
                        } else {
                            push(2, `a[${l}] + a[${r}] = ${s} &gt; ${X}. a[${r}] terlalu besar bahkan dengan elemen terkecil yang tersisa: buang, r−−.`, view({ [l]: "in", [r]: "bad" }), [["l", l], ["r", r], ["a[l]+a[r]", s], ["cnt", cnt]], { mark: "skip" });
                            r--;
                        }
                    }
                    push(-1, `l bertemu r. Total <b>${cnt}</b> pasangan, dengan hanya ${a.length - 1} langkah, bukan ${(a.length * (a.length - 1)) / 2} pemeriksaan.`, view({}), [["cnt", cnt]], { mark: "done" });
                    return frames;
                }
                if (mode === "terpendek") {
                    const a = [1, 2, 3, 4, 5, 6, 7, 8];
                    const S = 11;
                    let l = 0;
                    let sum = 0;
                    let best = Infinity;
                    let bestR = null;
                    const view = (r, cls = {}) => {
                        const c = {};
                        if (bestR) for (let k = bestR[0]; k <= bestR[1]; k++) c[k] = "ok";
                        for (let k = l; k <= r; k++) c[k] = "in";
                        return K.cells(a, { cls: Object.assign(c, cls), ptr: r >= 0 ? (l === r ? { [l]: ["l", "r"] } : { [l]: "l", [r]: "r" }) : {}, size: "lg" });
                    };
                    const W = (r) => [["l", l], ["r", r], ["sum", sum], ["best", best === Infinity ? "∞" : best]];
                    push(0, `Cari subarray <b>terpendek</b> dengan jumlah ≥ ${S}. Semua elemen tak negatif, jadi memperlebar jendela tidak pernah mengurangi jumlah.`, view(-1), W(-1));
                    for (let r = 0; r < a.length; r++) {
                        sum += a[r];
                        push(1, `r = ${r}: tambah a[${r}] = ${a[r]}, sum = ${sum}${sum < S ? " (belum cukup)" : ""}.`, view(r, { [r]: "on" }), W(r));
                        while (sum >= S) {
                            const len = r - l + 1;
                            if (len < best) {
                                best = len;
                                bestR = [l, r];
                            }
                            push(2, `sum = ${sum} ≥ ${S}: jendela [${l}, ${r}] valid, panjang ${len}. Catat, lalu coba persempit dari kiri.`, view(r), W(r), { mark: "key" });
                            sum -= a[l];
                            push(3, `Buang a[${l}] = ${a[l]} → sum = ${sum}.`, view(r, { [l]: "bad" }), W(r), { mark: "skip" });
                            l++;
                        }
                    }
                    push(-1, `Selesai: panjang terpendek <b>${best}</b>. l bergerak ${l} kali dan r ${a.length} kali: total O(n).`, view(-1), [["best", best]], { mark: "done" });
                    return frames;
                }
                // berbeda
                const a = [2, 7, 1, 7, 3, 1, 4, 2, 5];
                const cnt = {};
                let l = 0;
                let best = 0;
                let bestR = null;
                const view = (r, cls = {}) => {
                    const c = {};
                    if (bestR) for (let k = bestR[0]; k <= bestR[1]; k++) c[k] = "ok";
                    for (let k = l; k <= r; k++) c[k] = "in";
                    return K.cells(a, { cls: Object.assign(c, cls), ptr: l === r ? { [l]: ["l", "r"] } : { [l]: "l", [r]: "r" }, size: "lg" });
                };
                push(0, "Cari subarray terpanjang yang semua nilainya <b>berbeda</b>. Simpan cnt[x] = banyak x di dalam jendela.", K.cells(a, { size: "lg" }), [["best", 0]]);
                for (let r = 0; r < a.length; r++) {
                    cnt[a[r]] = (cnt[a[r]] || 0) + 1;
                    push(1, `r = ${r}: masukkan ${a[r]}, cnt[${a[r]}] = ${cnt[a[r]]}.`, view(r, { [r]: "on" }), [["l", l], ["r", r], [`cnt[${a[r]}]`, cnt[a[r]]], ["best", best]]);
                    while (cnt[a[r]] > 1) {
                        push(2, `${a[r]} muncul dua kali: buang dari kiri a[${l}] = ${a[l]}.`, view(r, { [l]: "bad", [r]: "on" }), [["l", l], ["r", r], [`cnt[${a[r]}]`, cnt[a[r]]], ["best", best]], { mark: "skip" });
                        cnt[a[l]]--;
                        l++;
                    }
                    const len = r - l + 1;
                    const rec = len > best;
                    if (rec) {
                        best = len;
                        bestR = [l, r];
                    }
                    push(3, `Jendela [${l}, ${r}] tanpa kembar, panjang ${len}${rec ? ": <b>rekor baru</b>" : ""}.`, view(r), [["l", l], ["r", r], ["best", best, true]], {
                        mark: rec ? "key" : "",
                        ask: r === 5 ? { type: "value", prompt: "Berapa panjang jendela valid sekarang?", answer: String(len) } : undefined,
                    });
                }
                push(-1, `Jawaban: <b>${best}</b>.`, view(a.length - 1), [["best", best]], { mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Kadane ════════════════════════════
    V.register("kadane", (root) => {
        K.widget(root, {
            title: "Kadane: Sambung atau Mulai Baru?",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">array <input data-arr value="-2 1 -3 4 -1 2 1 -5 4" style="width:170px"></label>
                ${V.segmented("init", [["a0", "cur = a[0] (benar)"], ["nol", "cur = 0 (salah)"]], "a0")}
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sambung (cur + a[i])", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Mulai baru (a[i])", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Subarray terbaik", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
long long cur = a[0], best = a[0];          //@0
int mulai = 0, bl = 0, br = 0;
for (int i = 1; i < n; i++) {
    if (cur + a[i] >= a[i]) cur += a[i];   // sambung //@1
    else { cur = a[i]; mulai = i; }        // mulai baru //@2
    if (cur > best) {                         //@3
        best = cur; bl = mulai; br = i;       //@3
    }
}`,
            watch: "Keadaan",
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: -99, max: 99, limit: 11, def: [-2, 1, -3, 4, -1, 2, 1, -5, 4] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const wrong = K.segVal(ui, "init") === "nol";
                const frames = [];
                const curRow = new Array(a.length).fill("");
                let cur, best, start = 0, bl = 0, br = 0;
                if (wrong) {
                    cur = 0;
                    best = 0;
                } else {
                    cur = a[0];
                    best = a[0];
                    curRow[0] = cur;
                }
                const view = (i, cls = {}, hideCur = false) => {
                    const ac = {};
                    for (let k = bl; k <= br && best !== 0 | !wrong; k++) ac[k] = "ok";
                    if (i >= 0) for (let k = start; k <= i; k++) ac[k] = ac[k] || "in";
                    return (
                        K.cells(a, { label: "a", cls: Object.assign(ac, cls), ptr: i >= 0 ? { [i]: "i" } : {} }) +
                        K.cells(curRow.map((v, k) => (hideCur && k === i ? "?" : v)), { label: "cur", cls: Object.fromEntries(curRow.map((v, k) => [k, v === "" ? "empty" : k === i ? "on" : ""])) })
                    );
                };
                const W = () => [["cur", cur, true], ["best", best], ["terbaik", wrong && best === 0 ? "kosong?" : `a[${bl}..${br}]`]];
                frames.push({
                    line: 0,
                    text: wrong
                        ? "Versi yang sering beredar: <b>cur = 0, best = 0</b>. Perhatikan apa yang terjadi jika semua elemen negatif."
                        : `cur = best = a[0] = ${a[0]}. <b>cur</b> = jumlah terbesar subarray yang <em>berakhir di sini</em>; <b>best</b> = rekor sejauh ini.`,
                    html: view(wrong ? -1 : 0),
                    watch: W(),
                });
                for (let i = wrong ? 0 : 1; i < a.length; i++) {
                    let line;
                    let txt;
                    let cls;
                    if (wrong) {
                        cur += a[i];
                        if (cur < 0) {
                            curRow[i] = 0;
                            txt = `cur + a[${i}] = ${cur} &lt; 0 → reset cur = 0 (subarray <b>kosong</b>).`;
                            cur = 0;
                            start = i + 1;
                            line = 2;
                            cls = { [i]: "bad" };
                        } else {
                            curRow[i] = cur;
                            txt = `cur = ${cur}.`;
                            line = 1;
                            cls = {};
                        }
                    } else {
                        const sambung = cur + a[i];
                        if (sambung >= a[i]) {
                            cur = sambung;
                            line = 1;
                            txt = `Sambung: ${cur - a[i]} + ${a[i]} = <b>${cur}</b> ≥ ${a[i]} (mulai baru).`;
                            cls = { [i]: "in" };
                        } else {
                            cur = a[i];
                            start = i;
                            line = 2;
                            txt = `Masa lalu merugikan (${sambung - a[i]} &lt; 0): mulai baru dari a[${i}], cur = <b>${cur}</b>.`;
                            cls = { [i]: "on" };
                        }
                        curRow[i] = cur;
                    }
                    const rec = cur > best;
                    if (rec) {
                        best = cur;
                        bl = start;
                        br = i;
                    }
                    frames.push({
                        line: rec ? 3 : line,
                        text: txt + (rec ? ` Rekor baru: best = <b>${best}</b>.` : ""),
                        html: view(i, cls),
                        htmlMasked: view(i, cls, true),
                        watch: W(),
                        mark: rec ? "key" : line === 2 ? "skip" : "",
                        ask: !wrong && (i === 3 || i === 5 || i === 7) ? { type: "value", prompt: `Berapa cur di indeks ${i}?`, answer: String(cur) } : undefined,
                    });
                }
                frames.push({
                    line: -1,
                    text:
                        wrong && Math.max(...a) < 0
                            ? `Hasil versi salah: <b>0</b>, padahal subarray tidak boleh kosong dan jawaban sebenarnya ${Math.max(...a)}. Jangan mulai dari 0 kecuali subarray kosong diizinkan!`
                            : `Jawaban: <b>${best}</b>${wrong ? "" : `, yaitu a[${bl}..${br}]`}. Satu kali jalan, O(n).`,
                    html: view(-1),
                    watch: W(),
                    mark: "done",
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ Monotonic stack: next greater element ════════════════════════════
    V.register("mono-stack", (root) => {
        K.widget(root, {
            title: "Monotonic Stack: Siapa yang Lebih Tinggi Berikutnya?",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">tinggi <input data-arr value="2 1 5 6 2 3 1" style="width:150px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Elemen baru (i)", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Menunggu di stack", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Baru terjawab (di-pop)", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
vector<int> ans(n, -1);   // -1 = tidak ada       //@0
stack<int> st;            // berisi INDEKS
for (int i = 0; i < n; i++) {
    while (!st.empty() && a[st.top()] < a[i]) { //@1
        ans[st.top()] = i;  // i menjawab top   //@1
        st.pop();                               //@1
    }
    st.push(i);           // i menunggu jawaban //@2
}
// sisa di stack: tidak punya jawaban         //@3`,
            watch: "Keadaan",
            panels: [{ key: "st", type: "html", title: "Isi stack (indeks)", hint: "dasar → puncak" }],
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: 0, max: 20, limit: 10, def: [2, 1, 5, 6, 2, 3, 1] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const n = a.length;
                const ans = new Array(n).fill("");
                const st = [];
                const frames = [];
                const view = (cls, hide) =>
                    K.bars(a, { cls, labels: a.map((_, i) => i), h: 170 }) +
                    K.cells(ans.map((v, i) => (hide && hide.includes(i) ? "?" : v)), { label: "ans", cls: Object.fromEntries(ans.map((v, i) => [i, v === "" ? "empty" : hide && hide.includes(i) ? "ask" : ""])) });
                const stk = (cls = {}) => K.stack(st.map((i) => ({ label: i, sub: `a=${a[i]}`, cls: cls[i] || "" })), { dir: "h", front: "dasar", back: "puncak", empty: "kosong" });
                const base = () => Object.fromEntries(st.map((i) => [i, "in"]));
                frames.push({ line: 0, text: "Untuk setiap batang, cari batang <b>pertama di kanan</b> yang lebih tinggi. Stack menyimpan indeks yang masih <b>menunggu</b> jawaban.", html: view({}), st: stk(), watch: [["i", "–"]] });
                for (let i = 0; i < n; i++) {
                    const popped = [];
                    const tmp = st.slice();
                    while (tmp.length && a[tmp[tmp.length - 1]] < a[i]) popped.push(tmp.pop());
                    if (popped.length) {
                        const cls = Object.assign(base(), { [i]: "on" });
                        popped.forEach((j) => (cls[j] = "ok"));
                        frames.push({
                            line: 1,
                            text: `a[${i}] = ${a[i]} lebih tinggi dari puncak stack. Indeks <b>${popped.join(", ")}</b> akhirnya terjawab oleh ${i}, lalu di-pop.`,
                            html: view(cls),
                            htmlMasked: view(Object.assign(base(), { [i]: "on" })),
                            st: stk(Object.fromEntries(popped.map((j) => [j, "ok"]))),
                            stMasked: stk(),
                            watch: [["i", i], ["a[i]", a[i]], ["di-pop", popped.length, true]],
                            mark: "key",
                            ask: { type: "value", prompt: `a[${i}] = ${a[i]} datang. Berapa indeks yang di-pop dari stack?`, answer: String(popped.length) },
                        });
                        popped.forEach((j) => {
                            ans[j] = i;
                            st.pop();
                        });
                    }
                    st.push(i);
                    frames.push({
                        line: 2,
                        text: `push ${i}. Isi stack dari dasar ke puncak: nilai ${st.map((j) => a[j]).join(" ≥ ")}, selalu <b>tidak naik</b>.`,
                        html: view(Object.assign(base(), { [i]: "on" })),
                        st: stk({ [i]: "on" }),
                        watch: [["i", i], ["a[i]", a[i]], ["ukuran stack", st.length]],
                    });
                }
                st.forEach((j) => (ans[j] = -1));
                frames.push({
                    line: 3,
                    text: `Loop selesai. Indeks yang tersisa (${st.join(", ") || "tidak ada"}) tidak punya batang lebih tinggi di kanannya: jawabannya −1. Setiap indeks di-push sekali dan di-pop paling banyak sekali: <b>O(n)</b>.`,
                    html: view(Object.fromEntries(st.map((j) => [j, "bad"]))),
                    st: stk(),
                    watch: [["selesai", "ya"]],
                    mark: "done",
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ Monotonic deque: maksimum jendela geser ════════════════════════════
    V.register("mono-deque", (root) => {
        K.widget(root, {
            title: "Monotonic Deque: Maksimum Setiap Jendela",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">array <input data-arr value="1 3 -1 -3 5 3 6 7" style="width:150px"></label>
                <label class="viz-input">k <select data-k>${[2, 3, 4].map((x) => `<option ${x === 3 ? "selected" : ""}>${x}</option>`).join("")}</select></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Jendela saat ini", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Elemen baru", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Dibuang dari deque", "#ef4444", "rgba(239,68,68,.14)"],
                ["Maksimum jendela (depan deque)", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
deque<int> dq;          // indeks, nilai MENURUN dari depan //@0
for (int i = 0; i < n; i++) {
    while (!dq.empty() && a[dq.back()] <= a[i])  //@1
        dq.pop_back();     // kalah: lebih kecil & lebih tua //@1
    dq.push_back(i);                              //@2
    if (dq.front() <= i - k) dq.pop_front();      //@3
    if (i >= k - 1) cout << a[dq.front()];       //@4
}`,
            watch: "Keadaan",
            panels: [
                { key: "dq", type: "html", title: "Isi deque (indeks)", hint: "depan → belakang" },
                { key: "out", type: "ds", title: "Output", hint: "maksimum setiap jendela" },
            ],
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: -20, max: 20, limit: 11, def: [1, 3, -1, -3, 5, 3, 6, 7] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const k = Math.min(+K.val(ui, "k"), a.length);
                const dq = [];
                const out = [];
                const frames = [];
                const view = (i, cls = {}) => {
                    const c = {};
                    for (let j = Math.max(0, i - k + 1); j <= i; j++) c[j] = "in";
                    return K.cells(a, { cls: Object.assign(c, cls), ptr: i >= 0 ? { [i]: "i" } : {}, size: "lg" });
                };
                const dqv = (cls = {}, hideFront = false) => K.stack(dq.map((j, t) => ({ label: hideFront && t === 0 ? "?" : j, sub: hideFront && t === 0 ? "" : `a=${a[j]}`, cls: cls[j] || (t === 0 ? "ok" : "") })), { dir: "h", front: "depan", back: "belakang", empty: "kosong" });
                frames.push({ line: 0, text: `Deque menyimpan <b>indeks</b> kandidat maksimum, dengan nilai menurun dari depan ke belakang. Jendela berukuran k = ${k}.`, html: view(-1), dq: dqv(), out: [], watch: [["i", "–"]] });
                for (let i = 0; i < a.length; i++) {
                    const removed = [];
                    while (dq.length && a[dq[dq.length - 1]] <= a[i]) removed.push(dq.pop());
                    if (removed.length) {
                        dq.push(...removed.slice().reverse());
                        frames.push({ line: 1, text: `a[${i}] = ${a[i]} datang. Indeks ${removed.join(", ")} bernilai ≤ ${a[i]} dan lebih tua: <b>tidak akan pernah</b> jadi maksimum lagi. Buang dari belakang.`, html: view(i, Object.fromEntries(removed.map((j) => [j, "bad"]).concat([[i, "on"]]))), dq: dqv(Object.fromEntries(removed.map((j) => [j, "bad"]))), out: out.slice(), watch: [["i", i], ["a[i]", a[i]]], mark: "skip" });
                        removed.forEach(() => dq.pop());
                    }
                    dq.push(i);
                    frames.push({ line: 2, text: `push_back(${i}).`, html: view(i, { [i]: "on" }), dq: dqv({ [i]: "on" }), out: out.slice(), watch: [["i", i], ["a[i]", a[i]]] });
                    if (dq[0] <= i - k) {
                        const f = dq.shift();
                        frames.push({ line: 3, text: `Indeks depan ${f} sudah keluar dari jendela [${i - k + 1}, ${i}]: pop_front.`, html: view(i, { [f]: "bad" }), dq: dqv(), out: out.slice(), watch: [["i", i], ["batas kiri", i - k + 1]], mark: "skip" });
                    }
                    if (i >= k - 1) {
                        out.push(a[dq[0]]);
                        frames.push({
                            line: 4,
                            text: `Jendela [${i - k + 1}, ${i}]: maksimum = a[${dq[0]}] = <b>${a[dq[0]]}</b>, selalu di <b>depan</b> deque.`,
                            html: view(i, { [dq[0]]: "ok" }),
                            htmlMasked: view(i, {}),
                            dq: dqv(),
                            dqMasked: dqv({}, true),
                            out: out.slice(),
                            outMasked: out.slice(0, -1).concat(["?"]),
                            watch: [["i", i], ["maks", a[dq[0]], true]],
                            mark: "key",
                            ask: { type: "value", prompt: `Berapa maksimum jendela [${i - k + 1}, ${i}]?`, answer: String(a[dq[0]]) },
                        });
                    }
                }
                frames.push({ line: -1, text: `Selesai: [${out.join(", ")}]. Setiap indeks masuk dan keluar deque paling banyak sekali: <b>O(n)</b>, bukan O(n·k).`, html: view(-1), dq: dqv(), out, watch: [], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Kompresi koordinat ════════════════════════════
    V.register("compress", (root) => {
        K.widget(root, {
            title: "Kompresi Koordinat: Nilai Raksasa → Indeks Kecil",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">a <input data-arr value="1000000 5 999 -3 5 1000000 42" style="width:220px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sedang diproses", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Ditemukan lower_bound", "#22c55e", "rgba(34,197,94,.14)"],
                ["Kembar yang dibuang", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
vector<long long> b = a;                      //@0
sort(b.begin(), b.end());                     //@1
b.erase(unique(b.begin(), b.end()), b.end()); //@2
for (int i = 0; i < n; i++)
    c[i] = lower_bound(b.begin(), b.end(), a[i])
           - b.begin();       // 0 .. k-1      //@3
// nilai asli dari indeks: b[c[i]]           //@4`,
            watch: "Keadaan",
            build(ui) {
                const a = String(K.val(ui, "arr"))
                    .split(/[\s,]+/)
                    .filter(Boolean)
                    .map(Number)
                    .filter((x) => Number.isInteger(x) && Math.abs(x) <= 1e9)
                    .slice(0, 9);
                const arr = a.length ? a : [1000000, 5, 999, -3, 5, 1000000, 42];
                ui.head.querySelector("[data-arr]").value = arr.join(" ");
                const frames = [];
                let b = arr.slice();
                frames.push({ line: 0, text: `Nilai sampai ${Math.max(...arr.map(Math.abs)).toLocaleString("id-ID")}: array frekuensi sebesar itu mustahil. Tetapi hanya ada ${new Set(arr).size} nilai berbeda.`, html: K.cells(arr, { label: "a" }) + K.cells(b, { label: "b" }), watch: [["n", arr.length]] });
                b.sort((x, y) => x - y);
                frames.push({ line: 1, text: "Urutkan salinannya: nilai kembar menjadi bersebelahan.", html: K.cells(arr, { label: "a" }) + K.cells(b, { label: "b", cls: Object.fromEntries(b.map((v, i) => [i, i && b[i - 1] === v ? "bad" : ""])) }), watch: [["n", b.length]], mark: "key" });
                b = [...new Set(b)];
                frames.push({ line: 2, text: `Buang yang kembar. Sekarang b adalah <b>kamus dua arah</b>: b[i] memberi nilai asli, lower_bound pada b memberi nomor barunya. k = ${b.length}.`, html: K.cells(arr, { label: "a" }) + K.cells(b, { label: "b", cls: Object.fromEntries(b.map((_, i) => [i, "ok"])) }), watch: [["k", b.length]], mark: "key" });
                const c = new Array(arr.length).fill("");
                arr.forEach((x, i) => {
                    const j = b.indexOf(x);
                    c[i] = j;
                    frames.push({
                        line: 3,
                        text: `a[${i}] = ${x} → lower_bound menemukan b[${j}] = ${x}, jadi nomor barunya <b>${j}</b>.`,
                        html: K.cells(arr, { label: "a", cls: { [i]: "on" } }) + K.cells(b, { label: "b", cls: { [j]: "ok" } }) + K.cells(c, { label: "c", cls: Object.fromEntries(c.map((v, t) => [t, v === "" ? "empty" : t === i ? "on" : ""])) }),
                        htmlMasked: K.cells(arr, { label: "a", cls: { [i]: "on" } }) + K.cells(b, { label: "b" }) + K.cells(c.map((v, t) => (t === i ? "?" : v)), { label: "c", cls: Object.fromEntries(c.map((v, t) => [t, v === "" ? "empty" : t === i ? "ask" : ""])) }),
                        watch: [["i", i], ["c[i]", j, true]],
                        ask: i % 2 === 1 ? { type: "value", prompt: `Berapa nomor baru untuk a[${i}] = ${x}?`, answer: String(j) } : undefined,
                    });
                });
                frames.push({ line: 4, text: `Selesai. Semua nilai kini di 0..${b.length - 1}, dan urutannya sama persis: a[i] &lt; a[j] ⇔ c[i] &lt; c[j]. Nilai asli kembali lewat b[c[i]].`, html: K.cells(arr, { label: "a" }) + K.cells(c, { label: "c", cls: Object.fromEntries(c.map((_, t) => [t, "ok"])) }), watch: [["k", b.length]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Array frekuensi: mencari anagram ════════════════════════════
    V.register("freq-count", (root) => {
        K.widget(root, {
            title: "Array Frekuensi: Mencari Anagram dengan Jendela",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">teks <input data-t value="cbaebabacd" style="width:120px" maxlength="14"></label>
                <label class="viz-input">pola <input data-p value="abc" style="width:60px" maxlength="5"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Jendela", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Huruf masuk", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Huruf keluar", "#ef4444", "rgba(239,68,68,.14)"],
                ["Anagram ditemukan", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
int need[26] = {}, have[26] = {};
for (char ch : p) need[ch - 'a']++;          //@0
for (int i = 0; i < n; i++) {
    have[t[i] - 'a']++;            // masuk     //@1
    if (i >= m) have[t[i - m] - 'a']--; // keluar //@2
    if (i >= m - 1 && sama(have, need))      //@3
        jawaban.push_back(i - m + 1);         //@3
}`,
            watch: "Keadaan",
            build(ui) {
                let t = String(K.val(ui, "t")).toLowerCase().replace(/[^a-z]/g, "").slice(0, 14) || "cbaebabacd";
                let p = String(K.val(ui, "p")).toLowerCase().replace(/[^a-z]/g, "").slice(0, 5) || "abc";
                if (p.length > t.length) p = p.slice(0, t.length);
                ui.head.querySelector("[data-t]").value = t;
                ui.head.querySelector("[data-p]").value = p;
                const m = p.length;
                const letters = [...new Set((t + p).split(""))].sort();
                const need = {};
                const have = {};
                letters.forEach((c) => {
                    need[c] = 0;
                    have[c] = 0;
                });
                for (const c of p) need[c]++;
                const frames = [];
                const found = [];
                const tbl = (hi = {}) =>
                    K.table([["need", ...letters.map((c) => need[c])], ["have", ...letters.map((c) => have[c])]], {
                        head: ["", ...letters],
                        cls: Object.assign(
                            { "0,0": "head", "1,0": "head" },
                            Object.fromEntries(letters.map((c, j) => [`1,${j + 1}`, have[c] === need[c] ? hi[c] || "ok" : hi[c] || "bad"])),
                        ),
                    });
                const textRow = (i, cls = {}) => {
                    const c = {};
                    found.forEach((s) => (c[s] = c[s] || "ok"));
                    for (let j = Math.max(0, i - m + 1); j <= i; j++) c[j] = "in";
                    return K.cells(t.split(""), { cls: Object.assign(c, cls), size: "sm" });
                };
                const same = () => letters.every((c) => have[c] === need[c]);
                frames.push({ line: 0, text: `Hitung huruf pola "<b>${p}</b>" ke need[]. Dua string adalah anagram jika <b>array frekuensinya sama</b>. Sel hijau: frekuensi huruf itu sudah cocok.`, html: textRow(-1) + tbl(), watch: [["m", m]] });
                for (let i = 0; i < t.length; i++) {
                    have[t[i]]++;
                    const cls = { [i]: "on" };
                    let txt = `Masuk '${t[i]}'.`;
                    if (i >= m) {
                        have[t[i - m]]--;
                        cls[i - m] = "bad";
                        txt += ` Keluar '${t[i - m]}'.`;
                    }
                    if (i >= m - 1) {
                        const ok = same();
                        if (ok) found.push(i - m + 1);
                        frames.push({
                            line: ok ? 3 : 2,
                            text: `${txt} Jendela "${t.slice(i - m + 1, i + 1)}": ${ok ? `<b>anagram!</b> posisi ${i - m + 1}.` : "frekuensi berbeda."}`,
                            html: textRow(i, cls) + tbl({ [t[i]]: "on" }),
                            watch: [["jendela", t.slice(i - m + 1, i + 1)], ["anagram?", ok ? "ya" : "tidak", true]],
                            mark: ok ? "key" : "",
                            ask: { type: "choice", prompt: `Apakah jendela "${t.slice(i - m + 1, i + 1)}" anagram dari "${p}"?`, options: ["ya", "tidak"], answer: ok ? 0 : 1 },
                        });
                    } else {
                        frames.push({ line: 1, text: txt + " Jendela belum penuh.", html: textRow(i, cls) + tbl({ [t[i]]: "on" }), watch: [["jendela", t.slice(0, i + 1)]] });
                    }
                }
                frames.push({ line: -1, text: `Posisi anagram: <b>${found.join(", ") || "tidak ada"}</b>. Setiap geseran hanya mengubah dua sel frekuensi, jadi totalnya O(n · 26) atau O(n) dengan penghitung selisih.`, html: textRow(-1) + tbl(), watch: [["banyak", found.length]], mark: "done" });
                return frames;
            },
        });
    });
})();
