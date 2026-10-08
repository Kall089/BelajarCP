/* Visualizer DP tambahan: segitiga Pascal (menghitung cara + modulo), DP pada pohon, Digit DP */
(() => {
    "use strict";

    const V = window.Viz;
    const formulaHtml = (f) => `<div class="formula">${f}</div>`;

    // ════════════════════════════ Segitiga Pascal: C(n, k) modulo M ════════════════════════════
    V.register("pascal", (root) => {
        const sh = V.shell(root, {
            title: "Menghitung Cara: Segitiga Pascal C(n, k)",
            controls: `
                <label class="viz-input">n = <select data-n>${[4, 5, 6, 7, 8].map((k) => `<option ${k === 6 ? "selected" : ""}>${k}</option>`).join("")}</select></label>
                <label class="viz-input">modulo <select data-mod><option value="1000000007">10⁹ + 7</option><option value="7">7</option><option value="10">10</option></select></label>`,
            legend: [
                ["Sel dihitung", "#f59e0b", "#f59e0b"],
                ["Kiri atas: k diambil", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Tepat atas: k tidak diambil", "#ec4899", "rgba(236,72,153,.14)"],
                ["Base case", "#a78bfa", "rgba(139,92,246,.14)"],
            ],
        });
        const nSel = sh.head.querySelector("[data-n]");
        const modSel = sh.head.querySelector("[data-mod]");
        let render = () => {};

        function build() {
            const n = +nSel.value;
            const MOD = +modSel.value;
            sh.side.innerHTML = "";
            const code = V.codePanel(
                sh.side,
                `
const int MOD = ${MOD === 1000000007 ? "1e9 + 7" : MOD};
// C[i][j] = banyak cara memilih j benda dari i benda
for (int i = 0; i <= n; i++) {                       //@0
    C[i][0] = 1;           // tidak memilih apa pun  //@1
    for (int j = 1; j <= i; j++)                     //@2
        C[i][j] = (C[i - 1][j - 1]                   //@3
                 + C[i - 1][j]) % MOD;               //@3
}`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const table = V.tableView(sh.stage, {
                rows: n + 1,
                cols: n + 1,
                rowHead: Array.from({ length: n + 1 }, (_, i) => `i=${i}`),
                colHead: Array.from({ length: n + 1 }, (_, j) => `j=${j}`),
                corner: "i \\ j",
                cell: 50,
            });
            const C = Array.from({ length: n + 1 }, () => new Array(n + 1).fill(null));
            const frames = [];
            const snap = (line, text, cur, extra = {}) => {
                const cls = {};
                for (let i = 0; i <= n; i++) {
                    if (C[i][0] !== null) cls[`${i},0`] = "base";
                    for (let j = i + 1; j <= n; j++) cls[`${i},${j}`] = "block";
                }
                if (cur) cls[cur.join(",")] = "current";
                if (extra.a) cls[extra.a.join(",")] = "dep";
                if (extra.b) cls[extra.b.join(",")] = "dep alt";
                frames.push({
                    line,
                    text,
                    vals: C.map((r) => [...r]),
                    cls,
                    hlRow: cur ? cur[0] : undefined,
                    hlCol: cur ? cur[1] : undefined,
                    arrows: [extra.a && { from: extra.a, to: cur, bend: 0 }, extra.b && { from: extra.b, to: cur, alt: true, bend: 0 }].filter(Boolean),
                    formula: extra.formula || "",
                    formulaQ: extra.formulaQ || "",
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };
            snap(-1, `<code>C[i][j]</code> = banyak cara memilih <b>j</b> benda dari <b>i</b> benda. Untuk benda terakhir (ke-i) hanya ada dua kemungkinan: ikut dipilih atau tidak.`);
            for (let i = 0; i <= n; i++) {
                C[i][0] = 1;
                snap(1, `Baris ${i}: <code>C[${i}][0] = 1</code>, hanya satu cara memilih nol benda.`, [i, 0], { mark: i === 0 ? "key" : undefined });
                for (let j = 1; j <= i; j++) {
                    const a = C[i - 1][j - 1];
                    const b = C[i - 1][j] ?? 0;
                    const raw = a + b;
                    C[i][j] = raw % MOD;
                    snap(
                        3,
                        `<code>C[${i}][${j}]</code>: benda ke-${i} <b>diambil</b> → pilih ${j - 1} dari ${i - 1} sisanya (${a} cara), atau <b>tidak diambil</b> → pilih ${j} dari ${i - 1} (${b} cara).` +
                            (raw >= MOD ? ` Jumlahnya ${raw}, setelah modulo ${MOD} menjadi <b>${C[i][j]}</b>.` : ""),
                        [i, j],
                        {
                            a: [i - 1, j - 1],
                            b: j <= i - 1 ? [i - 1, j] : undefined,
                            formula: `(<span class="a">${a}</span> + <span class="b">${b}</span>) % ${MOD} = <span class="r">${C[i][j]}</span>`,
                            formulaQ: `(<span class="a">${a}</span> + <span class="b">${b}</span>) % ${MOD} = <span class="r">?</span>`,
                            ask: { type: "value", answer: C[i][j], prompt: `Berapa <code>C[${i}][${j}]</code>?`, hint: "Jumlahkan sel kiri atas dan sel tepat di atasnya, lalu ambil modulonya.", mask: [i, j] },
                        },
                    );
                }
            }
            snap(-1, `Selesai. Baris ke-${n} berisi banyak cara memilih 0, 1, …, ${n} benda dari ${n} benda${MOD < 1000 ? ` (modulo ${MOD})` : ""}. Total O(n²).`, null, { mark: "done" });
            render = (f) => {
                code.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        nSel.onchange = build;
        modSel.onchange = build;
        build();
    });

    // ════════════════════════════ DP pada pohon: himpunan independen bernilai maksimum ════════════════════════════
    V.register("dp-tree", (root) => {
        const sh = V.shell(root, {
            title: "DP pada Pohon: Undangan Pesta",
            controls: `
                <button class="btn btn-sm" data-random>Pohon acak</button>
                <button class="btn btn-sm btn-ghost" data-preset>Reset</button>`,
            legend: [
                ["Sedang dihitung", "#f59e0b", "#f59e0b"],
                ["Di call stack", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Selesai: label = tidak / diundang", "#a78bfa", "#5b3fc4"],
                ["Diundang (solusi optimal)", "#22c55e", "rgba(34,197,94,.3)"],
            ],
        });
        const PRESET = { n: 8, edges: [[1, 2], [1, 3], [1, 4], [2, 5], [2, 6], [4, 7], [4, 8]], val: [0, 3, 6, 2, 1, 4, 5, 3, 3] };
        let tree = JSON.parse(JSON.stringify(PRESET));
        const view = V.graphView(sh.stage, { hint: "Label: nilai → dp[u][0] / dp[u][1]" });
        const code = V.codePanel(
            sh.side,
            `
// dp[u][0] = terbaik di subtree u, u TIDAK diundang
// dp[u][1] = terbaik di subtree u, u DIUNDANG
void dfs(int u, int p) {                          //@0
    dp[u][0] = 0;                                 //@1
    dp[u][1] = nilai[u];                          //@1
    for (int v : adj[u]) {                        //@2
        if (v == p) continue;                     //@2
        dfs(v, u);                                //@3
        dp[u][0] += max(dp[v][0], dp[v][1]);      //@4
        dp[u][1] += dp[v][0];   // anak tak boleh //@5
    }
}
// jawaban = max(dp[1][0], dp[1][1])              //@6`,
        );
        const formula = V.htmlPanel(sh.side, "Perhitungan");
        const arr = V.arrayPanel(sh.side, "dp[u][0] / dp[u][1]", "tidak / diundang");

        function build() {
            const n = tree.n;
            const g = V.graphFromEdges(n, tree.edges, { tree: true, root: 1 });
            g.edges.forEach((e) => (e.w = 1));
            view.draw(g);
            const adj = V.adjacency(g);
            const val = tree.val;
            const dp0 = new Array(n + 1).fill(null);
            const dp1 = new Array(n + 1).fill(null);
            const frames = [];
            const st = { nodes: {}, badges: {}, edges: {} };
            const push = (line, text, fx = {}) => {
                for (let i = 1; i <= n; i++) {
                    st.badges[i] = dp0[i] === null ? { text: `nilai ${val[i]}`, cls: "" } : { text: `${dp0[i]} / ${dp1[i]}`, cls: "changed" };
                }
                frames.push({
                    line,
                    text,
                    nodes: { ...st.nodes },
                    badges: { ...st.badges },
                    edges: { ...st.edges },
                    colors: fx.colors,
                    arr: Array.from({ length: n }, (_, i) => ({ key: i + 1, val: dp0[i + 1] === null ? "–" : `${dp0[i + 1]}/${dp1[i + 1]}` })),
                    formula: fx.formula || "",
                    ...fx,
                });
            };
            for (let i = 1; i <= n; i++) st.nodes[i] = "";
            push(-1, "Setiap karyawan punya nilai keceriaan. Seseorang tidak mau datang jika <b>atasan langsungnya</b> juga diundang. Pilih tamu agar total keceriaan maksimum. Untuk setiap simpul kita simpan dua jawaban: u tidak diundang / u diundang.");
            const dfs = (u, p) => {
                st.nodes[u] = "current";
                dp0[u] = 0;
                dp1[u] = val[u];
                push(1, `Masuk ${u}: mulai dari <code>dp[${u}][0] = 0</code> dan <code>dp[${u}][1] = nilai[${u}] = ${val[u]}</code>.`, { pulse: [u] });
                for (const { v } of adj[u]) {
                    if (v === p) continue;
                    st.nodes[u] = "queued";
                    st.edges[V.ek(u, v, false)] = "tree";
                    push(3, `Selesaikan subtree anak <b>${v}</b> dulu.`, { flow: [u, v] });
                    dfs(v, u);
                    st.nodes[u] = "current";
                    const b0 = dp0[u];
                    dp0[u] += Math.max(dp0[v], dp1[v]);
                    push(4, `u = ${u} tidak diundang: anak ${v} bebas, ambil yang terbaik: max(${dp0[v]}, ${dp1[v]}) = ${Math.max(dp0[v], dp1[v])}.`, {
                        formula: `dp[${u}][0] = <span class="a">${b0}</span> + max(${dp0[v]}, ${dp1[v]}) = <span class="r">${dp0[u]}</span>`,
                    });
                    const b1 = dp1[u];
                    dp1[u] += dp0[v];
                    push(5, `u = ${u} diundang: anak ${v} <b>tidak boleh</b> diundang, ambil dp[${v}][0] = ${dp0[v]}.`, {
                        formula: `dp[${u}][1] = <span class="a">${b1}</span> + ${dp0[v]} = <span class="r">${dp1[u]}</span>`,
                        mark: "key",
                    });
                }
                st.nodes[u] = "done";
                push(2, `Simpul ${u} selesai: <code>dp[${u}] = (${dp0[u]}, ${dp1[u]})</code>.`, { mark: "discover" });
            };
            dfs(1, 0);
            // Rekonstruksi tamu yang diundang
            const colors = {};
            const pick = (u, p, canInvite) => {
                const invite = canInvite && dp1[u] > dp0[u];
                if (invite) colors[u] = ["rgba(34,197,94,.35)", "#22c55e"];
                for (const { v } of adj[u]) if (v !== p) pick(v, u, !invite);
            };
            pick(1, 0, true);
            for (let i = 1; i <= n; i++) st.nodes[i] = "";
            push(6, `Jawaban = max(${dp0[1]}, ${dp1[1]}) = <b>${Math.max(dp0[1], dp1[1])}</b>. Simpul hijau adalah salah satu pilihan tamu terbaik. Setiap simpul dihitung sekali: <b>O(N)</b>.`, {
                colors,
                mark: "done",
                formula: `max(${dp0[1]}, ${dp1[1]}) = <span class="r">${Math.max(dp0[1], dp1[1])}</span>`,
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            code.set(f.line);
            formula.set(f.formula ? formulaHtml(f.formula) : '<span class="muted">–</span>');
            arr.set(f.arr);
        });
        sh.head.querySelector("[data-random]").onclick = () => {
            const n = V.rand(7, 10);
            const edges = [];
            for (let v = 2; v <= n; v++) edges.push([V.rand(Math.max(1, v - 3), v - 1), v]);
            tree = { n, edges, val: [0, ...Array.from({ length: n }, () => V.rand(1, 9))] };
            build();
        };
        sh.head.querySelector("[data-preset]").onclick = () => {
            tree = JSON.parse(JSON.stringify(PRESET));
            build();
        };
        build();
    });

    // ════════════════════════════ Digit DP: banyak bilangan 0..N dengan jumlah digit S ════════════════════════════
    V.register("digit", (root) => {
        const sh = V.shell(root, {
            title: "Digit DP: Bilangan 0..N dengan Jumlah Digit S",
            controls: `
                <label class="viz-input">N <input data-n value="325" style="width:90px"></label>
                <label class="viz-input">S <input class="short" data-s value="7"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sel dihitung", "#f59e0b", "#f59e0b"],
                ["Sel yang dijumlahkan", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Dipakai untuk jawaban", "#22c55e", "rgba(34,197,94,.2)"],
                ["Base case", "#a78bfa", "rgba(139,92,246,.14)"],
            ],
        });
        const nIn = sh.head.querySelector("[data-n]");
        const sIn = sh.head.querySelector("[data-s]");
        let render = () => {};

        function build() {
            let N = String(nIn.value).replace(/\D/g, "").replace(/^0+(?=\d)/, "").slice(0, 5) || "325";
            nIn.value = N;
            const S = V.clampInt(sIn.value, 0, 14, 7);
            sIn.value = S;
            const L = N.length;
            const dig = [...N].map(Number);
            sh.side.innerHTML = "";
            const code = V.codePanel(
                sh.side,
                `
// f[i][s] = cara mengisi digit ke-i..akhir BEBAS (0-9)
//           agar total = S, jika jumlah sejauh ini s
for (int s = 0; s <= S; s++) f[L][s] = (s == S);      //@0
for (int i = L - 1; i >= 0; i--)                       //@1
    for (int s = 0; s <= S; s++)                       //@1
        for (int d = 0; d <= 9 && s + d <= S; d++)     //@2
            f[i][s] += f[i + 1][s + d];                //@2

long long jawaban = 0;
int pref = 0;   // jumlah digit awalan yang sama dengan N
for (int i = 0; i < L; i++) {                          //@3
    for (int d = 0; d < N[i]; d++)    // lebih kecil   //@4
        if (pref + d <= S)                             //@4
            jawaban += f[i + 1][pref + d];             //@4
    pref += N[i];                     // tetap "ketat" //@5
}
if (pref == S) jawaban++;             // N sendiri     //@6`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const watch = V.watchPanel(sh.side);
            const table = V.tableView(sh.stage, {
                rows: L + 1,
                cols: S + 1,
                rowHead: Array.from({ length: L + 1 }, (_, i) => (i < L ? `i=${i} (${dig[i]})` : `i=${L}`)),
                colHead: Array.from({ length: S + 1 }, (_, s) => `s=${s}`),
                corner: "i \\ s",
                cell: 46,
            });
            const f = Array.from({ length: L + 1 }, () => new Array(S + 1).fill(null));
            const frames = [];
            let used = [];
            const snap = (line, text, cur, extra = {}) => {
                const cls = {};
                for (let s = 0; s <= S; s++) if (f[L][s] !== null) cls[`${L},${s}`] = "base";
                used.forEach((k) => (cls[k] = "path"));
                (extra.deps || []).forEach((d) => (cls[d.join(",")] = "dep"));
                if (cur) cls[cur.join(",")] = "current";
                frames.push({ line, text, vals: f.map((r) => [...r]), cls, hlRow: cur ? cur[0] : extra.hlRow, hlCol: cur ? cur[1] : undefined, formula: extra.formula || "", watch: extra.watch || [], mark: extra.mark });
            };
            snap(-1, `Hitung bilangan 0..<b>${N}</b> yang jumlah digitnya tepat <b>${S}</b>. Bilangan ditulis dengan tepat ${L} digit (boleh ada nol di depan, misalnya 007 = 7). Tahap 1 mengisi tabel untuk digit yang bebas dipilih.`);
            for (let s = 0; s <= S; s++) f[L][s] = s === S ? 1 : 0;
            snap(0, `Baris terakhir: tidak ada digit tersisa. Hanya berhasil jika jumlahnya sudah tepat ${S}.`, null, { hlRow: L, mark: "key" });
            for (let i = L - 1; i >= 0; i--) {
                for (let s = 0; s <= S; s++) {
                    let tot = 0;
                    const deps = [];
                    for (let d = 0; d <= 9 && s + d <= S; d++) {
                        tot += f[i + 1][s + d];
                        deps.push([i + 1, s + d]);
                    }
                    f[i][s] = tot;
                    snap(2, `<code>f[${i}][${s}]</code>: digit ke-${i} bisa 0..${Math.min(9, S - s)}, jumlahkan baris di bawahnya dari kolom ${s} sampai ${Math.min(s + 9, S)} → <b>${tot}</b>.`, [i, s], {
                        deps,
                        formula: `f[${i}][${s}] = <span class="r">${tot}</span>`,
                    });
                }
            }
            let ans = 0;
            let pref = 0;
            snap(-1, `Tahap 2: telusuri digit N dari kiri. Selama awalan masih <b>sama</b> dengan N kita "ketat"; begitu memilih digit yang <b>lebih kecil</b>, sisa digit bebas, dan banyak caranya sudah ada di tabel.`, null, { mark: "key" });
            for (let i = 0; i < L; i++) {
                const add = [];
                let part = 0;
                for (let d = 0; d < dig[i]; d++) {
                    if (pref + d <= S) {
                        part += f[i + 1][pref + d];
                        add.push([i + 1, pref + d]);
                    }
                }
                ans += part;
                add.forEach((c) => used.push(c.join(",")));
                snap(4, `Posisi ${i} (digit N = ${dig[i]}), awalan berjumlah ${pref}: pilih digit 0..${dig[i] - 1} → tambah ${add.length ? add.map((c) => `f[${c[0]}][${c[1]}]`).join(" + ") : "0"} = <b>${part}</b>.`, null, {
                    hlRow: i + 1,
                    formula: `jawaban = <span class="r">${ans}</span>`,
                    watch: [["i", i], ["pref", pref], ["tambahan", part], ["jawaban", ans]],
                });
                pref += dig[i];
                snap(5, `Lanjut dengan digit yang sama dengan N (${dig[i]}): pref = ${pref}.`, null, { hlRow: i, watch: [["pref", pref], ["jawaban", ans]] });
            }
            if (pref === S) ans++;
            snap(6, `N sendiri (${N}) punya jumlah digit ${pref}${pref === S ? ", jadi dihitung juga (+1)" : ", tidak dihitung"}. Jawaban: <b>${ans}</b> bilangan. Kerja O(L · S · 10), bukan O(N).`, null, {
                mark: "done",
                formula: `jawaban = <span class="r">${ans}</span>`,
                watch: [["jawaban", ans]],
            });
            render = (fr) => {
                code.set(fr.line);
                table.update(fr);
                formula.set(fr.formula ? formulaHtml(fr.formula) : '<span class="muted">–</span>');
                watch.set(fr.watch);
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (fr) => render(fr));
        sh.head.querySelector("[data-apply]").onclick = build;
        [nIn, sIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
