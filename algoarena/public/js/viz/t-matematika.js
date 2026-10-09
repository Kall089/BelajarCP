/* Visualisasi track Teori Bilangan & Kombinatorika. */
(() => {
    "use strict";
    const V = window.Viz;
    const K = window.Kit;

    const clampInt = (v, lo, hi, def) => {
        const x = parseInt(v, 10);
        return Number.isFinite(x) ? Math.max(lo, Math.min(hi, x)) : def;
    };
    const gcd = (a, b) => {
        while (b) [a, b] = [b, a % b];
        return Math.abs(a);
    };
    const modpow = (a, e, m) => {
        let r = 1n;
        let b = BigInt(a) % BigInt(m);
        let x = BigInt(e);
        const M = BigInt(m);
        while (x > 0n) {
            if (x & 1n) r = (r * b) % M;
            b = (b * b) % M;
            x >>= 1n;
        }
        return Number(r);
    };
    /** Grid angka (baris berisi `per` sel). */
    const grid = (vals, cls, per = 10, sub = {}) => {
        let out = "";
        for (let i = 0; i < vals.length; i += per) {
            const c = {};
            const s = {};
            for (let k = i; k < Math.min(vals.length, i + per); k++) {
                if (cls[k]) c[k - i] = cls[k];
                if (sub[k] !== undefined) s[k - i] = sub[k];
            }
            out += K.cells(vals.slice(i, i + per), { idx: false, cls: c, sub: s, size: "sm" });
        }
        return `<div class="kx-col">${out}</div>`;
    };

    // ════════════════════════════ Inklusi-eksklusi ════════════════════════════
    V.register("venn", (root) => {
        K.widget(root, {
            title: "Inklusi-Eksklusi: Habis Dibagi Salah Satu",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">N <input class="short" data-n value="30"></label>
                <label class="viz-input">pembagi <input data-d value="2 3 5" style="width:80px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [["Dihitung di suku ini", "#f59e0b", "rgba(245,158,11,.18)"], ["Habis dibagi salah satu", "#22c55e", "rgba(34,197,94,.14)"]],
            code: `
long long hasil = 0;
for (int mask = 1; mask < (1 << k); mask++) {
    long long l = 1;                              // KPK pembagi terpilih
    for (int i = 0; i < k; i++) if (mask >> i & 1) l = lcm(l, d[i]);   //@0
    long long banyak = N / l;
    if (__builtin_popcount(mask) % 2) hasil += banyak;   // ganjil: tambah //@1
    else hasil -= banyak;                                // genap: kurang //@2
}`,
            watch: "Keadaan",
            build(ui) {
                const N = clampInt(K.val(ui, "n"), 1, 60, 30);
                const d = K.nums(K.val(ui, "d"), { min: 2, max: 30, limit: 3, def: [2, 3, 5] });
                ui.head.querySelector("[data-n]").value = N;
                ui.head.querySelector("[data-d]").value = d.join(" ");
                const k = d.length;
                const vals = [];
                for (let x = 1; x <= N; x++) vals.push(x);
                const lcm = (a, b) => (a / gcd(a, b)) * b;
                const frames = [];
                let hasil = 0;
                const rows = [];
                const view = (cur, tutup = false) =>
                    grid(vals, Object.fromEntries(vals.map((x, i) => [i, cur && x % cur === 0 ? "on" : ""])), 10) +
                    K.table(rows, { head: ["himpunan", "KPK", "N / KPK", "tanda", "total"], rowCls: { [rows.length - 1]: "on" }, mask: tutup ? { [`${rows.length - 1},2`]: true, [`${rows.length - 1},4`]: true } : {} });
                frames.push({ line: -1, text: `Hitung bilangan 1..${N} yang habis dibagi paling sedikit satu dari {${d.join(", ")}}. Menjumlahkan ⌊N/d⌋ menghitung ganda bilangan seperti ${d.reduce(lcm, 1) <= N ? d.reduce(lcm, 1) : lcm(d[0], d[1] || d[0])}; inklusi-eksklusi memperbaikinya.`, html: view(0), watch: [["hasil", 0]] });
                const masks = [];
                for (let mask = 1; mask < 1 << k; mask++) masks.push(mask);
                masks.sort((x, y) => K.bin(x, k).split("1").length - K.bin(y, k).split("1").length || x - y);
                masks.forEach((mask, t) => {
                    const sel = d.filter((_, i) => mask >> i & 1);
                    const l = sel.reduce(lcm, 1);
                    const c = Math.floor(N / l);
                    const odd = sel.length % 2 === 1;
                    hasil += odd ? c : -c;
                    rows.push([`{${sel.join(", ")}}`, l, c, odd ? "+" : "−", hasil]);
                    frames.push({
                        line: odd ? 1 : 2,
                        text: `${odd ? "Tambah" : "Kurangi"} bilangan yang habis dibagi ${sel.join(" dan ")} (yaitu kelipatan ${l}): ⌊${N}/${l}⌋ = ${c}.`,
                        html: view(l),
                        htmlMasked: view(l, true),
                        watch: [["KPK", l], ["suku", (odd ? "+" : "−") + c], ["hasil", hasil, true]],
                        ask: t === 1 || t === k ? { type: "value", prompt: `Berapa banyak kelipatan ${l} di 1..${N}?`, answer: String(c) } : undefined,
                        mark: odd ? "take" : "skip",
                    });
                });
                frames.push({
                    line: -1,
                    text: `Hasil <b>${hasil}</b>. Setiap bilangan yang habis dibagi tepat j pembagi dihitung C(j,1) − C(j,2) + C(j,3) − … = 1 kali.`,
                    html: grid(vals, Object.fromEntries(vals.map((x, i) => [i, d.some((y) => x % y === 0) ? "ok" : "dim"])), 10),
                    watch: [["hasil", hasil]],
                    mark: "done",
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ Catalan ════════════════════════════
    V.register("catalan", (root) => {
        K.widget(root, {
            title: "Bilangan Catalan: Memecah di Kurung Pertama",
            stageClass: "kx-stage",
            practice: true,
            controls: `<label class="viz-input">n <input class="short" data-n value="5"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [["Sedang dihitung", "#f59e0b", "rgba(245,158,11,.18)"], ["Suku yang dipakai", "#22d3ee", "rgba(34,211,238,.16)"]],
            code: `
// barisan kurung seimbang dengan n pasang:
// "(" A ")" B, dengan A berisi i pasang dan B berisi n-1-i pasang
C[0] = 1;                                     //@0
for (int n = 1; n <= N; n++)
    for (int i = 0; i < n; i++)
        C[n] += C[i] * C[n - 1 - i];          //@1
// rumus langsung: C[n] = C(2n, n) / (n + 1)  //@2`,
            watch: "Keadaan",
            build(ui) {
                const N = clampInt(K.val(ui, "n"), 1, 10, 5);
                ui.head.querySelector("[data-n]").value = N;
                const C = new Array(N + 1).fill(0);
                C[0] = 1;
                const frames = [];
                const ex = (n) => {
                    // contoh barisan dengan pola "(" A ")" B untuk i = 0
                    const out = [];
                    const gen = (s, o, c) => {
                        if (out.length >= 6) return;
                        if (s.length === 2 * n) {
                            out.push(s);
                            return;
                        }
                        if (o < n) gen(s + "(", o + 1, c);
                        if (c < o) gen(s + ")", o, c + 1);
                    };
                    gen("", 0, 0);
                    return out;
                };
                const view = (n, i) =>
                    K.cells(C.map((v, j) => (j <= n || j < n ? v : "")), {
                        idx: C.map((_, j) => `C${j}`),
                        cls: Object.fromEntries(C.map((_, j) => [j, j === n ? "on" : i !== undefined && (j === i || j === n - 1 - i) ? "in" : ""])),
                    }) + (n >= 1 && n <= 4 ? K.line(`contoh n=${n}`, ex(n).map((s) => K.chip(s)).join(" ")) : "");
                frames.push({ line: 0, text: `C<sub>0</sub> = 1 (barisan kosong). Barisan kurung seimbang yang tidak kosong selalu berbentuk ( A ) B: kurung pertama dan pasangannya membungkus A.`, html: view(0), watch: [["n", 0], ["C[n]", 1]] });
                for (let n = 1; n <= N; n++) {
                    for (let i = 0; i < n; i++) {
                        C[n] += C[i] * C[n - 1 - i];
                        if (n <= 4 || i === n - 1)
                            frames.push({
                                line: 1,
                                text: `n = ${n}: A berisi ${i} pasang (C${i} = ${C[i]} cara), B berisi ${n - 1 - i} pasang (C${n - 1 - i} = ${C[n - 1 - i]}). Tambah ${C[i] * C[n - 1 - i]} → C${n} = ${C[n]}.`,
                                html: view(n, i),
                                watch: [["n", n], ["i", i], ["C[n]", C[n], true]],
                                ask: i === n - 1 && n >= 3 && n <= 5 ? { type: "value", prompt: `Berapa C${n}?`, answer: String(C[n]) } : undefined,
                            });
                    }
                }
                frames.push({ line: 2, text: `C = ${C.join(", ")}. Rumus langsung C<sub>n</sub> = C(2n, n)/(n + 1). Banyak hal dihitung oleh Catalan: pohon biner, triangulasi poligon, jalur di bawah diagonal.`, html: view(N), watch: [["C[N]", C[N]]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Möbius ════════════════════════════
    V.register("mobius", (root) => {
        K.widget(root, {
            title: "Fungsi Möbius & Pasangan Koprima",
            stageClass: "kx-stage",
            practice: true,
            controls: `<label class="viz-input">N <input class="short" data-n value="12"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [["μ = 1", "#22c55e", "rgba(34,197,94,.14)"], ["μ = −1", "#ef4444", "rgba(239,68,68,.12)"], ["μ = 0", "#94a3b8", "rgba(148,163,184,.14)"]],
            code: `
// μ(d) = 0 jika d punya faktor kuadrat, (-1)^(banyak faktor prima) jika tidak  //@0
// banyak pasangan (i, j) di [1..N]² dengan gcd(i, j) = 1:
long long hasil = 0;
for (int d = 1; d <= N; d++)
    hasil += mu[d] * (N / d) * (N / d);  // pasangan yang keduanya kelipatan d //@1`,
            watch: "Keadaan",
            build(ui) {
                const N = clampInt(K.val(ui, "n"), 2, 30, 12);
                ui.head.querySelector("[data-n]").value = N;
                const mu = new Array(N + 1).fill(1);
                const isComp = new Array(N + 1).fill(false);
                for (let p = 2; p <= N; p++) {
                    if (isComp[p]) continue;
                    for (let x = p; x <= N; x += p) {
                        if (x > p) isComp[x] = true;
                        mu[x] = -mu[x];
                    }
                    for (let x = p * p; x <= N; x += p * p) mu[x] = 0;
                }
                const ds = [];
                for (let d = 1; d <= N; d++) ds.push(d);
                const cls = (upto) => Object.fromEntries(ds.map((d, i) => [i, d > upto ? "" : mu[d] === 1 ? "ok" : mu[d] === -1 ? "bad" : "dim"]));
                const frames = [];
                frames.push({ line: 0, text: `μ(1) = 1. Untuk d &gt; 1: jika ada p² yang membagi d, μ(d) = 0; jika tidak, μ(d) = (−1)<sup>banyak faktor prima</sup>.`, html: K.cells(ds.map((d) => "?"), { idx: ds, size: "sm" }), watch: [["N", N]] });
                ds.forEach((d) => {
                    if (d > 10 && d !== N) return;
                    frames.push({
                        line: 0,
                        text: `μ(${d}) = ${mu[d]}${d === 1 ? "" : mu[d] === 0 ? " (punya faktor kuadrat)" : ` (${d} hasil kali ${mu[d] === 1 ? "genap" : "ganjil"} banyak prima berbeda)`}.`,
                        html: K.cells(ds.map((x) => (x <= d ? mu[x] : "?")), { idx: ds, cls: cls(d), size: "sm" }),
                        htmlMasked: K.cells(ds.map((x) => (x < d ? mu[x] : "?")), { idx: ds, cls: cls(d - 1), size: "sm" }),
                        watch: [["d", d], ["μ(d)", mu[d], true]],
                        ask: [6, 8, 10].includes(d) ? { type: "value", prompt: `Berapa μ(${d})?`, answer: String(mu[d]) } : undefined,
                    });
                });
                let hasil = 0;
                const rows = [];
                ds.forEach((d) => {
                    if (!mu[d]) return;
                    const t = mu[d] * Math.floor(N / d) ** 2;
                    hasil += t;
                    rows.push([d, mu[d], Math.floor(N / d), t > 0 ? `+${t}` : t, hasil]);
                    frames.push({
                        line: 1,
                        text: `d = ${d}: ada ⌊${N}/${d}⌋² = ${Math.floor(N / d) ** 2} pasangan yang keduanya kelipatan ${d}; dikalikan μ(${d}) = ${mu[d]}.`,
                        html: K.cells(ds.map((x) => mu[x]), { idx: ds, cls: { ...cls(N), [d - 1]: "on" }, size: "sm" }) + K.table(rows, { head: ["d", "μ(d)", "⌊N/d⌋", "suku", "total"], rowCls: { [rows.length - 1]: "on" } }),
                        watch: [["d", d], ["total", hasil]],
                    });
                });
                let brute = 0;
                for (let i = 1; i <= N; i++) for (let j = 1; j <= N; j++) if (gcd(i, j) === 1) brute++;
                frames.push({ line: -1, text: `Banyak pasangan koprima di [1..${N}]² = <b>${hasil}</b> (cek brute force: ${brute}). Möbius membuat inklusi-eksklusi atas semua prima sekaligus, dalam O(N).`, html: K.cells(ds.map((x) => mu[x]), { idx: ds, cls: cls(N), size: "sm" }), watch: [["hasil", hasil]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Basis XOR ════════════════════════════
    V.register("xor-basis", (root) => {
        const W = 6;
        K.widget(root, {
            title: "Basis Linear XOR",
            stageClass: "kx-stage",
            practice: true,
            controls: `<label class="viz-input">bilangan (&lt; 64) <input data-arr value="13 7 10 6 1" style="width:130px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [["Bilangan yang disisipkan", "#f59e0b", "rgba(245,158,11,.18)"], ["Isi basis", "#22c55e", "rgba(34,197,94,.14)"]],
            code: `
long long basis[60] = {};      // basis[b]: vektor dengan bit tertinggi b
void sisip(long long x) {
    for (int b = 59; b >= 0; b--) {
        if (!(x >> b & 1)) continue;
        if (!basis[b]) { basis[b] = x; return; }   // posisi kosong: masuk //@0
        x ^= basis[b];                             // hilangkan bit b     //@1
    }
    // x menjadi 0: sudah bisa dibentuk dari basis   //@2
}`,
            watch: "Keadaan",
            build(ui) {
                const a = K.nums(K.val(ui, "arr"), { min: 0, max: 63, limit: 7, def: [13, 7, 10, 6, 1] });
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const basis = new Array(W).fill(0);
                const frames = [];
                const bview = (hl = -1) =>
                    K.table(
                        [...Array(W).keys()].reverse().map((b) => [`bit ${b}`, basis[b] ? K.bin(basis[b], W) : "–", basis[b] || ""]),
                        { head: ["posisi", "biner", "nilai"], rowCls: Object.fromEntries([...Array(W).keys()].reverse().map((b, r) => [r, b === hl ? "on" : basis[b] ? "ok" : ""])) },
                    );
                const xv = (x) => K.line("x", K.cells(K.bin(x, W).split(""), { idx: [...Array(W).keys()].reverse(), cls: Object.fromEntries(K.bin(x, W).split("").map((c, k) => [k, c === "1" ? "on" : ""])), size: "sm" }));
                frames.push({ line: -1, text: `Sisipkan bilangan satu per satu. Basis menyimpan paling banyak satu vektor untuk setiap bit tertinggi; semua XOR subset dari input = semua XOR subset dari basis.`, html: bview(), watch: [["rank", 0]] });
                a.forEach((v) => {
                    let x = v;
                    frames.push({ line: -1, text: `Sisipkan ${v} = ${K.bin(v, W)}<sub>2</sub>.`, html: xv(x) + bview(), watch: [["x", x]] });
                    let placed = false;
                    for (let b = W - 1; b >= 0; b--) {
                        if (!(x >> b & 1)) continue;
                        if (!basis[b]) {
                            basis[b] = x;
                            placed = true;
                            frames.push({ line: 0, text: `Bit tertinggi ${b} belum punya wakil: ${x} (${K.bin(x, W)}) masuk basis.`, html: xv(x) + bview(b), watch: [["x", x], ["rank", basis.filter(Boolean).length]], mark: "take" });
                            break;
                        }
                        const nx = x ^ basis[b];
                        frames.push({
                            line: 1,
                            text: `Bit ${b} sudah punya wakil ${basis[b]}: x ← ${x} ⊕ ${basis[b]} = ${nx}.`,
                            html: xv(x) + bview(b),
                            watch: [["x", x], ["x baru", nx, true]],
                            ask: { type: "value", prompt: `${x} XOR ${basis[b]} = ?`, answer: String(nx) },
                        });
                        x = nx;
                    }
                    if (!placed) frames.push({ line: 2, text: `x menjadi 0: ${v} sudah bisa dibentuk dari isi basis, tidak ada yang baru.`, html: xv(0) + bview(), watch: [["x", 0]], mark: "skip" });
                });
                let best = 0;
                for (let b = W - 1; b >= 0; b--) if (basis[b] && (best ^ basis[b]) > best) best ^= basis[b];
                const r = basis.filter(Boolean).length;
                frames.push({ line: -1, text: `Rank ${r}: ada 2<sup>${r}</sup> = ${1 << r} nilai XOR berbeda. XOR maksimum: mulai dari 0, untuk bit tertinggi ke terendah ambil wakilnya jika memperbesar → <b>${best}</b>.`, html: bview(), watch: [["rank", r], ["XOR maks", best]], mark: "done" });
                return frames;
            },
        });
    });

    // ════════════════════════════ Pollard Rho ════════════════════════════
    V.register("rho", (root) => {
        K.widget(root, {
            title: "Pollard Rho: Kura-kura dan Kelinci",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">n <input class="short" data-n value="8051"></label>
                <label class="viz-input">c <input class="short" data-c value="1"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [["Kura-kura x", "#22d3ee", "rgba(34,211,238,.16)"], ["Kelinci y", "#7c5cdb", "rgba(124,92,219,.14)"], ["Faktor ditemukan", "#22c55e", "rgba(34,197,94,.14)"]],
            code: `
long long x = 2, y = 2, d = 1;
while (d == 1) {
    x = f(x);            // f(v) = v*v + c mod n, satu langkah //@0
    y = f(f(y));         // dua langkah                       //@0
    d = gcd(abs(x - y), n);                                  //@1
}
// d != n: faktor ditemukan; d == n: ulangi dengan c lain   //@2`,
            watch: "Keadaan",
            build(ui) {
                const n = clampInt(K.val(ui, "n"), 4, 999999, 8051);
                const c = clampInt(K.val(ui, "c"), 1, 50, 1);
                ui.head.querySelector("[data-n]").value = n;
                ui.head.querySelector("[data-c]").value = c;
                const f = (v) => (v * v + c) % n;
                let x = 2;
                let y = 2;
                let d = 1;
                const rows = [];
                const frames = [];
                frames.push({ line: -1, text: `Barisan x<sub>k+1</sub> = x<sub>k</sub>² + ${c} mod ${n} pasti berulang. Jika p faktor n, barisan mod p berulang jauh lebih cepat (sekitar √p langkah). Saat x ≡ y (mod p) tetapi x ≠ y, gcd(|x − y|, n) membongkar p.`, html: K.table([], { head: ["k", "x", "y", "gcd(|x−y|, n)"] }), watch: [["n", n]] });
                let k = 0;
                while (d === 1 && k < 40) {
                    k++;
                    x = f(x);
                    y = f(f(y));
                    d = gcd(Math.abs(x - y), n);
                    rows.push([k, x, y, d]);
                    frames.push({
                        line: d === 1 ? 0 : 1,
                        text: `Langkah ${k}: x = ${x}, y = ${y}, gcd(${Math.abs(x - y)}, ${n}) = ${d}.`,
                        html: K.table(rows, { head: ["k", "x", "y", "gcd(|x−y|, n)"], rowCls: { [rows.length - 1]: d === 1 ? "on" : "ok" } }),
                        watch: [["x", x], ["y", y], ["d", d, true]],
                        ask: k === 2 ? { type: "value", prompt: `gcd(|${x} − ${y}|, ${n}) = ?`, answer: String(d) } : undefined,
                        mark: d === 1 ? "" : "discover",
                    });
                }
                frames.push({
                    line: 2,
                    text:
                        d !== 1 && d !== n
                            ? `Faktor ditemukan: ${n} = ${d} × ${n / d} setelah ${k} langkah. Bandingkan dengan pembagian percobaan yang butuh ${Math.min(d, n / d)} percobaan.`
                            : d === n
                              ? `gcd = n: gagal untuk c ini (x dan y bertemu tepat). Coba c lain.`
                              : `Belum ketemu dalam 40 langkah (mungkin n prima). Untuk n prima, uji Miller-Rabin dulu.`,
                    html: K.table(rows, { head: ["k", "x", "y", "gcd(|x−y|, n)"] }),
                    watch: [["faktor", d]],
                    mark: "done",
                });
                return frames;
            },
        });
    });

    // ════════════════════════════ FFT / NTT kecil ════════════════════════════
    V.register("fft", (root) => {
        const P = 17;
        const G = 3; // akar primitif mod 17
        K.widget(root, {
            title: "NTT: Bagi Genap–Ganjil (modulo 17)",
            stageClass: "kx-stage",
            practice: true,
            controls: `<label class="viz-input">koefisien <input data-arr value="1 2 3 4" style="width:120px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [["Bagian genap", "#22d3ee", "rgba(34,211,238,.16)"], ["Bagian ganjil", "#7c5cdb", "rgba(124,92,219,.14)"], ["Sudah dievaluasi", "#22c55e", "rgba(34,197,94,.14)"]],
            code: `
// P(x) = Genap(x²) + x · Ganjil(x²)
void ntt(vector<long long>& a, long long w) {       // w: akar ke-n dari 1
    int n = a.size();
    if (n == 1) return;                              //@0
    vector<long long> g(n / 2), h(n / 2);
    for (int i = 0; i < n / 2; i++) { g[i] = a[2*i]; h[i] = a[2*i+1]; }   //@1
    ntt(g, w * w % P);  ntt(h, w * w % P);
    long long t = 1;
    for (int k = 0; k < n / 2; k++) {                //@2
        a[k]         = (g[k] + t * h[k]) % P;        // P(w^k)
        a[k + n / 2] = (g[k] - t * h[k] % P + P) % P; // P(w^(k+n/2)) = P(-w^k)
        t = t * w % P;
    }
}`,
            watch: "Keadaan",
            build(ui) {
                let a = K.nums(K.val(ui, "arr"), { min: 0, max: 16, limit: 8, def: [1, 2, 3, 4] });
                let n = 1;
                while (n < a.length) n *= 2;
                a = a.concat(new Array(n - a.length).fill(0));
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const pw = (b, e) => modpow(b, e, P);
                const w0 = pw(G, 16 / n);
                const nodes = [];
                const frames = [];
                let id = 0;
                const build = (arr, w, parent, side) => {
                    const me = { id: id++, arr: arr.slice(), w, parent, side, val: null };
                    nodes.push(me);
                    if (arr.length > 1) {
                        const g = arr.filter((_, i) => i % 2 === 0);
                        const h = arr.filter((_, i) => i % 2 === 1);
                        me.kids = [build(g, (w * w) % P, me.id, "in"), build(h, (w * w) % P, me.id, "vi")];
                    }
                    return me;
                };
                const rootN = build(a, w0, null, "");
                const draw = (cur) =>
                    K.tree({
                        nodes: nodes.map((nd) => ({
                            id: nd.id,
                            label: nd.val ? `[${nd.val.join(" ")}]` : `(${nd.arr.join(" ")})`,
                            parent: nd.parent,
                            cls: nd.id === cur ? "on" : nd.val ? "ok" : nd.side,
                        })),
                        w: 640,
                        h: 260,
                        r: 16,
                        wide: true,
                    });
                frames.push({ line: -1, text: `Koefisien (${a.join(", ")}) dievaluasi di ${n} titik: pangkat-pangkat ω = ${w0}, akar ke-${n} dari 1 modulo 17 (${w0}<sup>${n}</sup> ≡ 1). Kurung ( ) = koefisien, kurung [ ] = nilai di titik-titik.`, html: draw(-1), watch: [["n", n], ["ω", w0]] });
                const order = [];
                const post = (nd) => {
                    (nd.kids || []).forEach(post);
                    order.push(nd);
                };
                nodes
                    .filter((nd) => nd.kids)
                    .forEach((nd) => frames.push({ line: 1, text: `Pisahkan (${nd.arr.join(" ")}) menjadi indeks genap (${nd.kids[0].arr.join(" ")}) dan ganjil (${nd.kids[1].arr.join(" ")}).`, html: draw(nd.id), watch: [["ukuran", nd.arr.length]] }));
                post(rootN);
                let asked = 0;
                order.forEach((nd) => {
                    if (!nd.kids) {
                        nd.val = [nd.arr[0]];
                        return;
                    }
                    const g = nd.kids[0].val;
                    const h = nd.kids[1].val;
                    const m = nd.arr.length;
                    const out = new Array(m);
                    let t = 1;
                    for (let k = 0; k < m / 2; k++) {
                        out[k] = (g[k] + t * h[k]) % P;
                        out[k + m / 2] = (((g[k] - t * h[k]) % P) + P) % P;
                        t = (t * nd.w) % P;
                    }
                    nd.val = out;
                    frames.push({
                        line: 2,
                        text: `Gabungkan: nilai[k] = G[k] + ω<sup>k</sup>·H[k], nilai[k + ${m / 2}] = G[k] − ω<sup>k</sup>·H[k] (mod 17), dengan ω = ${nd.w}. Hasil [${out.join(" ")}].`,
                        html: draw(nd.id),
                        watch: [["ukuran", m], ["ω", nd.w], ["nilai[0]", out[0], true]],
                        ask: asked++ === 0 ? { type: "value", prompt: `nilai[0] = G[0] + H[0] mod 17 = ?`, answer: String(out[0]) } : undefined,
                        mark: "key",
                    });
                });
                frames.push({ line: -1, text: `Nilai di ${n} titik: [${rootN.val.join(" ")}], dalam O(n log n). Untuk mengalikan dua polinom: evaluasi keduanya, kalikan nilainya titik demi titik, lalu NTT balik.`, html: draw(-1), watch: [["hasil", rootN.val.join(" ")]], mark: "done" });
                return frames;
            },
        });
    });
})();
