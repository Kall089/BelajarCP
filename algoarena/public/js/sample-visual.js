/**
 * Visual contoh soal: mengubah input contoh menjadi gambar (graph, grid, atau batang)
 * agar siswa bisa "melihat" soalnya sebelum menulis kode.
 * Jenis visual ditentukan kolom problems.sample_visual (lihat database/seeders/problems/extras.php).
 */
(() => {
    "use strict";

    const P = window.PROBLEM;
    const V = window.Viz;
    if (!P || !P.sampleVisual || !V) return;

    const ints = (line) =>
        String(line ?? "")
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .map(Number);
    const linesOf = (s) => String(s).replace(/\r/g, "").split("\n");
    const el = (tag, cls, text) => {
        const e = document.createElement(tag);
        if (cls) e.className = cls;
        if (text !== undefined) e.textContent = text;
        return e;
    };

    function readEdges(lines, from, m, k) {
        const out = [];
        for (let i = 0; i < m; i++) {
            const a = ints(lines[from + i]);
            if (a.length >= 2) out.push(a.slice(0, k));
        }
        return out;
    }

    function isDag(n, edges) {
        const indeg = new Array(n + 1).fill(0);
        const adj = Array.from({ length: n + 1 }, () => []);
        edges.forEach(([u, v]) => {
            adj[u].push(v);
            indeg[v]++;
        });
        const q = [];
        for (let i = 1; i <= n; i++) if (!indeg[i]) q.push(i);
        let seen = 0;
        while (q.length) {
            const u = q.pop();
            seen++;
            adj[u].forEach((v) => --indeg[v] === 0 && q.push(v));
        }
        return seen === n;
    }

    function graph(body, n, edges, opts = {}, frame = {}, note = "") {
        if (n > 40) return tooBig(body);
        const stage = el("div");
        body.appendChild(stage);
        const view = V.graphView(stage, {});
        view.draw(V.graphFromEdges(n, edges, opts));
        view.update(frame);
        if (note) body.appendChild(el("p", "sample-visual-note", note));
    }

    function tooBig(body) {
        body.appendChild(el("p", "muted", "Contoh ini terlalu besar untuk digambar."));
    }

    function cells(body, rows, cols, cellOf, note = "") {
        if (rows * cols > 900) return tooBig(body);
        const grid = el("div", "mini-grid");
        grid.style.gridTemplateColumns = `repeat(${cols}, 24px)`;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const [cls, text] = cellOf(r, c);
                grid.appendChild(el("span", cls, text));
            }
        }
        body.appendChild(grid);
        if (note) body.appendChild(el("p", "sample-visual-note", note));
    }

    function bars(body, values, note = "") {
        if (values.length > 60) return tooBig(body);
        const max = Math.max(1, ...values.map(Math.abs));
        const wrap = el("div", "mini-bars");
        values.forEach((v, i) => {
            const col = el("div");
            col.appendChild(el("small", "", String(v)));
            const bar = el("i");
            bar.style.height = `${Math.max(4, (Math.abs(v) / max) * 100)}px`;
            col.appendChild(bar);
            col.appendChild(el("small", "idx", String(i)));
            wrap.appendChild(col);
        });
        body.appendChild(wrap);
        if (note) body.appendChild(el("p", "sample-visual-note", note));
    }

    const RENDER = {
        graph(body, L) {
            const [n, m] = ints(L[0]);
            graph(body, n, readEdges(L, 1, m, 2), {}, {}, "Lingkaran = simpul, garis = sisi dua arah. Seret simpul untuk merapikan.");
        },
        wgraph(body, L) {
            const [n, m] = ints(L[0]);
            graph(body, n, readEdges(L, 1, m, 3), { weighted: true }, {}, "Angka di tengah garis adalah bobot sisi.");
        },
        digraph(body, L) {
            const [n, m] = ints(L[0]);
            const edges = readEdges(L, 1, m, 2);
            graph(body, n, edges, { directed: true, layered: isDag(n, edges) }, {}, "Panah a → b: a harus didahulukan sebelum b.");
        },
        "wdigraph-s"(body, L) {
            const [n, m, s] = ints(L[0]);
            graph(body, n, readEdges(L, 1, m, 3), { directed: true, weighted: true }, { nodes: { [s]: "start" } }, `Simpul ${s} (lingkaran putus-putus hijau) adalah titik awal.`);
        },
        "digraph-t"(body, L) {
            const [n, m] = ints(L[0]);
            const t = ints(L[1]);
            const edges = readEdges(L, 2, m, 2);
            const badges = {};
            t.forEach((x, i) => (badges[i + 1] = `${x}h`));
            graph(body, n, edges, { directed: true, layered: isDag(n, edges) }, { badges }, "Label di atas simpul adalah lama pengerjaan tugas (hari).");
        },
        wdigraph(body, L) {
            const [n, m] = ints(L[0]);
            graph(body, n, readEdges(L, 1, m, 3), { directed: true, weighted: true }, {}, "Sisi berarah; angka = bobot (boleh negatif).");
        },
        tree(body, L) {
            const n = ints(L[0])[0];
            graph(body, n, readEdges(L, 1, n - 1, 2), { tree: true }, { nodes: { 1: "start" } }, "Pohon berakar di simpul 1 (paling atas).");
        },
        grid(body, L) {
            const [r, c] = ints(L[0]);
            const g = L.slice(1, r + 1);
            const CLS = { "#": ["wall", ""], S: ["start", "S"], E: ["end", "E"] };
            cells(body, r, c, (i, j) => CLS[g[i]?.[j]] || ["", ""], "Kotak gelap = tembok (#), kotak lain bisa dilewati.");
        },
        island(body, L) {
            const [r, c] = ints(L[0]);
            const g = L.slice(1, r + 1);
            cells(body, r, c, (i, j) => (g[i]?.[j] === "#" ? ["land", ""] : ["water", ""]), "Hijau = daratan (#), biru = air (.).");
        },
        numgrid(body, L) {
            const [r, c] = ints(L[0]);
            const g = L.slice(1, r + 1).map(ints);
            cells(body, r, c, (i, j) => ["", String(g[i]?.[j] ?? "")], "Angka di setiap petak.");
        },
        board(body, L) {
            const n = ints(L[0])[0];
            const g = L.slice(1, n + 1);
            cells(body, n, n, (i, j) => (g[i]?.[j] === "#" ? ["wall", ""] : ["", ""]), "Kotak gelap = petak rusak (#), tidak boleh ditempati.");
        },
        chess(body, L) {
            const [n, sr, sc, tr, tc] = ints(L[0]);
            cells(
                body,
                n,
                n,
                (i, j) =>
                    i + 1 === sr && j + 1 === sc
                        ? ["start", "♞"]
                        : i + 1 === tr && j + 1 === tc
                          ? ["end", "★"]
                          : [(i + j) % 2 ? "wall" : "", ""],
                "♞ = posisi kuda, ★ = tujuan.",
            );
        },
        bars(body, L) {
            bars(body, ints(L[1]), "Tinggi batang = nilai elemen; angka kecil di bawah = indeks.");
        },
        "bars-first"(body, L) {
            bars(body, ints(L[0]).slice(1), "Tinggi batang = nilai elemen.");
        },
    };

    document.querySelectorAll("[data-sample-visual]").forEach((det) => {
        det.addEventListener("toggle", () => {
            if (!det.open || det.dataset.drawn) return;
            det.dataset.drawn = "1";
            const sample = P.samples[+det.dataset.sampleVisual];
            const body = det.querySelector(".sample-visual-body");
            try {
                RENDER[P.sampleVisual](body, linesOf(sample.input));
            } catch (e) {
                console.error(e);
                body.innerHTML = '<p class="muted">Visual tidak tersedia untuk contoh ini.</p>';
            }
        });
    });
})();
