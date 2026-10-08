/* Visualizer DP lanjutan: DP Interval (menggabungkan tumpukan) dan DP Bitmask (TSP) */
(() => {
    "use strict";

    const V = window.Viz;
    const INF = Infinity;
    const fmt = (v) => (v === INF ? "∞" : v);
    const formulaHtml = (f) => `<div class="formula">${f}</div>`;

    // ════════════════════════════ DP Interval: menggabungkan tumpukan ════════════════════════════
    V.register("interval", (root) => {
        const sh = V.shell(root, {
            title: "DP Interval: Menggabungkan Tumpukan",
            controls: `
                <label class="viz-input">Tumpukan <input data-arr value="4, 1, 1, 4" style="width:150px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>
                <button class="btn btn-sm" data-random title="Tumpukan acak">Acak</button>`,
            legend: [
                ["Interval [l, r] dihitung", "#f59e0b", "#f59e0b"],
                ["Bagian kiri [l, k]", "#22d3ee", "rgba(34,211,238,.14)"],
                ["Bagian kanan [k+1, r]", "#ec4899", "rgba(236,72,153,.14)"],
                ["Interval panjang 1 (base case)", "#a78bfa", "rgba(139,92,246,.14)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        let render = () => {};

        function build() {
            let a = V.parseList(arrIn.value, { min: 1, max: 20, limit: 6 });
            if (a.length < 2) a = [4, 1, 1, 4];
            arrIn.value = a.join(", ");
            const n = a.length;
            const pre = [0];
            for (const x of a) pre.push(pre[pre.length - 1] + x);
            const sum = (l, r) => pre[r + 1] - pre[l];

            sh.side.innerHTML = "";
            const pseudo = V.pseudoPanel(sh.side, "Pseudocode", [
                "dp[i][i] ← 0 untuk semua i",
                "untuk len dari 2 sampai n:",
                "  untuk l dari 0 sampai n − len:",
                "    r ← l + len − 1;  dp[l][r] ← ∞",
                "    untuk k dari l sampai r − 1:",
                "      dp[l][r] ← min(dp[l][r], dp[l][k] + dp[k+1][r] + sum(l, r))",
            ]);
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const piles = V.htmlPanel(sh.side, "Tumpukan", "angka = banyak batu");
            const table = V.tableView(sh.stage, {
                rows: n,
                cols: n,
                rowHead: a.map((_, i) => `l=${i}`),
                colHead: a.map((_, i) => `r=${i}`),
                corner: "l \\ r",
                cell: 52,
            });
            const dp = Array.from({ length: n }, () => new Array(n).fill(null));
            const frames = [];
            const pileHtml = (l, r, k) =>
                `<div class="iv-piles">${a
                    .map((x, i) => {
                        let c = "";
                        if (l !== undefined && i >= l && i <= r) c = k === undefined ? "on" : i <= k ? "left" : "right";
                        return `<span class="${c}">${x}</span>`;
                    })
                    .join("")}</div>`;
            const snap = (line, text, extra = {}) => {
                const cls = {};
                for (let i = 0; i < n; i++) {
                    if (dp[i][i] !== null) cls[`${i},${i}`] = "base";
                    for (let j = 0; j < i; j++) cls[`${i},${j}`] = "block";
                }
                if (extra.cur) cls[extra.cur.join(",")] = "current";
                if (extra.left) cls[extra.left.join(",")] = "dep";
                if (extra.right) cls[extra.right.join(",")] = "dep alt";
                frames.push({
                    line,
                    text,
                    vals: dp.map((row) => row.map((v) => (v === null ? null : fmt(v)))),
                    cls,
                    hlRow: extra.cur ? extra.cur[0] : undefined,
                    hlCol: extra.cur ? extra.cur[1] : undefined,
                    arrows: [
                        extra.left && { from: extra.left, to: extra.cur, bend: 0.15 },
                        extra.right && { from: extra.right, to: extra.cur, alt: true, bend: -0.15 },
                    ].filter(Boolean),
                    formula: extra.formula || "",
                    piles: pileHtml(...(extra.span || [])),
                    mark: extra.mark,
                    ask: extra.ask,
                    formulaQ: extra.formulaQ || "",
                });
            };

            snap(-1, `Ada <b>${n}</b> tumpukan berjajar. Menggabungkan dua tumpukan <b>bersebelahan</b> berbiaya total batu keduanya. Cari biaya minimum sampai tinggal satu tumpukan. <code>dp[l][r]</code> = biaya termurah menyatukan tumpukan l..r.`);
            for (let i = 0; i < n; i++) dp[i][i] = 0;
            snap(0, "Base case: interval berisi satu tumpukan tidak perlu digabung, biayanya <b>0</b> (diagonal).", { mark: "key" });
            for (let len = 2; len <= n; len++) {
                snap(1, `Sekarang semua interval sepanjang <b>${len}</b>. Interval yang lebih pendek sudah selesai semua.`, { mark: "key" });
                for (let l = 0; l + len - 1 < n; l++) {
                    const r = l + len - 1;
                    let best = INF;
                    let bestK = -1;
                    for (let k = l; k < r; k++) {
                        const cand = dp[l][k] + dp[k + 1][r] + sum(l, r);
                        const better = cand < best;
                        if (better) {
                            best = cand;
                            bestK = k;
                        }
                        dp[l][r] = best;
                        snap(
                            5,
                            `[${l}, ${r}] dibelah di k = ${k}: kiri [${l}, ${k}] + kanan [${k + 1}, ${r}] + gabungan terakhir ${sum(l, r)} = <b>${cand}</b>${better ? " (terbaik sejauh ini)" : ""}.`,
                            {
                                cur: [l, r],
                                left: [l, k],
                                right: [k + 1, r],
                                span: [l, r, k],
                                formula: `<span class="a">${dp[l][k]}</span> + <span class="b">${dp[k + 1][r]}</span> + ${sum(l, r)} = <span class="r">${cand}</span>`,
                            },
                        );
                    }
                    snap(
                        5,
                        `<code>dp[${l}][${r}] = ${best}</code>, dengan belahan terbaik di k = ${bestK}.`,
                        {
                            cur: [l, r],
                            left: [l, bestK],
                            right: [bestK + 1, r],
                            span: [l, r, bestK],
                            formula: `dp[${l}][${r}] = <span class="r">${best}</span>`,
                            formulaQ: `dp[${l}][${r}] = <span class="r">?</span>`,
                            ask: {
                                type: "value",
                                answer: best,
                                prompt: `Berapa <code>dp[${l}][${r}]</code>?`,
                                hint: "Ambil minimum dari semua belahan k, masing-masing kiri + kanan + jumlah batu l..r.",
                                mask: [l, r],
                                context: `Interval tumpukan ${l}..${r}, jumlah batu ${sum(l, r)}.`,
                            },
                        },
                    );
                }
            }
            snap(-1, `Selesai. Biaya minimum menyatukan semua tumpukan = <code>dp[0][${n - 1}] = ${dp[0][n - 1]}</code>.`, { cur: [0, n - 1], span: [0, n - 1], mark: "done" });

            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                const fm = f.masked ? f.formulaQ : f.formula;
                formula.set(fm ? formulaHtml(fm) : '<span class="muted">–</span>');
                piles.set(f.piles);
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        sh.head.querySelector("[data-apply]").onclick = build;
        arrIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        sh.head.querySelector("[data-random]").onclick = () => {
            arrIn.value = Array.from({ length: V.rand(3, 5) }, () => V.rand(1, 9)).join(", ");
            build();
        };
        build();
    });

    // ════════════════════════════ DP Bitmask: TSP ════════════════════════════
    V.register("bitmask", (root) => {
        const sh = V.shell(root, {
            title: "DP Bitmask: Rute Terpendek Mengunjungi Semua Kota",
            controls: `
                <label class="viz-input">Banyak kota <select data-n><option>3</option><option selected>4</option><option>5</option></select></label>
                <button class="btn btn-sm" data-random title="Jarak acak">Jarak acak</button>`,
            legend: [
                ["State asal dp[mask][v]", "#22d3ee", "rgba(34,211,238,.14)"],
                ["State tujuan yang diperbarui", "#f59e0b", "#f59e0b"],
                ["Awal: hanya kota 0", "#a78bfa", "rgba(139,92,246,.14)"],
                ["Rute optimal", "#22c55e", "rgba(34,197,94,.14)"],
            ],
        });
        const nSel = sh.head.querySelector("[data-n]");
        let dist = null;
        let render = () => {};

        const randomDist = (n) => {
            const d = Array.from({ length: n }, () => new Array(n).fill(0));
            for (let i = 0; i < n; i++) for (let j = i + 1; j < n; j++) d[i][j] = d[j][i] = V.rand(1, 9);
            return d;
        };

        function build() {
            const n = +nSel.value;
            if (!dist || dist.length !== n) dist = n === 4 ? [[0, 2, 9, 4], [2, 0, 6, 3], [9, 6, 0, 5], [4, 3, 5, 0]] : randomDist(n);
            const FULL = (1 << n) - 1;
            // Hanya mask yang memuat kota 0 (titik awal) yang relevan
            const masks = [];
            for (let m = 1; m <= FULL; m++) if (m & 1) masks.push(m);
            const rowOf = new Map(masks.map((m, i) => [m, i]));
            const bin = (m) => m.toString(2).padStart(n, "0");

            sh.side.innerHTML = "";
            const pseudo = V.pseudoPanel(sh.side, "Pseudocode", [
                "dp[1][0] ← 0;  selain itu ∞      // mask 0…01: baru di kota 0",
                "untuk mask naik, untuk v di mask:",
                "  jika dp[mask][v] = ∞: lewati",
                "  untuk u yang BELUM di mask:",
                "    baru ← mask | (1 << u)",
                "    dp[baru][u] ← min(dp[baru][u], dp[mask][v] + d[v][u])",
                "jawaban ← min( dp[penuh][v] + d[v][0] )",
            ]);
            const formula = V.htmlPanel(sh.side, "Perhitungan");
            const matrix = V.htmlPanel(sh.side, "Jarak antar kota", "d[baris][kolom]");
            const table = V.tableView(sh.stage, {
                rows: masks.length,
                cols: n,
                rowHead: masks.map(bin),
                colHead: Array.from({ length: n }, (_, v) => `v=${v}`),
                corner: "mask \\ v",
                cell: 46,
            });
            const dp = Array.from({ length: masks.length }, () => new Array(n).fill(INF));
            const frames = [];
            const matrixHtml = (hi) =>
                `<table class="mini-matrix"><tr><th></th>${dist.map((_, j) => `<th>${j}</th>`).join("")}</tr>${dist
                    .map((row, i) => `<tr><th>${i}</th>${row.map((x, j) => `<td class="${hi && hi[0] === i && hi[1] === j ? "on" : ""}">${i === j ? "–" : x}</td>`).join("")}</tr>`)
                    .join("")}</table>`;
            const snap = (line, text, extra = {}) => {
                const cls = {};
                cls[`0,0`] = "base";
                if (extra.from) cls[extra.from.join(",")] = "dep";
                if (extra.to) cls[extra.to.join(",")] = "current";
                (extra.path || []).forEach((p) => (cls[p] = "path"));
                frames.push({
                    line,
                    text,
                    // Kolom kota 0 hanya berarti di awal: rute tidak boleh kembali ke 0 sebelum selesai
                    vals: dp.map((row, r) => row.map((v, c) => ((masks[r] >> c) & 1 && (c > 0 || masks[r] === 1) ? fmt(v) : null))),
                    cls,
                    hlRow: extra.to ? extra.to[0] : undefined,
                    arrows: extra.from && extra.to ? [{ from: extra.from, to: extra.to, bend: 0.2 }] : [],
                    formula: extra.formula || "",
                    matrix: matrixHtml(extra.edge),
                    mark: extra.mark,
                });
            };

            dp[0][0] = 0;
            snap(0, `Setiap baris adalah <b>mask</b>: bit ke-i bernilai 1 jika kota i sudah dikunjungi (bit paling kanan = kota 0). Kolom v = kota tempat kita berada sekarang. Mulai di kota 0: <code>dp[${bin(1)}][0] = 0</code>.`, { mark: "key" });
            for (const mask of masks) {
                const r = rowOf.get(mask);
                for (let v = 0; v < n; v++) {
                    if (!((mask >> v) & 1) || dp[r][v] === INF) continue;
                    for (let u = 0; u < n; u++) {
                        if ((mask >> u) & 1) continue;
                        const nm = mask | (1 << u);
                        const nr = rowOf.get(nm);
                        const cand = dp[r][v] + dist[v][u];
                        const old = dp[nr][u];
                        if (cand < old) dp[nr][u] = cand;
                        snap(
                            5,
                            `Dari mask <code>${bin(mask)}</code> di kota ${v}, pergi ke kota <b>${u}</b> yang belum dikunjungi: ${dp[r][v]} + d[${v}][${u}] = <b>${cand}</b>${cand < old ? (old === INF ? " (state baru)" : ` (lebih baik dari ${old})`) : ` (tidak lebih baik dari ${old})`}.`,
                            {
                                from: [r, v],
                                to: [nr, u],
                                edge: [v, u],
                                formula: `<span class="a">${dp[r][v]}</span> + ${dist[v][u]} = <span class="r">${cand}</span>`,
                            },
                        );
                    }
                }
            }
            // Kembali ke kota 0
            const fr = rowOf.get(FULL);
            let best = INF;
            let last = -1;
            for (let v = 1; v < n; v++) {
                const c = dp[fr][v] + dist[v][0];
                if (c < best) {
                    best = c;
                    last = v;
                }
            }
            // Rekonstruksi rute
            const route = [0];
            const path = [];
            let mask = FULL;
            let v = last;
            while (v !== 0) {
                path.push(`${rowOf.get(mask)},${v}`);
                route.push(v);
                const pm = mask ^ (1 << v);
                const pr = rowOf.get(pm);
                let found = 0;
                for (let p = 0; p < n; p++) {
                    if ((pm >> p) & 1 && dp[pr][p] + dist[p][v] === dp[rowOf.get(mask)][v]) {
                        found = p;
                        break;
                    }
                }
                mask = pm;
                v = found;
            }
            path.push("0,0");
            route.push(0);
            const routeStr = [0, ...route.slice(1, -1).reverse(), 0].join(" → ");
            snap(6, `Semua kota sudah dikunjungi (mask <code>${bin(FULL)}</code>). Tambahkan jarak pulang ke kota 0 dan ambil minimum: <b>${best}</b>. Rute: <b>${routeStr}</b>.`, {
                path,
                mark: "done",
                formula: `min(dp[${bin(FULL)}][v] + d[v][0]) = <span class="r">${best}</span>`,
            });

            render = (f) => {
                pseudo.set(f.line);
                table.update(f);
                formula.set(f.formula ? formulaHtml(f.formula) : '<span class="muted">–</span>');
                matrix.set(f.matrix);
            };
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => render(f));
        nSel.onchange = () => {
            dist = null;
            build();
        };
        sh.head.querySelector("[data-random]").onclick = () => {
            dist = randomDist(+nSel.value);
            build();
        };
        build();
    });
})();
