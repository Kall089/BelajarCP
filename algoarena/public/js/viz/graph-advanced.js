/* Visualizer graph lanjutan: Floyd-Warshall & Bellman-Ford, LCA dengan binary lifting */
(() => {
    "use strict";

    const V = window.Viz;
    const { ek } = V;
    const INF = Infinity;
    const fmt = (d) => (d === INF ? "∞" : d);

    const P = (list) => [null, ...list.map(([x, y], i) => ({ id: i + 1, x, y }))];
    const E = (list) => list.map(([u, v, w = 1]) => ({ u, v, w }));
    const clone = (g) => JSON.parse(JSON.stringify(g));

    const PRESETS = {
        floyd: {
            n: 4,
            nodes: P([[110, 110], [330, 60], [545, 200], [270, 315]]),
            edges: E([[1, 2, 8], [1, 4, 1], [4, 2, 2], [2, 3, 1], [3, 1, 4], [4, 3, 9]]),
            directed: true,
            weighted: true,
        },
        bf: {
            n: 5,
            nodes: P([[80, 190], [235, 80], [235, 305], [430, 305], [570, 175]]),
            edges: E([[4, 5, 2], [3, 4, -3], [2, 3, 4], [1, 2, 5], [1, 3, 10]]),
            directed: true,
            weighted: true,
        },
        lca: {
            n: 12,
            edges: E([[1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7], [3, 8], [8, 9], [9, 10], [10, 11], [1, 12]]),
        },
    };

    /** Pencatat frame (sama seperti di graph-algos.js). */
    function recorder() {
        const frames = [];
        return {
            frames,
            push(line, text, st, fx = {}) {
                frames.push({
                    line,
                    text,
                    nodes: { ...st.nodes },
                    badges: { ...st.badges },
                    edges: { ...st.edges },
                    colors: st.colors ? { ...st.colors } : undefined,
                    ...JSON.parse(JSON.stringify(st.extra || {})),
                    ...fx,
                });
            },
        };
    }

    /** Panggung terbagi: graph di atas, tabel di bawah. */
    function splitStage(stage, extra = "") {
        stage.innerHTML = `<div class="split-stage ${extra}"><div class="split-graph"></div><div class="split-table"></div></div>`;
        return { graph: stage.querySelector(".split-graph"), table: stage.querySelector(".split-table") };
    }

    const CYAN = ["rgba(34,211,238,.30)", "#22d3ee"];
    const PINK = ["rgba(236,72,153,.32)", "#f472b6"];
    const GREEN = ["rgba(34,197,94,.35)", "#4ade80"];

    // ════════════════════════════ Floyd-Warshall & Bellman-Ford ════════════════════════════
    V.register("floyd", (root) => {
        const sh = V.shell(root, {
            title: "Jarak Semua Pasangan & Bobot Negatif",
            controls: `
                ${V.segmented("mode", [["floyd", "Floyd-Warshall"], ["bf", "Bellman-Ford"]], "floyd")}
                <button class="btn btn-sm" data-random>🎲 Acak</button>
                <button class="btn btn-sm" data-cycle hidden>🔁 Tambah siklus negatif</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Perantara k / simpul diproses", "#f59e0b", "#f59e0b"],
                ["Asal i / dist[i][k]", "#22d3ee", "rgba(34,211,238,.25)"],
                ["Tujuan j / dist[k][j]", "#f472b6", "rgba(236,72,153,.25)"],
                ["Nilai yang membaik", "#22c55e", "rgba(34,197,94,.2)"],
            ],
        });
        let mode = "floyd";
        let graph = clone(PRESETS.floyd);
        let negCycle = false;
        let view;
        let table;
        let pseudo;
        let watch;
        let edgePanel;
        let distPanel;
        const cycleBtn = sh.head.querySelector("[data-cycle]");
        const cycleLabel = () => (cycleBtn.textContent = negCycle ? "✂️ Hapus siklus negatif" : "🔁 Tambah siklus negatif");

        const PSEUDO = {
            floyd: [
                "dist[i][j] ← bobot i→j  (0 jika i = j, ∞ jika tak ada sisi)",
                "untuk k ← 1..N:            ← perantara yang diizinkan",
                "  untuk i ← 1..N:",
                "    untuk j ← 1..N:",
                "      jika dist[i][k] + dist[k][j] < dist[i][j]:",
                "        dist[i][j] ← dist[i][k] + dist[k][j]",
            ],
            bf: [
                "dist[semua] ← ∞,  dist[1] ← 0",
                "ulangi N − 1 kali:",
                "  untuk setiap sisi (u, v, w):",
                "    jika dist[u] + w < dist[v]:",
                "      dist[v] ← dist[u] + w",
                "untuk setiap sisi (u, v, w):   ← ronde ke-N",
                "  jika masih bisa membaik: ada siklus negatif!",
            ],
        };

        function setup() {
            sh.side.innerHTML = "";
            pseudo = V.pseudoPanel(sh.side, "Pseudocode", PSEUDO[mode]);
            if (mode === "floyd") {
                const parts = splitStage(sh.stage);
                view = V.graphView(parts.graph, { hint: "Seret simpul untuk merapikan · 🎲 Acak untuk graph lain" });
                table = { el: parts.table };
                edgePanel = null;
                distPanel = null;
            } else {
                sh.stage.innerHTML = "";
                view = V.graphView(sh.stage, { hint: "Urutan sisi di panel kanan menentukan urutan relaksasi" });
                table = null;
                edgePanel = V.htmlPanel(sh.side, "Daftar sisi", "diproses berurutan");
                distPanel = V.arrayPanel(sh.side, "Array dist", "dari simpul 1");
            }
            watch = V.watchPanel(sh.side);
            view.onNodeClick((id) => player.answerNode(id));
            cycleBtn.hidden = mode !== "bf";
        }

        // ── Floyd-Warshall ──
        function buildFloyd() {
            const g = graph;
            g.directed = true;
            g.weighted = true;
            view.draw(g);
            const n = g.n;
            const head = Array.from({ length: n }, (_, i) => String(i + 1));
            const tv = V.tableView(table.el, { rows: n, cols: n, rowHead: head, colHead: head, corner: "i\\j", cell: 44 });
            table.view = tv;
            const d = Array.from({ length: n }, (_, i) => Array.from({ length: n }, (_, j) => (i === j ? 0 : INF)));
            for (const e of g.edges) d[e.u - 1][e.v - 1] = Math.min(d[e.u - 1][e.v - 1], e.w);
            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, colors: {}, extra: {} };
            let watchRows = [];
            const snap = () => d.map((r) => r.map(fmt));
            const put = (line, text, extra = {}, fx = {}) => {
                st.extra = { vals: snap(), cls: extra.cls || {}, hlRow: extra.hlRow, hlCols: extra.hlCols, watch: watchRows };
                rec.push(line, text, st, fx);
            };

            watchRows = [["simpul", n], ["sisi", g.edges.length]];
            put(0, "Isi matriks awal: <b>dist[i][j]</b> = bobot sisi i → j, <b>0</b> di diagonal, dan <b>∞</b> jika tidak ada sisi langsung. Satu matriks ini menyimpan jarak <b>semua pasangan</b> sekaligus.", {}, { mark: "key" });

            let changes = 0;
            for (let k = 0; k < n; k++) {
                const rowCls = {};
                for (let x = 0; x < n; x++) {
                    rowCls[`${k},${x}`] = "range";
                    rowCls[`${x},${k}`] = "range";
                }
                st.nodes = { [k + 1]: "current" };
                st.colors = {};
                st.badges = { [k + 1]: "k" };
                watchRows = [["k (perantara)", k + 1], ["perubahan sejauh ini", changes]];
                put(1, `<b>Putaran k = ${k + 1}</b>: sekarang simpul <b>${k + 1}</b> boleh dipakai sebagai <b>perantara</b>. Baris & kolom ${k + 1} (biru muda) adalah bahan perhitungan putaran ini.`, { cls: rowCls, hlRow: k, hlCols: [k] }, { mark: "key" });

                for (let i = 0; i < n; i++) {
                    if (i === k || d[i][k] === INF) continue;
                    for (let j = 0; j < n; j++) {
                        if (j === k || j === i || d[k][j] === INF) continue;
                        const via = d[i][k] + d[k][j];
                        const old = d[i][j];
                        const better = via < old;
                        const cls = { ...rowCls, [`${i},${k}`]: "dep", [`${k},${j}`]: "dep alt", [`${i},${j}`]: better ? "current" : "current" };
                        st.nodes = { [k + 1]: "current" };
                        st.colors = { [i + 1]: CYAN, [j + 1]: PINK };
                        st.badges = { [i + 1]: "i", [k + 1]: "k", [j + 1]: "j" };
                        if (better) {
                            d[i][j] = via;
                            changes++;
                        }
                        watchRows = [
                            ["i, k, j", `${i + 1}, ${k + 1}, ${j + 1}`],
                            [`dist[${i + 1}][${k + 1}] + dist[${k + 1}][${j + 1}]`, `${d[i][k]} + ${d[k][j]} = ${via}`, true],
                            [`dist[${i + 1}][${j + 1}] lama`, fmt(old)],
                            [`dist[${i + 1}][${j + 1}] baru`, fmt(d[i][j]), true],
                        ];
                        put(
                            better ? 5 : 4,
                            better
                                ? `Lewat ${k + 1}: ${i + 1} → ${k + 1} → ${j + 1} = ${d[i][k]} + ${d[k][j]} = <b>${via}</b>, lebih pendek dari ${fmt(old)}. Perbarui <b>dist[${i + 1}][${j + 1}] = ${via}</b>.`
                                : `Lewat ${k + 1}: ${d[i][k]} + ${d[k][j]} = ${via}, <b>tidak</b> lebih pendek dari ${fmt(old)}. dist[${i + 1}][${j + 1}] tetap.`,
                            { cls, hlRow: i, hlCols: [j] },
                            {
                                mark: better ? "relax" : undefined,
                                ask: better
                                    ? {
                                          type: "value",
                                          mask: [i, j],
                                          answer: via,
                                          prompt: `Berapa <b>dist[${i + 1}][${j + 1}]</b> setelah boleh lewat simpul ${k + 1}?`,
                                          hint: `Bandingkan dist[${i + 1}][${j + 1}] lama dengan dist[${i + 1}][${k + 1}] + dist[${k + 1}][${j + 1}], ambil yang lebih kecil.`,
                                          context: `Sel biru = dist[${i + 1}][${k + 1}], sel pink = dist[${k + 1}][${j + 1}].`,
                                      }
                                    : {
                                          type: "choice",
                                          options: ["Membaik", "Tetap"],
                                          answer: 1,
                                          prompt: `Apakah <b>dist[${i + 1}][${j + 1}]</b> = ${fmt(old)} membaik jika lewat simpul ${k + 1}?`,
                                          hint: `Hitung ${d[i][k]} + ${d[k][j]} lalu bandingkan dengan ${fmt(old)}.`,
                                      },
                            },
                        );
                    }
                }
            }

            st.nodes = {};
            st.colors = {};
            st.badges = {};
            const pathCls = {};
            watchRows = [["total perubahan", changes], ["kompleksitas", "O(N³)"]];
            const qi = n >= 3 ? 2 : 0;
            const qj = n >= 2 ? 1 : 0;
            put(-1, `🎉 Selesai! Setelah semua simpul pernah menjadi perantara, <b>dist[i][j]</b> berisi jarak terpendek dari i ke j untuk <b>setiap</b> pasangan. Totalnya N³ = ${n ** 3} pengecekan.`, { cls: pathCls }, {
                mark: "done",
                ask:
                    d[qi][qj] !== INF && qi !== qj
                        ? {
                              type: "value",
                              answer: d[qi][qj],
                              mask: [qi, qj],
                              prompt: `Berapa jarak terpendek dari simpul <b>${qi + 1}</b> ke simpul <b>${qj + 1}</b>?`,
                              hint: "Cari rute di graph yang boleh melewati beberapa simpul perantara.",
                          }
                        : undefined,
            });
            player.load(rec.frames);
        }

        // ── Bellman-Ford ──
        function buildBF() {
            const g = graph;
            g.directed = true;
            g.weighted = true;
            const edges = g.edges.map((e) => ({ ...e }));
            if (negCycle && !edges.some((e) => e.u === 5 && e.v === 3)) edges.push({ u: 5, v: 3, w: -4 });
            const shown = { ...g, edges };
            view.draw(shown);
            const n = g.n;
            const dist = new Array(n + 1).fill(INF);
            dist[1] = 0;
            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, extra: {} };
            const status = edges.map(() => "pending");
            let watchRows = [];
            const edgeHtml = (cur) =>
                `<div class="edge-list">${edges
                    .map((e, i) => {
                        const s = i === cur ? "current" : status[i];
                        const icon = { take: "✓", current: "?", pending: "" }[s] ?? "";
                        return `<div class="edge-row ${s}"><span>${e.u}→${e.v}</span><b>${e.w}</b><i>${icon}</i></div>`;
                    })
                    .join("")}</div>`;
            const distCells = (changed) =>
                Array.from({ length: n }, (_, i) => ({ key: i + 1, val: fmt(dist[i + 1]), cls: changed === i + 1 ? "changed" : "" }));
            const badges = () => {
                const b = {};
                for (let v = 1; v <= n; v++) b[v] = { text: fmt(dist[v]), cls: "final" };
                return b;
            };
            const put = (line, text, cur, changed, fx = {}) => {
                st.badges = badges();
                st.extra = { edgeList: edgeHtml(cur), dist: distCells(changed), watch: watchRows };
                rec.push(line, text, st, fx);
            };

            st.nodes = { 1: "start" };
            watchRows = [["simpul", n], ["sisi", edges.length], ["ronde maksimum", n - 1]];
            put(0, "Bellman-Ford tidak memilih simpul terdekat seperti Dijkstra. Ia cukup <b>merelaksasi semua sisi</b> berulang kali. Awalnya dist[1] = 0 dan lainnya ∞.", -1, 0, { mark: "key" });

            let cycle = false;
            for (let round = 1; round <= n; round++) {
                const check = round === n;
                status.fill("pending");
                st.edges = {};
                watchRows = [["ronde", check ? `${round} (pemeriksaan)` : `${round} dari ${n - 1}`]];
                put(check ? 5 : 1, check ? `<b>Ronde ke-${n} (pemeriksaan)</b>: jika masih ada jarak yang bisa membaik, berarti ada <b>siklus negatif</b>.` : `<b>Ronde ${round}</b>: periksa setiap sisi sesuai urutan daftar.`, -1, 0, { mark: "key" });
                let changed = false;
                for (let i = 0; i < edges.length; i++) {
                    const e = edges[i];
                    const key = ek(e.u, e.v, true);
                    st.nodes = { [e.u]: "current", [e.v]: "hl" };
                    if (dist[e.u] === INF) {
                        st.edges[key] = "dim";
                        watchRows = [["ronde", round], ["sisi", `${e.u} → ${e.v} (w = ${e.w})`], [`dist[${e.u}]`, "∞"]];
                        put(check ? 6 : 3, `Sisi ${e.u} → ${e.v}: dist[${e.u}] masih ∞ (belum terjangkau), jadi belum bisa dipakai. Lewati.`, i, 0);
                        st.edges[key] = "";
                        continue;
                    }
                    const via = dist[e.u] + e.w;
                    const old = dist[e.v];
                    if (via < old) {
                        changed = true;
                        status[i] = "take";
                        st.edges[key] = "path";
                        if (check) {
                            cycle = true;
                            watchRows = [["ronde", round], ["sisi", `${e.u} → ${e.v} (w = ${e.w})`], ["dist[u] + w", via], [`dist[${e.v}]`, fmt(old)]];
                            st.nodes = { [e.u]: "current", [e.v]: "current" };
                            put(6, `Di ronde ke-${n}, sisi ${e.u} → ${e.v} <b>masih</b> bisa membuat dist[${e.v}] lebih kecil (${fmt(old)} → ${via}). Ini hanya mungkin jika ada <b>siklus negatif</b>: berputar di siklus itu terus mengurangi jarak tanpa batas.`, i, e.v, { mark: "skip" });
                            break;
                        }
                        dist[e.v] = via;
                        watchRows = [["ronde", round], ["sisi", `${e.u} → ${e.v} (w = ${e.w})`], ["dist[u] + w", `${dist[e.u]} + ${e.w} = ${via}`, true], [`dist[${e.v}]`, `${fmt(old)} → ${via}`, true]];
                        put(4, `Sisi ${e.u} → ${e.v}: ${dist[e.u]} + (${e.w}) = <b>${via}</b> &lt; ${fmt(old)}. Relaksasi: <b>dist[${e.v}] = ${via}</b>.`, i, e.v, {
                            mark: "relax",
                            flow: [e.u, e.v],
                            ask: {
                                type: "value",
                                answer: via,
                                maskKey: e.v,
                                maskBadge: e.v,
                                prompt: `Sisi ${e.u} → ${e.v} berbobot ${e.w}. Berapa <b>dist[${e.v}]</b> sekarang?`,
                                hint: `Bandingkan dist[${e.v}] = ${fmt(old)} dengan dist[${e.u}] + w = ${dist[e.u]} + (${e.w}).`,
                            },
                        });
                        st.edges[key] = "tree";
                    } else {
                        st.edges[key] = "skip";
                        watchRows = [["ronde", round], ["sisi", `${e.u} → ${e.v} (w = ${e.w})`], ["dist[u] + w", via], [`dist[${e.v}]`, fmt(old)]];
                        put(check ? 6 : 3, `Sisi ${e.u} → ${e.v}: ${dist[e.u]} + (${e.w}) = ${via}, tidak lebih kecil dari dist[${e.v}] = ${fmt(old)}. Tidak berubah.`, i, 0);
                        st.edges[key] = "";
                    }
                }
                if (cycle) break;
                if (!changed) {
                    st.nodes = {};
                    watchRows = [["ronde", round], ["perubahan", "tidak ada"]];
                    put(check ? 5 : 1, check ? `Ronde pemeriksaan tidak mengubah apa pun: <b>tidak ada siklus negatif</b>, semua jarak sudah final.` : `Ronde ${round} tidak mengubah apa pun. Jarak sudah final, jadi kita boleh <b>berhenti lebih awal</b> (tidak perlu menunggu ${n - 1} ronde).`, -1, 0, { mark: "done" });
                    break;
                }
            }
            st.nodes = {};
            st.edges = {};
            watchRows = cycle ? [["hasil", "siklus negatif!"]] : [["hasil", Array.from({ length: n }, (_, i) => fmt(dist[i + 1])).join(" ")]];
            put(-1, cycle ? "❗ Graph ini punya <b>siklus negatif</b> yang terjangkau dari simpul 1, sehingga jarak terpendek <b>tidak terdefinisi</b> (bisa terus mengecil)." : `🎉 Jarak terpendek dari simpul 1: <b>${Array.from({ length: n }, (_, i) => fmt(dist[i + 1])).join(", ")}</b>. Perhatikan: berkat sisi negatif, beberapa jarak lebih kecil daripada jika memakai rute "biasa".`, -1, 0, { mark: "done" });
            player.load(rec.frames);
        }

        function build() {
            if (mode === "floyd") buildFloyd();
            else buildBF();
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            watch.set(f.watch, f.masked);
            if (mode === "floyd") table.view?.update(f);
            else {
                edgePanel.set(f.edgeList);
                distPanel.set(f.dist, f.masked && f.ask ? f.ask.maskKey : undefined);
            }
        });

        V.bindSegmented(sh.head, "mode", (m) => {
            mode = m;
            negCycle = false;
            cycleLabel();
            graph = clone(PRESETS[m]);
            setup();
            build();
        });
        sh.head.querySelector("[data-random]").onclick = () => {
            if (mode === "floyd") {
                graph = V.randomGraph({ n: V.rand(4, 5), m: V.rand(6, 8), directed: true, weighted: true, connected: true, maxW: 9 });
            } else {
                // Acak lalu beri beberapa bobot negatif tanpa membuat siklus negatif
                let g;
                for (let tries = 0; tries < 30; tries++) {
                    g = V.randomGraph({ n: 5, m: V.rand(6, 7), directed: true, weighted: true, connected: true, maxW: 9, dag: true });
                    g.edges.forEach((e) => {
                        if (Math.random() < 0.3) e.w = -V.rand(1, 4);
                    });
                    if (g.edges.some((e) => e.w < 0)) break;
                }
                graph = g;
                negCycle = false;
                cycleLabel();
            }
            build();
        };
        cycleBtn.onclick = () => {
            graph = clone(PRESETS.bf);
            negCycle = !negCycle;
            cycleLabel();
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            graph = clone(PRESETS[mode]);
            negCycle = false;
            cycleLabel();
            build();
        };

        setup();
        build();
    });

    // ════════════════════════════ LCA dengan binary lifting ════════════════════════════
    V.register("lca", (root) => {
        const sh = V.shell(root, {
            title: "Lowest Common Ancestor: Binary Lifting",
            controls: `
                <label class="viz-input">u <select data-u></select></label>
                <label class="viz-input">v <select data-v></select></label>
                <button class="btn btn-sm" data-random>🎲 Pohon acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Simpul u", "#22d3ee", "rgba(34,211,238,.3)"],
                ["Simpul v", "#f472b6", "rgba(236,72,153,.3)"],
                ["LCA", "#4ade80", "rgba(34,197,94,.35)"],
                ["Sel tabel yang dipakai", "#22d3ee"],
            ],
        });
        let tree = { n: PRESETS.lca.n, edges: clone(PRESETS.lca.edges) };
        let qu = 7;
        let qv = 10;
        const parts = splitStage(sh.stage, "tall");
        const view = V.graphView(parts.graph, { hint: "Akar di simpul 1 (paling atas) · klik simpul untuk menjawab di Mode Tebak" });
        const pseudo = V.pseudoPanel(sh.side, "Pseudocode", [
            "up[0][v] ← parent(v),  depth[v] dari BFS akar",
            "untuk j ← 1..LOG−1:  up[j][v] ← up[j−1][ up[j−1][v] ]",
            "LCA(u, v):",
            "  jika depth[u] < depth[v]: tukar u dan v",
            "  naikkan u sebanyak depth[u] − depth[v] (per bit)",
            "  jika u = v: kembalikan u",
            "  untuk j ← LOG−1 turun ke 0:",
            "    jika up[j][u] ≠ up[j][v]: u ← up[j][u];  v ← up[j][v]",
            "  kembalikan up[0][u]",
        ]);
        const watch = V.watchPanel(sh.side);
        const selU = sh.head.querySelector("[data-u]");
        const selV = sh.head.querySelector("[data-v]");
        let tv;

        function options(sel, val) {
            sel.innerHTML = Array.from({ length: tree.n }, (_, i) => `<option value="${i + 1}" ${i + 1 === val ? "selected" : ""}>${i + 1}</option>`).join("");
        }

        function build() {
            const n = tree.n;
            const g = { n, nodes: V.treeLayout(n, tree.edges, 1), edges: tree.edges, directed: false, weighted: false };
            view.draw(g);
            const adj = Array.from({ length: n + 1 }, () => []);
            tree.edges.forEach((e) => {
                adj[e.u].push(e.v);
                adj[e.v].push(e.u);
            });
            const depth = new Array(n + 1).fill(-1);
            const par = new Array(n + 1).fill(1);
            depth[1] = 0;
            const order = [1];
            for (let h = 0; h < order.length; h++) {
                const u = order[h];
                for (const v of adj[u]) {
                    if (depth[v] === -1) {
                        depth[v] = depth[u] + 1;
                        par[v] = u;
                        order.push(v);
                    }
                }
            }
            const maxD = Math.max(...depth.slice(1));
            let LOG = 1;
            while (1 << LOG <= maxD) LOG++;
            LOG = Math.max(LOG, 2);
            const up = Array.from({ length: LOG }, () => new Array(n + 1).fill(null));
            const cols = Array.from({ length: n }, (_, i) => String(i + 1));
            const rowsHead = ["depth", ...Array.from({ length: LOG }, (_, j) => `2^${j}`)];
            tv = V.tableView(parts.table, { rows: LOG + 1, cols: n, rowHead: rowsHead, colHead: cols, corner: "j\\v", cell: n > 12 ? 30 : 34 });

            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, colors: {}, extra: {} };
            let watchRows = [];
            // Baris 0 tabel = depth, baris j + 1 = up[j]. Koordinat cls/mask di bawah memakai indeks up[j].
            const snap = () => [depth.slice(1), ...up.map((row) => row.slice(1).map((x) => (x === null ? "" : x)))];
            const shift = (cls = {}) => {
                const out = {};
                for (let c = 0; c < n; c++) out[`0,${c}`] = "base";
                for (const [k, val] of Object.entries(cls)) {
                    const [r, c] = k.split(",").map(Number);
                    out[`${r + 1},${c}`] = val;
                }
                return out;
            };
            const put = (line, text, extra = {}, fx = {}) => {
                st.extra = { vals: snap(), cls: shift(extra.cls), hlRow: extra.hlRow === undefined ? undefined : extra.hlRow + 1, hlCols: extra.hlCols, watch: watchRows };
                if (fx.ask && fx.ask.mask) fx = { ...fx, ask: { ...fx.ask, mask: [fx.ask.mask[0] + 1, fx.ask.mask[1]] } };
                rec.push(line, text, st, fx);
            };

            // Fase 1: depth & parent
            for (let v = 1; v <= n; v++) up[0][v] = par[v];
            st.badges = {};
            st.nodes = { 1: "start" };
            watchRows = [["simpul", n], ["kedalaman maks", maxD], ["LOG", LOG]];
            put(0, `Langkah persiapan: BFS dari akar 1 untuk mengisi baris <b>depth</b> (kedalaman) dan baris <b>2^0</b>, yaitu <b>up[0][v]</b> = parent v. Akar menunjuk dirinya sendiri, sehingga lompatan yang "kebablasan" tetap berhenti di akar.`, { hlRow: 0 }, { mark: "key" });

            // Fase 2: isi tabel lompatan baris demi baris
            const deepest = order[order.length - 1];
            for (let j = 1; j < LOG; j++) {
                const mid = up[j - 1][deepest];
                const val = up[j - 1][mid];
                const cls = { [`${j - 1},${deepest - 1}`]: "dep", [`${j - 1},${mid - 1}`]: "dep alt", [`${j},${deepest - 1}`]: "current" };
                st.nodes = { [deepest]: "current", [mid]: "hl", [val]: "target" };
                st.colors = {};
                for (let v = 1; v <= n; v++) if (v !== deepest) up[j][v] = up[j - 1][up[j - 1][v]];
                watchRows = [["j", j], ["lompatan", `${1 << j} langkah`], [`up[${j - 1}][${deepest}]`, mid], [`up[${j}][${deepest}]`, val, true]];
                up[j][deepest] = val;
                put(1, `Baris <b>2^${j} = ${1 << j}</b> langkah: lompat ${1 << (j - 1)} lalu ${1 << (j - 1)} lagi. Contoh simpul ${deepest}: up[${j - 1}][${deepest}] = ${mid}, lalu up[${j - 1}][${mid}] = <b>${val}</b>. Seluruh baris diisi dengan rumus yang sama.`, { cls, hlRow: j, hlCols: [deepest - 1] }, {
                    ask: {
                        type: "value",
                        mask: [j, deepest - 1],
                        answer: val,
                        prompt: `Berapa <b>up[${j}][${deepest}]</b> (leluhur ${1 << j} langkah di atas simpul ${deepest})?`,
                        hint: `Pakai baris sebelumnya: up[${j - 1}][ up[${j - 1}][${deepest}] ] = up[${j - 1}][${mid}].`,
                        context: "Sel biru dan pink adalah bahan perhitungannya.",
                    },
                });
            }
            st.nodes = {};
            watchRows = [["ukuran tabel", `${LOG} × ${n}`], ["waktu", "O(N log N)"]];
            put(1, `Tabel lompatan lengkap. Setiap sel dihitung sekali, jadi persiapannya hanya <b>O(N log N)</b>. Sekarang setiap pertanyaan LCA bisa dijawab dalam <b>O(log N)</b>.`, {}, { mark: "done" });

            // Fase 3: pertanyaan LCA(u, v)
            let u = Math.min(qu, n);
            let v = Math.min(qv, n);
            const show = (a, b, lca) => {
                st.colors = { [a]: CYAN, [b]: PINK };
                if (lca) st.colors[lca] = GREEN;
                st.badges = { [a]: "u", [b]: a === b ? "u=v" : "v" };
            };
            st.nodes = {};
            show(u, v);
            watchRows = [["u", `${u} (depth ${depth[u]})`], ["v", `${v} (depth ${depth[v]})`]];
            put(2, `Pertanyaan: <b>LCA(${u}, ${v})</b>, yaitu leluhur bersama terdalam dari simpul ${u} dan ${v}.`, {}, { mark: "key" });
            if (depth[u] < depth[v]) {
                [u, v] = [v, u];
                show(u, v);
                watchRows = [["u", `${u} (depth ${depth[u]})`], ["v", `${v} (depth ${depth[v]})`]];
                put(3, `depth[u] lebih kecil, jadi <b>tukar</b> agar u selalu yang lebih dalam: sekarang u = ${u}, v = ${v}.`);
            }
            let diff = depth[u] - depth[v];
            if (diff > 0) {
                put(4, `Selisih kedalaman = ${depth[u]} − ${depth[v]} = <b>${diff}</b> = ${diff.toString(2)}<sub>2</sub>. Naikkan u sesuai bit-bit yang menyala.`);
            }
            for (let j = 0; diff > 0; j++, diff >>= 1) {
                if (!(diff & 1)) continue;
                const nu = up[j][u];
                watchRows = [["bit / lompatan", `2^${j} = ${1 << j}`], [`up[${j}][${u}]`, nu, true]];
                const cls = { [`${j},${u - 1}`]: "dep" };
                const from = u;
                u = nu;
                show(u, v);
                put(4, `Bit ${j} menyala: lompat <b>${1 << j}</b> langkah dari ${from} ke <b>up[${j}][${from}] = ${u}</b>.`, { cls, hlRow: j, hlCols: [from - 1] }, {
                    ask: { type: "node", answer: u, prompt: `u = ${from} melompat 2^${j} = ${1 << j} langkah ke atas. Klik simpul tujuannya!`, hint: `Lihat tabel: up[${j}][${from}].` },
                });
            }
            if (u === v) {
                show(u, v, u);
                watchRows = [["u = v", u, true]];
                put(5, `Setelah disamakan kedalamannya, u = v = <b>${u}</b>. Berarti ${u} sendiri adalah LCA (salah satu simpul adalah leluhur yang lain).`, {}, {
                    mark: "done",
                    ask: { type: "node", answer: u, prompt: "Klik simpul yang merupakan LCA!", hint: "u dan v sudah bertemu." },
                });
            } else {
                watchRows = [["u", `${u} (depth ${depth[u]})`], ["v", `${v} (depth ${depth[v]})`]];
                put(6, `Kedalaman sudah sama (${depth[u]}), tetapi u ≠ v. Sekarang lompat <b>bersama-sama</b>, dari lompatan terbesar ke terkecil, selama hasilnya masih <b>berbeda</b>.`);
                for (let j = LOG - 1; j >= 0; j--) {
                    const a = up[j][u];
                    const b = up[j][v];
                    const cls = { [`${j},${u - 1}`]: "dep", [`${j},${v - 1}`]: "dep alt" };
                    if (a !== b) {
                        watchRows = [["j", j], [`up[${j}][${u}] / up[${j}][${v}]`, `${a} / ${b}`], ["keputusan", "lompat"]];
                        const pu = u;
                        const pv = v;
                        u = a;
                        v = b;
                        show(u, v);
                        put(7, `j = ${j}: up[${j}][${pu}] = ${a} ≠ up[${j}][${pv}] = ${b}. Masih di bawah LCA, jadi <b>lompat</b>: u = ${a}, v = ${b}.`, { cls, hlRow: j, hlCols: [pu - 1, pv - 1] }, {
                            mark: "relax",
                            ask: { type: "choice", options: ["Lompat", "Jangan lompat"], answer: 0, prompt: `j = ${j}: up[${j}][${pu}] = ${a} dan up[${j}][${pv}] = ${b}. Lompat atau tidak?`, hint: "Lompat hanya jika hasilnya masih berbeda." },
                        });
                    } else {
                        watchRows = [["j", j], [`up[${j}][${u}] / up[${j}][${v}]`, `${a} / ${b}`], ["keputusan", "jangan lompat"]];
                        show(u, v);
                        put(7, `j = ${j}: up[${j}][${u}] = up[${j}][${v}] = ${a}. Hasilnya sama, berarti lompatan ${1 << j} langkah sampai di LCA <b>atau melewatinya</b>. Jangan lompat.`, { cls, hlRow: j, hlCols: [u - 1, v - 1] }, {
                            ask: { type: "choice", options: ["Lompat", "Jangan lompat"], answer: 1, prompt: `j = ${j}: up[${j}][${u}] dan up[${j}][${v}] sama-sama ${a}. Lompat atau tidak?`, hint: "Jika hasilnya sama, kita mungkin melewati LCA." },
                        });
                    }
                }
                const lca = up[0][u];
                show(u, v, lca);
                watchRows = [["u, v", `${u}, ${v}`], ["LCA = up[0][u]", lca, true]];
                put(8, `u dan v sekarang tepat di bawah LCA. Jawabannya parent mereka: <b>LCA = up[0][${u}] = ${lca}</b>.`, { cls: { [`0,${u - 1}`]: "path" } }, {
                    mark: "done",
                    ask: { type: "node", answer: lca, prompt: "Klik simpul yang merupakan LCA!", hint: "Parent dari u (dan juga dari v)." },
                });
            }
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            tv?.update(f);
            pseudo.set(f.line);
            watch.set(f.watch, f.masked);
        });
        view.onNodeClick((id) => player.answerNode(id));

        const refresh = () => {
            options(selU, qu);
            options(selV, qv);
        };
        selU.onchange = () => {
            qu = +selU.value;
            build();
        };
        selV.onchange = () => {
            qv = +selV.value;
            build();
        };
        sh.head.querySelector("[data-random]").onclick = () => {
            const n = V.rand(9, 13);
            const edges = [];
            for (let i = 2; i <= n; i++) edges.push({ u: V.rand(Math.max(1, i - 4), i - 1), v: i, w: 1 });
            tree = { n, edges };
            qu = V.rand(Math.ceil(n / 2), n);
            do qv = V.rand(2, n);
            while (qv === qu);
            refresh();
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            tree = { n: PRESETS.lca.n, edges: clone(PRESETS.lca.edges) };
            qu = 7;
            qv = 10;
            refresh();
            build();
        };
        refresh();
        build();
    });
})();
