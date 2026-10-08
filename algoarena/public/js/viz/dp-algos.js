/* Visualizer Dynamic Programming: Fibonacci, DP 1D, Grid, Knapsack, LCS, LIS */
(() => {
    "use strict";

    const V = window.Viz;
    const { esc } = V;
    const INF = "∞";
    const fmtVal = (v) => (v === Infinity ? INF : v);
    const formulaHtml = (f) => `<div class="formula">${f}</div>`;

    // ════════════════════════════ Fibonacci: rekursi vs memo vs tabel ════════════════════════════
    V.register("fib", (root) => {
        const sh = V.shell(root, {
            title: "Fibonacci: Rekursi → Memoization → Tabulasi",
            controls: `
                ${V.segmented("mode", [["naive", "Rekursi biasa"], ["memo", "Memoization"], ["table", "Tabulasi"]], "naive")}
                <label class="viz-input">n = <select data-n></select></label>`,
            legend: [
                ["Sedang dihitung", "#f59e0b", "#f59e0b"],
                ["Sudah kembali", "#a78bfa", "rgba(139,92,246,.3)"],
                ["Dihitung ulang (boros!)", "#ef4444", "rgba(239,68,68,.18)"],
                ["Diambil dari memo", "#22d3ee", "rgba(34,211,238,.14)"],
            ],
        });
        let mode = "naive";
        let n = 5;
        const nSelect = sh.head.querySelector("[data-n]");
        const fillN = () => {
            const max = mode === "table" ? 14 : 7;
            n = Math.min(n, max);
            nSelect.innerHTML = Array.from({ length: max - 1 }, (_, i) => i + 2)
                .map((k) => `<option ${k === n ? "selected" : ""}>${k}</option>`)
                .join("");
        };
        let render = () => {};

        /** Grafik batang: banyak pemanggilan rekursi biasa vs memo untuk n = 1..12 */
        function growthChart(current) {
            const fib = [0, 1];
            for (let i = 2; i <= 14; i++) fib[i] = fib[i - 1] + fib[i - 2];
            const ks = Array.from({ length: 12 }, (_, i) => i + 1);
            const naive = ks.map((k) => 2 * fib[k + 1] - 1);
            const memo = ks.map((k) => 2 * k - 1);
            const max = Math.max(...naive);
            const W = 280;
            const H = 120;
            const bw = W / ks.length;
            let bars = "";
            ks.forEach((k, i) => {
                const x = i * bw + 3;
                const hn = (naive[i] / max) * (H - 18);
                const hm = Math.max(2, (memo[i] / max) * (H - 18));
                const on = k === current ? " on" : "";
                bars += `<rect class="gc-naive${on}" x="${x}" y="${H - 14 - hn}" width="${bw / 2 - 2}" height="${hn}" rx="2"/>`;
                bars += `<rect class="gc-memo${on}" x="${x + bw / 2 - 1}" y="${H - 14 - hm}" width="${bw / 2 - 2}" height="${hm}" rx="2"/>`;
                bars += `<text class="gc-k${on}" x="${x + bw / 2 - 2}" y="${H - 2}">${k}</text>`;
            });
            const ci = current - 1;
            return `<svg class="growth-chart" viewBox="0 0 ${W} ${H}">${bars}</svg>
                <div class="gc-legend"><span><i class="gc-naive"></i>rekursi biasa: <b>${naive[ci] ?? "–"}</b> panggilan</span><span><i class="gc-memo"></i>memo: <b>${memo[ci] ?? "–"}</b></span></div>`;
        }

        // ---------- Mode pohon (rekursi / memo) ----------
        function buildTree() {
            sh.stage.innerHTML = "";
            sh.stage.classList.remove("graph-stage");
            sh.side.innerHTML = "";
            const memoMode = mode === "memo";
            const pseudo = V.codePanel(
                sh.side,
                memoMode
                    ? `
long long memo[100];
bool sudah[100];

long long fib(int n) {                       //@0
    if (sudah[n]) return memo[n];              //@1
    if (n <= 1) return n;                      //@2
    long long hasil = fib(n - 1) + fib(n - 2); //@3
    sudah[n] = true;                           //@4
    memo[n] = hasil;                           //@4
    return hasil;                              //@5
}`
                    : `
long long fib(int n) {                   //@0
    if (n <= 1) return n;                  //@1
    return fib(n - 1) + fib(n - 2);        //@2
}`,
            );
            const counter = V.htmlPanel(sh.side, "Statistik pemanggilan");
            const memoPanel = memoMode ? V.arrayPanel(sh.side, "Tabel memo", "hasil yang diingat") : null;
            const chart = V.htmlPanel(sh.side, "Pertumbuhan pemanggilan", "n = 1..12");
            chart.set(growthChart(n));

            // 1) Bangun pohon pemanggilan
            const nodes = [];
            const memoSet = new Set();
            function grow(k, parent, depth) {
                const node = { id: nodes.length, k, parent, depth, children: [], memo: false };
                nodes.push(node);
                if (parent) parent.children.push(node);
                if (memoMode && memoSet.has(k)) {
                    node.memo = true;
                    return node;
                }
                if (k > 1) {
                    grow(k - 1, node, depth + 1);
                    grow(k - 2, node, depth + 1);
                }
                if (memoMode) memoSet.add(k);
                return node;
            }
            const rootNode = grow(n, null, 0);

            // 2) Tata letak: daun berurutan dari kiri, induk di tengah anak-anaknya
            let leaf = 0;
            (function place(node) {
                if (!node.children.length) {
                    node.x = leaf++;
                } else {
                    node.children.forEach(place);
                    node.x = (node.children[0].x + node.children[node.children.length - 1].x) / 2;
                }
            })(rootNode);
            const maxDepth = Math.max(...nodes.map((d) => d.depth));
            const gapX = 56;
            const gapY = 68;
            const W = Math.max(600, leaf * gapX + 40);
            const H = (maxDepth + 1) * gapY + 46;
            const svgEl = V.svg("svg", { class: "graph tree", viewBox: `0 0 ${W} ${H}`, style: `min-width:${Math.min(W, 1100)}px` }, sh.stage);
            const pos = (d) => ({ x: 20 + gapX / 2 + d.x * gapX + (W - 40 - leaf * gapX) / 2, y: 32 + d.depth * gapY });
            const els = {};
            nodes.forEach((d) => {
                if (!d.parent) return;
                const a = pos(d.parent);
                const b = pos(d);
                els["e" + d.id] = V.svg("path", { class: "tree-edge hidden", d: `M${a.x} ${a.y + 15} C${a.x} ${(a.y + b.y) / 2}, ${b.x} ${(a.y + b.y) / 2}, ${b.x} ${b.y - 15}` }, svgEl);
            });
            nodes.forEach((d) => {
                const p = pos(d);
                const g = V.svg("g", { class: "tree-node hidden", transform: `translate(${p.x} ${p.y})` }, svgEl);
                V.svg("rect", { x: -25, y: -15, width: 50, height: 30, rx: 10 }, g);
                const t = V.svg("text", {}, g);
                t.textContent = `f(${d.k})`;
                const val = V.svg("text", { class: "val", y: 27 }, g);
                els["n" + d.id] = { g, val };
            });

            // 3) Simulasi eksekusi → frame
            const frames = [];
            const state = {};
            const seenK = {};
            let calls = 0;
            let repeats = 0;
            let hits = 0;
            const memo = {};
            const snap = (line, text, fx = {}) =>
                frames.push({
                    line,
                    text,
                    state: JSON.parse(JSON.stringify(state)),
                    calls,
                    repeats,
                    hits,
                    memo: { ...memo },
                    ...fx,
                });

            function run(d) {
                calls++;
                const repeated = !d.memo && (seenK[d.k] || 0) > 0;
                state[d.id] = { cls: "active" + (repeated && !memoMode ? " repeat" : ""), val: "" };
                if (repeated && !memoMode) repeats++;
                snap(0, `Panggil <b>fib(${d.k})</b>.` + (repeated && !memoMode ? ` ⚠️ fib(${d.k}) <b>sudah pernah dihitung</b> sebelumnya, tapi rekursi biasa menghitungnya lagi dari nol!` : ""), {
                    mark: repeated && !memoMode ? "skip" : undefined,
                });
                if (d.memo) {
                    hits++;
                    state[d.id] = { cls: "memo", val: `=${memo[d.k]}` };
                    snap(1, `<code>memo[${d.k}]</code> sudah ada → langsung kembalikan <b>${memo[d.k]}</b> tanpa menghitung ulang. ⚡`, { mark: "discover" });
                    return memo[d.k];
                }
                let result;
                if (d.k <= 1) {
                    result = d.k;
                    state[d.id] = { cls: "done", val: `=${result}` };
                    snap(memoMode ? 2 : 1, `Base case: <b>fib(${d.k}) = ${result}</b>.`);
                } else {
                    const a = run(d.children[0]);
                    state[d.id].cls = "active" + (repeated && !memoMode ? " repeat" : "");
                    snap(memoMode ? 3 : 2, `fib(${d.k - 1}) = ${a}. Sekarang hitung fib(${d.k - 2}).`);
                    const b = run(d.children[1]);
                    result = a + b;
                    state[d.id] = { cls: "done" + (repeated && !memoMode ? " repeat" : ""), val: `=${result}` };
                    const ask = {
                        type: "value",
                        answer: result,
                        prompt: `Anak-anak <b>fib(${d.k})</b> mengembalikan <b>${a}</b> dan <b>${b}</b>. Berapa nilai fib(${d.k})?`,
                        hint: "fib(n) = fib(n−1) + fib(n−2)",
                        maskNode: d.id,
                        context: `Kedua anak fib(${d.k}) sudah selesai.`,
                    };
                    if (memoMode) {
                        memo[d.k] = result;
                        snap(4, `fib(${d.k}) = ${a} + ${b} = <b>${result}</b>. Simpan di <code>memo[${d.k}]</code>.`, { ask, mark: "key" });
                    } else {
                        snap(2, `fib(${d.k}) = ${a} + ${b} = <b>${result}</b>. Kembalikan ke pemanggil.`, { ask, mark: "key" });
                    }
                }
                if (memoMode && d.k <= 1) memo[d.k] = result;
                seenK[d.k] = (seenK[d.k] || 0) + 1;
                return result;
            }
            const ans = run(rootNode);
            snap(
                -1,
                memoMode
                    ? `🎉 fib(${n}) = <b>${ans}</b> hanya dengan <b>${calls}</b> pemanggilan (${hits} di antaranya langsung dari memo). Setiap fib(k) dihitung <b>sekali</b>, jadi O(n).`
                    : `fib(${n}) = <b>${ans}</b>, tapi butuh <b>${calls}</b> pemanggilan, dan <b>${repeats}</b> di antaranya menghitung ulang hal yang sama! Jumlahnya tumbuh eksponensial (≈1,6ⁿ). Coba mode <b>Memoization</b>.`,
                { mark: "done" },
            );

            render = (f) => {
                pseudo.set(f.line);
                const maskNode = f.masked && f.ask ? f.ask.maskNode : undefined;
                nodes.forEach((d) => {
                    const s = f.state[d.id];
                    const el = els["n" + d.id];
                    const masked = maskNode === d.id;
                    el.g.setAttribute("class", "tree-node " + (s ? s.cls : "hidden") + (masked ? " ask" : ""));
                    el.val.textContent = masked ? "=?" : s ? s.val : "";
                    const e = els["e" + d.id];
                    if (e) e.setAttribute("class", "tree-edge" + (s ? "" : " hidden"));
                });
                counter.set(
                    `<div class="counter-box"><div><b>${f.calls}</b><small>pemanggilan</small></div>` +
                        (memoMode
                            ? `<div><b style="color:var(--cyan)">${f.hits}</b><small>dari memo</small></div>`
                            : `<div class="bad"><b>${f.repeats}</b><small>hitung ulang</small></div>`) +
                        "</div>",
                );
                if (memoPanel) {
                    memoPanel.set(Array.from({ length: n + 1 }, (_, i) => ({ key: i, val: f.memo[i] ?? "–", cls: f.memo[i] !== undefined ? "final" : "" })));
                }
            };
            player.load(frames);
        }

        // ---------- Mode tabulasi ----------
        function buildTable() {
            sh.side.innerHTML = "";
            const pseudo = V.codePanel(
                sh.side,
                `
vector<long long> dp(n + 1);
dp[0] = 0;                            //@0
dp[1] = 1;                            //@1
for (int i = 2; i <= n; i++)          //@2
    dp[i] = dp[i - 1] + dp[i - 2];    //@3
cout << dp[n] << '\\n';              //@4`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const table = V.tableView(sh.stage, { rows: 1, cols: n + 1, colHead: Array.from({ length: n + 1 }, (_, i) => i), rowHead: ["dp"], cell: 48 });
            const dp = new Array(n + 1).fill(null);
            const frames = [];
            const snap = (line, text, cur, extra = {}) => {
                const cls = {};
                dp.forEach((v, i) => v !== null && i <= 1 && (cls[`0,${i}`] = "base"));
                if (cur !== undefined) cls[`0,${cur}`] = "current";
                (extra.deps || []).forEach((d, k) => (cls[`0,${d}`] = k ? "dep alt" : "dep"));
                frames.push({
                    line,
                    text,
                    vals: [[...dp]],
                    cls,
                    hlCol: cur,
                    arrows: (extra.deps || []).map((d, k) => ({ from: [0, d], to: [0, cur], alt: k > 0, bend: k ? -0.55 : -0.35 })),
                    formula: extra.formula || "",
                    formulaQ: extra.formulaQ || "",
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };
            snap(-1, `Daripada rekursi dari atas, kita isi tabel <b>dari bawah</b>: mulai dari kasus terkecil sampai n = ${n}.`);
            dp[0] = 0;
            snap(0, "Base case: <code>dp[0] = 0</code>.", 0);
            dp[1] = 1;
            snap(1, "Base case: <code>dp[1] = 1</code>.", 1);
            for (let i = 2; i <= n; i++) {
                dp[i] = dp[i - 1] + dp[i - 2];
                snap(3, `<code>dp[${i}] = dp[${i - 1}] + dp[${i - 2}] = ${dp[i - 1]} + ${dp[i - 2]} = ${dp[i]}</code>. Kedua nilai itu <b>sudah ada di tabel</b>, jadi tidak perlu dihitung ulang.`, i, {
                    deps: [i - 1, i - 2],
                    formula: `dp[${i}] = <span class="a">${dp[i - 1]}</span> + <span class="b">${dp[i - 2]}</span> = <span class="r">${dp[i]}</span>`,
                    formulaQ: `dp[${i}] = <span class="a">dp[${i - 1}]</span> + <span class="b">dp[${i - 2}]</span> = <span class="r">?</span>`,
                    mark: "key",
                    ask: { type: "value", answer: dp[i], prompt: `Berapa nilai <code>dp[${i}]</code>?`, hint: "Jumlahkan dua sel sebelumnya.", mask: [0, i], context: `Menghitung sel <b>dp[${i}]</b>.` },
                });
            }
            snap(4, `🎉 fib(${n}) = <b>${dp[n]}</b>. Tabel terisi dalam <b>${n + 1}</b> langkah: O(n) waktu, tanpa rekursi sama sekali.`, n, { mark: "done" });

            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        const build = () => (mode === "table" ? buildTable() : buildTree());
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            fillN();
            build();
        });
        nSelect.onchange = () => {
            n = +nSelect.value;
            build();
        };
        fillN();
        build();
    });

    // ════════════════════════════ DP 1D: tangga & koin ════════════════════════════
    V.register("coin", (root) => {
        const sh = V.shell(root, {
            title: "DP 1 Dimensi",
            controls: `
                ${V.segmented("mode", [["ways", "Banyak cara (tangga)"], ["min", "Koin minimum"]], "ways")}
                <label class="viz-input" data-set-label><span>Langkah</span> <input data-set value="1, 2"></label>
                <label class="viz-input">Target <input class="short" data-n value="7"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sel yang dihitung", "#f59e0b", "#f59e0b"],
                ["Sel yang dibutuhkan", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Base case", "#a78bfa", "rgba(139,92,246,.14)"],
                ["Jawaban / jejak", "#22c55e", "rgba(34,197,94,.14)"],
            ],
        });
        let mode = "ways";
        const setInput = sh.head.querySelector("[data-set]");
        const nInput = sh.head.querySelector("[data-n]");
        let render = () => {};

        function build() {
            const S = [...new Set(V.parseList(setInput.value, { min: 1, max: 15, limit: 5 }))].sort((a, b) => a - b);
            if (!S.length) S.push(1);
            setInput.value = S.join(", ");
            const n = V.clampInt(nInput.value, 1, 15, 7);
            nInput.value = n;
            sh.side.innerHTML = "";
            const pseudo = V.codePanel(
                sh.side,
                mode === "ways"
                    ? `
vector<long long> dp(n + 1, 0);
dp[0] = 1;   // satu cara: diam di bawah          //@0
for (int i = 1; i <= n; i++) {                    //@1
    dp[i] = 0;                                    //@2
    for (int s : langkah)                         //@3
        if (s <= i) dp[i] += dp[i - s];           //@4
}
cout << dp[n] << '\\n';                          //@5`
                    : `
const int INF = 1e9;
vector<int> dp(n + 1, INF);
dp[0] = 0;   // nominal 0 butuh 0 koin            //@0
for (int x = 1; x <= n; x++) {                    //@1
    dp[x] = INF;                                  //@2
    for (int c : koin)                            //@3
        if (c <= x && dp[x - c] != INF)           //@4
            dp[x] = min(dp[x], dp[x - c] + 1);    //@4
}
// INF berarti nominal n mustahil dibentuk
cout << (dp[n] == INF ? -1 : dp[n]) << '\\n';     //@5`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const setPanel = V.dsPanel(sh.side, mode === "ways" ? "Pilihan langkah" : "Jenis koin");
            const table = V.tableView(sh.stage, { rows: 1, cols: n + 1, colHead: Array.from({ length: n + 1 }, (_, i) => i), rowHead: ["dp"], cell: 46 });
            const dp = new Array(n + 1).fill(null);
            const frames = [];
            const snap = (line, text, cur, extra = {}) => {
                const cls = { "0,0": "base" };
                if (cur !== undefined) cls[`0,${cur}`] = "current";
                (extra.deps || []).forEach((d) => (cls[`0,${d}`] = "dep"));
                (extra.path || []).forEach((d) => (cls[`0,${d}`] = "path"));
                frames.push({
                    line,
                    text,
                    vals: [dp.map((v) => (v === null ? null : fmtVal(v)))],
                    cls,
                    hlCol: cur,
                    arrows: (extra.deps || []).map((d, k) => ({ from: [0, d], to: [0, cur], bend: -0.3 - k * 0.12, label: extra.labels ? extra.labels[k] : undefined })),
                    pathLine: extra.pathLine,
                    formula: extra.formula || "",
                    formulaQ: extra.formulaQ || "",
                    set: S.map((s) => ({ label: s, cls: (extra.hotSet || []).includes(s) ? "hot" : "" })),
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };

            if (mode === "ways") {
                snap(-1, `Ada berapa cara mencapai anak tangga <b>${n}</b> jika setiap langkah naik <b>${S.join(" atau ")}</b>? <code>dp[i]</code> = banyak cara berdiri di anak tangga i.`);
                dp[0] = 1;
                snap(0, "Base case: <code>dp[0] = 1</code>, ada tepat satu cara berada di bawah, yaitu tidak melangkah.", 0);
                for (let i = 1; i <= n; i++) {
                    const steps = S.filter((s) => s <= i);
                    const deps = steps.map((s) => i - s);
                    snap(3, `Anak tangga <b>${i}</b> bisa dicapai dari ${deps.map((d) => `<b>${d}</b>`).join(", ") || "tidak ada"} (langkah terakhir ${steps.join("/") || "-"}).`, i, {
                        deps,
                        hotSet: steps,
                        labels: steps.map((s) => `+${s}`),
                        formula: `dp[${i}] = ${deps.map((d) => `<span class="a">dp[${d}]</span>`).join(" + ") || "0"}`,
                    });
                    dp[i] = deps.reduce((s, d) => s + dp[d], 0);
                    snap(4, `Jumlahkan semua cara: <code>dp[${i}] = ${deps.map((d) => dp[d]).join(" + ") || 0} = ${dp[i]}</code>.`, i, {
                        deps,
                        hotSet: steps,
                        labels: steps.map((s) => `+${s}`),
                        formula: `dp[${i}] = ${deps.map((d) => `<span class="a">${dp[d]}</span>`).join(" + ") || "0"} = <span class="r">${dp[i]}</span>`,
                        formulaQ: `dp[${i}] = ${deps.map((d) => `<span class="a">${dp[d]}</span>`).join(" + ") || "0"} = <span class="r">?</span>`,
                        mark: "key",
                        ask: { type: "value", answer: dp[i], prompt: `Berapa banyak cara mencapai anak tangga ${i}? (<code>dp[${i}]</code>)`, hint: "Jumlahkan nilai sel-sel yang ditunjuk panah.", mask: [0, i], context: `Menghitung <b>dp[${i}]</b>.` },
                    });
                }
                snap(5, `🎉 Ada <b>${dp[n]}</b> cara berbeda untuk mencapai anak tangga ${n}.`, undefined, { path: [n], mark: "done" });
            } else {
                snap(-1, `Bentuk nominal <b>${n}</b> dengan koin {${S.join(", ")}} sesedikit mungkin. <code>dp[x]</code> = koin minimum untuk nominal x.`);
                dp[0] = 0;
                snap(0, "Base case: <code>dp[0] = 0</code>.", 0);
                const choice = new Array(n + 1).fill(0);
                for (let x = 1; x <= n; x++) {
                    const usable = S.filter((c) => c <= x);
                    const deps = usable.map((c) => x - c);
                    snap(3, usable.length ? `Untuk nominal <b>${x}</b>, coba setiap koin terakhir: ${usable.map((c) => `koin ${c} → sisa ${x - c}`).join(", ")}.` : `Tidak ada koin ≤ ${x}.`, x, {
                        deps,
                        hotSet: usable,
                        labels: usable.map((c) => `+${c}`),
                        formula: usable.length ? `dp[${x}] = min(${deps.map((d) => `<span class="a">dp[${d}]</span>+1`).join(", ")})` : `dp[${x}] = ∞`,
                    });
                    let best = Infinity;
                    usable.forEach((c) => {
                        if (dp[x - c] + 1 < best) {
                            best = dp[x - c] + 1;
                            choice[x] = c;
                        }
                    });
                    dp[x] = best;
                    snap(
                        4,
                        best === Infinity ? `Semua sisa mustahil dibentuk, jadi <code>dp[${x}] = ∞</code>.` : `Pilihan terbaik: koin <b>${choice[x]}</b> + dp[${x - choice[x]}] → <code>dp[${x}] = ${best}</code>.`,
                        x,
                        {
                            deps,
                            hotSet: usable,
                            labels: usable.map((c) => `+${c}`),
                            formula: usable.length
                                ? `dp[${x}] = min(${deps.map((d) => `<span class="a">${fmtVal(dp[d])}</span>+1`).join(", ")}) = <span class="r">${fmtVal(best)}</span>`
                                : `dp[${x}] = <span class="r">∞</span>`,
                            formulaQ: usable.length
                                ? `dp[${x}] = min(${deps.map((d) => `<span class="a">${fmtVal(dp[d])}</span>+1`).join(", ")}) = <span class="r">?</span>`
                                : "",
                            mark: "key",
                            ask: usable.length
                                ? {
                                      type: "value",
                                      answer: best === Infinity ? "∞" : best,
                                      accept: best === Infinity ? ["inf", "tak hingga", "-1", "mustahil"] : [],
                                      prompt: `Berapa koin minimum untuk nominal ${x}? (<code>dp[${x}]</code>)`,
                                      hint: "Ambil yang terkecil di antara dp[x − c] + 1. Ketik ∞ atau 'inf' jika mustahil.",
                                      mask: [0, x],
                                      context: `Menghitung <b>dp[${x}]</b>.`,
                                  }
                                : undefined,
                        },
                    );
                }
                if (dp[n] === Infinity) {
                    snap(5, `Nominal <b>${n}</b> <b>mustahil</b> dibentuk dengan koin {${S.join(", ")}}.`, n, { mark: "done" });
                } else {
                    const path = [];
                    const used = [];
                    for (let x = n; x > 0; x -= choice[x]) {
                        path.push(x);
                        used.push(choice[x]);
                        snap(5, `Telusuri balik: dari ${x} pakai koin <b>${choice[x]}</b> → ke ${x - choice[x]}.`, undefined, {
                            path: [...path],
                            pathLine: path.map((p) => [0, p]),
                        });
                    }
                    path.push(0);
                    snap(5, `🎉 Minimum <b>${dp[n]}</b> koin: <b>${used.join(" + ")}</b> = ${n}.`, undefined, { path, pathLine: path.map((p) => [0, p]), mark: "done" });
                }
            }
            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
                setPanel.set(f.set);
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            sh.head.querySelector("[data-set-label] span").textContent = v === "ways" ? "Langkah" : "Koin";
            setInput.value = v === "ways" ? "1, 2" : "1, 3, 4";
            nInput.value = v === "ways" ? 7 : 6;
            build();
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [setInput, nInput].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });

    // ════════════════════════════ DP grid: banyak jalur ════════════════════════════
    V.register("grid", (root) => {
        const sh = V.shell(root, {
            title: "DP Grid: Menghitung Jalur",
            controls: `
                <label class="viz-input">Baris <input class="short" data-r value="4"></label>
                <label class="viz-input">Kolom <input class="short" data-c value="5"></label>
                <button class="btn btn-sm" data-random>🎲 Rintangan</button>
                <button class="btn btn-sm btn-ghost" data-clear>Kosongkan</button>
                <button class="btn btn-sm btn-ghost" data-heat>🌡 Heatmap</button>`,
            legend: [
                ["Sel dihitung", "#f59e0b", "#f59e0b"],
                ["Dari atas", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Dari kiri", "#ec4899", "rgba(236,72,153,.14)"],
                ["Rintangan (klik sel untuk ubah)", "#ef4444", "#341a1d"],
            ],
        });
        const rIn = sh.head.querySelector("[data-r]");
        const cIn = sh.head.querySelector("[data-c]");
        const heatBtn = sh.head.querySelector("[data-heat]");
        let heat = false;
        let R = 4;
        let C = 5;
        let blocked = new Set(["1,1", "2,3"]);
        let render = () => {};
        let table = null;

        function build() {
            R = V.clampInt(rIn.value, 2, 7, 4);
            C = V.clampInt(cIn.value, 2, 8, 5);
            rIn.value = R;
            cIn.value = C;
            blocked = new Set(
                [...blocked].filter((k) => {
                    const [r, c] = k.split(",").map(Number);
                    return r < R && c < C && !(r === 0 && c === 0) && !(r === R - 1 && c === C - 1);
                }),
            );
            sh.side.innerHTML = "";
            const pseudo = V.codePanel(
                sh.side,
                `
dp[0][0] = 1;                                      //@0
for (int i = 0; i < R; i++)                        //@1
    for (int j = 0; j < C; j++) {                  //@1
        if (i == 0 && j == 0) continue;
        if (petak[i][j] == '#') {                  //@2
            dp[i][j] = 0;   // rintangan           //@2
            continue;
        }
        long long atas = i > 0 ? dp[i - 1][j] : 0; //@3,4
        long long kiri = j > 0 ? dp[i][j - 1] : 0; //@5
        dp[i][j] = atas + kiri;                    //@6
    }`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            table = V.tableView(sh.stage, {
                rows: R,
                cols: C,
                rowHead: Array.from({ length: R }, (_, i) => i),
                colHead: Array.from({ length: C }, (_, j) => j),
                cell: 52,
                heat,
            });
            table.onCellClick((r, c) => {
                if ((r === 0 && c === 0) || (r === R - 1 && c === C - 1)) return;
                const k = `${r},${c}`;
                blocked.has(k) ? blocked.delete(k) : blocked.add(k);
                build();
            });

            const dp = Array.from({ length: R }, () => new Array(C).fill(null));
            const frames = [];
            const snap = (line, text, cur, extra = {}) => {
                const cls = {};
                blocked.forEach((k) => (cls[k] = "block"));
                cls["0,0"] = cls["0,0"] || "base";
                if (cur) cls[cur.join(",")] = "current";
                if (extra.top) cls[extra.top.join(",")] = "dep";
                if (extra.left) cls[extra.left.join(",")] = "dep alt";
                (extra.path || []).forEach((p) => (cls[p] = "path"));
                const vals = dp.map((row, r) => row.map((v, c) => (blocked.has(`${r},${c}`) ? "✕" : v)));
                frames.push({
                    line,
                    text,
                    vals,
                    cls,
                    hlRow: cur ? cur[0] : undefined,
                    hlCol: cur ? cur[1] : undefined,
                    arrows: [extra.top && { from: extra.top, to: cur, bend: 0, label: "atas" }, extra.left && { from: extra.left, to: cur, alt: true, bend: 0, label: "kiri" }].filter(Boolean),
                    pathLine: extra.pathLine,
                    formula: extra.formula || "",
                    formulaQ: extra.formulaQ || "",
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };
            snap(-1, `Robot berangkat dari kiri atas (0,0) ke kanan bawah (${R - 1},${C - 1}), hanya boleh ke <b>kanan</b> atau <b>bawah</b>. Klik sel untuk menambah atau menghapus rintangan.`);
            for (let i = 0; i < R; i++) {
                for (let j = 0; j < C; j++) {
                    const cur = [i, j];
                    if (i === 0 && j === 0) {
                        dp[0][0] = 1;
                        snap(0, "Base case: ada <b>1</b> cara berada di titik awal.", cur);
                        continue;
                    }
                    if (blocked.has(`${i},${j}`)) {
                        dp[i][j] = 0;
                        snap(2, `Sel (${i},${j}) adalah <b>rintangan</b>, jadi tidak ada jalur yang melewatinya: <code>dp = 0</code>.`, cur, { mark: "skip" });
                        continue;
                    }
                    const top = i > 0 ? [i - 1, j] : null;
                    const left = j > 0 ? [i, j - 1] : null;
                    const a = top ? dp[i - 1][j] : 0;
                    const b = left ? dp[i][j - 1] : 0;
                    dp[i][j] = a + b;
                    snap(
                        6,
                        `Sel (${i},${j}) dicapai dari ${top ? `<b>atas</b> (${a} jalur)` : "atas: tidak ada"} dan ${left ? `<b>kiri</b> (${b} jalur)` : "kiri: tidak ada"} → <code>dp = ${dp[i][j]}</code>.`,
                        cur,
                        {
                            top,
                            left,
                            formula: `dp[${i}][${j}] = <span class="a">${top ? a : "0"}</span> + <span class="b">${left ? b : "0"}</span> = <span class="r">${dp[i][j]}</span>`,
                            formulaQ: `dp[${i}][${j}] = <span class="a">${top ? a : "0"}</span> + <span class="b">${left ? b : "0"}</span> = <span class="r">?</span>`,
                            ask: {
                                type: "value",
                                answer: dp[i][j],
                                prompt: `Ada berapa jalur menuju sel (${i},${j})?`,
                                hint: "Jumlahkan jalur dari atas dan dari kiri (0 jika di luar grid).",
                                mask: [i, j],
                                context: `Menghitung sel <b>(${i},${j})</b>.`,
                            },
                        },
                    );
                }
            }
            const ans = dp[R - 1][C - 1];
            const path = [];
            if (ans > 0) {
                let i = R - 1;
                let j = C - 1;
                path.push([i, j]);
                while (i > 0 || j > 0) {
                    if (i > 0 && dp[i - 1][j] > 0) i--;
                    else j--;
                    path.push([i, j]);
                }
                path.reverse();
            }
            snap(
                -1,
                ans > 0 ? `🎉 Ada <b>${ans}</b> jalur berbeda menuju pojok kanan bawah. Garis hijau adalah salah satu contohnya.` : "Tidak ada jalur sama sekali, karena rintangan menutup semua rute.",
                null,
                { path: path.map((p) => p.join(",")), pathLine: path, mark: "done" },
            );

            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        [rIn, cIn].forEach((el) => el.addEventListener("change", build));
        heatBtn.onclick = () => {
            heat = !heat;
            heatBtn.classList.toggle("active", heat);
            table?.setHeat(heat);
        };
        sh.head.querySelector("[data-random]").onclick = () => {
            blocked = new Set();
            const count = Math.floor(R * C * 0.18);
            for (let k = 0; k < count * 3 && blocked.size < count; k++) {
                const r = V.rand(0, R - 1);
                const c = V.rand(0, C - 1);
                if ((r === 0 && c === 0) || (r === R - 1 && c === C - 1)) continue;
                blocked.add(`${r},${c}`);
            }
            build();
        };
        sh.head.querySelector("[data-clear]").onclick = () => {
            blocked = new Set();
            build();
        };
        build();
    });

    // ════════════════════════════ Knapsack 0/1 ════════════════════════════
    V.register("knapsack", (root) => {
        const sh = V.shell(root, {
            title: "Knapsack 0/1",
            controls: `
                <label class="viz-input">Barang (berat:nilai) <input data-items value="1:1, 3:4, 4:5, 5:7" style="width:170px"></label>
                <label class="viz-input">Kapasitas <input class="short" data-w value="7"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>
                <button class="btn btn-sm" data-random title="Barang acak">🎲</button>
                <button class="btn btn-sm btn-ghost" data-heat>🌡 Heatmap</button>`,
            legend: [
                ["Sel dihitung", "#f59e0b", "#f59e0b"],
                ["Tidak ambil (atas)", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Ambil (diagonal)", "#ec4899", "rgba(236,72,153,.14)"],
                ["Jejak pilihan optimal", "#22c55e", "rgba(34,197,94,.14)"],
            ],
        });
        const itemsIn = sh.head.querySelector("[data-items]");
        const wIn = sh.head.querySelector("[data-w]");
        const heatBtn = sh.head.querySelector("[data-heat]");
        let heat = false;
        let table = null;
        const ICONS = ["🎒", "🔦", "⛺", "🥾", "🍫", "🧭"];
        let render = () => {};

        function parseItems() {
            const items = itemsIn.value
                .split(/[,;]+/)
                .map((s) => s.trim().split(/[:\s]+/).map(Number))
                .filter(([w, v]) => Number.isInteger(w) && Number.isInteger(v) && w >= 1 && w <= 10 && v >= 1 && v <= 99)
                .slice(0, 6);
            return items.length ? items : [[1, 1]];
        }

        function build() {
            const items = parseItems();
            itemsIn.value = items.map(([w, v]) => `${w}:${v}`).join(", ");
            const W = V.clampInt(wIn.value, 1, 10, 7);
            wIn.value = W;
            const n = items.length;
            sh.side.innerHTML = "";
            const pseudo = V.codePanel(
                sh.side,
                `
// baris 0 (tanpa barang) bernilai 0 semua
vector<vector<int>> dp(n + 1, vector<int>(W + 1, 0));   //@0
for (int i = 1; i <= n; i++)          // barang ke-i     //@1
    for (int c = 0; c <= W; c++) {    // kapasitas       //@2
        dp[i][c] = dp[i - 1][c];      // tidak diambil   //@3
        if (w[i] <= c)                                   //@4
            dp[i][c] = max(dp[i][c],                     //@5
                           dp[i - 1][c - w[i]] + v[i]);  //@5
    }`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const itemPanel = V.htmlPanel(sh.side, "Daftar barang");
            table = V.tableView(sh.stage, {
                rows: n + 1,
                cols: W + 1,
                rowHead: ["–", ...items.map((_, i) => `${ICONS[i]}${i + 1}`)],
                colHead: Array.from({ length: W + 1 }, (_, c) => c),
                corner: "i \\ c",
                cell: 44,
                heat,
            });
            const dp = Array.from({ length: n + 1 }, () => new Array(W + 1).fill(null));
            const frames = [];
            const itemsHtml = (hot, picked = [], cap = null) =>
                `<div class="items-list">${items
                    .map(
                        ([w, v], i) =>
                            `<div class="item-row ${hot === i + 1 ? "hot" : ""} ${picked.includes(i + 1) ? "pick" : ""}"><span class="ic">${ICONS[i]}</span><span>berat ${w}</span><span>nilai ${v}</span></div>`,
                    )
                    .join("")}</div>` +
                (cap !== null
                    ? `<div class="cap-meter"><div class="cap-bar"><i style="width:${(cap / W) * 100}%"></i></div><small>Isi ransel: <b>${cap}</b> / ${W} kg</small></div>`
                    : "");
            const snap = (line, text, cur, extra = {}) => {
                const cls = {};
                for (let c = 0; c <= W; c++) if (dp[0][c] !== null) cls[`0,${c}`] = "base";
                if (cur) cls[cur.join(",")] = "current";
                if (extra.top) cls[extra.top.join(",")] = "dep";
                if (extra.diag) cls[extra.diag.join(",")] = "dep alt";
                (extra.path || []).forEach((p) => (cls[p] = "path"));
                frames.push({
                    line,
                    text,
                    vals: dp.map((r) => [...r]),
                    cls,
                    hlRow: cur ? cur[0] : extra.hlRow,
                    hlCol: cur ? cur[1] : undefined,
                    arrows: [
                        extra.top && { from: extra.top, to: cur, bend: 0, label: "skip" },
                        extra.diag && { from: extra.diag, to: cur, alt: true, bend: 0.12, label: `+${extra.v}` },
                    ].filter(Boolean),
                    pathLine: extra.pathLine,
                    formula: extra.formula || "",
                    formulaQ: extra.formulaQ || "",
                    items: itemsHtml(cur ? cur[0] : extra.hot, extra.picked || [], extra.cap ?? null),
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };

            snap(-1, `Ransel berkapasitas <b>${W}</b>. Setiap barang hanya bisa <b>diambil atau tidak</b>. Sel <code>dp[i][c]</code> = nilai terbaik memakai barang 1..i dengan kapasitas c.`);
            for (let c = 0; c <= W; c++) dp[0][c] = 0;
            snap(0, "Baris 0: tanpa barang apa pun, nilainya selalu <b>0</b>.");
            for (let i = 1; i <= n; i++) {
                const [w, v] = items[i - 1];
                snap(1, `Sekarang pertimbangkan barang <b>${ICONS[i - 1]} ${i}</b> (berat ${w}, nilai ${v}).`, null, { hot: i, hlRow: i, mark: "key" });
                for (let c = 0; c <= W; c++) {
                    const skip = dp[i - 1][c];
                    const ask = (ans) => ({
                        type: "value",
                        answer: ans,
                        prompt: `Berapa nilai <code>dp[${i}][${c}]</code>?`,
                        hint: w <= c ? "Ambil yang terbesar: tidak ambil (sel atas) atau ambil (sel diagonal + nilai barang)." : "Barang tidak muat, jadi salin nilai sel di atasnya.",
                        mask: [i, c],
                        context: `Barang <b>${i}</b> (berat ${w}, nilai ${v}) dengan kapasitas <b>${c}</b>.`,
                    });
                    if (w <= c) {
                        const take = dp[i - 1][c - w] + v;
                        dp[i][c] = Math.max(skip, take);
                        snap(
                            5,
                            `Kapasitas ${c}: <b>tidak ambil</b> = ${skip}, <b>ambil</b> = dp[${i - 1}][${c - w}] + ${v} = ${take}. Pilih ${take > skip ? "<b>ambil</b>" : "<b>tidak ambil</b>"} → <code>${dp[i][c]}</code>.`,
                            [i, c],
                            {
                                top: [i - 1, c],
                                diag: [i - 1, c - w],
                                v,
                                formula: `max(<span class="a">${skip}</span>, <span class="b">${dp[i - 1][c - w]}</span>+${v}) = <span class="r">${dp[i][c]}</span>`,
                                formulaQ: `max(<span class="a">${skip}</span>, <span class="b">${dp[i - 1][c - w]}</span>+${v}) = <span class="r">?</span>`,
                                ask: ask(dp[i][c]),
                            },
                        );
                    } else {
                        dp[i][c] = skip;
                        snap(3, `Kapasitas ${c} &lt; berat ${w}: barang ${i} <b>tidak muat</b>, jadi salin nilai dari atas → <code>${skip}</code>.`, [i, c], {
                            top: [i - 1, c],
                            formula: `dp[${i}][${c}] = <span class="a">dp[${i - 1}][${c}]</span> = <span class="r">${skip}</span>`,
                            formulaQ: `dp[${i}][${c}] = <span class="a">dp[${i - 1}][${c}]</span> = <span class="r">?</span>`,
                            ask: ask(skip),
                        });
                    }
                }
            }
            // Telusur balik
            const path = [];
            const pathLine = [];
            const picked = [];
            let c = W;
            let cap = 0;
            for (let i = n; i >= 1; i--) {
                path.push(`${i},${c}`);
                pathLine.push([i, c]);
                const took = dp[i][c] !== dp[i - 1][c];
                const q = {
                    type: "choice",
                    options: ["✅ Diambil", "❌ Tidak diambil"],
                    answer: took ? 0 : 1,
                    prompt: `Telusur balik di <code>dp[${i}][${c}] = ${dp[i][c]}</code> (sel atasnya ${dp[i - 1][c]}). Apakah barang ${ICONS[i - 1]} ${i} diambil?`,
                    hint: "Jika nilainya berbeda dari sel di atasnya, berarti barang itu diambil.",
                    context: "Menelusuri pilihan optimal dari pojok kanan bawah.",
                };
                if (took) {
                    picked.push(i);
                    cap += items[i - 1][0];
                    snap(-1, `Telusur balik: <code>dp[${i}][${c}] = ${dp[i][c]}</code> ≠ dp[${i - 1}][${c}] = ${dp[i - 1][c]} → barang <b>${ICONS[i - 1]} ${i}</b> <b>diambil</b>.`, null, {
                        path: [...path],
                        pathLine: [...pathLine],
                        picked: [...picked],
                        hot: i,
                        cap,
                        mark: "take",
                        ask: q,
                    });
                    c -= items[i - 1][0];
                } else {
                    snap(-1, `Telusur balik: <code>dp[${i}][${c}]</code> sama dengan baris atasnya → barang ${i} <b>tidak diambil</b>.`, null, {
                        path: [...path],
                        pathLine: [...pathLine],
                        picked: [...picked],
                        hot: i,
                        cap,
                        mark: "skip",
                        ask: q,
                    });
                }
            }
            path.push(`0,${c}`);
            pathLine.push([0, c]);
            snap(-1, `🎉 Nilai maksimum <b>${dp[n][W]}</b> dengan membawa barang <b>${[...picked].reverse().join(", ") || "–"}</b> (total berat ${cap} ≤ ${W}).`, null, {
                path,
                pathLine,
                picked,
                cap,
                mark: "done",
            });

            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
                itemPanel.set(f.items);
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        sh.head.querySelector("[data-apply]").onclick = build;
        [itemsIn, wIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        heatBtn.onclick = () => {
            heat = !heat;
            heatBtn.classList.toggle("active", heat);
            table?.setHeat(heat);
        };
        sh.head.querySelector("[data-random]").onclick = () => {
            const k = V.rand(3, 5);
            itemsIn.value = Array.from({ length: k }, () => `${V.rand(1, 6)}:${V.rand(1, 12)}`).join(", ");
            wIn.value = V.rand(5, 10);
            build();
        };
        build();
    });

    // ════════════════════════════ LCS ════════════════════════════
    V.register("lcs", (root) => {
        const sh = V.shell(root, {
            title: "Longest Common Subsequence",
            controls: `
                <label class="viz-input">A <input data-a value="ACGTAG" style="width:100px"></label>
                <label class="viz-input">B <input data-b value="CATAGG" style="width:100px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>
                <button class="btn btn-sm btn-ghost" data-heat>🌡 Heatmap</button>`,
            legend: [
                ["Sel dihitung", "#f59e0b", "#f59e0b"],
                ["Huruf sama: diagonal + 1", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Huruf beda: max(atas, kiri)", "#ec4899", "rgba(236,72,153,.14)"],
                ["Jejak LCS", "#22c55e", "rgba(34,197,94,.14)"],
            ],
        });
        const aIn = sh.head.querySelector("[data-a]");
        const bIn = sh.head.querySelector("[data-b]");
        const heatBtn = sh.head.querySelector("[data-heat]");
        let heat = false;
        let table = null;
        let render = () => {};

        function build() {
            const clean = (s) =>
                s
                    .toUpperCase()
                    .replace(/[^A-Z]/g, "")
                    .slice(0, 8) || "AB";
            const A = clean(aIn.value);
            const B = clean(bIn.value);
            aIn.value = A;
            bIn.value = B;
            const n = A.length;
            const m = B.length;
            sh.side.innerHTML = "";
            const pseudo = V.codePanel(
                sh.side,
                `
int n = A.size(), m = B.size();
// baris 0 dan kolom 0 = string kosong, bernilai 0
vector<vector<int>> dp(n + 1, vector<int>(m + 1, 0));   //@0
for (int i = 1; i <= n; i++)                             //@1
    for (int j = 1; j <= m; j++) {                       //@2
        if (A[i - 1] == B[j - 1])                        //@3
            dp[i][j] = dp[i - 1][j - 1] + 1;             //@4
        else                                             //@5
            dp[i][j] = max(dp[i - 1][j], dp[i][j - 1]);  //@6
    }`,
            );
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const result = V.htmlPanel(sh.side, "LCS terbentuk");
            table = V.tableView(sh.stage, {
                rows: n + 1,
                cols: m + 1,
                rowHead: ["∅", ...A.split("")],
                colHead: ["∅", ...B.split("")],
                rowHeadBig: true,
                colHeadBig: true,
                cell: 44,
                heat,
            });
            const dp = Array.from({ length: n + 1 }, () => new Array(m + 1).fill(null));
            const frames = [];
            const snap = (line, text, cur, extra = {}) => {
                const cls = {};
                for (let i = 0; i <= n; i++) for (let j = 0; j <= m; j++) if ((i === 0 || j === 0) && dp[i][j] !== null) cls[`${i},${j}`] = "base";
                if (cur) cls[cur.join(",")] = "current";
                (extra.deps || []).forEach((d) => (cls[d.at.join(",")] = d.alt ? "dep alt" : "dep"));
                (extra.path || []).forEach((p) => (cls[p] = "path"));
                frames.push({
                    line,
                    text,
                    vals: dp.map((r) => [...r]),
                    cls,
                    hlRow: cur ? cur[0] : extra.hlRow,
                    hlCol: cur ? cur[1] : extra.hlCol,
                    arrows: (extra.deps || []).map((d) => ({ from: d.at, to: cur, alt: d.alt, bend: 0, label: d.label })),
                    pathLine: extra.pathLine,
                    formula: extra.formula || "",
                    formulaQ: extra.formulaQ || "",
                    lcs: extra.lcs ?? "",
                    mark: extra.mark,
                    ask: extra.ask,
                });
            };

            snap(-1, `Bandingkan <b>${A}</b> dan <b>${B}</b>. <code>dp[i][j]</code> = panjang LCS dari <b>i huruf pertama A</b> dan <b>j huruf pertama B</b>.`);
            for (let i = 0; i <= n; i++) dp[i][0] = 0;
            for (let j = 0; j <= m; j++) dp[0][j] = 0;
            snap(0, "Baris & kolom ∅: LCS dengan string kosong selalu <b>0</b>.");
            for (let i = 1; i <= n; i++) {
                for (let j = 1; j <= m; j++) {
                    const cur = [i, j];
                    const ask = (ans) => ({
                        type: "value",
                        answer: ans,
                        prompt: `Huruf A[${i}] = '<b>${A[i - 1]}</b>' dan B[${j}] = '<b>${B[j - 1]}</b>'. Berapa <code>dp[${i}][${j}]</code>?`,
                        hint: A[i - 1] === B[j - 1] ? "Huruf sama: ambil sel diagonal lalu tambah 1." : "Huruf beda: ambil yang terbesar antara sel atas dan sel kiri.",
                        mask: cur,
                        context: `Membandingkan '<b>${A[i - 1]}</b>' dan '<b>${B[j - 1]}</b>'.`,
                    });
                    if (A[i - 1] === B[j - 1]) {
                        dp[i][j] = dp[i - 1][j - 1] + 1;
                        snap(4, `A[${i}] = '<b>${A[i - 1]}</b>' sama dengan B[${j}] = '<b>${B[j - 1]}</b>'! Perpanjang LCS diagonal: ${dp[i - 1][j - 1]} + 1 = <b>${dp[i][j]}</b>.`, cur, {
                            deps: [{ at: [i - 1, j - 1], label: "+1" }],
                            formula: `dp[${i}][${j}] = <span class="a">${dp[i - 1][j - 1]}</span> + 1 = <span class="r">${dp[i][j]}</span>`,
                            formulaQ: `dp[${i}][${j}] = <span class="a">${dp[i - 1][j - 1]}</span> + 1 = <span class="r">?</span>`,
                            mark: "take",
                            ask: ask(dp[i][j]),
                        });
                    } else {
                        dp[i][j] = Math.max(dp[i - 1][j], dp[i][j - 1]);
                        snap(6, `'${A[i - 1]}' ≠ '${B[j - 1]}'. Buang salah satu huruf dan ambil yang terbaik: max(${dp[i - 1][j]}, ${dp[i][j - 1]}) = <b>${dp[i][j]}</b>.`, cur, {
                            deps: [
                                { at: [i - 1, j], alt: true },
                                { at: [i, j - 1], alt: true },
                            ],
                            formula: `max(<span class="b">${dp[i - 1][j]}</span>, <span class="b">${dp[i][j - 1]}</span>) = <span class="r">${dp[i][j]}</span>`,
                            formulaQ: `max(<span class="b">${dp[i - 1][j]}</span>, <span class="b">${dp[i][j - 1]}</span>) = <span class="r">?</span>`,
                            ask: ask(dp[i][j]),
                        });
                    }
                }
            }
            // Rekonstruksi
            let i = n;
            let j = m;
            const path = [`${i},${j}`];
            const pathLine = [[i, j]];
            let lcs = "";
            while (i > 0 && j > 0) {
                if (A[i - 1] === B[j - 1]) {
                    lcs = A[i - 1] + lcs;
                    snap(-1, `Rekonstruksi: '${A[i - 1]}' sama → huruf ini <b>bagian dari LCS</b>, mundur diagonal.`, null, { path: [...path], pathLine: [...pathLine], lcs, hlRow: i, hlCol: j, mark: "key" });
                    i--;
                    j--;
                } else if (dp[i - 1][j] >= dp[i][j - 1]) {
                    i--;
                } else {
                    j--;
                }
                path.push(`${i},${j}`);
                pathLine.push([i, j]);
            }
            snap(-1, `🎉 Panjang LCS = <b>${dp[n][m]}</b>, salah satu LCS-nya adalah <b>"${lcs || "–"}"</b>.`, null, { path, pathLine, lcs, mark: "done" });

            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
                result.set(f.lcs ? `<span class="result-chip">${esc(f.lcs)}</span>` : '<span class="muted">Muncul saat rekonstruksi</span>');
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        sh.head.querySelector("[data-apply]").onclick = build;
        [aIn, bIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        heatBtn.onclick = () => {
            heat = !heat;
            heatBtn.classList.toggle("active", heat);
            table?.setHeat(heat);
        };
        build();
    });

    // ════════════════════════════ LIS (Longest Increasing Subsequence) ════════════════════════════
    function barsView(stage) {
        stage.innerHTML = "";
        stage.classList.remove("graph-stage");
        let svgEl = null;
        let geo = null;
        let els = null;

        function draw(arr) {
            stage.innerHTML = "";
            const n = arr.length;
            const W = Math.max(560, n * 62 + 60);
            const H = 330;
            svgEl = V.svg("svg", { class: "graph bars", viewBox: `0 0 ${W} ${H}` }, stage);
            const defs = V.svg("defs", {}, svgEl);
            for (const [id, color] of [
                ["aa-lis-a", "#22c55e"],
                ["aa-lis-b", "#22d3ee"],
            ]) {
                const m = V.svg("marker", { id, viewBox: "0 0 10 10", refX: "8", refY: "5", markerWidth: "6", markerHeight: "6", orient: "auto-start-reverse" }, defs);
                V.svg("path", { d: "M0 0 10 5 0 10z", fill: color }, m);
            }
            const max = Math.max(...arr);
            const slot = (W - 60) / n;
            const barW = Math.min(40, slot * 0.62);
            const base = 222;
            const top = 74;
            const cx = (i) => 30 + slot * i + slot / 2;
            const bh = (v) => 10 + (v / max) * (base - top - 10);
            geo = { cx, bh, base, barW };
            const gArcs = V.svg("g", { class: "lis-arcs" }, svgEl);
            const gBars = V.svg("g", {}, svgEl);
            const gLine = V.svg("g", {}, svgEl);
            els = { gArcs, gLine, bars: [], vals: [], dps: [], dpTexts: [] };
            arr.forEach((v, i) => {
                const g = V.svg("g", { class: "lis-bar" }, gBars);
                V.svg("rect", { class: "bar", x: cx(i) - barW / 2, y: base - bh(v), width: barW, height: bh(v), rx: 7 }, g);
                const t = V.svg("text", { class: "bar-val", x: cx(i), y: base - bh(v) - 8 }, g);
                t.textContent = v;
                const idx = V.svg("text", { class: "bar-idx", x: cx(i), y: base + 18 }, g);
                idx.textContent = `i=${i}`;
                const dpG = V.svg("g", { class: "lis-dp" }, gBars);
                V.svg("rect", { x: cx(i) - barW / 2 - 4, y: 258, width: barW + 8, height: 34, rx: 9 }, dpG);
                const dt = V.svg("text", { x: cx(i), y: 275 }, dpG);
                els.bars.push(g);
                els.dps.push(dpG);
                els.dpTexts.push(dt);
            });
            const lbl = V.svg("text", { class: "lis-dp-label", x: 6, y: 279 }, gBars);
            lbl.textContent = "dp";
        }

        function arc(j, i, cls) {
            const x1 = geo.cx(j);
            const x2 = geo.cx(i);
            const y = 64;
            const h = Math.min(56, 14 + Math.abs(x2 - x1) * 0.18);
            return V.svg(
                "path",
                { class: `lis-arc ${cls}`, d: `M${x1} ${y} C${x1} ${y - h}, ${x2} ${y - h}, ${x2} ${y}`, "marker-end": `url(#${cls === "take" ? "aa-lis-a" : "aa-lis-b"})` },
                els.gArcs,
            );
        }

        function update(f) {
            if (!els) return;
            const maskIdx = f.masked && f.ask ? f.ask.maskIdx : undefined;
            els.bars.forEach((g, i) => {
                const cls = ["lis-bar"];
                if (f.lis && f.lis.includes(i)) cls.push("lis");
                if (f.cur === i) cls.push("cur");
                if (f.cmp === i) cls.push(f.cmpOk ? "cmp-ok" : "cmp-bad");
                if (f.cur !== undefined && i > f.cur && !(f.lis && f.lis.length)) cls.push("later");
                g.setAttribute("class", cls.join(" "));
            });
            els.dpTexts.forEach((t, i) => {
                const v = f.dp[i];
                t.textContent = maskIdx === i ? "?" : v === null ? "" : v;
                els.dps[i].setAttribute("class", `lis-dp ${maskIdx === i ? "ask" : ""} ${f.cur === i ? "cur" : ""} ${f.lis && f.lis.includes(i) ? "lis" : ""}`);
            });
            els.gArcs.innerHTML = "";
            (f.arcs || []).forEach(([j, i, cls]) => arc(j, i, cls));
            els.gLine.innerHTML = "";
            if (f.lis && f.lis.length > 1) {
                const pts = f.lis.map((i) => `${geo.cx(i)},${geo.base - geo.bh(f.arr[i]) - 22}`).join(" ");
                V.svg("polyline", { class: "lis-line", points: pts }, els.gLine);
            }
        }

        return { draw, update };
    }

    V.register("lis", (root) => {
        const sh = V.shell(root, {
            title: "Longest Increasing Subsequence",
            controls: `
                <label class="viz-input">Array <input data-arr value="3, 10, 2, 1, 20, 4, 6, 8" style="width:190px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>
                <button class="btn btn-sm" data-random title="Array acak">🎲</button>`,
            legend: [
                ["Elemen i (sedang dihitung)", "#f59e0b", "#f59e0b"],
                ["a[j] < a[i]: bisa disambung", "#22d3ee", "rgba(34,211,238,.18)"],
                ["a[j] ≥ a[i]: tidak bisa", "#ef4444", "rgba(239,68,68,.16)"],
                ["LIS akhir", "#22c55e", "rgba(34,197,94,.2)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const view = barsView(sh.stage);
        const pseudo = V.codePanel(
            sh.side,
            `
vector<int> dp(n), prev(n);
for (int i = 0; i < n; i++) {                       //@0
    dp[i] = 1;                                      //@1
    prev[i] = -1;                                   //@1
    for (int j = 0; j < i; j++)                     //@2
        if (a[j] < a[i] && dp[j] + 1 > dp[i]) {     //@3
            dp[i] = dp[j] + 1;                      //@4
            prev[i] = j;                            //@4
        }
}
int jawaban = *max_element(dp.begin(), dp.end());   //@5`,
        );
        const dPanel = V.arrayPanel(sh.side, "Array dp", "LIS yang berakhir di i");
        const watch = V.watchPanel(sh.side);

        function build() {
            let arr = V.parseList(arrIn.value, { min: 1, max: 99, limit: 10 });
            if (arr.length < 2) arr = [3, 10, 2, 1, 20, 4, 6, 8];
            arrIn.value = arr.join(", ");
            view.draw(arr);
            const n = arr.length;
            const dp = new Array(n).fill(null);
            const prev = new Array(n).fill(-1);
            const frames = [];
            const snap = (line, text, f = {}) =>
                frames.push({
                    line,
                    text,
                    arr,
                    dp: [...dp],
                    dpCells: dp.map((v, i) => ({ key: i, val: v === null ? "–" : v, cls: f.cur === i ? "changed" : "" })),
                    arcs: f.arcs || [],
                    ...f,
                });

            // Hitung dp akhir lebih dulu agar Mode Tebak bisa menanyakannya sebelum perbandingan dimulai
            const finalDp = arr.map(() => 1);
            for (let i = 0; i < n; i++) for (let j = 0; j < i; j++) if (arr[j] < arr[i]) finalDp[i] = Math.max(finalDp[i], finalDp[j] + 1);

            snap(-1, `Cari subsequence <b>naik</b> terpanjang dari array ini. <code>dp[i]</code> = panjang LIS yang <b>berakhir</b> di elemen ke-i.`);
            for (let i = 0; i < n; i++) {
                dp[i] = 1;
                prev[i] = -1;
                snap(1, `Mulai elemen <b>i = ${i}</b> (nilai ${arr[i]}). Paling tidak, LIS-nya adalah elemen itu sendiri, jadi mulai dari <code>dp[${i}] = 1</code> lalu periksa semua j sebelumnya.`, {
                    cur: i,
                    mark: "key",
                    watch: [
                        ["i", i],
                        ["a[i]", arr[i]],
                        ["dp[i]", 1],
                    ],
                    ask:
                        i === 0
                            ? undefined
                            : {
                                  type: "value",
                                  answer: finalDp[i],
                                  prompt: `Tebak sebelum dihitung: berapa <code>dp[${i}]</code> nantinya (LIS terpanjang yang berakhir di nilai <b>${arr[i]}</b>)?`,
                                  hint: "Cari elemen sebelumnya yang nilainya lebih kecil dari a[i], ambil dp terbesarnya, lalu tambah 1.",
                                  maskIdx: i,
                                  context: `Elemen berikutnya: <b>i = ${i}</b> dengan nilai <b>${arr[i]}</b>.`,
                              },
                });
                for (let j = 0; j < i; j++) {
                    const ok = arr[j] < arr[i];
                    const better = ok && dp[j] + 1 > dp[i];
                    const arcs = prev[i] >= 0 && !better ? [[prev[i], i, "take"]] : [];
                    if (better) {
                        dp[i] = dp[j] + 1;
                        prev[i] = j;
                    }
                    snap(
                        better ? 4 : 3,
                        ok
                            ? better
                                ? `a[${j}] = ${arr[j]} &lt; ${arr[i]}: sambung LIS yang berakhir di ${j} (panjang ${dp[j]}) → <code>dp[${i}] = ${dp[i]}</code>.`
                                : `a[${j}] = ${arr[j]} &lt; ${arr[i]}, tapi dp[${j}] + 1 = ${dp[j] + 1} tidak lebih baik dari ${dp[i]}.`
                            : `a[${j}] = ${arr[j]} ≥ ${arr[i]}: tidak bisa disambung (harus naik).`,
                        {
                            cur: i,
                            cmp: j,
                            cmpOk: ok,
                            arcs: better ? [[j, i, "take"]] : [...arcs, [j, i, "check"]],
                            mark: better ? "take" : undefined,
                            watch: [
                                ["i", i],
                                ["j", j],
                                ["a[j] < a[i]", ok ? "ya" : "tidak"],
                                ["dp[j] + 1", dp[j] + 1],
                                ["dp[i]", dp[i]],
                            ],
                        },
                    );
                }
                snap(0, `Elemen ${i} selesai: <code>dp[${i}] = ${dp[i]}</code>${prev[i] >= 0 ? ` (disambung dari i = ${prev[i]})` : " (tidak ada elemen sebelumnya yang lebih kecil)"}.`, {
                    cur: i,
                    arcs: prev[i] >= 0 ? [[prev[i], i, "take"]] : [],
                    watch: [
                        ["i", i],
                        ["dp[i]", dp[i]],
                        ["prev[i]", prev[i]],
                    ],
                });
            }
            let best = 0;
            for (let i = 1; i < n; i++) if (dp[i] > dp[best]) best = i;
            const lis = [];
            for (let k = best; k >= 0; k = prev[k]) lis.unshift(k);
            const arcs = [];
            for (let k = 1; k < lis.length; k++) arcs.push([lis[k - 1], lis[k], "take"]);
            snap(5, `Jawaban = nilai terbesar di dp = <b>${dp[best]}</b> (di i = ${best}). Ikuti <code>prev</code> mundur untuk merekonstruksi: <b>${lis.map((k) => arr[k]).join(" → ")}</b>.`, {
                lis,
                arcs,
                mark: "done",
                watch: [
                    ["panjang LIS", dp[best], true],
                    ["LIS", lis.map((k) => arr[k]).join(", ")],
                ],
                ask: {
                    type: "value",
                    answer: dp[best],
                    prompt: "Berapa <b>panjang LIS</b> array ini?",
                    hint: "Jawabannya adalah nilai terbesar di array dp.",
                    context: "Semua dp[i] sudah terhitung.",
                },
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            view.update(f);
            pseudo.set(f.line);
            dPanel.set(f.dpCells, f.masked ? f.ask?.maskIdx : undefined);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        arrIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        sh.head.querySelector("[data-random]").onclick = () => {
            arrIn.value = Array.from({ length: V.rand(7, 10) }, () => V.rand(1, 40)).join(", ");
            build();
        };
        build();
    });
})();
