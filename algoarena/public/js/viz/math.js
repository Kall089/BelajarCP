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

    // ════════════════════════════ Saringan Eratosthenes (+ faktor prima terkecil) ════════════════════════════
    V.register("sieve", (root) => {
        const sh = V.shell(root, {
            title: "Saringan Eratosthenes",
            controls: V.segmented(
                "n",
                [
                    ["60", "N = 60"],
                    ["100", "N = 100"],
                    ["120", "N = 120"],
                ],
                "100",
            ),
            legend: [
                ["Prima p yang sedang dipakai", "#f59e0b", "rgba(245,158,11,.35)"],
                ["Baru dicoret (kelipatan p)", "#ef4444", "rgba(239,68,68,.18)"],
                ["Prima", "#22c55e", "rgba(34,197,94,.2)"],
                ["Sudah dicoret", "#94a3b8", "rgba(148,163,184,.14)"],
            ],
        });
        const code = V.codePanel(
            sh.side,
            `
vector<bool> prima(n + 1, true);                 //@0
prima[0] = prima[1] = false;                     //@0
for (long long p = 2; p * p <= n; p++) {         //@1
    if (!prima[p]) continue;      // sudah dicoret //@2
    for (long long k = p * p; k <= n; k += p)    //@3
        prima[k] = false;         // spf[k] = p  //@3
}
// yang masih true adalah bilangan prima         //@4`,
        );
        const watch = V.watchPanel(sh.side);
        let n = 100;

        function build() {
            const prime = new Array(n + 1).fill(true);
            prime[0] = prime[1] = false;
            const spf = new Array(n + 1).fill(0);
            const frames = [];
            let crossOps = 0;
            const render = (st) => {
                let h = '<div class="sv-grid">';
                for (let i = 1; i <= n; i++) {
                    const cls = ["sv-cell"];
                    if (i === 1) cls.push("one");
                    else if (st.p === i) cls.push("cur");
                    else if (st.fresh && st.fresh.has(i)) cls.push("fresh");
                    else if (!prime[i]) cls.push("out");
                    else if (st.known && st.known.has(i)) cls.push("prime");
                    h += `<div class="${cls.join(" ")}"><b>${i}</b>${spf[i] && !prime[i] ? `<small>${spf[i]}</small>` : ""}</div>`;
                }
                return `${h}</div><p class="mq-note">angka kecil di pojok = faktor prima terkecil (pencoret pertama)</p>`;
            };
            const known = new Set();
            frames.push({ line: 0, text: `Tandai semua bilangan 2..${n} sebagai calon prima. Kita akan mencoret kelipatan setiap prima; yang tidak pernah tercoret pasti prima.`, html: render({ known }), watch: [["n", n], ["√n", Math.floor(Math.sqrt(n))]] });
            for (let p = 2; p * p <= n; p++) {
                if (!prime[p]) {
                    frames.push({ line: 2, text: `${p} sudah dicoret (kelipatan ${spf[p]}), jadi bukan prima dan kelipatannya sudah dicoret oleh ${spf[p]}. Lewati.`, html: render({ known, p }), watch: [["p", p], ["dicoret oleh", spf[p]]], mark: "skip" });
                    continue;
                }
                known.add(p);
                const fresh = new Set();
                for (let k = p * p; k <= n; k += p) {
                    crossOps++;
                    if (prime[k]) {
                        prime[k] = false;
                        spf[k] = p;
                        fresh.add(k);
                    }
                }
                frames.push({
                    line: 3,
                    text: `${p} belum dicoret, jadi <b>${p} prima</b>. Coret kelipatannya mulai dari <b>${p}² = ${p * p}</b>: ${[...fresh].slice(0, 8).join(", ")}${fresh.size > 8 ? ", …" : ""} (${fresh.size} bilangan baru). Kelipatan yang lebih kecil dari ${p * p}, seperti ${p * 2}, sudah dicoret oleh prima yang lebih kecil.`,
                    html: render({ known, p, fresh }),
                    watch: [["p", p], ["mulai dari", p * p], ["baru dicoret", fresh.size]],
                    ask: p === 3 ? { type: "value", answer: 9, prompt: "Mulai dari bilangan berapa kelipatan 3 dicoret?", hint: "Kelipatan 3 yang lebih kecil sudah dicoret oleh 2." } : undefined,
                    htmlMasked: p === 3 ? render({ known, p }) : undefined,
                });
            }
            const primes = [];
            for (let i = 2; i <= n; i++) if (prime[i]) {
                primes.push(i);
                known.add(i);
            }
            frames.push({
                line: 4,
                text: `p sudah melewati √${n} ≈ ${Math.sqrt(n).toFixed(1)}, berhenti. Setiap bilangan komposit ≤ ${n} punya faktor prima ≤ √${n}, jadi pasti sudah tercoret. Ada <b>${primes.length}</b> bilangan prima ≤ ${n}. Total coretan hanya ${crossOps}, sesuai O(N log log N).`,
                html: render({ known }),
                watch: [["banyak prima", primes.length], ["coretan", crossOps]],
                mark: "done",
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        V.bindSegmented(sh.head, "n", (v) => {
            n = +v;
            build();
        });
        build();
    });

    // ════════════════════════════ Jalur grid: kombinasi & inklusi–eksklusi ════════════════════════════
    V.register("gridpath", (root) => {
        const sh = V.shell(root, {
            title: "Jalur Grid = Kombinasi",
            controls: `
                <label class="viz-input">Baris <input class="short" data-h value="5"></label>
                <label class="viz-input">Kolom <input class="short" data-w value="7"></label>
                <label class="viz-input">Lubang r c <input class="short" data-x value="2 3" style="width:52px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sedang diisi", "#f59e0b", "rgba(245,158,11,.25)"],
                ["Awal → lubang", "#22d3ee", "rgba(34,211,238,.18)"],
                ["Lubang → akhir", "#8b5cf6", "rgba(139,92,246,.16)"],
                ["Lubang", "#ef4444", "rgba(239,68,68,.25)"],
            ],
        });
        const hIn = sh.head.querySelector("[data-h]");
        const wIn = sh.head.querySelector("[data-w]");
        const xIn = sh.head.querySelector("[data-x]");
        const code = V.codePanel(
            sh.side,
            `
// jalur hanya ke kanan/bawah dari (0, 0) ke (r, c):
// pilih r langkah "bawah" di antara r + c langkah
long long jalur(int r, int c) { return C(r + c, r); }   //@0
// cara DP: jalur[r][c] = jalur[r-1][c] + jalur[r][c-1]  //@1
// hindari satu lubang X = (xr, xc):
total  = jalur(H - 1, W - 1);                            //@2
lewatX = jalur(xr, xc) * jalur(H - 1 - xr, W - 1 - xc);  //@3
jawab  = total - lewatX;                                 //@4`,
        );
        const watch = V.watchPanel(sh.side);
        const binom = (n, k) => {
            if (k < 0 || k > n) return 0;
            let r = 1;
            for (let i = 1; i <= k; i++) r = (r * (n - k + i)) / i;
            return Math.round(r);
        };

        function build() {
            const H = V.clampInt(hIn.value, 2, 7, 5);
            const W = V.clampInt(wIn.value, 2, 9, 7);
            hIn.value = H;
            wIn.value = W;
            const xs = xIn.value.trim().split(/[\s,]+/).map(Number);
            let xr = V.clampInt(xs[0], 0, H - 1, 2);
            let xc = V.clampInt(xs[1], 0, W - 1, 3);
            if ((xr === 0 && xc === 0) || (xr === H - 1 && xc === W - 1)) {
                xr = Math.floor(H / 2);
                xc = Math.floor(W / 2);
            }
            xIn.value = `${xr} ${xc}`;
            const val = Array.from({ length: H }, () => new Array(W).fill(null));
            const frames = [];
            const render = (st) => {
                let h = `<div class="gp-grid" style="grid-template-columns: repeat(${W}, minmax(0, 1fr))">`;
                for (let r = 0; r < H; r++)
                    for (let c = 0; c < W; c++) {
                        const cls = ["gp-cell"];
                        if (st.diag !== undefined && r + c === st.diag) cls.push("cur");
                        if (st.split) {
                            if (r === xr && c === xc) cls.push("hole");
                            else if (r <= xr && c <= xc) cls.push("pre");
                            else if (r >= xr && c >= xc) cls.push("post");
                        }
                        const v = st.values ? st.values[r][c] : val[r][c];
                        h += `<div class="${cls.join(" ")}">${v === null ? "" : v}</div>`;
                    }
                return `${h}</div><p class="mq-note">mulai di pojok kiri atas, akhir di pojok kanan bawah, hanya ke kanan atau ke bawah</p>`;
            };
            frames.push({ line: 0, text: `Berapa banyak jalur dari pojok kiri atas ke pojok kanan bawah grid ${H} × ${W} jika hanya boleh ke <b>kanan</b> atau ke <b>bawah</b>? Setiap jalur terdiri dari ${H - 1} langkah bawah dan ${W - 1} langkah kanan.`, html: render({}), watch: [["bawah", H - 1], ["kanan", W - 1]] });
            for (let d = 0; d <= H + W - 2; d++) {
                const cells = [];
                for (let r = 0; r < H; r++) {
                    const c = d - r;
                    if (c < 0 || c >= W) continue;
                    val[r][c] = r === 0 || c === 0 ? 1 : val[r - 1][c] + val[r][c - 1];
                    cells.push([r, c]);
                }
                const [er, ec] = cells[Math.floor(cells.length / 2)];
                frames.push({
                    line: 1,
                    text: d === 0 ? "Sel awal: 1 jalur (diam di tempat)." : `Diagonal ${d}: setiap sel = sel atas + sel kiri (langkah terakhir datang dari atas atau dari kiri). Contoh (${er}, ${ec}): ${val[er][ec]} = <code>C(${er + ec}, ${er})</code>, yaitu memilih posisi ${er} langkah bawah di antara ${er + ec} langkah.`,
                    html: render({ diag: d }),
                    watch: [["diagonal r + c", d], [`C(${er + ec}, ${er})`, binom(er + ec, er)]],
                });
            }
            const total = binom(H + W - 2, H - 1);
            const pre = binom(xr + xc, xr);
            const post = binom(H - 1 - xr + W - 1 - xc, H - 1 - xr);
            frames.push({ line: 2, text: `Inilah segitiga Pascal yang dimiringkan. Total jalur = <code>C(${H + W - 2}, ${H - 1}) = ${total}</code>, tanpa perlu mengisi tabel.`, html: render({}), watch: [["total", total]], mark: "key" });
            frames.push({
                line: 3,
                text: `Sekarang sel (${xr}, ${xc}) berlubang. Jalur yang <b>melewati</b> lubang = jalur awal → lubang × jalur lubang → akhir = <code>C(${xr + xc}, ${xr}) · C(${H - 1 - xr + W - 1 - xc}, ${H - 1 - xr}) = ${pre} · ${post} = ${pre * post}</code> (aturan perkalian).`,
                html: render({ split: true }),
                watch: [["awal → lubang", pre], ["lubang → akhir", post], ["lewat lubang", pre * post]],
                ask: { type: "value", answer: pre * post, prompt: "Berapa jalur yang melewati lubang?", hint: "Kalikan jalur ke lubang dengan jalur dari lubang." },
            });
            const blocked = Array.from({ length: H }, () => new Array(W).fill(0));
            for (let r = 0; r < H; r++)
                for (let c = 0; c < W; c++) {
                    if (r === xr && c === xc) blocked[r][c] = 0;
                    else if (r === 0 && c === 0) blocked[r][c] = 1;
                    else blocked[r][c] = (r > 0 ? blocked[r - 1][c] : 0) + (c > 0 ? blocked[r][c - 1] : 0);
                }
            frames.push({
                line: 4,
                text: `Jalur yang menghindari lubang = total − lewat lubang = <code>${total} − ${pre * post} = ${total - pre * post}</code>. Tabel di atas dihitung ulang dengan DP (lubang bernilai 0) dan hasilnya sama. Dengan banyak lubang, kurangi jalur yang melewati lubang <em>pertama</em> yang diinjak: inklusi–eksklusi, lihat Level 3.`,
                html: render({ split: true, values: blocked.map((row, r) => row.map((v, c) => (r === xr && c === xc ? "×" : v))) }),
                watch: [["total", total], ["lewat lubang", pre * post], ["jawaban", total - pre * post]],
                mark: "done",
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [hIn, wIn, xIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
