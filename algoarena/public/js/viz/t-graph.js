/* Visualisasi track Graph (lanjutan): 2-SAT dan aliran biaya minimum. */
(() => {
    "use strict";
    const V = window.Viz;
    const K = window.Kit;
    const COLORS = ["#8b5cf6", "#06b6d4", "#ec4899", "#f59e0b", "#22c55e", "#3b82f6", "#ef4444", "#14b8a6"];

    // ════════════════════════════ 2-SAT ════════════════════════════
    V.register("twosat", (root) => {
        const PRESET = {
            contoh: [3, "1 2, -1 3, -2 -3, 1 3"],
            mustahil: [2, "1 2, -1 2, 1 -2, -1 -2"],
            paksa: [4, "1 1, -1 2, -2 -3, 3 4"],
        };
        let lastSet = null;
        K.widget(root, {
            title: "2-SAT: Graph Implikasi dan SCC",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                ${V.segmented("set", [["contoh", "Contoh"], ["mustahil", "Mustahil"], ["paksa", "Dipaksa"]], "contoh")}
                <label class="viz-input">n <input class="short" data-n value="3"></label>
                <label class="viz-input">klausa <input data-cl value="1 2, -1 3, -2 -3, 1 3" style="width:15em"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sisi baru / simpul aktif", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sudah selesai (tahap 1)", "#7c5cdb", "rgba(124,92,219,.16)"],
                ["Bertentangan", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
void klausa(int a, int b) {          // (a ∨ b)        //@0
    g[a ^ 1].push_back(b);           // ¬a → b         //@1
    g[b ^ 1].push_back(a);           // ¬b → a         //@1
}
// tahap 1: dfs1 di g, catat urutan selesai          //@2
// tahap 2: dfs2 di graph terbalik, urut terbalik    //@3
for (int i = 0; i < n; i++) {
    if (komp[2*i] == komp[2*i+1]) mustahil();       //@4
    x[i] = komp[2*i] > komp[2*i+1];                  //@5
}`,
            watch: "Keadaan",
            panels: [{ key: "info", type: "html", title: "Urutan & komponen", hint: "hasil Kosaraju" }],
            build(ui) {
                const set = K.segVal(ui, "set");
                const nIn = ui.head.querySelector("[data-n]");
                const cIn = ui.head.querySelector("[data-cl]");
                if (set !== lastSet) {
                    nIn.value = PRESET[set][0];
                    cIn.value = PRESET[set][1];
                    lastSet = set;
                }
                const n = Math.max(1, Math.min(4, parseInt(nIn.value, 10) || 3));
                nIn.value = n;
                const cls = [];
                for (const part of String(cIn.value).split(/[,;]+/)) {
                    const t = part.trim().split(/\s+/).map(Number);
                    if (t.length === 2 && t.every((v) => Number.isInteger(v) && v !== 0 && Math.abs(v) <= n)) cls.push(t);
                    if (cls.length >= 7) break;
                }
                if (!cls.length) cls.push([1, 1]);
                cIn.value = cls.map((c) => c.join(" ")).join(", ");

                const N = 2 * n;
                const lit = (x) => (x > 0 ? 2 * (x - 1) : 2 * (-x - 1) + 1);
                const name = (v) => (v % 2 ? "¬x" : "x") + (Math.floor(v / 2) + 1);
                const litName = (x) => (x > 0 ? `x${x}` : `¬x${-x}`);
                // posisi: x_i di atas, ¬x_i tepat di bawahnya (elips)
                const W = 600;
                const H = 300;
                const pos = [];
                for (let i = 0; i < n; i++) {
                    const t = Math.PI + ((i + 0.5) * Math.PI) / n;
                    const b = Math.PI - ((i + 0.5) * Math.PI) / n;
                    pos[2 * i] = { x: 300 + 250 * Math.cos(t), y: 150 + 105 * Math.sin(t) };
                    pos[2 * i + 1] = { x: 300 + 250 * Math.cos(b), y: 150 + 105 * Math.sin(b) };
                }
                const g = Array.from({ length: N }, () => []);
                const rg = Array.from({ length: N }, () => []);
                const edges = [];
                const nodeCls = new Array(N).fill("");
                const sub = new Array(N).fill(undefined);
                const color = new Array(N).fill(undefined);
                const draw = (opts = {}) => {
                    const nodes = [];
                    for (let v = 0; v < N; v++)
                        nodes.push({ id: v, x: pos[v].x, y: pos[v].y, label: name(v), cls: (opts.cls && opts.cls[v]) || nodeCls[v], sub: sub[v], color: color[v] });
                    const es = edges.map((e) => ({ u: e.u, v: e.v, cls: (opts.ecls && opts.ecls(e)) || "" }));
                    return K.graph({ nodes, edges: es, w: W, h: H, r: 19 });
                };
                const frames = [];
                let info = "";
                frames.push({
                    line: -1,
                    text: `${n} peubah → ${N} simpul: x<sub>i</sub> di atas, ¬x<sub>i</sub> di bawahnya. Ada ${cls.length} klausa; masing-masing akan menjadi dua sisi.`,
                    html: draw(),
                    watch: [["simpul", N], ["klausa", cls.length]],
                    info: K.line("klausa", cls.map((c) => K.chip(`(${litName(c[0])} ∨ ${litName(c[1])})`)).join(" ")),
                });
                cls.forEach((c, k) => {
                    const a = lit(c[0]);
                    const b = lit(c[1]);
                    const e1 = { u: a ^ 1, v: b, k };
                    const e2 = { u: b ^ 1, v: a, k };
                    edges.push(e1);
                    g[a ^ 1].push(b);
                    rg[b].push(a ^ 1);
                    if (a !== b) {
                        edges.push(e2);
                        g[b ^ 1].push(a);
                        rg[a].push(b ^ 1);
                    }
                    const same = a === b;
                    const opts = [`${name(b ^ 1)} → ${name(a)}`, `${name(a)} → ${name(b)}`, `${name(b)} → ${name(a ^ 1)}`];
                    frames.push({
                        line: 1,
                        text: same
                            ? `Klausa (${litName(c[0])} ∨ ${litName(c[0])}) berarti ${litName(c[0])} <b>wajib benar</b>: satu sisi ${name(a ^ 1)} → ${name(a)}.`
                            : `Klausa (${litName(c[0])} ∨ ${litName(c[1])}): jika ${name(a)} salah maka ${name(b)} wajib benar → sisi <b>${name(a ^ 1)} → ${name(b)}</b>, dan kontrapositifnya <b>${name(b ^ 1)} → ${name(a)}</b>.`,
                        html: draw({ ecls: (e) => (e.k === k ? "on" : "") }),
                        watch: [["klausa ke", k + 1], ["sisi", edges.length]],
                        ask:
                            k === 1 && !same
                                ? { type: "choice", prompt: `Klausa (${litName(c[0])} ∨ ${litName(c[1])}) menghasilkan ${name(a ^ 1)} → ${name(b)} dan…`, options: opts, answer: opts[0] }
                                : undefined,
                        htmlMasked: k === 1 && !same ? draw({ ecls: (e) => (e === e2 ? "dim" : e.k === k ? "on" : "") }) : undefined,
                    });
                });
                // Kosaraju tahap 1
                const vis = new Array(N).fill(false);
                const order = [];
                const orderView = () => K.line("selesai", order.length ? order.map((v) => K.chip(name(v), "in")).join(" ") : "–");
                const dfs1 = (u) => {
                    vis[u] = true;
                    for (const v of g[u]) if (!vis[v]) dfs1(v);
                    order.push(u);
                    nodeCls[u] = "vi";
                    frames.push({
                        line: 2,
                        text: `${name(u)} selesai (semua yang bisa dicapai darinya sudah dijelajahi). Urutan selesai: ${order.map(name).join(", ")}.`,
                        html: draw({ cls: { [u]: "on" } }),
                        watch: [["selesai", order.length + " / " + N]],
                        info: orderView(),
                    });
                };
                for (let v = 0; v < N; v++) if (!vis[v]) dfs1(v);
                nodeCls.fill("");
                // tahap 2
                const komp = new Array(N).fill(-1);
                let c = 0;
                const compList = [];
                const compView = () =>
                    orderView() +
                    K.line(
                        "komponen",
                        compList.map((m, i) => `<span class="kx-chip" style="border-color:${COLORS[i % COLORS.length]}">K${i}: ${m.map(name).join(", ")}</span>`).join(" "),
                    );
                for (const s of [...order].reverse()) {
                    if (komp[s] !== -1) continue;
                    const mem = [];
                    const st = [s];
                    komp[s] = c;
                    while (st.length) {
                        const u = st.pop();
                        mem.push(u);
                        for (const v of rg[u])
                            if (komp[v] === -1) {
                                komp[v] = c;
                                st.push(v);
                            }
                    }
                    mem.sort((p, q) => p - q);
                    compList.push(mem);
                    for (const v of mem) {
                        color[v] = COLORS[c % COLORS.length];
                        sub[v] = "K" + c;
                    }
                    frames.push({
                        line: 3,
                        text: `Mulai dari ${name(s)} (urutan selesai terbalik) di graph terbalik: komponen <b>K${c}</b> = {${mem.map(name).join(", ")}}. Nomor komponen Kosaraju mengikuti urutan topologis: makin besar, makin hilir.`,
                        html: draw(),
                        watch: [["komponen", c + 1]],
                        info: compView(),
                    });
                    c++;
                }
                // cek & penugasan
                const x = [];
                let bad = -1;
                for (let i = 0; i < n; i++) {
                    if (komp[2 * i] === komp[2 * i + 1]) {
                        bad = i;
                        break;
                    }
                }
                if (bad >= 0) {
                    frames.push({
                        line: 4,
                        text: `x${bad + 1} dan ¬x${bad + 1} sama-sama di <b>K${komp[2 * bad]}</b>: ada jalur x${bad + 1} ⇝ ¬x${bad + 1} dan sebaliknya. Kedua nilai memaksa lawannya, jadi rumus ini <b>TIDAK MUNGKIN</b> dipenuhi.`,
                        html: draw({ cls: { [2 * bad]: "bad", [2 * bad + 1]: "bad" } }),
                        watch: [["hasil", "TIDAK MUNGKIN"]],
                        info: compView(),
                        mark: "done",
                    });
                    return frames;
                }
                for (let i = 0; i < n; i++) {
                    x[i] = komp[2 * i] > komp[2 * i + 1] ? 1 : 0;
                    const pick = x[i] ? 2 * i : 2 * i + 1;
                    frames.push({
                        line: 5,
                        text: `komp[x${i + 1}] = K${komp[2 * i]}, komp[¬x${i + 1}] = K${komp[2 * i + 1]}. Yang lebih hilir: <b>${name(pick)}</b>, jadi x${i + 1} = ${x[i]}.`,
                        html: draw({ cls: { [pick]: "ok" } }),
                        watch: [["x" + (i + 1), x[i], true]],
                        info: compView(),
                        ask: i === n - 1 ? { type: "choice", prompt: `komp[x${i + 1}] = K${komp[2 * i]}, komp[¬x${i + 1}] = K${komp[2 * i + 1]}. Nilai x${i + 1}?`, options: ["1 (benar)", "0 (salah)"], answer: x[i] ? "1 (benar)" : "0 (salah)" } : undefined,
                        htmlMasked: i === n - 1 ? draw() : undefined,
                    });
                }
                const val = (l) => (l > 0 ? x[l - 1] : 1 - x[-l - 1]);
                const rows = cls.map((cc) => [`(${litName(cc[0])} ∨ ${litName(cc[1])})`, `${val(cc[0])} ∨ ${val(cc[1])}`, val(cc[0]) || val(cc[1]) ? "benar" : "SALAH"]);
                frames.push({
                    line: -1,
                    text: `Penugasan x = (${x.join(", ")}). Periksa: setiap klausa punya minimal satu literal benar.`,
                    html: draw({ cls: Object.fromEntries(x.map((b, i) => [b ? 2 * i : 2 * i + 1, "ok"])) }),
                    watch: x.map((b, i) => ["x" + (i + 1), b]),
                    info: K.table(rows, { head: ["klausa", "nilai", "hasil"] }),
                    mark: "done",
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ Aliran biaya minimum ════════════════════════════
    V.register("mcmf", (root) => {
        const PRESET = { contoh: "1 2 / 1 9", tiga: "4 1 3 / 2 0 5 / 3 2 2", rakus: "1 2 9 / 2 9 9 / 9 9 3" };
        let lastSet = null;
        K.widget(root, {
            title: "Aliran Biaya Minimum: Penugasan dengan SPFA",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                ${V.segmented("set", [["contoh", "2×2"], ["tiga", "3×3"], ["rakus", "Rakus gagal"]], "contoh")}
                <label class="viz-input">biaya <input data-m value="1 2 / 1 9" style="width:12em"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Jalur termurah", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Penugasan terpakai", "#22c55e", "rgba(34,197,94,.14)"],
                ["Sisi balik (biaya negatif)", "#7c5cdb", "rgba(124,92,219,.16)"],
            ],
            code: `
while (true) {
    // SPFA: jarak (biaya) termurah dari S di graph sisa   //@0
    if (dist[T] == INF) break;                              //@1
    // telusuri balik jalur, cari kapasitas terkecil        //@2
    for (sisi e di jalur) {                                 //@3
        e.cap -= f;  balik(e).cap += f;   // balik: biaya -c //@3
    }
    aliran += f;  biaya += f * dist[T];                     //@4
}`,
            watch: "Keadaan",
            panels: [{ key: "log", type: "html", title: "Iterasi", hint: "jalur penambah yang dipilih" }],
            build(ui) {
                const set = K.segVal(ui, "set");
                const mIn = ui.head.querySelector("[data-m]");
                if (set !== lastSet) {
                    mIn.value = PRESET[set];
                    lastSet = set;
                }
                let rows = String(mIn.value)
                    .split("/")
                    .map((r) => r.trim().split(/\s+/).map(Number).filter((v) => Number.isInteger(v) && v >= 0 && v <= 99));
                const k = Math.max(1, Math.min(3, rows.length));
                rows = rows.slice(0, k);
                const C = [];
                for (let i = 0; i < k; i++) {
                    C.push([]);
                    for (let j = 0; j < k; j++) C[i].push(rows[i] && rows[i][j] !== undefined ? rows[i][j] : 5);
                }
                mIn.value = C.map((r) => r.join(" ")).join(" / ");

                // simpul: 0 = S, 1..k pekerja, k+1..2k tugas, 2k+1 = T
                const S = 0;
                const T = 2 * k + 1;
                const NN = 2 * k + 2;
                const label = (v) => (v === S ? "S" : v === T ? "T" : v <= k ? "p" + v : "t" + (v - k));
                const W = 600;
                const H = k === 3 ? 300 : 250;
                const P = [];
                P[S] = { x: 45, y: H / 2 };
                P[T] = { x: 555, y: H / 2 };
                for (let i = 1; i <= k; i++) {
                    const y = H / 2 + (i - (k + 1) / 2) * (k === 3 ? 95 : 110);
                    P[i] = { x: 200, y };
                    P[k + i] = { x: 400, y };
                }
                const E = []; // {u, v, cap, cost}, sisi balik di indeks ^1
                const add = (u, v, cost) => {
                    E.push({ u, v, cap: 1, cost, fwd: true });
                    E.push({ u: v, v: u, cap: 0, cost: -cost, fwd: false });
                };
                for (let i = 1; i <= k; i++) add(S, i, 0);
                for (let i = 1; i <= k; i++) for (let j = 1; j <= k; j++) add(i, k + j, C[i - 1][j - 1]);
                for (let j = 1; j <= k; j++) add(k + j, T, 0);
                const adj = Array.from({ length: NN }, () => []);
                E.forEach((e, id) => adj[e.u].push(id));

                const draw = (opts = {}) => {
                    const nodes = [];
                    for (let v = 0; v < NN; v++) {
                        const d = opts.dist ? opts.dist[v] : undefined;
                        nodes.push({
                            id: v,
                            x: P[v].x,
                            y: P[v].y,
                            label: label(v),
                            cls: opts.onNodes && opts.onNodes.has(v) ? "on" : "",
                            sub: d === undefined ? undefined : d >= 1e9 ? "∞" : "d=" + d,
                        });
                    }
                    const es = [];
                    E.forEach((e, id) => {
                        const onPath = opts.path && opts.path.has(id);
                        if (e.fwd) {
                            const used = e.cap === 0;
                            const mid = e.u !== S && e.v !== T;
                            if (used && !onPath && opts.hideUsed) return;
                            es.push({
                                u: e.u,
                                v: e.v,
                                cls: onPath ? "on" : used ? (mid ? "ok" : "ok dim") : "",
                                label: mid ? String(e.cost) : undefined,
                                at: 0.28,
                            });
                        } else if (e.cap > 0 && e.u !== T && e.v !== S && e.u > k) {
                            es.push({ u: e.u, v: e.v, cls: (onPath ? "on" : "vi") + " dash", label: String(e.cost), at: 0.5 });
                        }
                    });
                    return K.graph({ nodes, edges: es, w: W, h: H, r: 18 });
                };
                const frames = [];
                const log = [];
                const logView = () =>
                    log.length
                        ? K.table(log, { head: ["iter", "jalur", "biaya"] })
                        : `<div class="kx-note">belum ada jalur</div>`;
                frames.push({
                    line: -1,
                    text: `Jaringan penugasan ${k}×${k}: S → pekerja (kapasitas 1, biaya 0), pekerja → tugas (kapasitas 1, biaya = angka di sisi), tugas → T. Aliran ${k} satuan = setiap pekerja dapat tepat satu tugas.`,
                    html: draw(),
                    watch: [["aliran", 0], ["biaya", 0]],
                    log: logView(),
                });
                let flow = 0;
                let cost = 0;
                for (let it = 1; it <= k + 1; it++) {
                    // SPFA
                    const INF = 1e9;
                    const dist = new Array(NN).fill(INF);
                    const inq = new Array(NN).fill(false);
                    const pe = new Array(NN).fill(-1);
                    dist[S] = 0;
                    const q = [S];
                    let qh = 0;
                    while (qh < q.length) {
                        const u = q[qh++];
                        inq[u] = false;
                        for (const id of adj[u]) {
                            const e = E[id];
                            if (e.cap > 0 && dist[u] + e.cost < dist[e.v]) {
                                dist[e.v] = dist[u] + e.cost;
                                pe[e.v] = id;
                                if (!inq[e.v]) {
                                    inq[e.v] = true;
                                    q.push(e.v);
                                }
                            }
                        }
                    }
                    if (dist[T] >= INF) {
                        frames.push({
                            line: 1,
                            text: `SPFA tidak menemukan jalur ke T lagi: semua sisi ke T sudah penuh. Selesai dengan aliran ${flow} dan biaya total <b>${cost}</b>.`,
                            html: draw({ dist }),
                            watch: [["aliran", flow], ["biaya", cost]],
                            log: logView(),
                        });
                        break;
                    }
                    frames.push({
                        line: 0,
                        text: `Iterasi ${it}: SPFA menghitung biaya termurah dari S ke setiap simpul di graph sisa (angka d di bawah simpul). ${it > 1 ? "Sisi balik ungu berbiaya negatif ikut dipertimbangkan." : ""}`,
                        html: draw({ dist }),
                        htmlMasked: draw({ dist: dist.map((d, v) => (v === T ? undefined : d)) }),
                        watch: [["iterasi", it], ["dist[T]", dist[T], true]],
                        log: logView(),
                        ask: it === 2 || (k === 1 && it === 1) ? { type: "value", prompt: `Berapa biaya jalur termurah S → T pada iterasi ${it}?`, answer: String(dist[T]) } : undefined,
                    });
                    const path = new Set();
                    const pnodes = new Set([T]);
                    const seq = [T];
                    for (let v = T; v !== S; v = E[pe[v]].u) {
                        path.add(pe[v]);
                        pnodes.add(E[pe[v]].u);
                        seq.push(E[pe[v]].u);
                    }
                    seq.reverse();
                    const usesBack = [...path].some((id) => !E[id].fwd);
                    frames.push({
                        line: 2,
                        text: `Jalur termurah: <b>${seq.map(label).join(" → ")}</b> dengan biaya ${dist[T]}.${
                            usesBack ? " Jalur ini <b>melewati sisi balik</b>: penugasan lama dibatalkan dan pekerjanya dipindah ke tugas lain." : ""
                        }`,
                        html: draw({ dist, path, onNodes: pnodes }),
                        watch: [["iterasi", it], ["dist[T]", dist[T]]],
                        log: logView(),
                    });
                    for (const id of path) {
                        E[id].cap -= 1;
                        E[id ^ 1].cap += 1;
                    }
                    flow += 1;
                    cost += dist[T];
                    log.push([it, seq.map(label).join("→"), dist[T]]);
                    frames.push({
                        line: 4,
                        text: `Alirkan 1 satuan. Sisi yang terpakai menjadi hijau, dan sisi baliknya (ungu, biaya negatif) muncul agar keputusan ini bisa dibatalkan nanti. Total: aliran ${flow}, biaya ${cost}.`,
                        html: draw(),
                        watch: [["aliran", flow], ["biaya", cost]],
                        log: logView(),
                    });
                }
                // rakus pembanding
                const usedW = new Array(k).fill(false);
                const usedT = new Array(k).fill(false);
                const pairs = [];
                for (let i = 0; i < k; i++) for (let j = 0; j < k; j++) pairs.push([C[i][j], i, j]);
                pairs.sort((a, b) => a[0] - b[0] || a[1] - b[1] || a[2] - b[2]);
                let greedy = 0;
                for (const [c, i, j] of pairs)
                    if (!usedW[i] && !usedT[j]) {
                        usedW[i] = usedT[j] = true;
                        greedy += c;
                    }
                const assign = [];
                for (let i = 1; i <= k; i++)
                    for (const id of adj[i]) {
                        const e = E[id];
                        if (e.fwd && e.v > k && e.v !== T && e.cap === 0) assign.push(`p${i}→t${e.v - k} (${e.cost})`);
                    }
                frames.push({
                    line: -1,
                    text: `Penugasan optimal: ${assign.join(", ")}, total <b>${cost}</b>. Pembanding rakus (ambil pasangan termurah dulu) memberi ${greedy}${
                        greedy > cost ? ", lebih mahal: rakus tidak bisa menarik kembali pilihannya." : ", kebetulan sama di kasus ini."
                    }`,
                    html: draw({ hideUsed: false }),
                    watch: [["MCMF", cost], ["rakus", greedy]],
                    log: logView(),
                    mark: "done",
                });
                return frames;
            },
        });
    });
})();
