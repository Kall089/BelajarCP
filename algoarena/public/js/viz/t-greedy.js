/* Visualisasi track Greedy: koin, interval, Huffman, regret, konstruktif, invarian. */
(() => {
    "use strict";
    const V = window.Viz;
    const K = window.Kit;
    const esc = V.esc;

    /** Garis waktu interval (SVG). iv: [{l, r, label, cls}], opts: lo, hi, marks [{x, label, cls}] */
    const timeline = (iv, opts = {}) => {
        const lo = opts.lo ?? Math.min(...iv.map((v) => v.l));
        const hi = opts.hi ?? Math.max(...iv.map((v) => v.r));
        const W = 600;
        const rowH = 24;
        const top = 8;
        const pad = 26;
        const H = top + iv.length * rowH + 30;
        const X = (x) => pad + ((x - lo) / (hi - lo || 1)) * (W - 2 * pad);
        let g = "";
        iv.forEach((v, i) => {
            const y = top + i * rowH;
            const w = Math.max(6, X(v.r) - X(v.l));
            g += `<g class="kx-iv ${v.cls || ""}"><rect x="${X(v.l)}" y="${y + 3}" width="${w}" height="${rowH - 6}" rx="5"/>${
                v.label !== undefined ? `<text x="${X(v.l) + w / 2}" y="${y + rowH / 2 + 4}">${esc(v.label)}</text>` : ""
            }</g>`;
        });
        const ay = top + iv.length * rowH + 8;
        let ax = `<line x1="${pad}" y1="${ay}" x2="${W - pad}" y2="${ay}"/>`;
        const step = Math.max(1, Math.ceil((hi - lo) / 16));
        for (let x = lo; x <= hi; x += step) ax += `<line x1="${X(x)}" y1="${ay - 3}" x2="${X(x)}" y2="${ay + 3}"/><text x="${X(x)}" y="${ay + 16}">${x}</text>`;
        let mk = "";
        for (const m of opts.marks || []) {
            mk += `<g class="kx-tmark ${m.cls || ""}"><line x1="${X(m.x)}" y1="${top - 4}" x2="${X(m.x)}" y2="${ay}"/>${
                m.label ? `<text x="${X(m.x) + 4}" y="${top + 6}">${esc(m.label)}</text>` : ""
            }</g>`;
        }
        return `<svg class="kx-tl" viewBox="0 0 ${W} ${H + 12}">${g}<g class="kx-axis">${ax}</g>${mk}</svg>`;
    };

    const parseIv = (s, def, limit = 12) => {
        const out = [];
        for (const tok of String(s).split(/[\s,;]+/)) {
            const m = tok.match(/^(-?\d+)\s*-\s*(-?\d+)$/);
            if (m) {
                let l = +m[1];
                let r = +m[2];
                if (l > r) [l, r] = [r, l];
                if (l >= 0 && r <= 99) out.push([l, r]);
            }
            if (out.length >= limit) break;
        }
        return out.length ? out : def;
    };

    // ════════════════════════════ Greedy koin ════════════════════════════
    V.register("greedy-coin", (root) => {
        const SETS = { rupiah: [100, 50, 20, 10, 5, 2, 1], us: [25, 10, 5, 1], aneh: [4, 3, 1] };
        K.widget(root, {
            title: "Greedy Koin: Kapan Benar, Kapan Salah",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                ${V.segmented("set", [["rupiah", "1,2,5,…,100"], ["us", "1,5,10,25"], ["aneh", "1,3,4"]], "aneh")}
                <label class="viz-input">jumlah <input class="short" data-x value="6"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Koin yang dicoba", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Diambil greedy", "#22c55e", "rgba(34,197,94,.14)"],
                ["Jawaban optimal (DP)", "#2f5bd3", "rgba(47,91,211,.12)"],
            ],
            code: `
int sisa = X, banyak = 0;
for (int c : koin) {             // dari nilai terbesar   //@0
    while (sisa >= c) {          // ambil selama masih muat //@1
        sisa -= c;                                         //@2
        banyak++;                                          //@2
    }
}
// bandingkan dengan DP: minimum koin yang sebenarnya   //@3`,
            watch: "Keadaan",
            build(ui) {
                const coins = SETS[K.segVal(ui, "set")];
                const X = Math.max(1, Math.min(300, parseInt(K.val(ui, "x"), 10) || 6));
                ui.head.querySelector("[data-x]").value = X;
                const frames = [];
                let sisa = X;
                const taken = [];
                const view = (ci, extra = "") =>
                    K.line("koin", K.cells(coins, { cls: ci >= 0 ? { [ci]: "on" } : {}, idx: false })) +
                    K.line("sisa", `<b>${sisa}</b>`) +
                    K.line("diambil", taken.length ? taken.map((c) => K.chip(c, "ok")).join(" ") : "–") +
                    extra;
                frames.push({ line: -1, text: `Bayar ${X} dengan koin sesedikit mungkin. Strategi serakah: selalu ambil koin terbesar yang masih muat.`, html: view(-1), watch: [["sisa", sisa], ["banyak", 0]] });
                coins.forEach((c, ci) => {
                    frames.push({ line: 0, text: `Coba koin ${c}.`, html: view(ci), watch: [["sisa", sisa], ["banyak", taken.length]] });
                    while (sisa >= c) {
                        const before = sisa;
                        sisa -= c;
                        taken.push(c);
                        frames.push({
                            line: 2,
                            text: `${before} ≥ ${c}: ambil koin ${c}, sisa ${sisa}.`,
                            html: view(ci),
                            watch: [["sisa", sisa, true], ["banyak", taken.length]],
                            ask: taken.length === 2 ? { type: "value", prompt: `Sisa ${before}, ambil koin ${c}. Sisa menjadi?`, answer: String(sisa) } : undefined,
                        });
                    }
                });
                // DP pembanding
                const INF = 1e9;
                const dp = new Array(X + 1).fill(INF);
                const from = new Array(X + 1).fill(0);
                dp[0] = 0;
                for (let s = 1; s <= X; s++)
                    for (const c of coins)
                        if (c <= s && dp[s - c] + 1 < dp[s]) {
                            dp[s] = dp[s - c] + 1;
                            from[s] = c;
                        }
                const opt = [];
                for (let s = X; s > 0; s -= from[s]) opt.push(from[s]);
                const good = dp[X] === taken.length;
                frames.push({
                    line: 3,
                    text: good
                        ? `Greedy memakai ${taken.length} koin, sama dengan optimum DP. Untuk sistem koin seperti ini (disebut <em>kanonik</em>) greedy selalu benar.`
                        : `Greedy memakai ${taken.length} koin, padahal cukup <b>${dp[X]}</b>: ${opt.join(" + ")}. Ini <b>contoh penyangkal</b>: satu kasus kecil sudah membuktikan greedy salah untuk koin {${coins.join(", ")}}.`,
                    html: view(-1, K.line("optimal", opt.map((c) => K.chip(c, "in")).join(" "))),
                    watch: [["greedy", taken.length], ["optimal", dp[X]]],
                    mark: "done",
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ Greedy interval ════════════════════════════
    V.register("interval-sched", (root) => {
        const DEF = [[1, 4], [3, 5], [0, 6], [5, 7], [3, 9], [5, 9], [6, 10], [8, 11], [8, 12], [2, 14], [12, 16]];
        const CODE = {
            kegiatan: `
sort(iv.begin(), iv.end(), [](auto& p, auto& q) {
    return p.r < q.r;            // urut berdasarkan SELESAI //@0
});
int akhir = -INF, dipilih = 0;
for (auto& v : iv)
    if (v.l >= akhir) {          // tidak bentrok dengan yang terakhir //@1
        dipilih++;
        akhir = v.r;                                           //@2
    }                            // bentrok: lewati          //@3`,
            ruang: `
sort(iv.begin(), iv.end());      // urut berdasarkan MULAI   //@0
priority_queue<int, vector<int>, greater<int>> selesai;  // min-heap
for (auto& v : iv) {
    if (!selesai.empty() && selesai.top() <= v.l)
        selesai.pop();           // pakai ulang ruang yang sudah kosong //@1
    selesai.push(v.r);           // (atau ruang baru)                  //@2
}
// banyak ruang = ukuran heap terbesar                   //@3`,
        };
        K.widget(root, {
            title: "Greedy Interval",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                ${V.segmented("mode", [["kegiatan", "kegiatan terbanyak"], ["ruang", "ruang minimum"]], "kegiatan")}
                <label class="viz-input">interval <input data-iv value="${DEF.map((p) => p.join("-")).join(" ")}" style="width:230px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sedang diperiksa", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Dipilih", "#22c55e", "rgba(34,197,94,.14)"],
                ["Dilewati", "#ef4444", "rgba(239,68,68,.12)"],
            ],
            code: (ui) => CODE[K.segVal(ui, "mode")],
            watch: "Keadaan",
            panels: [{ key: "heap", type: "html", title: "Min-heap waktu selesai", hint: "teratas = paling cepat kosong" }],
            build(ui) {
                const mode = K.segVal(ui, "mode");
                const raw = parseIv(K.val(ui, "iv"), DEF);
                ui.head.querySelector("[data-iv]").value = raw.map((p) => p.join("-")).join(" ");
                const frames = [];
                if (mode === "kegiatan") {
                    const NOTE = '<span class="faint">tidak dipakai pada mode ini</span>';
                    const iv = raw.map(([l, r], i) => ({ l, r, id: i })).sort((p, q) => p.r - q.r || p.l - q.l);
                    const st = iv.map(() => "");
                    const draw = (cur, akhir) =>
                        timeline(
                            iv.map((v, i) => ({ l: v.l, r: v.r, label: `${v.l}–${v.r}`, cls: i === cur ? "on" : st[i] })),
                            { marks: akhir > -1 ? [{ x: akhir, label: `akhir = ${akhir}` }] : [] },
                        );
                    frames.push({ heap: NOTE, line: 0, text: `Urutkan interval berdasarkan waktu <b>selesai</b>. Ide: kegiatan yang selesai paling awal menyisakan waktu paling banyak untuk yang lain.`, html: draw(-1, -1), watch: [["dipilih", 0]] });
                    let akhir = -1;
                    let cnt = 0;
                    iv.forEach((v, i) => {
                        const ok = v.l >= akhir;
                        frames.push({
                            heap: NOTE,
                            line: 1,
                            text: `[${v.l}, ${v.r}]: mulai ${v.l} ${ok ? "≥" : "&lt;"} akhir ${akhir < 0 ? "(belum ada)" : akhir}.`,
                            html: draw(i, akhir),
                            watch: [["akhir", akhir < 0 ? "–" : akhir], ["dipilih", cnt]],
                            ask: i > 0 && i % 2 === 0 ? { type: "choice", prompt: `Akhir = ${akhir}. Apakah [${v.l}, ${v.r}] dipilih?`, options: ["dipilih", "dilewati"], answer: ok ? 0 : 1 } : undefined,
                        });
                        if (ok) {
                            st[i] = "ok";
                            cnt++;
                            akhir = v.r;
                            frames.push({ heap: NOTE, line: 2, text: `Pilih [${v.l}, ${v.r}], akhir = ${akhir}.`, html: draw(-1, akhir), watch: [["akhir", akhir], ["dipilih", cnt]], mark: "discover" });
                        } else {
                            st[i] = "bad";
                            frames.push({ heap: NOTE, line: 3, text: `Bentrok dengan kegiatan terakhir yang dipilih: lewati.`, html: draw(-1, akhir), watch: [["akhir", akhir], ["dipilih", cnt]], mark: "skip" });
                        }
                    });
                    frames.push({ heap: NOTE, line: -1, text: `Terpilih <b>${cnt}</b> kegiatan tanpa bentrok. Exchange argument: kegiatan pertama di solusi optimal mana pun bisa diganti dengan kegiatan yang selesai paling awal tanpa merusak apa pun.`, html: draw(-1, -1), watch: [["dipilih", cnt]], mark: "done" });
                    return frames;
                }
                // ruang minimum
                const iv = raw.map(([l, r], i) => ({ l, r, id: i })).sort((p, q) => p.l - q.l || p.r - q.r);
                const room = iv.map(() => -1);
                const heap = []; // [selesai, ruang]
                const freeRooms = [];
                let rooms = 0;
                let maxH = 0;
                const COLORS = ["ok", "in", "vi", "ok", "in", "vi", "ok", "in", "vi", "ok", "in", "vi"];
                const draw = (cur) =>
                    timeline(iv.map((v, i) => ({ l: v.l, r: v.r, label: room[i] >= 0 ? `R${room[i] + 1}` : `${v.l}–${v.r}`, cls: i === cur ? "on" : room[i] >= 0 ? COLORS[room[i]] : "dim" })), {
                        marks: cur >= 0 ? [{ x: iv[cur].l, label: `t = ${iv[cur].l}` }] : [],
                    });
                const heapView = () => {
                    const s = [...heap].sort((p, q) => p[0] - q[0]);
                    return s.length ? s.map(([e, r], k) => K.chip(`${e} (R${r + 1})`, k === 0 ? "on" : "")).join(" ") : "kosong";
                };
                frames.push({ line: 0, text: `Urutkan berdasarkan waktu <b>mulai</b>. Setiap rapat butuh ruang; ruang bisa dipakai ulang setelah rapat sebelumnya selesai.`, html: draw(-1), heap: heapView(), watch: [["ruang", 0]] });
                iv.forEach((v, i) => {
                    heap.sort((p, q) => p[0] - q[0]);
                    if (heap.length && heap[0][0] <= v.l) {
                        const [e, r] = heap.shift();
                        room[i] = r;
                        frames.push({ line: 1, text: `Rapat [${v.l}, ${v.r}]: ruang R${r + 1} sudah kosong sejak ${e} ≤ ${v.l}. Pakai ulang.`, html: draw(i), heap: heapView(), watch: [["ruang", rooms]], mark: "key" });
                    } else {
                        room[i] = freeRooms.length ? freeRooms.pop() : rooms++;
                        frames.push({
                            line: 2,
                            text: `Rapat [${v.l}, ${v.r}]: ${heap.length ? `ruang paling cepat kosong baru di ${heap[0][0]} &gt; ${v.l}` : "belum ada ruang"}. Buka ruang baru R${room[i] + 1}.`,
                            html: draw(i),
                            heap: heapView(),
                            watch: [["ruang", rooms, true]],
                            ask: i > 1 ? { type: "value", prompt: `Setelah rapat [${v.l}, ${v.r}], berapa banyak ruang yang sudah dibuka?`, answer: String(rooms) } : undefined,
                        });
                    }
                    heap.push([v.r, room[i]]);
                    maxH = Math.max(maxH, heap.length);
                });
                frames.push({ line: 3, text: `Butuh <b>${rooms}</b> ruang. Itu sama dengan banyak rapat yang berlangsung bersamaan pada saat tersibuk, jadi tidak mungkin lebih sedikit.`, html: draw(-1), heap: heapView(), watch: [["ruang", rooms]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Huffman ════════════════════════════
    V.register("huffman", (root) => {
        K.widget(root, {
            title: "Huffman: Gabungkan Dua Terkecil",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">frekuensi <input data-arr value="5 9 12 13 16 45" style="width:150px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Dua terkecil", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Simpul gabungan", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
priority_queue<long long, vector<long long>, greater<long long>> pq(a.begin(), a.end());
long long biaya = 0;
while (pq.size() > 1) {
    long long x = pq.top(); pq.pop();     // terkecil       //@0
    long long y = pq.top(); pq.pop();     // terkecil kedua //@0
    biaya += x + y;                                        //@1
    pq.push(x + y);                       // simpul baru    //@2
}`,
            watch: "Keadaan",
            panels: [{ key: "pq", type: "html", title: "Min-heap", hint: "terurut naik" }],
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: 1, max: 99, limit: 8, def: [5, 9, 12, 13, 16, 45] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const nodes = a.map((v, i) => ({ id: "L" + i, label: String(v), parent: null }));
                let pq = a.map((v, i) => ({ v, id: "L" + i }));
                let biaya = 0;
                let k = 0;
                const frames = [];
                const draw = (hl = [], neu = null) =>
                    K.tree({ nodes: nodes.map((nd) => ({ ...nd, cls: hl.includes(nd.id) ? "on" : nd.id === neu ? "ok" : nd.id[0] === "N" ? "in" : "" })), w: 620, h: 280, r: 15 });
                const heapView = (hl = []) => [...pq].sort((p, q) => p.v - q.v).map((o) => K.chip(o.v, hl.includes(o.id) ? "on" : "")).join(" ");
                frames.push({ line: -1, text: `Setiap angka adalah frekuensi satu simbol (atau panjang satu tali). Menggabungkan dua kelompok berbiaya jumlah keduanya. Minimalkan total biaya.`, html: draw(), pq: heapView(), watch: [["biaya", 0]] });
                while (pq.length > 1) {
                    pq.sort((p, q) => p.v - q.v);
                    const x = pq[0];
                    const y = pq[1];
                    frames.push({
                        line: 0,
                        text: `Ambil dua terkecil: ${x.v} dan ${y.v}.`,
                        html: draw([x.id, y.id]),
                        pq: heapView([x.id, y.id]),
                        watch: [["x", x.v], ["y", y.v], ["biaya", biaya]],
                    });
                    const id = "N" + k++;
                    nodes.push({ id, label: String(x.v + y.v), parent: null });
                    nodes.find((n) => n.id === x.id).parent = id;
                    nodes.find((n) => n.id === y.id).parent = id;
                    biaya += x.v + y.v;
                    pq = pq.slice(2);
                    pq.push({ v: x.v + y.v, id });
                    frames.push({
                        line: 2,
                        text: `Gabung menjadi ${x.v + y.v}; biaya bertambah ${x.v + y.v} → ${biaya}. Masukkan kembali ke heap.`,
                        html: draw([], id),
                        pq: heapView([id]),
                        watch: [["x + y", x.v + y.v], ["biaya", biaya, true]],
                        ask: k === 2 ? { type: "value", prompt: `Total biaya setelah penggabungan ini?`, answer: String(biaya) } : undefined,
                        mark: "key",
                    });
                }
                frames.push({ line: -1, text: `Total biaya <b>${biaya}</b> = jumlah (frekuensi × kedalaman daun). Simbol yang sering muncul berada dekat akar, sehingga kodenya pendek.`, html: draw(), pq: heapView(), watch: [["biaya", biaya]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Regret dengan heap ════════════════════════════
    V.register("regret", (root) => {
        K.widget(root, {
            title: "Ambil Dulu, Menyesal Kemudian",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">tenggat:untung <input data-jobs value="2:100 1:19 2:27 1:25 3:15 3:40" style="width:220px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Pekerjaan diperiksa", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Di jadwal (heap)", "#22c55e", "rgba(34,197,94,.14)"],
                ["Dibuang (menyesal)", "#ef4444", "rgba(239,68,68,.12)"],
            ],
            code: `
sort(job.begin(), job.end());           // urut tenggat naik
priority_queue<long long, vector<long long>, greater<long long>> pq;  // untung terkecil di atas
for (auto& j : job) {
    pq.push(j.untung);                  // ambil dulu   //@0
    if ((int)pq.size() > j.tenggat)     // jadwal terlalu penuh?
        pq.pop();                       // buang yang paling kecil untungnya //@1
}
// jawaban = jumlah isi pq                               //@2`,
            watch: "Keadaan",
            panels: [{ key: "pq", type: "html", title: "Min-heap untung", hint: "teratas = paling murah dibuang" }],
            build(ui) {
                const jobs = [];
                for (const tok of String(K.val(ui, "jobs")).split(/[\s,;]+/)) {
                    const m = tok.match(/^(\d+):(\d+)$/);
                    if (m && +m[1] >= 1 && +m[1] <= 9 && jobs.length < 10) jobs.push({ d: +m[1], p: +m[2] });
                }
                if (!jobs.length) jobs.push({ d: 2, p: 100 }, { d: 1, p: 19 }, { d: 2, p: 27 });
                ui.head.querySelector("[data-jobs]").value = jobs.map((j) => `${j.d}:${j.p}`).join(" ");
                jobs.forEach((j, i) => (j.id = i));
                jobs.sort((x, y) => x.d - y.d);
                const st = jobs.map(() => "");
                let pq = [];
                const frames = [];
                const tbl = (cur) =>
                    K.table(
                        jobs.map((j) => [`#${j.id + 1}`, j.d, j.p]),
                        { head: ["pekerjaan", "tenggat", "untung"], rowCls: Object.fromEntries(jobs.map((_, i) => [i, i === cur ? "on" : st[i]])) },
                    );
                const pqView = (hl) => (pq.length ? [...pq].sort((x, y) => x.p - y.p).map((j) => K.chip(j.p, j === hl ? "bad" : "ok")).join(" ") : "kosong");
                const sum = () => pq.reduce((s, j) => s + j.p, 0);
                frames.push({ line: -1, text: `Setiap pekerjaan butuh 1 hari dan harus selesai paling lambat hari ke-tenggat. Maksimalkan total untung. Proses berdasarkan tenggat naik.`, html: tbl(-1), pq: pqView(), watch: [["total", 0]] });
                jobs.forEach((j, i) => {
                    pq.push(j);
                    st[i] = "ok";
                    frames.push({ line: 0, text: `Ambil pekerjaan #${j.id + 1} (tenggat ${j.d}, untung ${j.p}). Jadwal berisi ${pq.length} pekerjaan.`, html: tbl(i), pq: pqView(), watch: [["isi jadwal", pq.length], ["tenggat", j.d], ["total", sum()]] });
                    if (pq.length > j.d) {
                        let mn = pq[0];
                        for (const x of pq) if (x.p < mn.p) mn = x;
                        frames.push({
                            line: 1,
                            text: `${pq.length} pekerjaan tidak muat dalam ${j.d} hari. Buang yang untungnya paling kecil: ${mn.p}.`,
                            html: tbl(i),
                            pq: pqView(mn),
                            pqMasked: pqView(),
                            watch: [["dibuang", mn.p, true], ["total", sum() - mn.p, true]],
                            ask: { type: "value", prompt: `Jadwal kelebihan satu. Untung berapa yang dibuang?`, answer: String(mn.p) },
                            mark: "skip",
                        });
                        pq = pq.filter((x) => x !== mn);
                        st[jobs.indexOf(mn)] = "bad";
                    }
                });
                frames.push({ line: 2, text: `Total untung maksimum <b>${sum()}</b>. Setiap kali jadwal kelebihan, membuang untung terkecil adalah pilihan terbaik: semua sisanya tetap muat sebelum tenggat masing-masing.`, html: tbl(-1), pq: pqView(), watch: [["total", sum()]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Konstruktif: digit terkecil ════════════════════════════
    V.register("constructive", (root) => {
        K.widget(root, {
            title: "Membangun Bilangan Digit demi Digit",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                ${V.segmented("arah", [["kecil", "terkecil"], ["besar", "terbesar"]], "kecil")}
                <label class="viz-input">panjang <input class="short" data-n value="4"></label>
                <label class="viz-input">jumlah digit <input class="short" data-s value="20"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Posisi yang diisi", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sudah pasti", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: (ui) =>
                K.segVal(ui, "arah") === "kecil"
                    ? `
for (int i = 0; i < n; i++) {
    int sisaPosisi = n - 1 - i;
    int d = max(i == 0 ? 1 : 0,          // tanpa nol di depan //@0
                S - 9 * sisaPosisi);     // sisanya harus tetap muat //@1
    hasil += char('0' + d);
    S -= d;                                                    //@2
}`
                    : `
for (int i = 0; i < n; i++) {
    int d = min(9, S);                   // sebesar mungkin //@0
    hasil += char('0' + d);                                  //@1
    S -= d;                                                  //@2
}`,
            watch: "Keadaan",
            build(ui) {
                const kecil = K.segVal(ui, "arah") === "kecil";
                const n = Math.max(1, Math.min(10, parseInt(K.val(ui, "n"), 10) || 4));
                let S = Math.max(0, Math.min(90, parseInt(K.val(ui, "s"), 10) || 0));
                ui.head.querySelector("[data-n]").value = n;
                ui.head.querySelector("[data-s]").value = S;
                const frames = [];
                const d = new Array(n).fill("?");
                const view = (cur) => K.line("bilangan", K.cells(d, { cls: Object.fromEntries(d.map((x, i) => [i, i === cur ? "on" : x === "?" ? "" : "ok"])), size: "lg" }));
                const S0 = S;
                if (S > 9 * n || (S === 0 && n > 1)) {
                    frames.push({ line: -1, text: `Tidak mungkin: ${S > 9 * n ? `jumlah digit ${n} angka paling besar 9 × ${n} = ${9 * n}` : `bilangan ${n} digit tanpa nol di depan punya jumlah digit minimal 1`}. Jawabannya -1.`, html: view(-1), watch: [["S", S]], mark: "done" });
                    return frames;
                }
                frames.push({ line: -1, text: `Bangun bilangan ${n} digit dengan jumlah digit ${S} yang ${kecil ? "<b>terkecil</b>" : "<b>terbesar</b>"}. Digit paling kiri paling menentukan, jadi putuskan dari kiri.`, html: view(-1), watch: [["S", S]] });
                for (let i = 0; i < n; i++) {
                    const rest = n - 1 - i;
                    let v;
                    if (kecil) {
                        const lo = i === 0 && n > 1 ? 1 : 0;
                        v = Math.max(lo, S - 9 * rest);
                        frames.push({
                            line: 1,
                            text: `Posisi ${i + 1}: masih ada ${rest} posisi di kanan yang bisa menampung paling banyak 9 × ${rest} = ${9 * rest}. Digit di sini minimal ${S} − ${9 * rest}${i === 0 && n > 1 ? " (dan minimal 1, tanpa nol di depan)" : ""}.`,
                            html: view(i),
                            watch: [["S", S], ["kapasitas kanan", 9 * rest], ["digit", v, true]],
                            ask: i < n - 1 ? { type: "value", prompt: `S = ${S}, kapasitas kanan ${9 * rest}. Digit terkecil yang bisa dipakai?`, answer: String(v) } : undefined,
                        });
                    } else {
                        v = Math.min(9, S);
                        frames.push({ line: 0, text: `Posisi ${i + 1}: ambil digit sebesar mungkin, min(9, ${S}) = ${v}.`, html: view(i), watch: [["S", S], ["digit", v, true]], ask: i === 1 ? { type: "value", prompt: `S = ${S}. Digit terbesar yang bisa dipakai?`, answer: String(v) } : undefined });
                    }
                    d[i] = v;
                    S -= v;
                    frames.push({ line: 2, text: `Tulis ${v}, sisa jumlah ${S}.`, html: view(-1), watch: [["S", S]], mark: "key" });
                }
                frames.push({ line: -1, text: `Hasil: <b>${d.join("")}</b> (jumlah digit ${S0}). Setiap digit adalah pilihan terbaik yang masih <em>menyisakan jawaban yang sah</em>: itulah pola greedy konstruktif.`, html: view(-1), watch: [["hasil", d.join("")]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Invarian: balik dua koin ════════════════════════════
    V.register("invariant", (root) => {
        K.widget(root, {
            title: "Invarian Paritas: Balik Dua Koin Bersebelahan",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">koin <input data-c value="THHTTT" style="width:120px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["H (kepala)", "#22c55e", "rgba(34,197,94,.14)"],
                ["T (ekor)", "#ef4444", "rgba(239,68,68,.12)"],
                ["Dibalik", "#f59e0b", "rgba(245,158,11,.18)"],
            ],
            code: `
int ops = 0;
for (int i = 0; i + 1 < n; i++)
    if (s[i] == 'T') {                 // T paling kiri harus hilang //@0
        balik(i); balik(i + 1);        // banyak T berubah -2, 0, atau +2 //@1
        ops++;
    }
if (s[n - 1] == 'T') cout << -1;       // paritas T ganjil: mustahil //@2
else cout << ops;                                        //@3`,
            watch: "Invarian",
            build(ui) {
                let s = String(K.val(ui, "c")).toUpperCase().replace(/[^HT]/g, "").slice(0, 12);
                if (s.length < 2) s = "THHTTT";
                ui.head.querySelector("[data-c]").value = s;
                const c = s.split("");
                const n = c.length;
                const frames = [];
                const cntT = () => c.filter((x) => x === "T").length;
                const view = (hl = []) => K.line("koin", K.cells(c, { cls: Object.fromEntries(c.map((x, i) => [i, hl.includes(i) ? "on" : x === "H" ? "ok" : "bad"])), size: "lg" }));
                const t0 = cntT();
                frames.push({ line: -1, text: `Operasi: balik dua koin bersebelahan. Bisakah semua menjadi H? Perhatikan <b>paritas</b> banyak T: membalik dua koin mengubah banyak T sebesar −2, 0, atau +2, jadi paritasnya tidak pernah berubah.`, html: view(), watch: [["banyak T", t0], ["paritas", t0 % 2 ? "ganjil" : "genap"]] });
                let ops = 0;
                for (let i = 0; i + 1 < n; i++) {
                    if (c[i] !== "T") continue;
                    const before = cntT();
                    c[i] = "H";
                    c[i + 1] = c[i + 1] === "T" ? "H" : "T";
                    ops++;
                    const after = cntT();
                    frames.push({
                        line: 1,
                        text: `Koin ${i + 1} adalah T dan tidak akan disentuh lagi setelah ini, jadi ia harus dibalik sekarang bersama koin ${i + 2}. Banyak T: ${before} → ${after}.`,
                        html: view([i, i + 1]),
                        watch: [["banyak T", after, true], ["paritas", after % 2 ? "ganjil" : "genap"], ["operasi", ops]],
                        ask: ops === 1 ? { type: "value", prompt: `Sebelumnya ada ${before} T. Setelah membalik koin ${i + 1} dan ${i + 2}, berapa banyak T?`, answer: String(after) } : undefined,
                        mark: "key",
                    });
                }
                const ok = c[n - 1] === "H";
                frames.push({
                    line: ok ? 3 : 2,
                    text: ok
                        ? `Semua H dengan <b>${ops}</b> operasi. Paritas awal genap (${t0} T), sesuai invarian. Strategi ini juga minimum: setiap T paling kiri memang harus didorong ke kanan sampai bertemu pasangannya.`
                        : `Tersisa satu T di ujung. Paritas awal ganjil (${t0} T), dan invarian menjamin selalu ada T: <b>mustahil</b>, cetak -1.`,
                    html: view(),
                    watch: [["banyak T", cntT()], ["paritas", cntT() % 2 ? "ganjil" : "genap"], ["operasi", ops]],
                    mark: "done",
                });
                return frames;
            },
        });
    });
})();
