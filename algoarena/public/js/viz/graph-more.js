/* Visualizer graph lanjutan: pohon (subtree & diameter), BFS 0-1, SCC (Kosaraju), jembatan & titik artikulasi */
(() => {
    "use strict";

    const V = window.Viz;
    const { ek } = V;

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

    const randomTree = (n) => {
        const edges = [];
        for (let v = 2; v <= n; v++) edges.push([V.rand(Math.max(1, v - 3), v - 1), v]);
        return edges;
    };

    // ════════════════════════════ Pohon: ukuran subtree & diameter ════════════════════════════
    V.register("tree", (root) => {
        const sh = V.shell(root, {
            title: "Pohon: DFS dari Akar & Diameter",
            controls: `
                ${V.segmented("mode", [["sub", "Kedalaman & subtree"], ["diam", "Diameter (2× BFS)"]], "sub")}
                <button class="btn btn-sm" data-random>Pohon acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sedang diproses", "#f59e0b", "#f59e0b"],
                ["Di call stack / antrian", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Selesai", "#a78bfa", "#5b3fc4"],
                ["Jalur diameter", "#22c55e"],
            ],
        });
        const PRESET = [[1, 2], [1, 3], [2, 4], [2, 5], [3, 6], [5, 7], [5, 8], [6, 9]];
        let edges = PRESET;
        let n = 9;
        let mode = "sub";
        const view = V.graphView(sh.stage, { hint: "Akar = simpul 1 (paling atas) · seret simpul untuk merapikan" });
        let pseudo;
        let arr;
        let watch;

        const CODE = {
            sub: `
// panggil: depth[1] = 0; dfs(1, 0);
int dfs(int u, int p) {             //@0
    parent[u] = p;                  //@1
    sz[u] = 1;   // dirinya sendiri //@1
    for (int v : adj[u]) {          //@2
        if (v == p) continue;       //@3
        depth[v] = depth[u] + 1;    //@4
        sz[u] += dfs(v, u);         //@5
    }
    return sz[u];                   //@6
}`,
            diam: `
// BFS dari s, kembalikan simpul TERJAUH
int terjauh(int s) {                       //@0
    dist.assign(n + 1, -1);                //@1
    dist[s] = 0;                           //@1
    queue<int> q;
    q.push(s);
    int far = s;
    while (!q.empty()) {                   //@2
        int u = q.front(); q.pop();        //@2
        if (dist[u] > dist[far]) far = u;  //@3
        for (int v : adj[u])               //@4
            if (dist[v] == -1) {           //@4
                dist[v] = dist[u] + 1;     //@5
                q.push(v);                 //@5
            }
    }
    return far;                            //@6
}

int a = terjauh(1);       // ujung pertama  //@7
int b = terjauh(a);       // ujung kedua    //@8
int diameter = dist[b];                    //@9`,
        };

        function setupSide() {
            sh.side.innerHTML = "";
            pseudo = V.codePanel(sh.side, CODE[mode]);
            arr = V.arrayPanel(sh.side, mode === "sub" ? "Ukuran subtree sz[v]" : "Array dist", mode === "sub" ? "terisi saat DFS kembali" : "jarak dari titik awal BFS");
            watch = V.watchPanel(sh.side);
        }

        function build() {
            setupSide();
            const g = V.graphFromEdges(n, edges, { tree: true, root: 1 });
            view.draw(g);
            const adj = V.adjacency(g);
            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, extra: {} };

            if (mode === "sub") {
                const sz = new Array(n + 1).fill(null);
                const depth = new Array(n + 1).fill(null);
                const stack = [];
                const sync = (w = []) => {
                    st.extra = {
                        arr: Array.from({ length: n }, (_, i) => ({ key: i + 1, val: sz[i + 1] ?? "–" })),
                        watch: [["call stack", stack.join(" → ") || "kosong"], ...w],
                    };
                    for (let i = 1; i <= n; i++) st.badges[i] = depth[i] === null ? "" : { text: sz[i] === null ? `d=${depth[i]}` : `sz=${sz[i]}`, cls: sz[i] === null ? "" : "changed" };
                };
                depth[1] = 0;
                sync();
                rec.push(-1, "Pohon berakar di simpul <b>1</b>. DFS akan menghitung <b>kedalaman</b> setiap simpul saat turun, dan <b>ukuran subtree</b> saat kembali naik.", st);
                const dfs = (u, p) => {
                    stack.push(u);
                    st.nodes[u] = "current";
                    sz[u] = null;
                    sync([["u", u], ["p (orang tua)", p || "–"]]);
                    rec.push(0, `Masuk <code>dfs(${u}, ${p})</code>. Kedalaman simpul ${u} = <b>${depth[u]}</b>.`, st, { pulse: [u] });
                    let acc = 1;
                    for (const { v } of adj[u]) {
                        if (v === p) {
                            sync([["u", u], ["v", v]]);
                            rec.push(3, `Tetangga ${v} adalah orang tua ${u}: lewati, agar tidak naik kembali.`, st, { mark: "skip" });
                            continue;
                        }
                        depth[v] = depth[u] + 1;
                        st.edges[ek(u, v, false)] = "tree";
                        st.nodes[u] = "queued";
                        sync([["u", u], ["v", v], ["depth[v]", depth[v]]]);
                        rec.push(4, `Turun ke anak <b>${v}</b>: <code>depth[${v}] = ${depth[u]} + 1 = ${depth[v]}</code>.`, st, { flow: [u, v] });
                        const got = dfs(v, u);
                        acc += got;
                        st.nodes[u] = "current";
                        sz[u] = null;
                        sync([["u", u], ["sz anak " + v, got], ["jumlah sementara", acc]]);
                        rec.push(5, `Kembali dari ${v} dengan ukuran subtree <b>${got}</b>. Jumlah sementara untuk ${u}: ${acc}.`, st);
                    }
                    sz[u] = acc;
                    stack.pop();
                    st.nodes[u] = "done";
                    sync([["u", u], ["sz[u]", acc]]);
                    rec.push(6, `Semua anak ${u} selesai: <code>sz[${u}] = ${acc}</code> (dirinya + semua keturunannya).`, st, {
                        mark: "discover",
                        ask: {
                            type: "value",
                            answer: acc,
                            prompt: `Berapa ukuran subtree simpul <b>${u}</b>?`,
                            hint: "1 (dirinya sendiri) + jumlah ukuran subtree semua anaknya.",
                            maskBadge: u,
                            maskKey: u,
                        },
                    });
                    return acc;
                };
                dfs(1, 0);
                sync();
                rec.push(-1, `Selesai. <code>sz[1] = ${sz[1]}</code> = banyak simpul. Kedalaman terbesar ${Math.max(...depth.slice(1))}. Setiap simpul dikunjungi tepat sekali: <b>O(N)</b>.`, st, { mark: "done" });
            } else {
                let dist = new Array(n + 1).fill(-1);
                const sync = (w = []) => {
                    st.extra = { arr: Array.from({ length: n }, (_, i) => ({ key: i + 1, val: dist[i + 1] === -1 ? "–" : dist[i + 1] })), watch: w };
                    for (let i = 1; i <= n; i++) st.badges[i] = dist[i] === -1 ? "" : String(dist[i]);
                };
                const bfs = (s, lineStart) => {
                    dist = new Array(n + 1).fill(-1);
                    for (let i = 1; i <= n; i++) st.nodes[i] = "";
                    st.edges = {};
                    dist[s] = 0;
                    st.nodes[s] = "queued start";
                    sync([["s", s]]);
                    rec.push(1, `BFS dari simpul <b>${s}</b>: semua jarak −1 (belum ditemukan), <code>dist[${s}] = 0</code>.`, st, { pulse: [s] });
                    const q = [s];
                    let far = s;
                    while (q.length) {
                        const u = q.shift();
                        st.nodes[u] = "current" + (u === s ? " start" : "");
                        if (dist[u] > dist[far]) far = u;
                        sync([["u", u], ["far", far], ["dist[far]", dist[far]]]);
                        rec.push(3, `Ambil <b>${u}</b> (jarak ${dist[u]}). Simpul terjauh sejauh ini: <b>${far}</b>.`, st);
                        for (const { v } of adj[u]) {
                            if (dist[v] !== -1) continue;
                            dist[v] = dist[u] + 1;
                            st.nodes[v] = "queued";
                            st.edges[ek(u, v, false)] = "tree";
                            q.push(v);
                            sync([["u", u], ["v", v], ["dist[v]", dist[v]]]);
                            rec.push(5, `Temukan ${v}: <code>dist[${v}] = ${dist[v]}</code>.`, st, { flow: [u, v] });
                        }
                        st.nodes[u] = "done" + (u === s ? " start" : "");
                    }
                    sync([["far", far], ["dist[far]", dist[far]]]);
                    rec.push(6, `BFS dari ${s} selesai. Simpul terjauh adalah <b>${far}</b> dengan jarak ${dist[far]}.`, st, { mark: "key" });
                    rec.push(lineStart, lineStart === 7 ? `Jadi <code>a = ${far}</code>: salah satu ujung diameter pasti simpul terjauh dari titik mana pun.` : `Jadi <code>b = ${far}</code>.`, st);
                    return far;
                };
                sync();
                rec.push(-1, "<b>Diameter</b> pohon = jalur terpanjang antara dua simpul. Triknya: dua kali BFS.", st);
                const a = bfs(1, 7);
                const b = bfs(a, 8);
                // Jalur a -> b lewat orang tua BFS terakhir
                const par = new Array(n + 1).fill(0);
                const seen = new Array(n + 1).fill(false);
                const q = [a];
                seen[a] = true;
                while (q.length) {
                    const u = q.shift();
                    for (const { v } of adj[u]) if (!seen[v]) (seen[v] = true), (par[v] = u), q.push(v);
                }
                for (let i = 1; i <= n; i++) st.nodes[i] = "";
                st.edges = {};
                for (let v = b; v !== a; v = par[v]) {
                    st.nodes[v] = "path";
                    st.edges[ek(v, par[v], false)] = "path";
                }
                st.nodes[a] = "path start";
                sync([["a", a], ["b", b], ["diameter", dist[b]]]);
                rec.push(9, `Diameter = <code>dist[${b}] = ${dist[b]}</code> sisi, yaitu jalur hijau dari ${a} ke ${b}. Total kerja dua BFS: <b>O(N)</b>.`, st, { mark: "done" });
            }
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            arr.set(f.arr, f.masked ? f.ask?.maskKey : undefined);
            watch.set(f.watch, f.masked);
        });
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            build();
        });
        sh.head.querySelector("[data-random]").onclick = () => {
            n = V.rand(7, 11);
            edges = randomTree(n);
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            n = 9;
            edges = PRESET;
            build();
        };
        build();
    });

    // ════════════════════════════ BFS 0-1 (deque) ════════════════════════════
    V.register("bfs01", (root) => {
        const sh = V.shell(root, {
            title: "BFS 0-1: Bobot Hanya 0 atau 1",
            controls: `
                <button class="btn btn-sm" data-random>Bobot acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sedang diproses", "#f59e0b", "#f59e0b"],
                ["Di deque", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Selesai", "#a78bfa", "#5b3fc4"],
                ["Sisi jalur terpendek", "#8b5cf6"],
            ],
        });
        const P = (list) => [null, ...list.map(([x, y], i) => ({ id: i + 1, x, y }))];
        const PRESET = {
            n: 7,
            nodes: P([[70, 200], [200, 90], [200, 310], [350, 200], [480, 90], [480, 310], [590, 200]]),
            edges: [[1, 2, 1], [1, 3, 0], [3, 4, 1], [2, 4, 0], [3, 6, 1], [4, 5, 0], [4, 6, 1], [5, 7, 1], [6, 7, 0], [2, 5, 1]].map(([u, v, w]) => ({ u, v, w })),
            directed: false,
            weighted: true,
        };
        let graph = JSON.parse(JSON.stringify(PRESET));
        const view = V.graphView(sh.stage, { hint: "Angka di sisi = bobot (0 atau 1) · seret simpul untuk merapikan" });
        const pseudo = V.codePanel(
            sh.side,
            `
deque<int> dq;
dist.assign(n + 1, INF);                    //@0
dist[s] = 0;                                //@0
dq.push_back(s);                            //@0
while (!dq.empty()) {                       //@1
    int u = dq.front();                     //@2
    dq.pop_front();                         //@2
    for (auto& e : adj[u]) {                //@3
        int v = e.first, w = e.second;      //@3
        if (dist[u] + w < dist[v]) {        //@4
            dist[v] = dist[u] + w;          //@5
            if (w == 0) dq.push_front(v);   //@6
            else dq.push_back(v);           //@7
        }
    }
}`,
        );
        const dq = V.dsPanel(sh.side, "Deque", "depan → belakang");
        const dPanel = V.arrayPanel(sh.side, "Array dist", "jarak dari simpul 1");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            view.draw(g);
            const adj = V.adjacency(g);
            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, extra: {} };
            const INF = Infinity;
            const dist = new Array(g.n + 1).fill(INF);
            const par = new Array(g.n + 1).fill(0);
            const deque = [];
            const fmt = (d) => (d === INF ? "∞" : d);
            const sync = (changed, w = []) => {
                st.extra = {
                    dq: deque.map((x, k) => ({ label: x, cls: x === changed ? "new" : k === 0 ? "front" : "" })),
                    dist: Array.from({ length: g.n }, (_, i) => ({ key: i + 1, val: fmt(dist[i + 1]), cls: changed === i + 1 ? "changed" : "" })),
                    watch: w,
                };
                for (let i = 1; i <= g.n; i++) st.badges[i] = { text: fmt(dist[i]), cls: i === changed ? "changed" : "" };
            };
            for (let i = 1; i <= g.n; i++) st.nodes[i] = i === 1 ? "start" : "";
            dist[1] = 0;
            deque.push(1);
            st.nodes[1] = "start queued";
            sync(1);
            rec.push(0, "Bobot sisi hanya <b>0</b> atau <b>1</b>. Kita pakai <b>deque</b> (antrian dua ujung): simpul lewat sisi 0 masuk di <b>depan</b>, lewat sisi 1 masuk di <b>belakang</b>.", st, { pulse: [1] });
            const processed = new Set();
            while (deque.length) {
                sync(null, [["isi deque", deque.join(", ")]]);
                rec.push(1, `Deque berisi ${deque.length} simpul.`, st);
                const u = deque.shift();
                if (processed.has(u)) {
                    sync(null, [["u", u]]);
                    rec.push(2, `Ambil <b>${u}</b> lagi dari depan, tetapi jaraknya sudah final. Pemeriksaan relaksasi di bawah tidak akan mengubah apa pun.`, st, { mark: "skip" });
                }
                processed.add(u);
                st.nodes[u] = "current" + (u === 1 ? " start" : "");
                sync(null, [["u", u], ["dist[u]", dist[u]]]);
                rec.push(2, `Ambil <b>${u}</b> dari depan deque (jarak ${dist[u]}). Deque selalu terurut menurut jarak, seperti priority queue.`, st, {
                    mark: "key",
                    ask: { type: "node", answer: u, prompt: "Simpul mana yang diambil dari <b>depan deque</b>?", hint: "Lihat simpul paling kiri di panel Deque." },
                });
                for (const { v, w } of adj[u]) {
                    const key = ek(u, v, false);
                    const before = st.edges[key];
                    st.edges[key] = "active";
                    sync(null, [["u", u], ["v", v], ["w", w], ["dist[u] + w", dist[u] + w], ["dist[v]", fmt(dist[v])]]);
                    rec.push(4, `Sisi ${u}–${v} berbobot <b>${w}</b>: ${dist[u]} + ${w} = ${dist[u] + w} ${dist[u] + w < dist[v] ? "&lt;" : "≥"} dist[${v}] = ${fmt(dist[v])}.`, st, { flow: [u, v] });
                    if (dist[u] + w < dist[v]) {
                        dist[v] = dist[u] + w;
                        if (par[v]) st.edges[ek(v, par[v], false)] = "";
                        par[v] = u;
                        st.edges[key] = "tree";
                        st.nodes[v] = "queued";
                        if (w === 0) deque.unshift(v);
                        else deque.push(v);
                        sync(v, [["u", u], ["v", v], ["dist[v]", dist[v], true]]);
                        rec.push(w === 0 ? 6 : 7, w === 0
                            ? `Bobot 0: jarak ${v} <b>sama</b> dengan ${u} (${dist[v]}), jadi ${v} masuk di <b>depan</b> deque.`
                            : `Bobot 1: <code>dist[${v}] = ${dist[v]}</code>, masuk di <b>belakang</b> deque.`, st, {
                            mark: "discover",
                            pulse: [v],
                            ask: { type: "value", answer: dist[v], prompt: `Berapa <code>dist[${v}]</code> yang baru?`, hint: "dist[u] + w", maskBadge: v, maskKey: v },
                        });
                    } else {
                        st.edges[key] = before || "";
                        sync(null, [["u", u], ["v", v]]);
                        rec.push(4, `Tidak lebih baik, lewati.`, st, { mark: "skip" });
                    }
                }
                st.nodes[u] = "done" + (u === 1 ? " start" : "");
            }
            sync();
            rec.push(-1, `Selesai. Jarak terpendek dari simpul 1: ${Array.from({ length: g.n }, (_, i) => `${i + 1}→${fmt(dist[i + 1])}`).join(", ")}. Total O(N + M), lebih cepat dari Dijkstra O((N + M) log N).`, st, { mark: "done" });
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            dq.set(f.dq);
            dPanel.set(f.dist, f.masked ? f.ask?.maskKey : undefined);
            watch.set(f.watch, f.masked);
        });
        view.onNodeClick((id) => player.answerNode(id));
        sh.head.querySelector("[data-random]").onclick = () => {
            graph.edges.forEach((e) => (e.w = V.rand(0, 1)));
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            graph = JSON.parse(JSON.stringify(PRESET));
            build();
        };
        build();
    });

    // ════════════════════════════ SCC: algoritma Kosaraju ════════════════════════════
    V.register("scc", (root) => {
        const sh = V.shell(root, {
            title: "Strongly Connected Components (Kosaraju)",
            controls: `
                <button class="btn btn-sm" data-random>Graph acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sedang dikunjungi", "#f59e0b", "#f59e0b"],
                ["Di call stack", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Selesai (masuk urutan)", "#a78bfa", "#5b3fc4"],
                ["Warna = komponen kuat", "#22c55e", "rgba(34,197,94,.3)"],
            ],
        });
        const P = (list) => [null, ...list.map(([x, y], i) => ({ id: i + 1, x, y }))];
        const PRESET = {
            n: 8,
            nodes: P([[80, 110], [200, 60], [180, 220], [320, 240], [440, 130], [470, 310], [580, 200], [600, 340]]),
            edges: [[1, 2], [2, 3], [3, 1], [3, 4], [4, 5], [5, 6], [6, 4], [6, 7], [7, 8], [8, 7]].map(([u, v]) => ({ u, v, w: 1 })),
            directed: true,
            weighted: false,
        };
        let graph = JSON.parse(JSON.stringify(PRESET));
        const view = V.graphView(sh.stage, { hint: "Graph berarah · seret simpul untuk merapikan" });
        const pseudo = V.codePanel(
            sh.side,
            `
// Tahap 1: DFS di graph asli, catat urutan SELESAI
void dfs1(int u) {                           //@0
    vis[u] = true;                           //@0
    for (int v : adj[u])                     //@1
        if (!vis[v]) dfs1(v);                //@1
    urutan.push_back(u);   // u selesai      //@2
}
// Tahap 2: DFS di graph TERBALIK
void dfs2(int u, int c) {                    //@3
    komp[u] = c;                             //@4
    for (int v : radj[u])                    //@5
        if (komp[v] == -1) dfs2(v, c);       //@5
}

for (int v = 1; v <= n; v++)                 //@6
    if (!vis[v]) dfs1(v);                    //@6
reverse(urutan.begin(), urutan.end());       //@7
int c = 0;
for (int v : urutan)                         //@8
    if (komp[v] == -1) dfs2(v, c++);         //@8`,
        );
        const orderPanel = V.dsPanel(sh.side, "Urutan selesai (tahap 1)");
        const compPanel = V.htmlPanel(sh.side, "Komponen kuat ditemukan");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            view.draw(g);
            const n = g.n;
            const adj = Array.from({ length: n + 1 }, () => []);
            const radj = Array.from({ length: n + 1 }, () => []);
            g.edges.forEach((e) => {
                adj[e.u].push(e.v);
                radj[e.v].push(e.u);
            });
            adj.forEach((l) => l.sort((a, b) => a - b));
            radj.forEach((l) => l.sort((a, b) => a - b));
            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, extra: {}, colors: {} };
            const vis = new Array(n + 1).fill(false);
            const urutan = [];
            const komp = new Array(n + 1).fill(-1);
            const groups = [];
            const stack = [];
            const compHtml = () =>
                groups.length
                    ? groups.map((gr, i) => `<span class="comp-chip" style="--c:${V.COMP_COLORS[i % V.COMP_COLORS.length][1]}">K${i + 1}: {${gr.join(", ")}}</span>`).join(" ")
                    : '<span class="muted">belum ada</span>';
            const sync = (w = []) => {
                st.extra = {
                    order: urutan.map((x) => ({ label: x })),
                    comps: compHtml(),
                    watch: [["call stack", stack.join(" → ") || "kosong"], ...w],
                };
            };
            for (let i = 1; i <= n; i++) st.nodes[i] = "";
            sync();
            rec.push(-1, "Dua simpul berada di <b>komponen kuat</b> yang sama jika masing-masing bisa mencapai yang lain. Kosaraju memakai dua kali DFS.", st);
            const dfs1 = (u) => {
                vis[u] = true;
                stack.push(u);
                st.nodes[u] = "current";
                sync([["u", u]]);
                rec.push(0, `Tahap 1: masuk <code>dfs1(${u})</code>.`, st, { pulse: [u] });
                for (const v of adj[u]) {
                    const key = ek(u, v, true);
                    if (!vis[v]) {
                        st.edges[key] = "tree";
                        st.nodes[u] = "queued";
                        sync([["u", u], ["v", v]]);
                        rec.push(1, `Sisi ${u} → ${v}: ${v} belum dikunjungi, masuk.`, st, { flow: [u, v] });
                        dfs1(v);
                        st.nodes[u] = "current";
                    } else {
                        sync([["u", u], ["v", v]]);
                        rec.push(1, `Sisi ${u} → ${v}: ${v} sudah dikunjungi, lewati.`, st, { mark: "skip" });
                    }
                }
                urutan.push(u);
                stack.pop();
                st.nodes[u] = "done";
                sync([["u", u]]);
                rec.push(2, `<b>${u}</b> selesai, dicatat ke urutan: [${urutan.join(", ")}].`, st, { mark: "key" });
            };
            for (let v = 1; v <= n; v++) {
                if (!vis[v]) {
                    sync([["mulai dari", v]]);
                    rec.push(6, `Simpul ${v} belum dikunjungi: mulai DFS baru dari ${v}.`, st);
                    dfs1(v);
                }
            }
            const order = [...urutan].reverse();
            st.edges = {};
            for (let i = 1; i <= n; i++) st.nodes[i] = "";
            sync([["urutan dibalik", order.join(", ")]]);
            rec.push(7, `Tahap 1 selesai. Balik urutannya: <b>${order.join(", ")}</b>. Simpul yang selesai paling akhir berada di komponen "hulu".`, st, { mark: "key" });
            let c = 0;
            const dfs2 = (u, cc) => {
                komp[u] = cc;
                groups[cc].push(u);
                st.colors[u] = V.COMP_COLORS[cc % V.COMP_COLORS.length];
                st.nodes[u] = "current";
                sync([["u", u], ["komponen", `K${cc + 1}`]]);
                rec.push(4, `Tahap 2: <code>komp[${u}] = K${cc + 1}</code>.`, st, { pulse: [u] });
                for (const v of radj[u]) {
                    const key = ek(v, u, true);
                    if (komp[v] === -1) {
                        st.edges[key] = "back";
                        sync([["u", u], ["v (lewat sisi terbalik)", v]]);
                        rec.push(5, `Di graph terbalik, ${u} → ${v} (sisi asli ${v} → ${u}). ${v} belum punya komponen: masuk.`, st, { flow: [u, v] });
                        dfs2(v, cc);
                    }
                }
                st.nodes[u] = "";
            };
            for (const v of order) {
                if (komp[v] !== -1) continue;
                groups.push([]);
                sync([["mulai dari", v]]);
                rec.push(8, `Simpul <b>${v}</b> belum punya komponen: semua yang ditemukan dari sini di graph terbalik adalah komponen baru <b>K${c + 1}</b>.`, st, { mark: "discover" });
                dfs2(v, c);
                groups[c].sort((a, b) => a - b);
                c++;
            }
            sync();
            rec.push(-1, `Selesai: <b>${c}</b> komponen kuat. Setiap simpul dan sisi diproses dua kali: <b>O(N + M)</b>.`, st, { mark: "done" });
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            orderPanel.set(f.order);
            compPanel.set(f.comps);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-random]").onclick = () => {
            graph = V.randomGraph({ n: V.rand(6, 8), m: V.rand(8, 11), directed: true, connected: false });
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            graph = JSON.parse(JSON.stringify(PRESET));
            build();
        };
        build();
    });

    // ════════════════════════════ Jembatan & titik artikulasi (Tarjan) ════════════════════════════
    V.register("bridges", (root) => {
        const sh = V.shell(root, {
            title: "Jembatan & Titik Artikulasi (tin / low)",
            controls: `
                <button class="btn btn-sm" data-random>Graph acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sedang dikunjungi", "#f59e0b", "#f59e0b"],
                ["Sisi pohon DFS", "#8b5cf6"],
                ["Sisi balik", "#22d3ee"],
                ["Jembatan", "#ef4444"],
                ["Titik artikulasi", "#ef4444", "rgba(239,68,68,.25)"],
            ],
        });
        const P = (list) => [null, ...list.map(([x, y], i) => ({ id: i + 1, x, y }))];
        const PRESET = {
            n: 8,
            nodes: P([[70, 110], [70, 300], [190, 200], [320, 200], [430, 90], [430, 310], [550, 200], [600, 340]]),
            edges: [[1, 2], [2, 3], [3, 1], [3, 4], [4, 5], [5, 6], [6, 4], [6, 7], [7, 8]].map(([u, v]) => ({ u, v, w: 1 })),
            directed: false,
            weighted: false,
        };
        let graph = JSON.parse(JSON.stringify(PRESET));
        const view = V.graphView(sh.stage, { hint: "Label = tin / low · seret simpul untuk merapikan" });
        const pseudo = V.codePanel(
            sh.side,
            `
void dfs(int u, int p) {                       //@0
    tin[u] = low[u] = timer++;                 //@1
    int anak = 0;
    for (int v : adj[u]) {                     //@2
        if (v == p) continue;                  //@2
        if (tin[v] != -1) {   // sisi balik    //@3
            low[u] = min(low[u], tin[v]);      //@3
        } else {
            dfs(v, u);                         //@4
            low[u] = min(low[u], low[v]);      //@5
            if (low[v] > tin[u])               //@6
                jembatan.push_back({u, v});    //@6
            if (p != 0 && low[v] >= tin[u])    //@7
                artikulasi[u] = true;          //@7
            anak++;
        }
    }
    if (p == 0 && anak > 1)                    //@8
        artikulasi[u] = true;                  //@8
}`,
        );
        const listPanel = V.htmlPanel(sh.side, "Hasil");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            view.draw(g);
            const n = g.n;
            const adj = V.adjacency(g);
            const rec = recorder();
            const st = { nodes: {}, badges: {}, edges: {}, extra: {}, colors: {} };
            const tin = new Array(n + 1).fill(-1);
            const low = new Array(n + 1).fill(-1);
            const art = new Array(n + 1).fill(false);
            const bridges = [];
            let timer = 0;
            const sync = (w = []) => {
                st.extra = {
                    res: `<div class="watch"><div class="watch-row"><span>jembatan</span><b>${bridges.map((b) => b.join("–")).join(", ") || "–"}</b></div><div class="watch-row"><span>titik artikulasi</span><b>${art.map((a, i) => (a ? i : null)).filter((x) => x).join(", ") || "–"}</b></div></div>`,
                    watch: w,
                };
                for (let i = 1; i <= n; i++) st.badges[i] = tin[i] === -1 ? "" : `${tin[i]}/${low[i]}`;
            };
            for (let i = 1; i <= n; i++) st.nodes[i] = "";
            sync();
            rec.push(-1, "<b>Jembatan</b>: sisi yang jika dihapus memutus graph. <b>Titik artikulasi</b>: simpul yang jika dihapus memutus graph. Keduanya ditemukan dengan satu DFS yang mencatat <code>tin</code> (waktu masuk) dan <code>low</code>.", st);
            const dfs = (u, p) => {
                tin[u] = low[u] = timer++;
                st.nodes[u] = "current";
                sync([["u", u], ["tin[u]", tin[u]]]);
                rec.push(1, `Masuk ${u}: <code>tin[${u}] = low[${u}] = ${tin[u]}</code>. <code>low</code> = tin terkecil yang bisa dicapai dari subtree ${u} lewat paling banyak satu sisi balik.`, st, { pulse: [u] });
                let anak = 0;
                for (const { v } of adj[u]) {
                    if (v === p) continue;
                    const key = ek(u, v, false);
                    if (tin[v] !== -1) {
                        if (st.edges[key] !== "tree") {
                            st.edges[key] = "back";
                            const old = low[u];
                            low[u] = Math.min(low[u], tin[v]);
                            sync([["u", u], ["v", v], ["tin[v]", tin[v]], ["low[u]", low[u]]]);
                            rec.push(3, `${u}–${v} adalah <b>sisi balik</b> ke leluhur yang sudah dikunjungi: <code>low[${u}] = min(${old}, ${tin[v]}) = ${low[u]}</code>.`, st, { mark: "skip" });
                        }
                        continue;
                    }
                    st.edges[key] = "tree";
                    st.nodes[u] = "queued";
                    sync([["u", u], ["v", v]]);
                    rec.push(4, `Turun ke ${v} lewat sisi pohon ${u}–${v}.`, st, { flow: [u, v] });
                    dfs(v, u);
                    st.nodes[u] = "current";
                    const old = low[u];
                    low[u] = Math.min(low[u], low[v]);
                    anak++;
                    sync([["u", u], ["v", v], ["low[v]", low[v]], ["tin[u]", tin[u]]]);
                    rec.push(5, `Kembali dari ${v}: <code>low[${u}] = min(${old}, low[${v}] = ${low[v]}) = ${low[u]}</code>.`, st);
                    if (low[v] > tin[u]) {
                        bridges.push([u, v]);
                        st.edges[key] = "bridge";
                        sync([["low[v]", low[v]], ["tin[u]", tin[u]]]);
                        rec.push(6, `<code>low[${v}] = ${low[v]} &gt; tin[${u}] = ${tin[u]}</code>: subtree ${v} tidak punya jalan lain naik ke ${u} atau di atasnya. Sisi <b>${u}–${v} adalah jembatan</b>.`, st, { mark: "discover" });
                    }
                    if (p !== 0 && low[v] >= tin[u] && !art[u]) {
                        art[u] = true;
                        st.colors[u] = ["rgba(239,68,68,.30)", "#ef4444"];
                        sync([["low[v]", low[v]], ["tin[u]", tin[u]]]);
                        rec.push(7, `<code>low[${v}] = ${low[v]} ≥ tin[${u}] = ${tin[u]}</code>: tanpa ${u}, subtree ${v} terputus. <b>${u} titik artikulasi</b>.`, st, { mark: "discover" });
                    }
                }
                if (p === 0 && anak > 1) {
                    art[u] = true;
                    st.colors[u] = ["rgba(239,68,68,.30)", "#ef4444"];
                    sync([["anak akar", anak]]);
                    rec.push(8, `${u} adalah akar DFS dengan ${anak} anak di pohon DFS, jadi ${u} titik artikulasi.`, st, { mark: "discover" });
                }
                st.nodes[u] = "done";
                sync([["u", u], ["low[u]", low[u]]]);
                rec.push(5, `${u} selesai dengan <code>low[${u}] = ${low[u]}</code>.`, st);
            };
            for (let v = 1; v <= n; v++) if (tin[v] === -1) dfs(v, 0);
            sync();
            rec.push(-1, `Selesai dalam satu DFS, <b>O(N + M)</b>. Jembatan: ${bridges.map((b) => b.join("–")).join(", ") || "tidak ada"}. Titik artikulasi: ${art.map((a, i) => (a ? i : null)).filter((x) => x).join(", ") || "tidak ada"}.`, st, { mark: "done" });
            player.load(rec.frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            listPanel.set(f.res);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-random]").onclick = () => {
            graph = V.randomGraph({ n: V.rand(7, 9), m: V.rand(8, 10), connected: true });
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            graph = JSON.parse(JSON.stringify(PRESET));
            build();
        };
        build();
    });
})();
