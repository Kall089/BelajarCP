/* Visualizer track Matematika: algoritma Euclid (geometris + diperluas), saringan Eratosthenes, segitiga Pascal / jalur grid */
(() => {
    "use strict";

    const V = window.Viz;

    // ════════════════════════════ Algoritma Euclid ════════════════════════════
    V.register("euclid", (root) => {
        const sh = V.shell(root, {
            title: "Algoritma Euclid: Ubin Persegi Terbesar",
            controls: `
                <label class="viz-input">a <input class="short" data-a value="42"></label>
                <label class="viz-input">b <input class="short" data-b value="30"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Persegi langkah ini", "#f59e0b", "rgba(245,158,11,.30)"],
                ["Persegi sebelumnya", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Sisa persegi panjang", "#94a3b8", "rgba(148,163,184,.12)"],
            ],
        });
        const aIn = sh.head.querySelector("[data-a]");
        const bIn = sh.head.querySelector("[data-b]");
        const code = V.codePanel(
            sh.side,
            `
long long fpb(long long a, long long b) {            //@0
    while (b != 0) {                                  //@1
        long long r = a % b;      // a = q·b + r      //@1
        a = b;                                        //@2
        b = r;                                        //@2
    }
    return a;                                         //@3
}
// a·x + b·y = fpb(a, b)
long long fpbDiperluas(long long a, long long b, long long& x, long long& y) {
    if (b == 0) { x = 1; y = 0; return a; }           //@4
    long long x1, y1;
    long long g = fpbDiperluas(b, a % b, x1, y1);     //@5
    x = y1;                                           //@6
    y = x1 - (a / b) * y1;                            //@6
    return g;
}`,
        );
        const table = V.htmlPanel(sh.side, "Langkah", "a = q · b + r");
        const watch = V.watchPanel(sh.side);

        function build() {
            const a0 = V.clampInt(aIn.value, 1, 99, 42);
            const b0 = V.clampInt(bIn.value, 1, 99, 30);
            aIn.value = a0;
            bIn.value = b0;
            // langkah Euclid
            const steps = [];
            let A = a0;
            let B = b0;
            while (B !== 0) {
                steps.push({ a: A, b: B, q: Math.floor(A / B), r: A % B });
                [A, B] = [B, A % B];
            }
            const g = A;
            // geometri: potong persegi dari persegi panjang a0 × b0
            const W = 540;
            const H = 280;
            const s = Math.min(W / a0, H / b0);
            const ox = (W - a0 * s) / 2 + 10;
            const oy = (H - b0 * s) / 2 + 26;
            const squares = [];
            const cuts = [];
            let x = 0;
            let y = 0;
            let w = a0;
            let h = b0;
            while (w > 0 && h > 0) {
                const list = [];
                if (w >= h) {
                    const q = Math.floor(w / h);
                    for (let i = 0; i < q; i++) list.push([x + i * h, y, h]);
                    x += q * h;
                    w -= q * h;
                } else {
                    const q = Math.floor(h / w);
                    for (let i = 0; i < q; i++) list.push([x, y + i * w, w]);
                    y += q * w;
                    h -= q * w;
                }
                cuts.push({ list, rest: [x, y, w, h] });
            }
            const svg = (upto, showGrid) => {
                let out = `<svg viewBox="0 0 ${W + 20} ${H + 40}" class="eu-svg">`;
                out += `<rect x="${ox}" y="${oy}" width="${a0 * s}" height="${b0 * s}" class="eu-rest"/>`;
                cuts.forEach((c, k) => {
                    if (k > upto) return;
                    c.list.forEach(([sx, sy, side]) => {
                        out += `<rect x="${ox + sx * s}" y="${oy + sy * s}" width="${side * s}" height="${side * s}" class="${k === upto ? "eu-cur" : "eu-old"}"/>`;
                        if (side * s >= 26) out += `<text x="${ox + (sx + side / 2) * s}" y="${oy + (sy + side / 2) * s}" class="eu-lbl">${side}</text>`;
                    });
                });
                if (showGrid && g * s >= 6) {
                    for (let gx = g; gx < a0; gx += g) out += `<line x1="${ox + gx * s}" y1="${oy}" x2="${ox + gx * s}" y2="${oy + b0 * s}" class="eu-grid"/>`;
                    for (let gy = g; gy < b0; gy += g) out += `<line x1="${ox}" y1="${oy + gy * s}" x2="${ox + a0 * s}" y2="${oy + gy * s}" class="eu-grid"/>`;
                }
                out += `<text x="${ox + (a0 * s) / 2}" y="${oy - 8}" class="eu-dim">${a0}</text><text x="${ox - 6}" y="${oy + (b0 * s) / 2}" class="eu-dim" style="text-anchor:end">${b0}</text></svg>`;
                return out;
            };
            const rowsHtml = (upto, ext) =>
                `<table class="eu-table"><tr><th>a</th><th>b</th><th>q</th><th>r</th>${ext ? "<th>x</th><th>y</th>" : ""}</tr>${steps
                    .map((st, k) => {
                        if (k > upto) return "";
                        const e = ext && ext[k];
                        return `<tr class="${k === upto && !ext ? "cur" : e && e.cur ? "cur" : ""}"><td>${st.a}</td><td>${st.b}</td><td>${st.q}</td><td>${st.r}</td>${ext ? `<td>${e ? e.x : ""}</td><td>${e ? e.y : ""}</td>` : ""}</tr>`;
                    })
                    .join("")}${ext ? `<tr class="${ext.base ? "cur" : ""}"><td>${g}</td><td>0</td><td>–</td><td>–</td><td>1</td><td>0</td></tr>` : ""}</table>`;
            const frames = [];
            frames.push({
                line: 0,
                text: `Lantai berukuran <b>${a0} × ${b0}</b> ingin ditutup ubin persegi yang sama besar, sebesar mungkin, tanpa memotong ubin. Sisi ubin terbesar itu adalah <b>FPB(${a0}, ${b0})</b>. Euclid menemukannya dengan terus memotong persegi terbesar.`,
                html: svg(-1, false),
                table: rowsHtml(-1),
                watch: [["a", a0], ["b", b0]],
            });
            steps.forEach((st, k) => {
                const sq = st.q === 0 ? "" : ` Potong <b>${st.q}</b> persegi ${st.b} × ${st.b}; sisanya ${st.r === 0 ? "habis" : `persegi panjang ${st.b} × ${st.r}`}.`;
                frames.push({
                    line: st.r === 0 ? 2 : 1,
                    text: `<code>${st.a} = ${st.q} · ${st.b} + ${st.r}</code>.${sq} Pembagi bersama ${st.a} dan ${st.b} sama persis dengan pembagi bersama ${st.b} dan ${st.r}, jadi <code>FPB(${st.a}, ${st.b}) = FPB(${st.b}, ${st.r})</code>.`,
                    html: svg(st.q === 0 ? -1 : cutIndex(k), false),
                    table: rowsHtml(k),
                    watch: [["a", st.a], ["b", st.b], ["r = a mod b", st.r]],
                });
            });
            frames.push({
                line: 3,
                text: `b menjadi 0, jadi <b>FPB = ${g}</b>: persegi terakhir berukuran ${g} × ${g}, dan garis putus-putus menunjukkan bahwa ubin ${g} × ${g} memang menutup seluruh lantai. Hanya ${steps.length} langkah; Euclid selalu O(log min(a, b)).`,
                html: svg(cuts.length - 1, true),
                table: rowsHtml(steps.length - 1),
                watch: [["FPB", g], ["KPK", (a0 / g) * b0]],
                mark: "key",
            });
            // Euclid diperluas: mundur dari bawah
            const ext = new Array(steps.length).fill(null);
            let xx = 1;
            let yy = 0;
            frames.push({
                line: 4,
                text: `<b>Euclid diperluas</b>: cari x, y dengan <code>${a0}·x + ${b0}·y = ${g}</code>. Mulai dari baris terakhir (${g}, 0): <code>${g}·1 + 0·0 = ${g}</code>, jadi x = 1, y = 0.`,
                html: svg(cuts.length - 1, true),
                table: rowsHtml(steps.length - 1, Object.assign([...ext], { base: true })),
                watch: [["x", 1], ["y", 0]],
            });
            for (let k = steps.length - 1; k >= 0; k--) {
                const st = steps[k];
                const nx = yy;
                const ny = xx - st.q * yy;
                ext[k] = { x: nx, y: ny };
                frames.push({
                    line: 6,
                    text: `Naik ke (${st.a}, ${st.b}): dari baris di bawahnya x₁ = ${xx}, y₁ = ${yy}, maka <code>x = y₁ = ${nx}</code> dan <code>y = x₁ − q·y₁ = ${xx} − ${st.q}·(${yy}) = ${ny}</code>. Cek: ${st.a}·(${nx}) + ${st.b}·(${ny}) = ${st.a * nx + st.b * ny}.`,
                    html: svg(cuts.length - 1, true),
                    table: rowsHtml(steps.length - 1, ext.map((e, i) => (e ? { ...e, cur: i === k } : null))),
                    watch: [["x", nx], ["y", ny]],
                });
                xx = nx;
                yy = ny;
            }
            frames.push({
                line: -1,
                text: `Hasil: <code>${a0}·(${xx}) + ${b0}·(${yy}) = ${g}</code>. Koefisien ini dipakai untuk invers modular dan persamaan Diophantine (lihat Level 3).`,
                html: svg(cuts.length - 1, true),
                table: rowsHtml(steps.length - 1, ext),
                watch: [["x", xx], ["y", yy], ["FPB", g]],
                mark: "done",
            });
            player.load(frames);

            function cutIndex(k) {
                // langkah dengan q = 0 (a < b) tidak memotong apa-apa; indeks potongan = langkah ke-k di antara q > 0
                let c = -1;
                for (let i = 0; i <= k; i++) if (steps[i].q > 0) c++;
                return c;
            }
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            table.set(f.table);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [aIn, bIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
