/* Visualisasi track Sorting & Searching: sorting dasar, merge sort, quick sort, radix, ternary, interaktif, meet in the middle */
(() => {
    "use strict";

    const V = window.Viz;
    const K = window.Kit;

    // ════════════════════════════ Bubble, selection, insertion ════════════════════════════
    const SB_CODE = {
        bubble: `
for (int i = 0; i < n - 1; i++)          // putaran ke-i
    for (int j = 0; j + 1 < n - i; j++) {
        if (a[j] > a[j + 1])                     //@0
            swap(a[j], a[j + 1]);                //@1
    }
// setelah putaran i: elemen terbesar ke-(i+1) di tempatnya //@2`,
        selection: `
for (int i = 0; i < n - 1; i++) {
    int mn = i;
    for (int j = i + 1; j < n; j++)
        if (a[j] < a[mn]) mn = j;                //@0
    swap(a[i], a[mn]);                           //@1
}
// setelah putaran i: a[0..i] terurut final     //@2`,
        insertion: `
for (int i = 1; i < n; i++) {
    int x = a[i], j = i - 1;                     //@0
    while (j >= 0 && a[j] > x) {
        a[j + 1] = a[j];   // geser ke kanan     //@1
        j--;
    }
    a[j + 1] = x;                                //@2
}`,
    };
    V.register("sort-basic", (root) => {
        K.widget(root, {
            title: "Tiga Sorting O(n²)",
            stageClass: "kx-stage",
            practice: true,
            controls: `${V.segmented("algo", [["bubble", "bubble"], ["selection", "selection"], ["insertion", "insertion"]], "insertion")}
                <label class="viz-input">array <input data-arr value="5 2 8 1 9 3 7" style="width:130px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Dibandingkan", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Ditukar / digeser", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sudah di tempat final", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: (ui) => SB_CODE[K.segVal(ui, "algo")],
            watch: "Hitungan",
            build(ui) {
                const algo = K.segVal(ui, "algo");
                const a = K.nums(K.val(ui, "arr"), { min: 1, max: 30, limit: 9, def: [5, 2, 8, 1, 9, 3, 7] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const n = a.length;
                const frames = [];
                let cmp = 0;
                let mv = 0;
                const done = {};
                const view = (cls = {}) => K.bars(a, { cls: Object.assign({}, done, cls), h: 170 });
                const W = (extra = []) => [["perbandingan", cmp], [algo === "insertion" ? "geseran" : "tukar", mv], ...extra];
                const push = (line, text, cls, extra = {}) => frames.push(Object.assign({ line, text, html: view(cls), watch: W(extra.w) }, extra));
                push(-1, { bubble: "Bubble sort: tukar pasangan bersebelahan yang terbalik; yang terbesar 'menggelembung' ke kanan.", selection: "Selection sort: pilih yang terkecil dari sisa, taruh di depan.", insertion: "Insertion sort: sisipkan setiap elemen ke posisi yang benar di bagian kiri yang sudah urut." }[algo], {});
                if (algo === "bubble") {
                    for (let i = 0; i < n - 1; i++) {
                        for (let j = 0; j + 1 < n - i; j++) {
                            cmp++;
                            const sw = a[j] > a[j + 1];
                            push(0, `a[${j}] = ${a[j]} ${sw ? "&gt;" : "≤"} a[${j + 1}] = ${a[j + 1]}${sw ? ": terbalik." : ": sudah benar."}`, { [j]: "in", [j + 1]: "in" }, {
                                ask: j === 1 && i < 2 ? { type: "choice", prompt: `Apakah a[${j}] dan a[${j + 1}] ditukar?`, options: ["ya", "tidak"], answer: sw ? 0 : 1 } : undefined,
                            });
                            if (sw) {
                                [a[j], a[j + 1]] = [a[j + 1], a[j]];
                                mv++;
                                push(1, "Tukar.", { [j]: "on", [j + 1]: "on" });
                            }
                        }
                        done[n - 1 - i] = "ok";
                        push(2, `Putaran ${i + 1} selesai: ${a[n - 1 - i]} sudah di tempat finalnya.`, {}, { mark: "key" });
                    }
                    done[0] = "ok";
                } else if (algo === "selection") {
                    for (let i = 0; i < n - 1; i++) {
                        let mn = i;
                        for (let j = i + 1; j < n; j++) {
                            cmp++;
                            if (a[j] < a[mn]) mn = j;
                        }
                        push(0, `Terkecil di a[${i}..${n - 1}] adalah a[${mn}] = ${a[mn]} (${n - 1 - i} perbandingan).`, { [mn]: "in" }, {
                            ask: i < 3 ? { type: "value", prompt: `Berapa nilai terkecil di a[${i}..${n - 1}]?`, answer: String(a[mn]) } : undefined,
                            htmlMasked: view({}),
                        });
                        if (mn !== i) {
                            [a[i], a[mn]] = [a[mn], a[i]];
                            mv++;
                        }
                        done[i] = "ok";
                        push(1, `Tukar ke posisi ${i}. a[0..${i}] sudah final.`, { [i]: "on" }, { mark: "key" });
                    }
                    done[n - 1] = "ok";
                } else {
                    done[0] = "ok";
                    for (let i = 1; i < n; i++) {
                        const x = a[i];
                        let j = i - 1;
                        push(0, `Ambil x = a[${i}] = ${x}. Bagian a[0..${i - 1}] sudah urut (relatif).`, { [i]: "on" });
                        while (j >= 0) {
                            cmp++;
                            if (a[j] <= x) break;
                            a[j + 1] = a[j];
                            mv++;
                            push(1, `a[${j}] = ${a[j]} &gt; ${x}: geser ke kanan.`, { [j]: "in", [j + 1]: "on" });
                            j--;
                        }
                        a[j + 1] = x;
                        for (let k = 0; k <= i; k++) done[k] = "ok";
                        push(2, `Sisipkan ${x} di posisi ${j + 1}.`, { [j + 1]: "on" }, {
                            mark: "key",
                            ask: i <= 3 ? { type: "value", prompt: `Di indeks berapa ${x} disisipkan?`, answer: String(j + 1) } : undefined,
                            htmlMasked: view({}),
                        });
                    }
                }
                Object.keys(done).forEach((k) => (done[k] = "ok"));
                push(-1, `Terurut dengan ${cmp} perbandingan dan ${mv} ${algo === "insertion" ? "geseran" : "tukar"}. Ketiganya O(n²) pada kasus terburuk.${algo === "insertion" ? " Banyak geseran insertion sort = banyak inversi." : ""}`, {}, { mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Merge sort + hitung inversi ════════════════════════════
    V.register("merge-sort", (root) => {
        K.widget(root, {
            title: "Merge Sort dan Menghitung Inversi",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">array <input data-arr value="5 2 8 1 9 3 7 4" style="width:150px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Bagian kiri", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Bagian kanan", "#7c5cdb", "rgba(124,92,219,.14)"],
                ["Diambil ke tmp", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sudah tergabung", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
// urutkan a[l, r) dan hitung inversinya
void ms(int l, int r) {
    if (r - l <= 1) return;                     //@0
    int m = l + (r - l) / 2;
    ms(l, m);  ms(m, r);                        //@0
    int i = l, j = m, k = 0;
    while (i < m && j < r) {
        if (a[i] <= a[j]) tmp[k++] = a[i++];    //@1
        else {
            inv += m - i;  // a[i..m-1] > a[j]  //@2
            tmp[k++] = a[j++];                  //@2
        }
    }
    // salin sisa, lalu tmp kembali ke a      //@3
}`,
            watch: "Keadaan",
            panels: [{ key: "tmp", type: "html", title: "tmp (hasil gabungan)", hint: "" }],
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: 0, max: 99, limit: 8, def: [5, 2, 8, 1, 9, 3, 7, 4] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const n = a.length;
                const frames = [];
                let inv = 0;
                const view = (l, m, r, cls = {}) => {
                    const c = {};
                    for (let k = l; k < m; k++) c[k] = "in";
                    for (let k = m; k < r; k++) c[k] = "vi";
                    for (let k = 0; k < n; k++) if (k < l || k >= r) c[k] = "dim";
                    return K.cells(a, { cls: Object.assign(c, cls), size: "lg" });
                };
                const tv = (tmp) => (tmp.length ? K.cells(tmp, { idx: false, size: "sm", cls: Object.fromEntries(tmp.map((_, k) => [k, "ok"])) }) : '<span class="muted">–</span>');
                const ms = (l, r, depth) => {
                    if (r - l <= 1) return;
                    const m = l + ((r - l) >> 1);
                    frames.push({ line: 0, text: `Pecah [${l}, ${r}) menjadi [${l}, ${m}) dan [${m}, ${r}).`, html: view(l, m, r), tmp: tv([]), watch: [["rentang", `[${l}, ${r})`], ["inv", inv]] });
                    ms(l, m, depth + 1);
                    ms(m, r, depth + 1);
                    let i = l;
                    let j = m;
                    const tmp = [];
                    frames.push({ line: 1, text: `Gabungkan dua bagian yang <b>sudah urut</b>: [${a.slice(l, m).join(", ")}] dan [${a.slice(m, r).join(", ")}].`, html: view(l, m, r), tmp: tv([]), watch: [["rentang", `[${l}, ${r})`], ["inv", inv]], mark: "key" });
                    while (i < m && j < r) {
                        if (a[i] <= a[j]) {
                            tmp.push(a[i]);
                            frames.push({ line: 1, text: `${a[i]} ≤ ${a[j]}: ambil dari kiri.`, html: view(l, m, r, { [i]: "on" }), tmp: tv(tmp), watch: [["i", i], ["j", j], ["inv", inv]] });
                            i++;
                        } else {
                            const add = m - i;
                            tmp.push(a[j]);
                            frames.push({
                                line: 2,
                                text: `${a[i]} &gt; ${a[j]}: ambil dari kanan. Semua sisa kiri (a[${i}..${m - 1}], ${add} elemen) lebih besar dari ${a[j]}: <b>inv += ${add}</b>.`,
                                html: view(l, m, r, Object.assign(Object.fromEntries(Array.from({ length: add }, (_, t) => [i + t, "bad"])), { [j]: "on" })),
                                tmp: tv(tmp),
                                watch: [["i", i], ["j", j], ["tambah", add, true], ["inv", inv + add, true]],
                                mark: "discover",
                                ask: { type: "value", prompt: `${a[i]} &gt; ${a[j]}. Berapa inversi yang bertambah?`, answer: String(add) },
                                htmlMasked: view(l, m, r, { [i]: "on", [j]: "on" }),
                            });
                            inv += add;
                            j++;
                        }
                    }
                    while (i < m) tmp.push(a[i++]);
                    while (j < r) tmp.push(a[j++]);
                    for (let k = 0; k < tmp.length; k++) a[l + k] = tmp[k];
                    frames.push({ line: 3, text: `Salin sisa dan kembalikan ke a. [${l}, ${r}) kini urut.`, html: view(l, m, r, Object.fromEntries(tmp.map((_, k) => [l + k, "ok"]))), tmp: tv(tmp), watch: [["rentang", `[${l}, ${r})`], ["inv", inv]] });
                };
                frames.push({ line: -1, text: "Merge sort memecah array menjadi dua, mengurutkan keduanya secara rekursif, lalu menggabungkan. Sambil menggabung, kita bisa menghitung <b>inversi</b>: pasangan i &lt; j dengan a[i] &gt; a[j].", html: K.cells(a, { size: "lg" }), tmp: tv([]), watch: [["inv", 0]] });
                ms(0, n, 0);
                frames.push({ line: -1, text: `Selesai: terurut, dan banyak inversi = <b>${inv}</b>. Kedalaman rekursi log n, setiap tingkat O(n): total <b>O(n log n)</b>.`, html: K.cells(a, { size: "lg", cls: Object.fromEntries(a.map((_, k) => [k, "ok"])) }), tmp: tv([]), watch: [["inv", inv]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Quick sort / quickselect (partisi Lomuto) ════════════════════════════
    V.register("quick-sort", (root) => {
        K.widget(root, {
            title: "Partisi di Sekitar Pivot",
            stageClass: "kx-stage",
            practice: true,
            controls: `${V.segmented("mode", [["sort", "quick sort"], ["select", "quickselect (k = n/2)"]], "sort")}
                <label class="viz-input">array <input data-arr value="7 2 9 4 3 8 1 6" style="width:140px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Pivot", "#7c5cdb", "rgba(124,92,219,.16)"],
                ["≤ pivot (kiri)", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Diperiksa (j)", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sudah final", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
int partisi(int l, int r) {        // a[l..r], pivot = a[r]
    int p = a[r], i = l;           // a[l..i-1] <= p     //@0
    for (int j = l; j < r; j++)
        if (a[j] <= p) swap(a[i++], a[j]);            //@1
    swap(a[i], a[r]);  // pivot ke posisi final      //@2
    return i;
}
void qs(int l, int r) {
    if (l >= r) return;
    int m = partisi(l, r);
    qs(l, m - 1);  qs(m + 1, r);   // quickselect: hanya satu sisi //@3
}`,
            watch: "Keadaan",
            build(ui) {
                const mode = K.segVal(ui, "mode");
                const a = K.nums(K.val(ui, "arr"), { min: 0, max: 99, limit: 9, def: [7, 2, 9, 4, 3, 8, 1, 6] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const n = a.length;
                const k = Math.floor((n - 1) / 2);
                const frames = [];
                const fin = {};
                let cmp = 0;
                const view = (l, r, cls = {}) => {
                    const c = Object.assign({}, fin);
                    for (let t = 0; t < n; t++) if ((t < l || t > r) && !fin[t]) c[t] = "dim";
                    return K.bars(a, { cls: Object.assign(c, cls), h: 160 });
                };
                frames.push({ line: -1, text: mode === "sort" ? "Quick sort: pilih pivot, susun ulang agar yang ≤ pivot di kiri dan yang &gt; pivot di kanan, lalu ulangi pada kedua sisi." : `Quickselect: cari elemen terkecil ke-${k} (indeks ${k} setelah diurutkan) tanpa mengurutkan semuanya.`, html: view(0, n - 1), watch: [["perbandingan", 0]] });
                const part = (l, r) => {
                    const p = a[r];
                    let i = l;
                    frames.push({ line: 0, text: `Partisi a[${l}..${r}] dengan pivot a[${r}] = <b>${p}</b>.`, html: view(l, r, { [r]: "vi" }), watch: [["pivot", p], ["i", i], ["perbandingan", cmp]] });
                    for (let j = l; j < r; j++) {
                        cmp++;
                        const small = a[j] <= p;
                        const cls = { [r]: "vi", [j]: "on" };
                        for (let t = l; t < i; t++) cls[t] = "in";
                        if (small) {
                            [a[i], a[j]] = [a[j], a[i]];
                            cls[i] = "in";
                            i++;
                        }
                        frames.push({ line: 1, text: `a[j] ${small ? "≤" : "&gt;"} ${p}${small ? `: tukar ke bagian kiri, i = ${i}.` : ": biarkan di kanan."}`, html: view(l, r, cls), watch: [["pivot", p], ["i", i], ["j", j], ["perbandingan", cmp]] });
                    }
                    [a[i], a[r]] = [a[r], a[i]];
                    fin[i] = "ok";
                    frames.push({
                        line: 2,
                        text: `Tukar pivot ke indeks <b>${i}</b>. Semua di kiri ≤ ${p}, semua di kanan &gt; ${p}: pivot sudah di posisi finalnya.`,
                        html: view(l, r, { [i]: "ok" }),
                        htmlMasked: view(l, r, {}),
                        watch: [["posisi pivot", i, true], ["perbandingan", cmp]],
                        mark: "key",
                        ask: { type: "value", prompt: `Pivot ${p} berakhir di indeks berapa?`, answer: String(i) },
                    });
                    return i;
                };
                if (mode === "sort") {
                    const qs = (l, r) => {
                        if (l > r) return;
                        if (l === r) {
                            fin[l] = "ok";
                            return;
                        }
                        const m = part(l, r);
                        qs(l, m - 1);
                        qs(m + 1, r);
                    };
                    qs(0, n - 1);
                    frames.push({ line: 3, text: `Terurut dengan ${cmp} perbandingan. Rata-rata O(n log n), tetapi pivot yang buruk (misalnya array sudah urut) membuatnya O(n²).`, html: view(0, n - 1), watch: [["perbandingan", cmp]], mark: "done" });
                } else {
                    let l = 0;
                    let r = n - 1;
                    while (true) {
                        if (l === r) {
                            fin[l] = "ok";
                            break;
                        }
                        const m = part(l, r);
                        if (m === k) break;
                        frames.push({ line: 3, text: `Pivot di ${m}, yang dicari indeks ${k}: lanjut hanya ke sisi <b>${k < m ? "kiri" : "kanan"}</b>. Sisi lain dibuang.`, html: view(l, r, {}), watch: [["perbandingan", cmp]] });
                        if (k < m) r = m - 1;
                        else l = m + 1;
                    }
                    frames.push({ line: 3, text: `Elemen ke-${k} = <b>${a[k]}</b>. Hanya ${cmp} perbandingan: rata-rata O(n), karena setiap langkah hanya menelusuri satu sisi.`, html: view(0, n - 1, { [k]: "ok" }), watch: [["jawaban", a[k]], ["perbandingan", cmp]], mark: "done" });
                }
                return frames;
            },
        });
    });

    // ════════════════════════════ Radix sort (LSD, basis 10) ════════════════════════════
    V.register("radix-sort", (root) => {
        K.widget(root, {
            title: "Radix Sort: Digit demi Digit",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">array <input data-arr value="170 45 75 90 802 24 2 66" style="width:190px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Digit yang sedang dipakai", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Ember (bucket)", "#22d3ee", "rgba(34,211,238,.16)"],
            ],
            code: `
for (long long e = 1; maks / e > 0; e *= 10) {   // satuan, puluhan, ...
    // counting sort STABIL berdasarkan digit (x / e) % 10
    vector<int> cnt(10, 0);
    for (int x : a) cnt[(x / e) % 10]++;             //@0
    for (int d = 1; d < 10; d++) cnt[d] += cnt[d-1]; //@1
    for (int i = n - 1; i >= 0; i--)   // dari belakang = stabil
        out[--cnt[(a[i] / e) % 10]] = a[i];          //@2
    a = out;                                         //@3
}`,
            watch: "Keadaan",
            build(ui) {
                let a = K.nums(K.val(ui, "arr"), { min: 0, max: 999, limit: 9, def: [170, 45, 75, 90, 802, 24, 2, 66] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const frames = [];
                const mx = Math.max(...a);
                const label = (x, e) => {
                    const s = String(x).padStart(String(mx).length, "0");
                    const pos = s.length - 1 - Math.round(Math.log10(e));
                    return s.slice(0, pos) + "[" + s[pos] + "]" + s.slice(pos + 1);
                };
                frames.push({ line: -1, text: "Radix sort (LSD) mengurutkan berdasarkan digit <b>satuan</b> dulu, lalu puluhan, lalu ratusan. Setiap putaran harus <b>stabil</b>, agar urutan dari putaran sebelumnya tidak rusak.", html: K.cells(a, { size: "lg" }), watch: [["putaran", 0]] });
                let round = 0;
                for (let e = 1; Math.floor(mx / e) > 0; e *= 10) {
                    round++;
                    const name = ["satuan", "puluhan", "ratusan", "ribuan"][round - 1];
                    const buckets = Array.from({ length: 10 }, () => []);
                    a.forEach((x) => buckets[Math.floor(x / e) % 10].push(x));
                    const bview = buckets
                        .map((b, d) => (b.length ? `<div class="kx-line"><span class="kx-lab">digit ${d}</span><div class="kx-line-body">${K.cells(b.map((x) => label(x, e)), { idx: false, size: "sm", cls: Object.fromEntries(b.map((_, t) => [t, "in"])) })}</div></div>` : ""))
                        .join("");
                    frames.push({ line: 0, text: `Putaran ${round}: kelompokkan menurut digit <b>${name}</b> (dalam kurung). Urutan di dalam setiap ember mengikuti urutan sebelumnya.`, html: K.cells(a.map((x) => label(x, e)), { size: "lg", cls: Object.fromEntries(a.map((_, t) => [t, "on"])) }) + bview, watch: [["putaran", round], ["digit", name]], mark: "key" });
                    a = buckets.flat();
                    frames.push({
                        line: 3,
                        text: `Gabungkan ember 0 sampai 9: a = [${a.join(", ")}].`,
                        html: K.cells(a, { size: "lg" }),
                        htmlMasked: K.cells(a.map((x, t) => (t === 0 ? "?" : x)), { size: "lg", cls: { 0: "ask" } }),
                        watch: [["putaran", round], ["a[0]", a[0], true]],
                        ask: { type: "value", prompt: `Setelah putaran ${round}, berapa a[0]?`, answer: String(a[0]) },
                    });
                }
                frames.push({ line: -1, text: `Terurut setelah ${round} putaran. Waktu O(d · (n + 10)) dengan d = banyak digit, tanpa membandingkan dua elemen sama sekali.`, html: K.cells(a, { size: "lg", cls: Object.fromEntries(a.map((_, t) => [t, "ok"])) }), watch: [["putaran", round]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Ternary search pada fungsi unimodal ════════════════════════════
    V.register("ternary", (root) => {
        const F = {
            parabola: { label: "f(x) = (x − 6.3)² + 2", f: (x) => (x - 6.3) ** 2 + 2, xr: [0, 12], yr: [0, 42] },
            vlike: { label: "f(x) = |x − 3| + |x − 7| + |x − 9|", f: (x) => Math.abs(x - 3) + Math.abs(x - 7) + Math.abs(x - 9), xr: [0, 12], yr: [0, 26] },
            max: { label: "f(x) = maks jarak waktu (cembung)", f: (x) => Math.max(Math.abs(x - 2) / 1, Math.abs(x - 9) / 2, Math.abs(x - 11) / 3), xr: [0, 12], yr: [0, 11] },
        };
        K.widget(root, {
            title: "Ternary Search: Membuang Sepertiga",
            stageClass: "kx-stage",
            practice: true,
            controls: V.segmented("fn", [["parabola", "parabola"], ["vlike", "jumlah jarak"], ["max", "maks waktu"]], "parabola"),
            legend: [
                ["Rentang [lo, hi]", "#22d3ee", "rgba(34,211,238,.16)"],
                ["m1 dan m2", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Bagian yang dibuang", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
double lo = L, hi = R;
for (int it = 0; it < 100; it++) {       // jumlah tetap //@0
    double m1 = lo + (hi - lo) / 3;
    double m2 = hi - (hi - lo) / 3;              //@1
    if (f(m1) < f(m2)) hi = m2;  // minimum bukan di (m2, hi] //@2
    else lo = m1;                // minimum bukan di [lo, m1) //@3
}
// jawaban ≈ (lo + hi) / 2                      //@4`,
            watch: "Keadaan",
            build(ui) {
                const fn = F[K.segVal(ui, "fn")];
                let [lo, hi] = fn.xr;
                const frames = [];
                const fmt = (v) => (Math.round(v * 1000) / 1000).toString();
                const plot = (extra = {}) =>
                    K.plot({
                        xr: fn.xr,
                        yr: fn.yr,
                        w: 600,
                        h: 300,
                        curve: { f: fn.f },
                        polys: extra.polys || [],
                        vlines: extra.vlines || [],
                        points: extra.points || [],
                    });
                const band = (a, b, cls) => ({ pts: [[a, fn.yr[0]], [b, fn.yr[0]], [b, fn.yr[1]], [a, fn.yr[1]]], cls });
                frames.push({ line: 0, text: `Fungsi <b>unimodal</b>: turun lalu naik, dengan satu lembah. ${fn.label}. Cari x yang meminimalkan f.`, html: plot({ polys: [band(lo, hi, "in")] }), watch: [["lo", fmt(lo)], ["hi", fmt(hi)]] });
                for (let it = 0; it < 7; it++) {
                    const m1 = lo + (hi - lo) / 3;
                    const m2 = hi - (hi - lo) / 3;
                    const f1 = fn.f(m1);
                    const f2 = fn.f(m2);
                    const pts = [
                        { x: m1, y: f1, label: "m1", cls: "on" },
                        { x: m2, y: f2, label: "m2", cls: "on" },
                    ];
                    frames.push({
                        line: 1,
                        text: `m1 = ${fmt(m1)} (f = ${fmt(f1)}), m2 = ${fmt(m2)} (f = ${fmt(f2)}).`,
                        html: plot({ polys: [band(lo, hi, "in")], vlines: [{ x: m1, cls: "on" }, { x: m2, cls: "on" }], points: pts }),
                        watch: [["lo", fmt(lo)], ["hi", fmt(hi)], ["f(m1)", fmt(f1)], ["f(m2)", fmt(f2)]],
                        ask: it < 3 ? { type: "choice", prompt: `f(m1) = ${fmt(f1)}, f(m2) = ${fmt(f2)}. Bagian mana yang dibuang?`, options: ["(m2, hi]", "[lo, m1)"], answer: f1 < f2 ? 0 : 1 } : undefined,
                    });
                    if (f1 < f2) {
                        frames.push({ line: 2, text: `f(m1) &lt; f(m2): lembah pasti di kiri m2. Buang (m2, hi].`, html: plot({ polys: [band(lo, m2, "in"), band(m2, hi, "bad")], vlines: [{ x: m2, cls: "on" }], points: pts }), watch: [["lo", fmt(lo)], ["hi", fmt(m2)]], mark: "skip" });
                        hi = m2;
                    } else {
                        frames.push({ line: 3, text: `f(m1) ≥ f(m2): lembah pasti di kanan m1. Buang [lo, m1).`, html: plot({ polys: [band(lo, m1, "bad"), band(m1, hi, "in")], vlines: [{ x: m1, cls: "on" }], points: pts }), watch: [["lo", fmt(m1)], ["hi", fmt(hi)]], mark: "skip" });
                        lo = m1;
                    }
                }
                const x = (lo + hi) / 2;
                frames.push({ line: 4, text: `Setiap langkah menyisakan 2/3 rentang. Setelah 7 langkah rentangnya tinggal ${fmt(hi - lo)}; 100 langkah sudah jauh melewati presisi double. x ≈ <b>${fmt(x)}</b>, f ≈ ${fmt(fn.f(x))}.`, html: plot({ polys: [band(lo, hi, "in")], points: [{ x, y: fn.f(x), label: "min", cls: "ok" }] }), watch: [["x", fmt(x)], ["f(x)", fmt(fn.f(x))]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Soal interaktif: tebak angka ════════════════════════════
    V.register("interactive", (root) => {
        K.widget(root, {
            title: "Percakapan dengan Juri: Tebak Angka",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">N <select data-n>${[16, 100, 1000].map((x) => `<option ${x === 100 ? "selected" : ""}>${x}</option>`).join("")}</select></label>
                <label class="viz-input">rahasia <input class="short" data-x value="73"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Pertanyaan program", "#2f5bd3", "rgba(47,91,211,.12)"],
                ["Jawaban juri", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
int lo = 1, hi = n;
while (lo < hi) {
    int mid = (lo + hi + 1) / 2;     // bulatkan ke atas
    cout << "? " << mid << endl;    // endl = flush! //@0
    string jawab;  cin >> jawab;    // "<" atau ">=" //@1
    if (jawab == "<") hi = mid - 1;              //@2
    else lo = mid;  // rahasia >= mid             //@2
}
cout << "! " << lo << endl;                       //@3`,
            watch: "Anggaran",
            panels: [{ key: "log", type: "html", title: "Transkrip", hint: "program ↔ juri" }],
            build(ui) {
                const n = +K.val(ui, "n");
                let x = Math.max(1, Math.min(n, parseInt(K.val(ui, "x"), 10) || 1));
                ui.head.querySelector("[data-x]").value = x;
                const budget = Math.ceil(Math.log2(n));
                const frames = [];
                const log = [];
                let lo = 1;
                let hi = n;
                let q = 0;
                const tl = () => `<div class="kx-chips" style="flex-direction:column;align-items:stretch">${log.map(([who, t]) => K.chip(t, who === "p" ? "in" : "ok")).join("")}</div>`;
                const range = () => {
                    const W = 40;
                    const cells = [];
                    for (let k = 0; k < W; k++) {
                        const a = 1 + Math.floor((k * n) / W);
                        const b = Math.floor(((k + 1) * n) / W);
                        cells.push(b < lo || a > hi ? "dim" : a <= x && x <= b ? "ok" : "in");
                    }
                    return `<div class="kx-note">kemungkinan rahasia: <b>[${lo}, ${hi}]</b> (${hi - lo + 1} angka)</div>` + K.cells(new Array(W).fill(""), { idx: false, size: "sm", cls: Object.fromEntries(cells.map((c, k) => [k, c])) });
                };
                frames.push({ line: -1, text: `Juri menyimpan angka rahasia di [1, ${n}]. Program boleh bertanya "? m" dan juri menjawab "&lt;" jika rahasia &lt; m, atau "&gt;=" jika tidak. Anggaran: ${budget} pertanyaan.`, html: range(), log: tl(), watch: [["pertanyaan", 0], ["anggaran", budget]] });
                while (lo < hi) {
                    const mid = Math.floor((lo + hi + 1) / 2);
                    q++;
                    log.push(["p", `program: ? ${mid}`]);
                    frames.push({ line: 0, text: `Tanya titik tengah (dibulatkan ke atas) m = ${mid}, lalu <b>flush</b> agar juri benar-benar menerimanya.`, html: range(), log: tl(), watch: [["pertanyaan", q], ["anggaran", budget]] });
                    const ans = x < mid ? "<" : ">=";
                    log.push(["j", `juri: ${ans === "<" ? "&lt;" : "&gt;="}`]);
                    frames.push({ line: 1, text: `Juri menjawab "${ans === "<" ? "&lt;" : "&gt;="}".`, html: range(), log: tl(), watch: [["pertanyaan", q]], ask: { type: "choice", prompt: `Rahasia ${x}, ditanya "? ${mid}". Apa jawaban juri?`, options: ["<", ">="], answer: ans === "<" ? 0 : 1 } });
                    if (ans === "<") hi = mid - 1;
                    else lo = mid;
                    frames.push({ line: 2, text: `Persempit menjadi [${lo}, ${hi}].`, html: range(), log: tl(), watch: [["pertanyaan", q]], mark: "key" });
                }
                log.push(["p", `program: ! ${lo}`]);
                frames.push({ line: 3, text: `Tinggal satu kemungkinan: jawab "! ${lo}". Memakai ${q} dari ${budget} pertanyaan. ⌈log₂ ${n}⌉ = ${budget} selalu cukup.`, html: range(), log: tl(), watch: [["pertanyaan", q], ["anggaran", budget]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Meet in the middle ════════════════════════════
    V.register("mitm", (root) => {
        K.widget(root, {
            title: "Meet in the Middle: Subset Sum",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">a <input data-arr value="3 5 2 8 4 6" style="width:120px"></label>
                <label class="viz-input">target <input class="short" data-t value="13"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Paruh kiri", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Paruh kanan", "#7c5cdb", "rgba(124,92,219,.14)"],
                ["Pasangan cocok", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
// 2^(n/2) jumlah di setiap paruh
vector<long long> L = semuaJumlah(kiri);     //@0
vector<long long> R = semuaJumlah(kanan);    //@0
sort(R.begin(), R.end());                   //@1
long long cara = 0;
for (long long s : L)                        // butuh T - s dari kanan
    cara += upper_bound(R.begin(), R.end(), T - s)
          - lower_bound(R.begin(), R.end(), T - s); //@2`,
            watch: "Keadaan",
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: 0, max: 20, limit: 8, def: [3, 5, 2, 8, 4, 6] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const T = parseInt(K.val(ui, "t"), 10) || 13;
                const h = Math.floor(a.length / 2);
                const left = a.slice(0, h);
                const right = a.slice(h);
                const sums = (arr) => {
                    const out = [];
                    for (let m = 0; m < 1 << arr.length; m++) {
                        let s = 0;
                        for (let i = 0; i < arr.length; i++) if (m >> i & 1) s += arr[i];
                        out.push(s);
                    }
                    return out;
                };
                const L = sums(left);
                const R = sums(right).sort((p, q) => p - q);
                const frames = [];
                const head = () => K.cells(a, { cls: Object.fromEntries(a.map((_, i) => [i, i < h ? "in" : "vi"])), size: "lg" });
                frames.push({ line: -1, text: `Mencoba semua 2<sup>${a.length}</sup> = ${1 << a.length} subset terlalu mahal jika n = 40. Belah dua: ${h} elemen kiri dan ${a.length - h} kanan.`, html: head(), watch: [["target", T]] });
                frames.push({ line: 0, text: `Semua jumlah subset kiri (${L.length}) dan kanan (${R.length} buah).`, html: head() + K.cells(L, { label: "L", size: "sm" }) + K.cells(R, { label: "R urut", size: "sm" }), watch: [["|L|", L.length], ["|R|", R.length]], mark: "key" });
                let cara = 0;
                L.forEach((s, k) => {
                    const need = T - s;
                    const lb = R.findIndex((v) => v >= need);
                    const lo = lb < 0 ? R.length : lb;
                    let hi = lo;
                    while (hi < R.length && R[hi] === need) hi++;
                    const c = hi - lo;
                    cara += c;
                    if (k < 6 || c) {
                        const rc = {};
                        for (let t = lo; t < hi; t++) rc[t] = "ok";
                        frames.push({
                            line: 2,
                            text: `L[${k}] = ${s}: butuh ${T} − ${s} = ${need} dari kanan → ${c} cara.`,
                            html: head() + K.cells(L, { label: "L", size: "sm", cls: { [k]: "on" } }) + K.cells(R, { label: "R urut", size: "sm", cls: rc }),
                            htmlMasked: head() + K.cells(L, { label: "L", size: "sm", cls: { [k]: "on" } }) + K.cells(R, { label: "R urut", size: "sm" }),
                            watch: [["butuh", need], ["cara sejauh ini", cara, true]],
                            mark: c ? "discover" : "",
                            ask: c && k > 0 ? { type: "value", prompt: `Berapa banyak R yang bernilai ${need}?`, answer: String(c) } : undefined,
                        });
                    }
                });
                frames.push({ line: -1, text: `Total <b>${cara}</b> subset berjumlah ${T}. Kerjanya O(2<sup>n/2</sup> · n) alih-alih O(2<sup>n</sup>): untuk n = 40, sekitar 2 · 10<sup>7</sup> lawan 10<sup>12</sup>.`, html: head(), watch: [["jawaban", cara]], mark: "done" });
                return frames;
            },
        });
    });
})();
