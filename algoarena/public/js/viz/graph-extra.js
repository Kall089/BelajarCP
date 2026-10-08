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
})();
