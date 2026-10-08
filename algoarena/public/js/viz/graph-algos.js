/* Visualizer algoritma graph: representasi, BFS, DFS, Dijkstra, Topological Sort, MST (Kruskal) */
(() => {
    "use strict";

    const V = window.Viz;
    const { ek, esc } = V;

    // ─────────────── Graph preset dengan tata letak rapi ───────────────
    const P = (list) => [null, ...list.map(([x, y], i) => ({ id: i + 1, x, y }))];
    const E = (list) => list.map(([u, v, w = 1]) => ({ u, v, w }));

    const PRESETS = {
        repr: {
            n: 5,
            nodes: P([[120, 120], [320, 80], [520, 130], [190, 310], [440, 310]]),
            edges: E([[1, 2], [1, 4], [2, 3], [2, 5], [4, 5], [3, 5]]),
        },
        bfs: {
            n: 8,
            nodes: P([[80, 200], [210, 100], [210, 300], [350, 70], [350, 200], [350, 330], [480, 120], [570, 260]]),
            edges: E([[1, 2], [1, 3], [2, 4], [2, 5], [3, 5], [3, 6], [4, 7], [5, 7], [6, 8], [7, 8]]),
        },
        dfs: {
            n: 9,
            nodes: P([[80, 130], [200, 75], [220, 210], [90, 300], [330, 310], [430, 100], [570, 85], [510, 215], [570, 325]]),
            edges: E([[1, 2], [1, 3], [2, 3], [1, 4], [3, 5], [6, 7], [7, 8], [6, 8]]),
        },
        dijkstra: {
            n: 6,
            nodes: P([[70, 200], [230, 95], [230, 310], [410, 95], [410, 310], [570, 200]]),
            edges: E([[1, 2, 4], [1, 3, 2], [3, 2, 1], [2, 4, 5], [3, 5, 8], [3, 4, 8], [4, 5, 2], [4, 6, 6], [5, 6, 3]]),
            weighted: true,
        },
        topo: {
            n: 7,
            nodes: P([[70, 120], [70, 300], [230, 120], [230, 300], [400, 180], [400, 330], [570, 230]]),
            edges: E([[1, 3], [2, 3], [2, 4], [3, 5], [4, 5], [4, 6], [5, 7], [6, 7]]),
            directed: true,
        },
        mst: {
            n: 7,
            nodes: P([[80, 200], [200, 95], [210, 310], [340, 195], [460, 85], [470, 315], [580, 200]]),
            edges: E([[1, 2, 4], [1, 3, 3], [2, 3, 5], [2, 4, 6], [3, 4, 7], [2, 5, 9], [4, 5, 2], [4, 6, 8], [3, 6, 10], [5, 7, 5], [6, 7, 3], [4, 7, 6]]),
            weighted: true,
        },
    };

    const clone = (g) => JSON.parse(JSON.stringify(g));
    const preset = (name, extra = {}) => ({ directed: false, weighted: false, ...clone(PRESETS[name]), ...extra });

    /** Pencatat frame: menyimpan snapshot keadaan graph saat ini. */
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

    const startOptions = (n, sel) =>
        Array.from({ length: n }, (_, i) => `<option value="${i + 1}" ${i + 1 === sel ? "selected" : ""}>${i + 1}</option>`).join("");
    const fmtD = (d) => (d === Infinity ? "∞" : d);

    // ════════════════════════════ Representasi graph ════════════════════════════
    V.register("graph-repr", (root) => {
        const sh = V.shell(root, {
            title: "Membangun Adjacency List & Matrix",
            practice: false,
            controls: `
                ${V.segmented("mode", [["0", "Tak berarah"], ["1", "Berarah"]], "0")}
                <button class="btn btn-sm" data-random>🎲 Acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sisi yang sedang dicatat", "#22d3ee", "rgba(34,211,238,.15)"],
                ["Sisi yang sudah tercatat", "#8b5cf6"],
                ["Simpul terlibat", "#f59e0b"],
            ],
        });
        let directed = false;
        let graph = preset("repr");
        const view = V.graphView(sh.stage, {
            hint: "Seret simpul · pakai alat di kanan atas untuk menggambar graph sendiri",
            editable: true,
            onEdit: (g) => {
                graph = g;
                build();
            },
        });

        const pseudoLines = (dir) =>
            dir
                ? `
vector<vector<int>> adj(n + 1);                          //@0
vector<vector<int>> mat(n + 1, vector<int>(n + 1, 0));   //@1
for (int i = 0; i < m; i++) {                            //@2
    int u, v;
    cin >> u >> v;           // sisi berarah u -> v      //@2
    adj[u].push_back(v);                                 //@3
    mat[u][v] = 1;                                       //@4
}`
                : `
vector<vector<int>> adj(n + 1);                          //@0
vector<vector<int>> mat(n + 1, vector<int>(n + 1, 0));   //@1
for (int i = 0; i < m; i++) {                            //@2
    int u, v;
    cin >> u >> v;           // sisi dua arah u - v      //@2
    adj[u].push_back(v);                                 //@3
    adj[v].push_back(u);                                 //@4
    mat[u][v] = 1;                                       //@5
    mat[v][u] = 1;                                       //@6
}`;

        let pseudo;
        let lp;
        let mp;
        function rebuildSide() {
            sh.side.innerHTML = "";
            pseudo = V.codePanel(sh.side, pseudoLines(directed));
            lp = V.htmlPanel(sh.side, "Adjacency List", "O(N + M) memori");
            mp = V.htmlPanel(sh.side, "Adjacency Matrix", "O(N²) memori");
        }

        function renderList(n, adj, hotRows, fresh) {
            let h = '<div class="adj-list">';
            for (let i = 1; i <= n; i++) {
                h += `<div class="adj-row ${hotRows.includes(i) ? "hot" : ""}"><span class="head">${i}</span><span class="arrow">→</span>`;
                h += adj[i].map((v) => `<span class="nb ${fresh.has(`${i}:${v}`) ? "new" : ""}">${v}</span>`).join("") || '<span class="faint">∅</span>';
                h += "</div>";
            }
            return h + "</div>";
        }

        function renderMatrix(n, mat, fresh, hl) {
            let h = '<table class="matrix"><tr><th></th>';
            for (let j = 1; j <= n; j++) h += `<th>${j}</th>`;
            h += "</tr>";
            for (let i = 1; i <= n; i++) {
                h += `<tr><th>${i}</th>`;
                for (let j = 1; j <= n; j++) {
                    const k = `${i}:${j}`;
                    const cls = fresh.has(k) ? "new" : mat[i][j] ? "one" : "";
                    h += `<td class="${cls} ${hl.includes(i) || hl.includes(j) ? "hl" : ""}">${mat[i][j]}</td>`;
                }
                h += "</tr>";
            }
            return h + "</table>";
        }

        function build() {
            const g = graph;
            g.directed = directed;
            view.draw(g);
            rebuildSide();
            const frames = [];
            const adj = Array.from({ length: g.n + 1 }, () => []);
            const mat = Array.from({ length: g.n + 1 }, () => new Array(g.n + 1).fill(0));
            const done = {};
            const snap = (line, text, extra = {}) =>
                frames.push({
                    line,
                    text,
                    mark: extra.mark,
                    flow: extra.flow,
                    pulse: extra.pulse,
                    edges: { ...done, ...(extra.edges || {}) },
                    nodes: extra.nodes || {},
                    badges: extra.badges || {},
                    list: renderList(g.n, adj, extra.hot || [], extra.freshList || new Set()),
                    matrix: renderMatrix(g.n, mat, extra.freshMat || new Set(), extra.hot || []),
                });

            snap(0, `Graph ini punya <b>${g.n} simpul</b> dan <b>${g.edges.length} sisi</b>. Kita siapkan list kosong untuk setiap simpul.`);
            snap(1, `Siapkan juga matrix ${g.n}×${g.n} berisi 0. <code>mat[u][v] = 1</code> artinya ada sisi dari u ke v.`);

            for (const e of g.edges) {
                const key = ek(e.u, e.v, g.directed);
                const nodes = { [e.u]: "hl", [e.v]: "hl" };
                snap(2, `Ambil sisi <b>${e.u} ${g.directed ? "→" : "–"} ${e.v}</b>.`, { edges: { [key]: "active" }, nodes, hot: [e.u, e.v], flow: [e.u, e.v], mark: "key" });
                adj[e.u].push(e.v);
                snap(3, `Tambahkan <b>${e.v}</b> ke <code>adj[${e.u}]</code>.`, { edges: { [key]: "new" }, nodes, hot: [e.u], freshList: new Set([`${e.u}:${e.v}`]) });
                if (!g.directed) {
                    adj[e.v].push(e.u);
                    snap(4, `Karena tak berarah, tambahkan juga <b>${e.u}</b> ke <code>adj[${e.v}]</code>.`, {
                        edges: { [key]: "new" },
                        nodes,
                        hot: [e.v],
                        freshList: new Set([`${e.v}:${e.u}`]),
                        flow: [e.v, e.u],
                    });
                }
                mat[e.u][e.v] = 1;
                snap(g.directed ? 4 : 5, `Set <code>mat[${e.u}][${e.v}] = 1</code>.`, { edges: { [key]: "new" }, nodes, hot: [e.u, e.v], freshMat: new Set([`${e.u}:${e.v}`]) });
                if (!g.directed) {
                    mat[e.v][e.u] = 1;
                    snap(6, `Dan <code>mat[${e.v}][${e.u}] = 1</code>. Matrix graph tak berarah selalu <b>simetris</b>.`, {
                        edges: { [key]: "new" },
                        nodes,
                        hot: [e.u, e.v],
                        freshMat: new Set([`${e.v}:${e.u}`]),
                    });
                }
                done[key] = "tree";
            }

            const badges = {};
            for (let i = 1; i <= g.n; i++) badges[i] = { text: g.directed ? `out ${adj[i].length}` : `deg ${adj[i].length}`, cls: "final" };
            const total = adj.reduce((s, l) => s + l.length, 0);
            snap(
                -1,
                `Selesai! Adjacency list menyimpan <b>${total}</b> entri, sedangkan matrix menyimpan <b>${g.n * g.n}</b> sel (kebanyakan 0). Angka di atas simpul adalah ${g.directed ? "<b>out-degree</b>" : "<b>derajat</b>"} = panjang list-nya.`,
                { badges, mark: "done" },
            );
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            lp.set(f.list);
            mp.set(f.matrix);
        });

        V.bindSegmented(sh.head, "mode", (v) => {
            directed = v === "1";
            build();
        });
        sh.head.querySelector("[data-random]").onclick = () => {
            graph = V.randomGraph({ n: V.rand(4, 6), m: V.rand(5, 7), connected: true });
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            graph = preset("repr");
            build();
        };
        build();
    });

    // ════════════════════════════ BFS ════════════════════════════
    V.register("bfs", (root) => {
        const sh = V.shell(root, {
            title: "Breadth-First Search",
            controls: `
                <label class="viz-input">Mulai dari <select data-start></select></label>
                <button class="btn btn-sm" data-random>🎲 Graph acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Simpul awal", "#22c55e"],
                ["Di antrian", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Sedang diproses", "#f59e0b", "#f59e0b"],
                ["Selesai", "#a78bfa", "#5b3fc4"],
                ["Sisi pohon BFS", "#8b5cf6"],
            ],
        });
        let graph = preset("bfs");
        let start = 1;
        const select = sh.head.querySelector("[data-start]");
        const view = V.graphView(sh.stage, {
            hint: "Klik simpul untuk memilih titik awal · seret untuk merapikan",
            editable: true,
            onEdit: (g) => {
                graph = g;
                refreshStart();
                build();
            },
        });
        const pseudo = V.codePanel(
            sh.side,
            `
void bfs(int s) {                           //@0
    dist.assign(n + 1, INF);                //@1
    dist[s] = 0;                            //@1
    queue<int> q;
    q.push(s);                              //@2
    while (!q.empty()) {                    //@3
        int u = q.front();                  //@4
        q.pop();                            //@4
        for (int v : adj[u]) {              //@5
            if (dist[v] == INF) {           //@6
                dist[v] = dist[u] + 1;      //@7
                q.push(v);                  //@8
            }
        }
    }
}`,
        );
        const qPanel = V.dsPanel(sh.side, "Antrian (Queue)", "depan → belakang");
        const dPanel = V.arrayPanel(sh.side, "Array dist", "jarak dari s");
        const watch = V.watchPanel(sh.side);

        function refreshStart() {
            start = Math.min(start, graph.n);
            select.innerHTML = startOptions(graph.n, start);
        }

        function build() {
            const g = graph;
            const adj = V.adjacency(g);
            const rec = recorder();
            const dist = new Array(g.n + 1).fill(Infinity);
            const st = { nodes: {}, badges: {}, edges: {}, extra: {} };
            const queue = [];
            let watchRows = [];
            const sync = (changed, curr) => {
                st.extra = {
                    queue: queue.map((x, k) => ({ label: x, cls: x === changed ? "new" : k === 0 && !curr ? "front" : "" })),
                    dist: Array.from({ length: g.n }, (_, i) => ({ key: i + 1, val: fmtD(dist[i + 1]), cls: changed === i + 1 ? "changed" : "" })),
                    watch: watchRows,
                };
                if (curr) st.extra.queue.unshift({ label: curr, cls: "hot", sub: "diambil" });
                for (let i = 1; i <= g.n; i++) st.badges[i] = { text: fmtD(dist[i]), cls: i === changed ? "changed" : "" };
            };
            const mark = (id, cls) => (st.nodes[id] = (id === start ? "start " : "") + cls);

            for (let i = 1; i <= g.n; i++) mark(i, "");
            sync();
            rec.push(0, `Kita akan menjelajahi graph mulai dari simpul <b>${start}</b>, lapis demi lapis, seperti riak air.`, st);
            dist[start] = 0;
            sync(start);
            rec.push(1, `Semua jarak diisi <b>∞</b> (belum ditemukan), kecuali <code>dist[${start}] = 0</code>.`, st, { pulse: [start] });
            queue.push(start);
            mark(start, "queued");
            sync(start);
            rec.push(2, `Masukkan simpul <b>${start}</b> ke antrian.`, st);

            const order = [];
            while (queue.length) {
                watchRows = [["isi antrian", queue.join(", ")]];
                sync();
                rec.push(3, `Antrian masih berisi <b>${queue.length}</b> simpul, jadi lanjutkan.`, st);
                const u = queue.shift();
                order.push(u);
                mark(u, "current");
                watchRows = [
                    ["u", u],
                    ["dist[u]", dist[u]],
                ];
                sync(null, u);
                rec.push(4, `Ambil simpul terdepan: <b>${u}</b> (jarak ${dist[u]}). Semua tetangga barunya akan berjarak <b>${dist[u] + 1}</b>.`, st, {
                    mark: "key",
                    ask: {
                        type: "node",
                        answer: u,
                        prompt: "Simpul mana yang diambil dari <b>depan antrian</b> berikutnya?",
                        hint: "Antrian bersifat FIFO: yang pertama masuk, pertama keluar. Lihat simpul paling kiri di panel Antrian.",
                    },
                });
                for (const { v } of adj[u]) {
                    const key = ek(u, v, g.directed);
                    const prevEdge = st.edges[key];
                    st.edges[key] = "active";
                    watchRows = [
                        ["u", u],
                        ["v", v],
                        ["dist[u]", dist[u]],
                        ["dist[v]", fmtD(dist[v])],
                    ];
                    sync(null, u);
                    rec.push(5, `Periksa tetangga <b>${v}</b> melalui sisi ${u}–${v}.`, st, { flow: [u, v] });
                    if (dist[v] === Infinity) {
                        dist[v] = dist[u] + 1;
                        st.edges[key] = "tree";
                        mark(v, "queued");
                        queue.push(v);
                        watchRows = [
                            ["u", u],
                            ["v", v],
                            ["dist[u]", dist[u]],
                            ["dist[v]", dist[v], true],
                        ];
                        sync(v, u);
                        rec.push(7, `<code>dist[${v}]</code> masih ∞ → belum pernah ditemukan! Set <code>dist[${v}] = ${dist[u]} + 1 = ${dist[v]}</code> dan masukkan ke antrian.`, st, {
                            mark: "discover",
                            pulse: [v],
                            ask: {
                                type: "value",
                                answer: dist[v],
                                prompt: `Simpul <b>${v}</b> baru ditemukan dari simpul <b>${u}</b>. Berapa <code>dist[${v}]</code>?`,
                                hint: "dist[v] = dist[u] + 1",
                                maskBadge: v,
                                maskKey: v,
                                context: `Simpul <b>${u}</b> (jarak ${dist[u]}) menemukan tetangga baru <b>${v}</b>.`,
                            },
                        });
                    } else {
                        st.edges[key] = "skip";
                        sync(null, u);
                        rec.push(6, `Simpul <b>${v}</b> sudah ditemukan (dist = ${dist[v]}), jadi <b>lewati</b>. Pemeriksaan ini mencegah BFS berputar-putar.`, st, {
                            mark: "skip",
                        });
                        st.edges[key] = prevEdge || "";
                    }
                }
                mark(u, "done");
                watchRows = [];
                sync();
                rec.push(3, `Semua tetangga <b>${u}</b> sudah diperiksa. Simpul ${u} selesai.`, st);
            }

            // Warnai simpul berdasarkan lapis (jarak)
            st.colors = {};
            let maxD = 0;
            for (let i = 1; i <= g.n; i++) {
                st.nodes[i] = i === start ? "start" : "";
                if (dist[i] !== Infinity) {
                    st.colors[i] = V.LEVEL_COLORS[Math.min(dist[i], V.LEVEL_COLORS.length - 1)];
                    maxD = Math.max(maxD, dist[i]);
                }
            }
            const unreached = [];
            for (let i = 1; i <= g.n; i++) if (dist[i] === Infinity) unreached.push(i);
            const layers = Array.from({ length: maxD + 1 }, (_, d) => `lapis ${d}: {${order.filter((x) => dist[x] === d).join(", ")}}`).join(" · ");
            sync();
            rec.push(
                -1,
                `🎉 Antrian kosong, BFS selesai! Urutan kunjungan: <b>${order.join(" → ")}</b>. Warna simpul menunjukkan <b>lapis</b>: ${layers}.` +
                    (unreached.length ? ` Simpul <b>${unreached.join(", ")}</b> tidak terjangkau (tetap ∞).` : ""),
                st,
                { mark: "done" },
            );
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            qPanel.set(f.queue);
            dPanel.set(f.dist, f.masked ? f.ask?.maskKey : undefined);
            watch.set(f.watch, f.masked);
        });

        const reset = (g) => {
            graph = g;
            refreshStart();
            view.draw(g);
            build();
        };
        view.onNodeClick((id) => {
            if (player.answerNode(id)) return;
            start = id;
            select.value = id;
            build();
        });
        select.onchange = () => {
            start = +select.value;
            build();
        };
        sh.head.querySelector("[data-random]").onclick = () => reset(V.randomGraph({ n: V.rand(7, 9), m: V.rand(9, 12), connected: Math.random() > 0.25 }));
        sh.head.querySelector("[data-preset]").onclick = () => {
            start = 1;
            reset(preset("bfs"));
        };
        reset(graph);
    });

    // ════════════════════════════ DFS ════════════════════════════
    V.register("dfs", (root) => {
        const sh = V.shell(root, {
            title: "Depth-First Search",
            controls: `
                ${V.segmented("mode", [["single", "Dari satu simpul"], ["comp", "Hitung komponen"]], "single")}
                <label class="viz-input" data-start-wrap>Mulai <select data-start></select></label>
                <button class="btn btn-sm" data-random>🎲 Acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sedang dikunjungi", "#f59e0b", "#f59e0b"],
                ["Menunggu di call stack", "#a78bfa", "rgba(139,92,246,.32)"],
                ["Selesai (backtrack)", "#a78bfa", "#5b3fc4"],
                ["Sisi pohon DFS", "#8b5cf6"],
            ],
        });
        let mode = "single";
        let graph = preset("dfs");
        let start = 1;
        const select = sh.head.querySelector("[data-start]");
        const view = V.graphView(sh.stage, {
            hint: "Klik simpul untuk memilih titik awal · seret untuk merapikan",
            editable: true,
            onEdit: (g) => {
                graph = g;
                refreshStart();
                build();
            },
        });
        let pseudo;
        let stackPanel;
        let orderPanel;
        let compPanel;
        let watch;

        const LINES = {
            single: `
void dfs(int u) {                   //@0
    visited[u] = true;              //@1
    for (int v : adj[u])            //@2
        if (!visited[v])            //@3
            dfs(v);                 //@4
}   // selesai: kembali (backtrack) ke pemanggil   //@5`,
            comp: `
void dfs(int u) {                   //@6
    visited[u] = true;              //@7
    for (int v : adj[u])            //@8
        if (!visited[v]) dfs(v);    //@9
}

int komponen = 0;                   //@0
for (int s = 1; s <= n; s++)        //@1
    if (!visited[s]) {              //@2
        komponen++;                 //@3
        dfs(s);                     //@4
    }`,
        };

        function refreshStart() {
            start = Math.min(start, graph.n);
            select.innerHTML = startOptions(graph.n, start);
        }

        function rebuildSide() {
            sh.side.innerHTML = "";
            pseudo = V.codePanel(sh.side, LINES[mode]);
            stackPanel = V.dsPanel(sh.side, "Call Stack", "puncak di atas", { vertical: true });
            orderPanel = V.dsPanel(sh.side, "Urutan kunjungan");
            compPanel = mode === "comp" ? V.htmlPanel(sh.side, "Komponen ditemukan") : null;
            watch = V.watchPanel(sh.side);
            sh.head.querySelector("[data-start-wrap]").style.display = mode === "comp" ? "none" : "";
        }

        function build() {
            rebuildSide();
            const g = graph;
            const adj = V.adjacency(g);
            const rec = recorder();
            const visited = new Array(g.n + 1).fill(false);
            const stack = [];
            const order = [];
            const st = { nodes: {}, badges: {}, edges: {}, colors: {}, extra: {} };
            let comp = 0;
            let watchRows = [];
            const L = mode === "comp" ? { visit: 7, loop: 8, check: 9, call: 9, back: 9 } : { visit: 1, loop: 2, check: 3, call: 4, back: 5 };
            const sync = () => {
                st.extra = {
                    stack: stack.map((x, i) => ({ label: x, cls: i === stack.length - 1 ? "hot" : "", sub: i === stack.length - 1 ? "puncak" : undefined })),
                    order: order.map((x) => ({ label: x, cls: "done" })),
                    comp: comp
                        ? `<div class="counter-box"><div><b>${comp}</b><small>komponen</small></div><div><b>${order.length}</b><small>simpul dikunjungi</small></div></div>`
                        : '<span class="muted">Belum ada</span>',
                    watch: watchRows,
                };
            };
            const colorOf = () => V.COMP_COLORS[(comp - 1) % V.COMP_COLORS.length];

            function dfs(u, parent) {
                visited[u] = true;
                order.push(u);
                stack.push(u);
                st.nodes[u] = "current";
                st.badges[u] = { text: `#${order.length}` };
                if (mode === "comp") st.colors[u] = colorOf();
                watchRows = [
                    ["u", u],
                    ["kedalaman", stack.length - 1],
                ];
                sync();
                rec.push(L.visit, `Masuk ke <b>DFS(${u})</b> dan tandai sudah dikunjungi. Ini kunjungan ke-${order.length}.`, st, { mark: "discover", pulse: [u] });
                for (const { v } of adj[u]) {
                    const key = ek(u, v, g.directed);
                    if (v === parent && !g.directed) continue;
                    const prev = st.edges[key];
                    st.edges[key] = "active";
                    watchRows = [
                        ["u", u],
                        ["v", v],
                        ["visited[v]", visited[v] ? "ya" : "belum"],
                    ];
                    sync();
                    rec.push(L.loop, `Dari <b>${u}</b>, lihat tetangga <b>${v}</b>.`, st, {
                        flow: [u, v],
                        ask: visited[v]
                            ? undefined
                            : {
                                  type: "node",
                                  answer: v,
                                  prompt: `DFS sedang di simpul <b>${u}</b>. Ke tetangga mana DFS akan <b>menyelam</b> berikutnya?`,
                                  hint: "Tetangga diperiksa dari nomor terkecil. Tetangga yang sudah dikunjungi dilewati.",
                              },
                    });
                    if (!visited[v]) {
                        st.edges[key] = "tree";
                        st.nodes[u] = "visited";
                        sync();
                        rec.push(L.call, `<b>${v}</b> belum dikunjungi, jadi <b>menyelam</b>: panggil DFS(${v}). Simpul ${u} menunggu di call stack.`, st);
                        dfs(v, u);
                        st.nodes[u] = "current";
                        watchRows = [
                            ["u", u],
                            ["kembali dari", v],
                        ];
                        sync();
                        rec.push(L.loop, `Kembali ke <b>${u}</b> setelah DFS(${v}) selesai. Lanjut ke tetangga berikutnya.`, st, {
                            flow: [v, u],
                            flowCls: "back",
                            ask: {
                                type: "node",
                                answer: u,
                                prompt: `DFS(${v}) sudah selesai. DFS <b>kembali</b> ke simpul mana?`,
                                hint: "Lihat call stack: simpul di puncak sekarang adalah pemanggil DFS(" + v + ").",
                            },
                        });
                    } else {
                        st.edges[key] = "skip";
                        sync();
                        rec.push(L.check, `<b>${v}</b> sudah dikunjungi, jadi lewati.`, st, { mark: "skip" });
                        st.edges[key] = prev || "";
                    }
                }
                stack.pop();
                st.nodes[u] = "done";
                watchRows = [["selesai", u]];
                sync();
                rec.push(L.back, `Semua tetangga <b>${u}</b> sudah dijelajahi. <b>Backtrack</b>: keluarkan ${u} dari call stack.`, st, { mark: "done" });
            }

            for (let i = 1; i <= g.n; i++) st.nodes[i] = "";
            sync();
            if (mode === "single") {
                st.nodes[start] = "start";
                rec.push(0, `DFS selalu menyelam <b>sedalam mungkin</b> ke satu cabang sebelum mundur. Mulai dari simpul <b>${start}</b>.`, st);
                dfs(start, 0);
                const missed = [];
                for (let i = 1; i <= g.n; i++) if (!visited[i]) missed.push(i);
                watchRows = [];
                sync();
                rec.push(
                    -1,
                    `🎉 DFS selesai. Urutan kunjungan: <b>${order.join(" → ")}</b>. Angka #k di atas simpul adalah urutan kunjungannya.` +
                        (missed.length ? ` Simpul <b>${missed.join(", ")}</b> tidak tercapai karena berada di komponen lain. Coba mode <b>Hitung komponen</b>!` : ""),
                    st,
                    { mark: "done" },
                );
            } else {
                rec.push(0, "Untuk menghitung komponen, mulai DFS dari <b>setiap</b> simpul yang belum dikunjungi.", st);
                for (let s = 1; s <= g.n; s++) {
                    const before = st.nodes[s] || "";
                    st.nodes[s] = before + " hl";
                    watchRows = [
                        ["s", s],
                        ["visited[s]", visited[s] ? "ya" : "belum"],
                        ["komponen", comp],
                    ];
                    sync();
                    rec.push(1, `Periksa simpul <b>${s}</b>.`, st);
                    st.nodes[s] = before;
                    if (visited[s]) {
                        rec.push(2, `Simpul ${s} sudah dikunjungi (bagian dari komponen sebelumnya), jadi lewati.`, st);
                        continue;
                    }
                    comp++;
                    watchRows = [
                        ["s", s],
                        ["komponen", comp],
                    ];
                    sync();
                    rec.push(3, `Simpul <b>${s}</b> belum dikunjungi → ditemukan <b>komponen baru ke-${comp}</b>!`, st, { mark: "key" });
                    dfs(s, 0);
                }
                watchRows = [["komponen", comp]];
                sync();
                rec.push(-1, `🎉 Selesai! Graph ini terdiri dari <b>${comp} komponen terhubung</b>, masing-masing ditandai warna berbeda.`, st, { mark: "done" });
            }
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            stackPanel.set(f.stack);
            orderPanel.set(f.order);
            if (compPanel) compPanel.set(f.comp);
            watch.set(f.watch, f.masked);
        });

        const reset = (g) => {
            graph = g;
            refreshStart();
            view.draw(g);
            build();
        };
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            build();
        });
        view.onNodeClick((id) => {
            if (player.answerNode(id)) return;
            start = id;
            select.value = id;
            if (mode === "single") build();
        });
        select.onchange = () => {
            start = +select.value;
            build();
        };
        sh.head.querySelector("[data-random]").onclick = () => reset(V.randomGraph({ n: V.rand(8, 10), m: V.rand(6, 9), connected: false }));
        sh.head.querySelector("[data-preset]").onclick = () => {
            start = 1;
            reset(preset("dfs"));
        };
        reset(graph);
    });

    // ════════════════════════════ Dijkstra ════════════════════════════
    V.register("dijkstra", (root) => {
        const sh = V.shell(root, {
            title: "Algoritma Dijkstra",
            controls: `
                <label class="viz-input">Sumber <select data-start></select></label>
                <button class="btn btn-sm" data-random>🎲 Graph acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Diambil dari PQ", "#f59e0b", "#f59e0b"],
                ["Di priority queue", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Jarak sudah final", "#a78bfa", "#5b3fc4"],
                ["Sisi jalur terpendek", "#8b5cf6"],
                ["Jarak berubah", "#22d3ee"],
            ],
        });
        let graph = preset("dijkstra");
        let start = 1;
        const select = sh.head.querySelector("[data-start]");
        const view = V.graphView(sh.stage, {
            hint: "Klik simpul untuk memilih sumber · seret untuk merapikan",
            editable: true,
            onEdit: (g) => {
                graph = g;
                refreshStart();
                build();
            },
        });
        const pseudo = V.codePanel(
            sh.side,
            `
typedef pair<long long, int> pli;   // (jarak, simpul)

void dijkstra(int s) {                                  //@0
    dist.assign(n + 1, INF);                            //@1
    dist[s] = 0;                                        //@1
    priority_queue<pli, vector<pli>, greater<pli>> pq;  // terkecil di atas
    pq.push({0, s});                                    //@2
    while (!pq.empty()) {                               //@3
        long long d = pq.top().first;                   //@4
        int u = pq.top().second;                        //@4
        pq.pop();                                       //@4
        if (d > dist[u]) continue;   // data basi       //@5
        for (auto& e : adj[u]) {                        //@6
            int v = e.first;                            //@6
            long long w = e.second;                     //@6
            if (dist[u] + w < dist[v]) {                //@7
                dist[v] = dist[u] + w;                  //@8
                prev[v] = u;                            //@8
                pq.push({dist[v], v});                  //@9
            }
        }
    }
}`,
        );
        const pqPanel = V.dsPanel(sh.side, "Priority Queue", "terkecil di kiri");
        const dPanel = V.arrayPanel(sh.side, "Array dist");
        const watch = V.watchPanel(sh.side);

        function refreshStart() {
            start = Math.min(start, graph.n);
            select.innerHTML = startOptions(graph.n, start);
        }

        function build() {
            const g = graph;
            const adj = V.adjacency(g);
            const rec = recorder();
            const dist = new Array(g.n + 1).fill(Infinity);
            const prev = new Array(g.n + 1).fill(0);
            const final = new Array(g.n + 1).fill(false);
            const pq = [];
            const st = { nodes: {}, badges: {}, edges: {}, extra: {} };
            let watchRows = [];
            const sortPq = () => pq.sort((a, b) => a[0] - b[0] || a[1] - b[1]);
            const sync = (changed, popped) => {
                const maxD = Math.max(1, ...pq.map(([d]) => d), popped ? popped[0] : 0);
                const items = pq.map(([d, v]) => ({ label: v, sub: `d=${d}`, bar: d / maxD, cls: changed === v && d === dist[v] ? "new" : "" }));
                if (popped) items.unshift({ label: popped[1], sub: `d=${popped[0]}`, cls: "hot", bar: popped[0] / maxD });
                st.extra = {
                    pq: items,
                    dist: Array.from({ length: g.n }, (_, i) => ({
                        key: i + 1,
                        val: fmtD(dist[i + 1]),
                        cls: changed === i + 1 ? "changed" : final[i + 1] ? "final" : "",
                    })),
                    watch: watchRows,
                };
                for (let i = 1; i <= g.n; i++) st.badges[i] = { text: fmtD(dist[i]), cls: i === changed ? "changed" : final[i] ? "final" : "" };
            };
            const base = (id) => (id === start ? "start " : "");

            for (let i = 1; i <= g.n; i++) st.nodes[i] = base(i);
            sync();
            rec.push(0, `Cari jarak terpendek dari <b>${start}</b> ke semua simpul. Angka di tengah sisi adalah <b>bobot</b> (biaya) sisi tersebut.`, st);
            dist[start] = 0;
            sync(start);
            rec.push(1, `Semua jarak awalnya ∞, kecuali <code>dist[${start}] = 0</code>.`, st, { pulse: [start] });
            pq.push([0, start]);
            st.nodes[start] = base(start) + "queued";
            sync(start);
            rec.push(2, `Masukkan <code>(0, ${start})</code> ke priority queue. PQ selalu mengeluarkan jarak <b>terkecil</b> lebih dulu.`, st);

            while (pq.length) {
                sortPq();
                watchRows = [["isi PQ", pq.map(([d, v]) => `(${d},${v})`).join(" ")]];
                sync();
                rec.push(3, `PQ berisi ${pq.length} entri.`, st);
                const [d, u] = pq.shift();
                st.nodes[u] = base(u) + "current";
                watchRows = [
                    ["u", u],
                    ["d", d],
                    ["dist[u]", fmtD(dist[u])],
                ];
                sync(null, [d, u]);
                rec.push(4, `Ambil entri terkecil: simpul <b>${u}</b> dengan jarak <b>${d}</b>.`, st, {
                    mark: "key",
                    ask: {
                        type: "node",
                        answer: u,
                        prompt: "Simpul mana yang keluar dari <b>priority queue</b> berikutnya?",
                        hint: "PQ selalu mengeluarkan jarak terkecil (jika sama, nomor simpul terkecil). Lihat panel Priority Queue.",
                    },
                });
                if (d > dist[u]) {
                    st.nodes[u] = base(u) + "done";
                    sync();
                    rec.push(5, `Entri ini <b>usang</b>: d = ${d} > dist[${u}] = ${dist[u]} (sudah ada jalur lebih murah). Lewati!`, st, { mark: "skip" });
                    continue;
                }
                final[u] = true;
                sync(null, [d, u]);
                rec.push(5, `d = dist[${u}] = ${d}, jadi jarak ke <b>${u}</b> sekarang <b>final</b>. Tidak mungkin ada jalur lebih murah, karena semua bobot positif.`, st);
                for (const { v, w } of adj[u]) {
                    const key = ek(u, v, g.directed);
                    const prevCls = st.edges[key];
                    st.edges[key] = "active";
                    const cand = dist[u] + w;
                    // Sengaja tidak menampilkan dist[u] + w di sini: itulah yang ditanyakan di Mode Tebak.
                    watchRows = [
                        ["u", u],
                        ["v", v],
                        ["w", w],
                        ["dist[u]", dist[u]],
                        ["dist[v]", fmtD(dist[v])],
                    ];
                    sync(null, [d, u]);
                    rec.push(6, `Periksa sisi <b>${u} → ${v}</b> dengan bobot <b>${w}</b>.`, st, { flow: [u, v] });
                    if (cand < dist[v]) {
                        const old = dist[v];
                        if (prev[v]) st.edges[ek(prev[v], v, g.directed)] = "";
                        dist[v] = cand;
                        prev[v] = u;
                        st.edges[key] = "tree";
                        watchRows = [
                            ["u", u],
                            ["v", v],
                            ["w", w],
                            ["dist[v] lama", fmtD(old)],
                            ["dist[v] baru", cand, true],
                        ];
                        sync(v, [d, u]);
                        rec.push(8, `<b>Relaksasi!</b> ${dist[u]} + ${w} = <b>${cand}</b> &lt; ${fmtD(old)}. Perbarui <code>dist[${v}] = ${cand}</code> dan <code>prev[${v}] = ${u}</code>.`, st, {
                            mark: "relax",
                            pulse: [v],
                            ask: {
                                type: "value",
                                answer: cand,
                                prompt: `dist[${u}] = ${dist[u]}, bobot ${u}→${v} = ${w}, dan dist[${v}] lama = ${fmtD(old)}. Berapa <code>dist[${v}]</code> sekarang?`,
                                hint: "Jika dist[u] + w lebih kecil dari dist[v] lama, dist[v] menjadi dist[u] + w.",
                                maskBadge: v,
                                maskKey: v,
                                context: `Relaksasi sisi <b>${u} → ${v}</b>.`,
                            },
                        });
                        pq.push([cand, v]);
                        sortPq();
                        if (!final[v]) st.nodes[v] = base(v) + "queued";
                        watchRows = [["masuk PQ", `(${cand}, ${v})`]];
                        sync(v, [d, u]);
                        rec.push(9, `Masukkan <code>(${cand}, ${v})</code> ke PQ.`, st);
                    } else {
                        st.edges[key] = "skip";
                        sync(null, [d, u]);
                        rec.push(7, `${dist[u]} + ${w} = ${cand} ≥ dist[${v}] = ${dist[v]}. Tidak lebih baik, jadi biarkan.`, st, { mark: "skip" });
                        st.edges[key] = prevCls || "";
                    }
                }
                st.nodes[u] = base(u) + "done";
                watchRows = [];
                sync();
                rec.push(3, `Simpul <b>${u}</b> selesai diproses.`, st);
            }

            for (let v = 1; v <= g.n; v++) if (prev[v]) st.edges[ek(prev[v], v, g.directed)] = "path";
            watchRows = [];
            sync();
            const summary = Array.from({ length: g.n }, (_, i) => `${i + 1}: ${fmtD(dist[i + 1])}`).join(", ");
            rec.push(
                -1,
                `🎉 PQ kosong, selesai! Jarak terpendek → <b>${summary}</b>. Garis hijau membentuk <b>pohon jalur terpendek</b>; ikuti <code>prev</code> mundur untuk merekonstruksi rute.`,
                st,
                { mark: "done" },
            );
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            pqPanel.set(f.pq);
            dPanel.set(f.dist, f.masked ? f.ask?.maskKey : undefined);
            watch.set(f.watch, f.masked);
        });

        const reset = (g) => {
            graph = g;
            refreshStart();
            view.draw(g);
            build();
        };
        view.onNodeClick((id) => {
            if (player.answerNode(id)) return;
            start = id;
            select.value = id;
            build();
        });
        select.onchange = () => {
            start = +select.value;
            build();
        };
        sh.head.querySelector("[data-random]").onclick = () =>
            reset(V.randomGraph({ n: V.rand(6, 7), m: V.rand(8, 10), weighted: true, connected: true }));
        sh.head.querySelector("[data-preset]").onclick = () => {
            start = 1;
            reset(preset("dijkstra"));
        };
        reset(graph);
    });

    // ════════════════════════════ Topological sort (Kahn) ════════════════════════════
    V.register("toposort", (root) => {
        const sh = V.shell(root, {
            title: "Topological Sort: Algoritma Kahn",
            controls: `
                <button class="btn btn-sm" data-random>🎲 DAG acak</button>
                <button class="btn btn-sm" data-cycle>🔁 Tambah siklus</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Indegree 0 (di antrian)", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Sedang diproses", "#f59e0b", "#f59e0b"],
                ["Sudah masuk urutan", "#a78bfa", "#5b3fc4"],
                ["Sisi yang dihapus", "#8b5cf6"],
            ],
        });
        let graph = preset("topo");
        const view = V.graphView(sh.stage, {
            hint: "Seret simpul · gunakan alat di kanan atas untuk membuat graph berarah sendiri",
            editable: true,
            onEdit: (g) => {
                graph = g;
                build();
            },
        });
        const pseudo = V.codePanel(
            sh.side,
            `
vector<int> indeg(n + 1, 0);
for (int u = 1; u <= n; u++)                  //@0
    for (int v : adj[u]) indeg[v]++;          //@0
queue<int> q;
for (int v = 1; v <= n; v++)                  //@1
    if (indeg[v] == 0) q.push(v);             //@1
vector<int> hasil;
while (!q.empty()) {                          //@2
    int u = q.front();                        //@3
    q.pop();                                  //@3
    hasil.push_back(u);                       //@3
    for (int v : adj[u]) {                    //@4
        indeg[v]--;                           //@5
        if (indeg[v] == 0) q.push(v);         //@6
    }
}
if ((int)hasil.size() < n)                    //@7
    cout << "ada siklus!\\n";                //@7`,
        );
        const inPanel = V.arrayPanel(sh.side, "Indegree", "banyak sisi masuk");
        const qPanel = V.dsPanel(sh.side, "Antrian");
        const resPanel = V.dsPanel(sh.side, "Hasil urutan");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            const adj = V.adjacency(g);
            const rec = recorder();
            const indeg = new Array(g.n + 1).fill(0);
            const queue = [];
            const result = [];
            const st = { nodes: {}, badges: {}, edges: {}, extra: {} };
            let watchRows = [];
            const sync = (changed, curr) => {
                st.extra = {
                    indeg: Array.from({ length: g.n }, (_, i) => ({
                        key: i + 1,
                        val: indeg[i + 1],
                        cls: changed === i + 1 ? "changed" : result.includes(i + 1) ? "final" : "",
                    })),
                    queue: (curr ? [{ label: curr, cls: "hot", sub: "diambil" }] : []).concat(queue.map((x) => ({ label: x, cls: x === changed ? "new" : "" }))),
                    result: result.map((x) => ({ label: x, cls: "done" })),
                    watch: watchRows,
                };
                for (let i = 1; i <= g.n; i++) st.badges[i] = { text: `in ${indeg[i]}`, cls: i === changed ? "changed" : "" };
            };

            for (let i = 1; i <= g.n; i++) st.nodes[i] = "";
            sync();
            rec.push(-1, "Setiap panah <b>u → v</b> berarti u harus dikerjakan <b>sebelum</b> v. Kita cari urutan yang menghormati semua panah.", st);
            for (const e of g.edges) indeg[e.v]++;
            sync();
            rec.push(0, "Hitung <b>indegree</b> (banyak panah masuk) setiap simpul. Simpul ber-indegree 0 tidak punya prasyarat.", st);
            for (let i = 1; i <= g.n; i++) {
                if (indeg[i] === 0) {
                    queue.push(i);
                    st.nodes[i] = "queued";
                }
            }
            sync();
            rec.push(1, `Simpul tanpa prasyarat: <b>${queue.join(", ") || "tidak ada!"}</b>. Masukkan semuanya ke antrian.`, st, { pulse: [...queue] });

            while (queue.length) {
                const u = queue.shift();
                result.push(u);
                st.nodes[u] = "current";
                watchRows = [
                    ["u", u],
                    ["hasil", result.join(" → ")],
                ];
                sync(null, u);
                rec.push(3, `Ambil <b>${u}</b> dari antrian dan tambahkan ke hasil. Urutan sejauh ini: ${result.join(" → ")}.`, st, {
                    mark: "key",
                    ask: {
                        type: "node",
                        answer: u,
                        prompt: "Simpul mana yang diambil dari antrian berikutnya?",
                        hint: "Antrian bersifat FIFO. Lihat simpul paling kiri di panel Antrian.",
                    },
                });
                for (const { v } of adj[u]) {
                    const key = ek(u, v, true);
                    st.edges[key] = "active";
                    watchRows = [
                        ["u", u],
                        ["v", v],
                        ["indeg[v]", indeg[v]],
                    ];
                    sync(null, u);
                    rec.push(4, `Sisi <b>${u} → ${v}</b>: karena ${u} sudah dikerjakan, satu prasyarat ${v} terpenuhi.`, st, { flow: [u, v] });
                    indeg[v]--;
                    st.edges[key] = "tree dim";
                    watchRows = [
                        ["u", u],
                        ["v", v],
                        ["indeg[v]", indeg[v], true],
                    ];
                    sync(v, u);
                    rec.push(5, `Kurangi <code>indeg[${v}]</code> menjadi <b>${indeg[v]}</b>.`, st, {
                        ask: {
                            type: "value",
                            answer: indeg[v],
                            prompt: `Sisi ${u} → ${v} dihapus. Berapa <code>indeg[${v}]</code> sekarang?`,
                            hint: "Indegree berkurang satu setiap kali satu prasyaratnya selesai.",
                            maskBadge: v,
                            maskKey: v,
                            context: `Menghapus sisi <b>${u} → ${v}</b>.`,
                        },
                    });
                    if (indeg[v] === 0) {
                        queue.push(v);
                        st.nodes[v] = "queued";
                        sync(v, u);
                        rec.push(6, `<code>indeg[${v}] = 0</code>: semua prasyarat ${v} beres! Masukkan ke antrian.`, st, { mark: "discover", pulse: [v] });
                    }
                }
                st.nodes[u] = "done";
                watchRows = [];
                sync();
                rec.push(2, `Simpul ${u} selesai.`, st);
            }

            if (result.length < g.n) {
                const stuck = [];
                for (let i = 1; i <= g.n; i++) {
                    if (!result.includes(i)) {
                        stuck.push(i);
                        st.nodes[i] = "hl";
                    }
                }
                sync();
                rec.push(
                    7,
                    `⚠️ Antrian kosong, tapi baru <b>${result.length}</b> dari ${g.n} simpul yang terurut. Simpul <b>${stuck.join(", ")}</b> saling menunggu, berarti ada <b>siklus</b> dan urutan topologis <b>mustahil</b>.`,
                    st,
                    { mark: "skip" },
                );
            } else {
                sync();
                rec.push(7, `🎉 Semua ${g.n} simpul terurut: <b>${result.join(" → ")}</b>. Tidak ada siklus, jadi graph ini adalah DAG.`, st, { mark: "done" });
            }
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            inPanel.set(f.indeg, f.masked ? f.ask?.maskKey : undefined);
            qPanel.set(f.queue);
            resPanel.set(f.result);
            watch.set(f.watch, f.masked);
        });

        const reset = (g) => {
            graph = g;
            view.draw(g);
            build();
        };
        view.onNodeClick((id) => player.answerNode(id));
        sh.head.querySelector("[data-random]").onclick = () => reset(V.randomGraph({ n: V.rand(6, 8), m: V.rand(7, 10), dag: true, connected: true }));
        sh.head.querySelector("[data-preset]").onclick = () => reset(preset("topo"));
        sh.head.querySelector("[data-cycle]").onclick = () => {
            const g = clone(graph);
            const has = new Set(g.edges.map((e) => `${e.u}>${e.v}`));
            const indeg = {};
            g.edges.forEach((e) => (indeg[e.v] = (indeg[e.v] || 0) + 1));
            // Utamakan siklus di "tengah" graph agar sebagian simpul tetap terproses sebelum macet
            const ok = (e) => !has.has(`${e.v}>${e.u}`) && g.edges.some((f) => f.u === e.v);
            const cand = g.edges.find((e) => ok(e) && indeg[e.u] > 0) || g.edges.find(ok);
            if (!cand) return;
            const next = g.edges.find((f) => f.u === cand.v);
            g.edges.push({ u: next.v, v: cand.u, w: 1 });
            reset(g);
        };
        reset(graph);
    });

    // ════════════════════════════ Minimum Spanning Tree (Kruskal + DSU) ════════════════════════════
    V.register("mst", (root) => {
        const sh = V.shell(root, {
            title: "Minimum Spanning Tree: Algoritma Kruskal",
            controls: `
                <button class="btn btn-sm" data-random>🎲 Graph acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sisi sedang diperiksa", "#f59e0b"],
                ["Sisi masuk MST", "#22c55e"],
                ["Ditolak (membentuk siklus)", "#ef4444"],
                ["Warna simpul = komponen DSU", "#a78bfa", "rgba(139,92,246,.35)"],
            ],
        });
        let graph = preset("mst");
        const view = V.graphView(sh.stage, {
            hint: "Seret simpul · gunakan alat di kanan atas untuk mengubah graph dan bobot",
            editable: true,
            onEdit: (g) => {
                graph = g;
                build();
            },
        });
        const pseudo = V.codePanel(
            sh.side,
            `
// sisi[i] = {w, u, v}
sort(sisi.begin(), sisi.end());   // bobot kecil dulu   //@0
for (int v = 1; v <= n; v++) parent[v] = v;               //@1
long long total = 0;
int dipakai = 0;
for (auto& e : sisi) {                                    //@2
    if (find(e[1]) != find(e[2])) {                       //@3
        unite(e[1], e[2]);                                //@4
        total += e[0];                                    //@4
        dipakai++;                                        //@4
    } else {                                              //@5
        continue;   // ditolak: akan membentuk siklus     //@5
    }
    if (dipakai == n - 1) break;   // MST lengkap         //@6
}`,
        );
        const edgePanel = V.htmlPanel(sh.side, "Sisi terurut", "bobot kecil → besar");
        const compPanel = V.htmlPanel(sh.side, "Komponen (Union-Find)");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            g.weighted = true;
            view.draw(g);
            const rec = recorder();
            const n = g.n;
            const parent = Array.from({ length: n + 1 }, (_, i) => i);
            const size = new Array(n + 1).fill(1);
            const colorIdx = {};
            let nextColor = 0;
            const find = (x) => {
                while (parent[x] !== x) {
                    parent[x] = parent[parent[x]];
                    x = parent[x];
                }
                return x;
            };
            const sorted = g.edges.map((e) => ({ ...e })).sort((a, b) => a.w - b.w || a.u - b.u || a.v - b.v);
            const status = sorted.map(() => "pending");
            const st = { nodes: {}, badges: {}, edges: {}, colors: {}, extra: {} };
            let watchRows = [];
            let total = 0;
            let taken = 0;

            const recolor = () => {
                st.colors = {};
                for (let v = 1; v <= n; v++) {
                    const r = find(v);
                    if (size[r] > 1) st.colors[v] = V.COMP_COLORS[colorIdx[r] % V.COMP_COLORS.length];
                }
            };
            const edgeHtml = (cur) =>
                `<div class="edge-list">${sorted
                    .map((e, i) => {
                        const s = i === cur ? "current" : status[i];
                        const icon = { take: "✓", reject: "✗", current: "?", pending: "" }[s] || "";
                        return `<div class="edge-row ${s}"><span>${e.u}–${e.v}</span><b>${e.w}</b><i>${icon}</i></div>`;
                    })
                    .join("")}</div>`;
            const compHtml = () => {
                const groups = {};
                for (let v = 1; v <= n; v++) (groups[find(v)] ||= []).push(v);
                const list = Object.entries(groups).sort((a, b) => a[1][0] - b[1][0]);
                return `<div class="comp-groups">${list
                    .map(([r, members]) => {
                        const col = size[r] > 1 ? V.COMP_COLORS[colorIdx[r] % V.COMP_COLORS.length] : ["#1a2030", "#3f4a64"];
                        return `<span class="comp-chip" style="background:${col[0]};border-color:${col[1]}">{${members.join(", ")}}</span>`;
                    })
                    .join("")}</div><div class="comp-count">${list.length} komponen</div>`;
            };
            const sync = (cur = -1) => {
                recolor();
                st.extra = { edgeList: edgeHtml(cur), comps: compHtml(), watch: watchRows };
            };

            for (let v = 1; v <= n; v++) st.nodes[v] = "";
            sync();
            rec.push(-1, `Hubungkan semua <b>${n} simpul</b> dengan total bobot <b>sekecil mungkin</b>, tanpa membentuk siklus. Hasilnya pohon dengan tepat N − 1 = ${n - 1} sisi.`, st);
            rec.push(0, `Urutkan semua ${sorted.length} sisi dari bobot terkecil (lihat panel <b>Sisi terurut</b>). Kruskal itu serakah: selalu coba sisi termurah dulu.`, st, { mark: "key" });
            rec.push(1, "Awalnya setiap simpul adalah <b>komponen sendiri</b>. Struktur <b>Union-Find (DSU)</b> akan mencatat simpul mana yang sudah tersambung.", st);

            for (let i = 0; i < sorted.length; i++) {
                if (taken === n - 1) break;
                const e = sorted[i];
                const key = ek(e.u, e.v, false);
                const ru = find(e.u);
                const rv = find(e.v);
                st.edges[key] = "active";
                st.nodes[e.u] = "hl";
                st.nodes[e.v] = "hl";
                watchRows = [
                    ["sisi", `${e.u}–${e.v} (w = ${e.w})`],
                    ["sisi diambil", `${taken} dari ${n - 1}`],
                    ["total", total],
                ];
                sync(i);
                rec.push(2, `Sisi termurah berikutnya: <b>${e.u}–${e.v}</b> dengan bobot <b>${e.w}</b>. Cek apakah ${e.u} dan ${e.v} sudah satu komponen.`, st, {
                    flow: [e.u, e.v],
                });
                st.nodes[e.u] = "";
                st.nodes[e.v] = "";
                if (ru !== rv) {
                    if (size[ru] < size[rv]) {
                        parent[ru] = rv;
                        size[rv] += size[ru];
                        colorIdx[rv] = colorIdx[rv] ?? colorIdx[ru] ?? nextColor++;
                    } else {
                        parent[rv] = ru;
                        size[ru] += size[rv];
                        colorIdx[ru] = colorIdx[ru] ?? colorIdx[rv] ?? nextColor++;
                    }
                    status[i] = "take";
                    total += e.w;
                    taken++;
                    st.edges[key] = "path";
                    watchRows = [
                        ["sisi", `${e.u}–${e.v} (w = ${e.w})`],
                        [`find(${e.u}) / find(${e.v})`, `${ru} / ${rv}`],
                        ["sisi diambil", `${taken} dari ${n - 1}`],
                        ["total", total],
                    ];
                    sync(-1);
                    rec.push(4, `find(${e.u}) = ${ru} ≠ find(${e.v}) = ${rv}: beda komponen, jadi <b>ambil</b> sisi ini dan gabungkan (union) kedua komponen. Total = <b>${total}</b>.`, st, {
                        mark: "take",
                        pulse: [e.u, e.v],
                        ask: {
                            type: "choice",
                            options: ["✅ Ambil", "⛔ Tolak (membentuk siklus)"],
                            answer: 0,
                            prompt: `Sisi <b>${e.u}–${e.v}</b> (bobot ${e.w}): ambil atau tolak?`,
                            hint: "Tolak hanya jika kedua simpul sudah satu komponen (warnanya sama).",
                        },
                    });
                } else {
                    status[i] = "reject";
                    st.edges[key] = "skip";
                    watchRows = [
                        ["sisi", `${e.u}–${e.v} (w = ${e.w})`],
                        ["find(u) = find(v)", ru],
                        ["total", total],
                    ];
                    sync(-1);
                    rec.push(5, `${e.u} dan ${e.v} sudah satu komponen (akar ${ru}). Menambah sisi ini akan membentuk <b>siklus</b>, jadi <b>tolak</b>.`, st, {
                        mark: "skip",
                        ask: {
                            type: "choice",
                            options: ["✅ Ambil", "⛔ Tolak (membentuk siklus)"],
                            answer: 1,
                            prompt: `Sisi <b>${e.u}–${e.v}</b> (bobot ${e.w}): ambil atau tolak?`,
                            hint: "Perhatikan warna simpul: warna sama berarti sudah satu komponen.",
                        },
                    });
                    st.edges[key] = "skip dim";
                }
                if (taken === n - 1) {
                    sync(-1);
                    rec.push(6, `Sudah <b>${taken} = N − 1</b> sisi. Semua simpul tersambung, jadi Kruskal <b>berhenti</b> lebih awal.`, st, { mark: "done" });
                }
            }

            sorted.forEach((e, i) => {
                const key = ek(e.u, e.v, false);
                if (status[i] !== "take") st.edges[key] = "dim";
            });
            watchRows = [
                ["sisi MST", `${taken}`],
                ["total bobot", total, true],
            ];
            sync(-1);
            const connected = taken === n - 1;
            rec.push(
                -1,
                connected
                    ? `🎉 Minimum Spanning Tree terbentuk dari <b>${taken} sisi hijau</b> dengan total bobot <b>${total}</b>. Tidak ada cara lain menghubungkan semua simpul dengan biaya lebih kecil.`
                    : `Graph ini <b>tidak terhubung</b>, jadi yang terbentuk adalah <i>spanning forest</i> dengan ${taken} sisi (total ${total}).`,
                st,
                {
                    mark: "done",
                    ask: connected
                        ? {
                              type: "value",
                              answer: total,
                              prompt: "Berapa <b>total bobot</b> Minimum Spanning Tree-nya?",
                              hint: "Jumlahkan bobot semua sisi yang diambil (lihat tanda ✓ di panel Sisi terurut).",
                              context: "Semua sisi MST sudah dipilih.",
                          }
                        : undefined,
                },
            );
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            edgePanel.set(f.edgeList);
            compPanel.set(f.comps);
            watch.set(f.watch, f.masked);
        });

        view.onNodeClick((id) => player.answerNode(id));
        sh.head.querySelector("[data-random]").onclick = () => {
            graph = V.randomGraph({ n: V.rand(6, 8), m: V.rand(9, 12), weighted: true, connected: true });
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            graph = preset("mst");
            build();
        };
        build();
    });
})();
