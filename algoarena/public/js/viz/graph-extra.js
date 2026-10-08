/* Visualizer graph tambahan: jalur Euler (Hierholzer) dan aliran maksimum (Edmonds-Karp) */
(() => {
    "use strict";

    const V = window.Viz;
    const { ek } = V;

    const P = (list) => [null, ...list.map(([x, y], i) => ({ id: i + 1, x, y }))];
    const mkGraph = (pos, edges, extra = {}) => ({
        n: pos.length,
        nodes: P(pos),
        edges: edges.map(([u, v, w]) => ({ u, v, w: w ?? 1 })),
        directed: false,
        weighted: false,
        ...extra,
    });

    // ════════════════════════════ Jalur Euler: algoritma Hierholzer ════════════════════════════
    V.register("euler", (root) => {
        const PRESETS = {
            kupu: mkGraph(
                [[90, 80], [90, 300], [310, 190], [530, 80], [530, 300]],
                [[1, 2], [2, 3], [3, 1], [3, 4], [4, 5], [5, 3]],
            ),
            amplop: mkGraph(
                [[170, 320], [450, 320], [450, 170], [170, 170], [310, 50]],
                [[1, 2], [2, 3], [3, 4], [4, 1], [1, 3], [2, 4], [3, 5], [4, 5]],
            ),
            k4: mkGraph(
                [[140, 80], [480, 80], [480, 300], [140, 300]],
                [[1, 2], [1, 3], [1, 4], [2, 3], [2, 4], [3, 4]],
            ),
        };
        const sh = V.shell(root, {
            title: "Algoritma Hierholzer",
            controls: V.segmented(
                "preset",
                [
                    ["kupu", "Kupu-kupu"],
                    ["amplop", "Amplop"],
                    ["k4", "Tidak ada"],
                ],
                "kupu",
            ),
            legend: [
                ["Puncak tumpukan", "#f59e0b", "#f59e0b"],
                ["Sisi sudah dipakai", "#8b5cf6"],
                ["Derajat ganjil", "#ef4444", "rgba(239,68,68,.25)"],
                ["Rute akhir", "#22c55e"],
            ],
        });
        let graph = JSON.parse(JSON.stringify(PRESETS.kupu));
        const view = V.graphView(sh.stage, { hint: "Angka di atas simpul = sisa sisi yang belum dipakai" });
        const code = V.codePanel(
            sh.side,
            `
// syarat: banyak simpul berderajat ganjil 0 atau 2         //@0
vector<int> tumpukan = {start}, rute;                     //@1
while (!tumpukan.empty()) {                               //@2
    int u = tumpukan.back();                              //@2
    while (ptr[u] < adj[u].size() && dipakai[...]) ptr[u]++;
    if (ptr[u] == adj[u].size()) {     // u buntu         //@3
        rute.push_back(u);                                //@3
        tumpukan.pop_back();                              //@3
    } else {                           // jalan terus     //@4
        int v = adj[u][ptr[u]].first;                     //@4
        dipakai[adj[u][ptr[u]].second] = true;            //@4
        tumpukan.push_back(v);                            //@4
    }
}
reverse(rute.begin(), rute.end());                        //@5`,
        );
        const stackPanel = V.dsPanel(sh.side, "Tumpukan", "bawah → puncak");
        const rutePanel = V.dsPanel(sh.side, "rute (terbalik)", "urutan simpul yang buntu");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            view.draw(g);
            const n = g.n;
            const adj = Array.from({ length: n + 1 }, () => []);
            g.edges.forEach((e, i) => {
                adj[e.u].push([e.v, i]);
                adj[e.v].push([e.u, i]);
            });
            adj.forEach((l) => l.sort((a, b) => a[0] - b[0] || a[1] - b[1]));
            const deg = adj.map((l) => l.length);
            const left = deg.slice();
            const used = new Array(g.edges.length).fill(false);
            const frames = [];
            const st = { nodes: {}, badges: {}, edges: {}, colors: {} };
            let stack = [];
            let rute = [];
            const snap = (line, text, fx = {}) => {
                for (let i = 1; i <= n; i++) st.badges[i] = String(left[i]);
                frames.push({
                    line,
                    text,
                    nodes: { ...st.nodes },
                    badges: { ...st.badges },
                    edges: { ...st.edges },
                    colors: { ...st.colors },
                    stack: stack.map((x, i) => ({ label: x, cls: i === stack.length - 1 ? "hot" : "" })),
                    rute: rute.map((x) => ({ label: x, cls: "done" })),
                    watch: fx.watch || [["tumpukan", stack.length], ["rute", rute.length], ["sisi tersisa", used.filter((u) => !u).length]],
                    ...fx,
                });
            };
            const odd = [];
            for (let v = 1; v <= n; v++)
                if (deg[v] % 2) {
                    odd.push(v);
                    st.colors[v] = ["rgba(239,68,68,.28)", "#ef4444"];
                }
            snap(0, `Jalur Euler melewati <b>setiap sisi tepat sekali</b>. Hitung derajat: ${odd.length ? `simpul berderajat ganjil adalah <b>${odd.join(", ")}</b>` : "<b>semua derajat genap</b>"}.`, {
                watch: [["simpul ganjil", odd.length]],
            });
            if (odd.length !== 0 && odd.length !== 2) {
                snap(-1, `Ada <b>${odd.length}</b> simpul berderajat ganjil. Jalur Euler butuh 0 (sirkuit) atau 2 (jalur) simpul ganjil, jadi <b>tidak ada</b> jalur Euler. Setiap kali jalur melewati sebuah simpul, ia memakai dua sisi (masuk dan keluar); hanya titik awal dan akhir yang boleh "kelebihan" satu.`, {
                    mark: "skip",
                    watch: [["simpul ganjil", odd.length]],
                });
                player.load(frames);
                return;
            }
            const start = odd.length ? odd[0] : 1;
            stack = [start];
            st.nodes[start] = "current";
            snap(1, odd.length ? `Tepat dua simpul ganjil: jalur harus dimulai di salah satunya. Mulai dari <b>${start}</b> (yang terkecil); jalur akan berakhir di <b>${odd[1]}</b>.` : `Semua derajat genap: ada <b>sirkuit</b> Euler. Mulai dari simpul <b>${start}</b>.`, { pulse: [start] });
            const ptr = new Array(n + 1).fill(0);
            while (stack.length) {
                const u = stack[stack.length - 1];
                while (ptr[u] < adj[u].length && used[adj[u][ptr[u]][1]]) ptr[u]++;
                for (let i = 1; i <= n; i++) st.nodes[i] = "";
                st.nodes[u] = "current";
                if (ptr[u] === adj[u].length) {
                    rute.push(u);
                    stack.pop();
                    const nu = stack[stack.length - 1];
                    for (let i = 1; i <= n; i++) st.nodes[i] = "";
                    if (nu) st.nodes[nu] = "current";
                    snap(3, `Simpul <b>${u}</b> tidak punya sisi tersisa: <b>buntu</b>. Pindahkan ${u} dari tumpukan ke <code>rute</code>${nu ? `, lalu kembali ke ${nu} untuk melihat apakah masih ada sisi yang belum dilewati` : ""}.`, { mark: rute.length === 1 ? "key" : undefined });
                } else {
                    const [v, id] = adj[u][ptr[u]];
                    used[id] = true;
                    left[u]--;
                    left[v]--;
                    st.edges[ek(u, v, false)] = "tree";
                    stack.push(v);
                    for (let i = 1; i <= n; i++) st.nodes[i] = "";
                    st.nodes[v] = "current";
                    snap(4, `Dari ${u}, ambil sisi belum terpakai ke tetangga terkecil: <b>${u}–${v}</b>. Tandai dipakai, dorong ${v} ke tumpukan.`, {
                        flow: [u, v],
                        ask: { type: "node", answer: v, prompt: `Dari simpul <b>${u}</b>, ke simpul mana Hierholzer berjalan?`, hint: "Ambil sisi yang belum dipakai dengan tetangga bernomor terkecil (lihat angka sisa di atas simpul)." },
                    });
                }
            }
            const path = rute.slice().reverse();
            for (let i = 1; i <= n; i++) st.nodes[i] = "";
            for (let i = 0; i + 1 < path.length; i++) st.edges[ek(path[i], path[i + 1], false)] = "path";
            st.nodes[path[0]] = "start";
            st.nodes[path[path.length - 1]] = path[0] === path[path.length - 1] ? "start" : "target";
            snap(5, `Balik <code>rute</code>: <b>${path.join(" → ")}</b>. Semua ${g.edges.length} sisi terpakai tepat sekali. Perhatikan sub-tur yang "buntu" lebih dulu justru masuk ke bagian akhir rute: inilah cara Hierholzer menyisipkan putaran kecil ke putaran besar.`, {
                mark: "done",
                watch: [["panjang rute", path.length], ["M + 1", g.edges.length + 1]],
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            code.set(f.line);
            stackPanel.set(f.stack || []);
            rutePanel.set(f.rute || []);
            watch.set(f.watch, f.masked);
        });
        view.onNodeClick((id) => player.answerNode(id));
        V.bindSegmented(sh.head, "preset", (k) => {
            graph = JSON.parse(JSON.stringify(PRESETS[k]));
            build();
        });
        build();
    });

    // ════════════════════════════ Aliran maksimum: Edmonds-Karp ════════════════════════════
    V.register("flow", (root) => {
        const PRESETS = {
            undo: mkGraph(
                [[60, 190], [210, 70], [340, 250], [190, 320], [460, 70], [580, 190]],
                [[1, 2, 3], [2, 3, 2], [3, 6, 2], [1, 4, 2], [4, 3, 2], [2, 5, 2], [5, 6, 3]],
                { directed: true, weighted: true },
            ),
            clrs: mkGraph(
                [[50, 190], [190, 70], [190, 310], [400, 70], [400, 310], [560, 190]],
                [[1, 2, 16], [1, 3, 13], [2, 3, 10], [3, 2, 4], [2, 4, 12], [4, 3, 9], [3, 5, 14], [5, 4, 7], [4, 6, 20], [5, 6, 4]],
                { directed: true, weighted: true },
            ),
        };
        const sh = V.shell(root, {
            title: "Aliran Maksimum (Edmonds-Karp)",
            controls: V.segmented(
                "preset",
                [
                    ["undo", "Butuh sisi balik"],
                    ["clrs", "Jaringan klasik"],
                ],
                "undo",
            ),
            legend: [
                ["Jalur augmentasi", "#f59e0b"],
                ["Dilewati terbalik (undo)", "#22d3ee"],
                ["Membawa aliran", "#8b5cf6"],
                ["Potongan minimum", "#ef4444"],
            ],
        });
        let graph = JSON.parse(JSON.stringify(PRESETS.undo));
        const view = V.graphView(sh.stage, { hint: "Label sisi = aliran / kapasitas · s = simpul 1, t = simpul terakhir" });
        const code = V.codePanel(
            sh.side,
            `
long long total = 0;                                     //@0
while (true) {
    // BFS di graph residual: hanya sisi dengan sisa > 0  //@1
    bfs(s);                                              //@1
    if (!dikunjungi[t]) break;     // tidak ada jalur    //@4
    long long tambah = LLONG_MAX;                        //@2
    for (int v = t; v != s; v = asal(v))                 //@2
        tambah = min(tambah, sisi[dariSisi[v]].sisa);    //@2
    for (int v = t; v != s; v = asal(v)) {               //@3
        sisi[dariSisi[v]].sisa -= tambah;      // maju   //@3
        sisi[dariSisi[v] ^ 1].sisa += tambah;  // balik  //@3
    }
    total += tambah;                                     //@3
}`,
        );
        const paths = V.htmlPanel(sh.side, "Jalur augmentasi");
        const watch = V.watchPanel(sh.side);

        function build() {
            const g = graph;
            view.draw(g);
            const n = g.n;
            const s = 1;
            const t = n;
            // sisi[e] dan sisi[e ^ 1]: pasangan maju / balik
            const E = [];
            const adj = Array.from({ length: n + 1 }, () => []);
            g.edges.forEach((e) => {
                adj[e.u].push(E.length);
                E.push({ to: e.v, cap: e.w, res: e.w, key: ek(e.u, e.v, true), fwd: true });
                adj[e.v].push(E.length);
                E.push({ to: e.u, cap: 0, res: 0, key: ek(e.u, e.v, true), fwd: false });
            });
            adj.forEach((l) => l.sort((a, b) => E[a].to - E[b].to));
            const flowOf = (i) => E[i].cap - E[i].res;
            const labels = () => {
                const o = {};
                for (let i = 0; i < E.length; i += 2) o[E[i].key] = `${flowOf(i)}/${E[i].cap}`;
                return o;
            };
            const baseEdges = () => {
                const o = {};
                for (let i = 0; i < E.length; i += 2) if (flowOf(i) > 0) o[E[i].key] = "tree";
                return o;
            };
            const frames = [];
            const log = [];
            let total = 0;
            const snap = (line, text, fx = {}) =>
                frames.push({
                    line,
                    text,
                    wlabels: labels(),
                    edges: fx.edges || baseEdges(),
                    nodes: fx.nodes || { [s]: "start", [t]: "target" },
                    badges: fx.badges || { [s]: "s", [t]: "t" },
                    colors: fx.colors || {},
                    html: `<div class="watch">${log.map((r) => `<div class="watch-row"><span>${r[0]}</span><b>${r[1]}</b></div>`).join("") || '<div class="watch-row"><span>belum ada</span><b>–</b></div>'}</div>`,
                    watch: [["total aliran", total], ["iterasi", log.length]],
                    ...fx,
                });
            snap(0, `Kirim aliran sebanyak mungkin dari <b>s = ${s}</b> ke <b>t = ${t}</b>. Label setiap sisi <code>aliran/kapasitas</code>. Awalnya semua aliran 0.`);
            for (;;) {
                const from = new Array(n + 1).fill(-1);
                const seen = new Array(n + 1).fill(false);
                const q = [s];
                seen[s] = true;
                while (q.length && !seen[t]) {
                    const u = q.shift();
                    for (const e of adj[u]) {
                        const v = E[e].to;
                        if (!seen[v] && E[e].res > 0) {
                            seen[v] = true;
                            from[v] = e;
                            q.push(v);
                        }
                    }
                }
                if (!seen[t]) {
                    const nodes = {};
                    const colors = {};
                    for (let v = 1; v <= n; v++) if (seen[v]) colors[v] = ["rgba(34,197,94,.25)", "#22c55e"];
                    const edges = baseEdges();
                    let cut = 0;
                    const cutList = [];
                    for (let i = 0; i < E.length; i += 2) {
                        const u = E[i + 1].to;
                        const v = E[i].to;
                        if (seen[u] && !seen[v]) {
                            edges[E[i].key] = "bridge";
                            cut += E[i].cap;
                            cutList.push(`${u}→${v} (${E[i].cap})`);
                        }
                    }
                    snap(4, `BFS tidak bisa mencapai t lagi: aliran sudah <b>maksimum = ${total}</b>. Simpul hijau masih terjangkau dari s di graph residual. Sisi merah keluar dari daerah hijau membentuk <b>potongan minimum</b>: ${cutList.join(" + ")} = <b>${cut}</b>. Aliran maksimum = potongan minimum.`, {
                        edges,
                        colors,
                        nodes,
                        mark: "done",
                    });
                    break;
                }
                const pathE = [];
                for (let v = t; v !== s; v = E[from[v] ^ 1].to) pathE.unshift(from[v]);
                const pathNodes = [s, ...pathE.map((e) => E[e].to)];
                const edges = baseEdges();
                let usesBack = false;
                for (const e of pathE) {
                    edges[E[e].key] = E[e].fwd ? "active" : "back";
                    if (!E[e].fwd) usesBack = true;
                }
                const nodes = { [s]: "start", [t]: "target" };
                pathNodes.slice(1, -1).forEach((v) => (nodes[v] = "queued"));
                snap(1, `BFS di graph residual menemukan jalur terpendek <b>${pathNodes.join(" → ")}</b>.${usesBack ? ' Perhatikan sisi <b>biru putus-putus</b>: jalur ini berjalan <b>melawan arah</b> sisi yang sudah membawa aliran, artinya membatalkan sebagian aliran lama dan mengalihkannya.' : ""}`, {
                    edges,
                    nodes,
                    ask: { type: "node", answer: pathNodes[1], prompt: "BFS mencari jalur terpendek di graph residual. Simpul mana yang dituju jalur ini setelah s?", hint: "Sisi yang penuh (aliran = kapasitas) tidak bisa dilewati maju; sisi yang membawa aliran bisa dilewati mundur." },
                });
                let add = Infinity;
                for (const e of pathE) add = Math.min(add, E[e].res);
                const resList = pathE.map((e) => `${E[e].res}`).join(", ");
                snap(2, `Sisa kapasitas di sepanjang jalur: ${resList}. Yang terkecil (<b>bottleneck</b>) = <b>${add}</b>: sebanyak itulah aliran yang bisa ditambahkan.`, { edges, nodes, mark: "key" });
                for (const e of pathE) {
                    E[e].res -= add;
                    E[e ^ 1].res += add;
                }
                total += add;
                log.push([pathNodes.join("→"), `+${add}`]);
                snap(3, `Kirim ${add} unit: sisa sisi maju berkurang ${add}, sisa sisi balik bertambah ${add} (agar bisa dibatalkan nanti). Total aliran sekarang <b>${total}</b>.`, { nodes });
            }
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            code.set(f.line);
            paths.set(f.html);
            watch.set(f.watch, f.masked);
        });
        view.onNodeClick((id) => player.answerNode(id));
        V.bindSegmented(sh.head, "preset", (k) => {
            graph = JSON.parse(JSON.stringify(PRESETS[k]));
            build();
        });
        build();
    });
})();
