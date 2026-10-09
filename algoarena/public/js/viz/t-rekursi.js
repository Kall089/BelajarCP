/* Visualisasi track Rekursi & Brute Force: enumerasi subset (bitmask, rekursi ambil/tidak, submask). */
(() => {
    "use strict";
    const V = window.Viz;
    const K = window.Kit;

    V.register("subset-enum", (root) => {
        const CODE = {
            biner: `
for (int mask = 0; mask < (1 << n); mask++) {   //@0
    long long jumlah = 0;
    for (int i = 0; i < n; i++)
        if (mask >> i & 1)                       //@1
            jumlah += a[i];                      //@1
    if (jumlah == target) cara++;                //@2
}`,
            rekursi: `
void cari(int i, long long jumlah) {
    if (i == n) {                                //@0
        if (jumlah == target) cara++;            //@0
        return;
    }
    cari(i + 1, jumlah + a[i]);    // ambil a[i]  //@1
    cari(i + 1, jumlah);           // lewati a[i] //@2
}`,
            submask: `
// semua submask dari m, dari besar ke kecil
for (int s = m; ; s = (s - 1) & m) {             //@0
    proses(s);                                   //@1
    if (s == 0) break;                           //@2
}`,
        };
        K.widget(root, {
            title: "Enumerasi Subset",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                ${V.segmented("mode", [["biner", "bitmask"], ["rekursi", "rekursi"], ["submask", "submask"]], "biner")}
                <label class="viz-input">a <input data-arr value="3 5 2 4" style="width:96px"></label>
                <label class="viz-input">target / m <input class="short" data-t value="7"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Bit 1 / diambil", "#22c55e", "rgba(34,197,94,.14)"],
                ["Sedang diperiksa", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Memenuhi target", "#2f5bd3", "rgba(47,91,211,.12)"],
            ],
            code: (ui) => CODE[K.segVal(ui, "mode")],
            watch: "Keadaan",
            build(ui) {
                const mode = K.segVal(ui, "mode");
                const lim = mode === "rekursi" ? 4 : 5;
                const a = K.nums(K.val(ui, "arr"), { min: 0, max: 30, limit: lim, def: [3, 5, 2, 4] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const n = a.length;
                const T = parseInt(K.val(ui, "t"), 10) || 0;
                const frames = [];

                if (mode === "biner") {
                    let cara = 0;
                    const found = [];
                    for (let mask = 0; mask < 1 << n; mask++) {
                        const bits = [];
                        const cls = {};
                        let s = 0;
                        for (let i = n - 1; i >= 0; i--) bits.push(mask >> i & 1);
                        a.forEach((v, i) => {
                            if (mask >> i & 1) {
                                cls[i] = "ok";
                                s += v;
                            } else cls[i] = "dim";
                        });
                        const hit = s === T;
                        if (hit) {
                            cara++;
                            found.push(mask);
                        }
                        const bitCells = K.cells(bits, { idx: bits.map((_, k) => `bit ${n - 1 - k}`), cls: Object.fromEntries(bits.map((b, k) => [k, b ? "ok" : ""])), size: "sm" });
                        const html =
                            K.line("mask", `<b class="kx-big">${mask}</b> = ${K.bin(mask, n)}<sub>2</sub>`) +
                            K.line("bit", bitCells) +
                            K.line("a", K.cells(a, { cls })) +
                            K.line("jumlah", `<b>${s}</b> ${hit ? K.chip("= target", "ok") : ""}`) +
                            (found.length ? K.line("cocok", found.map((m) => K.chip(K.bin(m, n), "in")).join(" ")) : "");
                        const showAsk = mask > 0 && mask % 3 === 1 && mask < (1 << n) - 1;
                        frames.push({
                            line: hit ? 2 : 1,
                            text: `mask = ${mask} (${K.bin(mask, n)}): bit ke-i bernilai 1 berarti a[i] diambil. Jumlah ${s}${hit ? ` = target, cara = ${cara}.` : "."}`,
                            html,
                            htmlMasked: K.line("mask", `<b class="kx-big">${mask}</b> = ${K.bin(mask, n)}<sub>2</sub>`) + K.line("a", K.cells(a)) + K.line("jumlah", "<b>?</b>"),
                            watch: [["mask", mask], ["jumlah", s, true], ["cara", cara]],
                            mark: hit ? "discover" : "",
                            ask: showAsk ? { type: "value", prompt: `mask = ${mask} (${K.bin(mask, n)}). Berapa jumlah elemen yang diambil?`, answer: String(s) } : undefined,
                        });
                    }
                    frames.push({ line: -1, text: `Semua 2<sup>${n}</sup> = ${1 << n} subset sudah diperiksa: <b>${cara}</b> berjumlah ${T}. Total O(2<sup>n</sup> · n).`, html: K.line("cocok", found.length ? found.map((m) => K.chip(`{${a.filter((_, i) => m >> i & 1).join(", ")}}`, "in")).join(" ") : "tidak ada"), watch: [["cara", cara]], mark: "done" });
                    return frames;
                }

                if (mode === "rekursi") {
                    // pohon keputusan lengkap: setiap simpul (i, jumlah)
                    const nodes = [];
                    const order = [];
                    let id = 0;
                    const mk = (i, s, parent, edge) => {
                        const me = id++;
                        nodes.push({ id: me, label: String(s), parent, edgeLabel: edge, depth: i, s });
                        order.push(me);
                        if (i < n) {
                            mk(i + 1, s + a[i], me, `+${a[i]}`);
                            mk(i + 1, s, me, "×");
                        }
                        return me;
                    };
                    mk(0, 0, null, undefined);
                    const state = {};
                    let cara = 0;
                    const draw = (cur) =>
                        K.tree({
                            nodes: nodes.map((nd) => ({
                                id: nd.id,
                                label: state[nd.id] === undefined ? "" : nd.label,
                                parent: nd.parent,
                                edgeLabel: state[nd.id] === undefined ? undefined : nd.edgeLabel,
                                cls: nd.id === cur ? "on" : state[nd.id] || "dim",
                                edgeCls: state[nd.id] ? "" : "dim",
                            })),
                            w: 640,
                            h: 300,
                            r: 13,
                        });
                    frames.push({ line: -1, text: `Setiap tingkat memutuskan satu elemen: ambil (+a[i]) atau lewati (×). Angka di simpul = jumlah sementara. Daun ada di kedalaman ${n}: total ${1 << n} daun.`, html: draw(-1), watch: [["target", T]] });
                    for (const nid of order) {
                        const nd = nodes[nid];
                        const leaf = nd.depth === n;
                        const hit = leaf && nd.s === T;
                        state[nid] = leaf ? (hit ? "ok" : "") : "in";
                        if (hit) cara++;
                        const showAsk = !leaf && nd.depth === n - 1 && nid % 2 === 0;
                        frames.push({
                            line: leaf ? 0 : nd.edgeLabel === "×" ? 2 : nd.parent === null ? -1 : 1,
                            text: leaf
                                ? `Daun: jumlah ${nd.s}${hit ? ` = target! cara = ${cara}` : " ≠ target"}. Kembali ke pemanggil (backtrack).`
                                : `cari(i = ${nd.depth}, jumlah = ${nd.s})${nd.depth < n ? `: berikutnya putuskan a[${nd.depth}] = ${a[nd.depth]}.` : ""}`,
                            html: draw(nid),
                            watch: [["i", nd.depth], ["jumlah", nd.s], ["cara", cara]],
                            mark: hit ? "discover" : "",
                            ask: showAsk ? { type: "value", prompt: `Jika a[${nd.depth}] = ${a[nd.depth]} diambil, jumlah di daun kiri menjadi?`, answer: String(nd.s + a[nd.depth]) } : undefined,
                        });
                    }
                    frames.push({ line: -1, text: `Rekursi mengunjungi 2<sup>${n + 1}</sup> − 1 = ${(1 << (n + 1)) - 1} simpul dan menemukan <b>${cara}</b> subset. Kelebihannya dibanding bitmask: cabang bisa <em>dipangkas</em>, misalnya berhenti jika jumlah sudah melewati target (untuk bilangan positif).`, html: draw(-1), watch: [["cara", cara]], mark: "done" });
                    return frames;
                }

                // submask
                const full = (1 << n) - 1;
                const m = Math.max(0, Math.min(full, T)) || full;
                const bitsOf = (x) => K.cells(K.bin(x, n).split(""), { idx: [...Array(n)].map((_, k) => n - 1 - k), cls: Object.fromEntries(K.bin(x, n).split("").map((b, k) => [k, b === "1" ? "ok" : "dim"])), size: "sm" });
                const seen = [];
                let s = m;
                frames.push({ line: -1, text: `m = ${m} (${K.bin(m, n)}). Submask dari m adalah semua s dengan bit 1 hanya di posisi yang juga 1 di m. Ada 2<sup>popcount(m)</sup> = ${1 << K.bin(m, n).split("1").length - 1} buah.`, html: K.line("m", bitsOf(m)), watch: [["m", K.bin(m, n)]] });
                for (;;) {
                    seen.push(s);
                    const elems = a.filter((_, i) => s >> i & 1);
                    frames.push({
                        line: 1,
                        text: `s = ${K.bin(s, n)} → himpunan {${elems.join(", ")}}.`,
                        html: K.line("m", bitsOf(m)) + K.line("s", bitsOf(s)) + K.line("urutan", seen.map((x) => K.chip(K.bin(x, n), x === s ? "on" : "")).join(" ")),
                        watch: [["s", K.bin(s, n)], ["s (desimal)", s]],
                    });
                    if (s === 0) break;
                    const nx = (s - 1) & m;
                    frames.push({
                        line: 0,
                        text: `s − 1 = ${K.bin(s - 1, n)} (bit 1 terendah menjadi 0, bit di bawahnya menjadi 1), lalu AND m membuang bit di luar m → ${K.bin(nx, n)}.`,
                        html: K.line("m", bitsOf(m)) + K.line("s − 1", bitsOf(s - 1)) + K.line("& m", bitsOf(nx)),
                        htmlMasked: K.line("m", bitsOf(m)) + K.line("s", bitsOf(s)) + K.line("& m", "<b>?</b>"),
                        watch: [["s", K.bin(s, n)], ["berikutnya", K.bin(nx, n), true]],
                        mark: "key",
                        ask: { type: "value", prompt: `s = ${K.bin(s, n)}, m = ${K.bin(m, n)}. Berapa (s − 1) & m dalam desimal?`, answer: String(nx) },
                    });
                    s = nx;
                }
                frames.push({ line: 2, text: `s = 0 sudah diproses, berhenti. Mengulang ini untuk <em>setiap</em> m memberi total 3<sup>n</sup> pasangan (m, s), bukan 4<sup>n</sup>.`, html: K.line("semua", seen.map((x) => K.chip(K.bin(x, n), "in")).join(" ")), watch: [["banyak", seen.length]], mark: "done" });
                return frames;
            },
        });
    });
})();
