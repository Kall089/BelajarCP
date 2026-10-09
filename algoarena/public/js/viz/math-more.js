/* Visualizer track Matematika (lanjutan): pangkat matriks, teori permainan, convex hull */
(() => {
    "use strict";

    const V = window.Viz;

    // ════════════════════════════ Pangkat cepat matriks ════════════════════════════
    V.register("matpow", (root) => {
        const sh = V.shell(root, {
            title: "Pangkat Cepat Matriks: Fibonacci ke-n",
            controls: `<label class="viz-input">n <input class="short" data-n value="13"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Bit yang sedang diperiksa", "#f59e0b", "rgba(245,158,11,.3)"],
                ["Matriks yang berubah", "#22c55e", "rgba(34,197,94,.25)"],
            ],
        });
        const nIn = sh.head.querySelector("[data-n]");
        const code = V.codePanel(
            sh.side,
            `
// M = [[1, 1], [1, 0]];  M^n = [[F(n+1), F(n)], [F(n), F(n-1)]]
Mat pangkat(Mat M, long long n) {
    Mat R = identitas();          // R = I                 //@0
    while (n > 0) {                                        //@1
        if (n & 1) R = kali(R, M);   // bit ini 1          //@2
        M = kali(M, M);              // M^(2^k) -> M^(2^(k+1)) //@3
        n >>= 1;                                           //@4
    }
    return R;                     // F(n) = R[0][1]        //@5
}`,
        );
        const watch = V.watchPanel(sh.side);
        const mul = (A, B) => [
            [A[0][0] * B[0][0] + A[0][1] * B[1][0], A[0][0] * B[0][1] + A[0][1] * B[1][1]],
            [A[1][0] * B[0][0] + A[1][1] * B[1][0], A[1][0] * B[0][1] + A[1][1] * B[1][1]],
        ];
        const mat = (A, label, cls = "") =>
            `<div class="mp-mat ${cls}"><span class="mp-lbl">${label}</span><div class="mp-grid">${A.flat().map((x) => `<b>${x}</b>`).join("")}</div></div>`;

        function build() {
            const n = V.clampInt(nIn.value, 1, 60, 13);
            nIn.value = n;
            const bits = n.toString(2).split("").reverse();
            let R = [[1, 0], [0, 1]];
            let M = [[1, 1], [1, 0]];
            let pw = 1;
            let rPow = 0;
            let mults = 0;
            const frames = [];
            const render = (k, st = {}) => {
                const bitRow = bits
                    .map((b, i) => `<span class="mp-bit ${i === k ? "cur" : ""} ${i < k ? "done" : ""} ${b === "1" ? "one" : ""}"><small>2<sup>${i}</sup></small><b>${b}</b></span>`)
                    .reverse()
                    .join("");
                return `<div class="mp-wrap">
                    <div class="mp-bits"><span class="mq-lbl">n = ${n}</span>${bitRow}</div>
                    <div class="mp-mats">${mat(R, `R = M<sup>${rPow}</sup>`, st.r ? "chg" : "")}${mat(M, `M<sup>${pw}</sup>`, st.m ? "chg" : "")}</div>
                    <p class="mq-note">angka asli (tanpa modulo) agar mudah dicek; F(${rPow}) = R[0][1] = ${R[0][1]}</p>
                </div>`;
            };
            const push = (line, text, k, st, mark, ask) =>
                frames.push({ line, text, mark, ask, html: render(k, st), htmlMasked: ask ? st.masked : undefined, watch: [["n sisa", st.rest ?? n], ["perkalian", mults, true], ["R", `M^${rPow}`, true]] });
            push(0, `Hitung F(${n}) dengan memangkatkan M = [[1, 1], [1, 0]]. Tulis n dalam biner: <b>${n.toString(2)}</b>. Setiap bit 1 berarti R dikali pangkat M yang sesuai.`, -1, {});
            let rest = n;
            for (let k = 0; k < bits.length; k++) {
                push(1, `Periksa bit ke-${k} (nilai 2<sup>${k}</sup> = ${pw}). Saat ini M = M<sup>${pw}</sup>.`, k, { rest });
                if (bits[k] === "1") {
                    const masked = render(k, { rest });
                    R = mul(R, M);
                    rPow += pw;
                    mults++;
                    push(2, `Bit bernilai 1: <b>R = R · M<sup>${pw}</sup></b>, sekarang R = M<sup>${rPow}</sup>.`, k, { r: true, rest, masked }, "take", {
                        type: "choice",
                        options: ["Kalikan R dengan M", "Lewati"],
                        answer: 0,
                        prompt: `Bit ke-${k} dari ${n} adalah ${bits[k]}. Apa yang terjadi pada R?`,
                        hint: "Hanya bit bernilai 1 yang ikut menyusun n.",
                    });
                } else push(2, `Bit bernilai 0: R tidak berubah.`, k, { rest }, "skip");
                if (k + 1 < bits.length) {
                    M = mul(M, M);
                    pw *= 2;
                    mults++;
                    push(3, `Kuadratkan: <b>M = M · M</b> menjadi M<sup>${pw}</sup>. Hanya ${bits.length} kali kuadrat untuk mencapai bit tertinggi.`, k, { m: true, rest });
                }
                rest = Math.floor(rest / 2);
            }
            push(5, `Selesai: F(${n}) = R[0][1] = <b>${R[0][1]}</b> dengan ${mults} perkalian matriks, bukan ${n - 1}. Untuk n = 10<sup>18</sup> hanya sekitar 120 perkalian.`, bits.length, { rest: 0 }, "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        nIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        build();
    });

    // ════════════════════════════ Teori permainan ════════════════════════════
    V.register("nim", (root) => {
        const sh = V.shell(root, {
            title: "Posisi Menang & Kalah: Ambil Batu",
            controls: `
                ${V.segmented("mode", [["mk", "Menang/Kalah"], ["grundy", "Nilai Grundy"]], "mk")}
                <label class="viz-input">Langkah S <input data-s value="1, 3, 4" style="width:90px"></label>
                <label class="viz-input">N <input class="short" data-n value="14"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Posisi menang (M)", "#22c55e", "rgba(34,197,94,.25)"],
                ["Posisi kalah (K)", "#ef4444", "rgba(239,68,68,.18)"],
                ["Sedang dihitung", "#f59e0b", "rgba(245,158,11,.3)"],
                ["Bisa dicapai satu langkah", "#22d3ee"],
            ],
        });
        const sIn = sh.head.querySelector("[data-s]");
        const nIn = sh.head.querySelector("[data-n]");
        let mode = "mk";
        const code = V.codePanel(
            sh.side,
            `
// menang[x]: pemain yang giliran di x (sisa x batu) pasti menang?
menang[0] = false;                    // tidak bisa melangkah: kalah //@0
for (int x = 1; x <= n; x++) {                                    //@1
    menang[x] = false;
    for (int s : S)                                               //@2
        if (s <= x && !menang[x - s]) menang[x] = true;           //@3
}
// Grundy: g[x] = mex { g[x - s] : s ∈ S, s ≤ x }                 //@4
// mex = bilangan cacah terkecil yang TIDAK ada di himpunan       //@4`,
        );
        const watch = V.watchPanel(sh.side);

        function build() {
            let S = [...new Set(V.parseList(sIn.value, { min: 1, max: 9, limit: 4 }))].sort((a, b) => a - b);
            if (!S.length) S = [1, 3, 4];
            sIn.value = S.join(", ");
            const n = V.clampInt(nIn.value, 4, 20, 14);
            nIn.value = n;
            const g = new Array(n + 1).fill(null);
            const frames = [];
            const isG = mode === "grundy";
            const render = (st = {}) => {
                const cells = Array.from({ length: n + 1 }, (_, x) => {
                    const c = ["gm-cell"];
                    const known = g[x] !== null && !(st.mask === x);
                    if (known) c.push(isG ? (g[x] === 0 ? "lose" : "win") : g[x] ? "win" : "lose");
                    if (st.cur === x) c.push("cur");
                    if (st.reach && st.reach.includes(x)) c.push("reach");
                    const val = !known ? (st.mask === x ? "?" : "") : isG ? g[x] : g[x] ? "M" : "K";
                    return `<div class="${c.join(" ")}"><small>${x}</small><b>${val}</b></div>`;
                }).join("");
                return `<div class="gm-wrap"><div class="gm-row">${cells}</div><p class="mq-note">angka kecil = sisa batu; langkah yang boleh: ambil ${S.join(" / ")} batu</p></div>`;
            };
            const mex = (arr) => {
                let m = 0;
                while (arr.includes(m)) m++;
                return m;
            };
            const push = (line, text, st, extra, mark, ask) =>
                frames.push({
                    line,
                    text,
                    mark,
                    ask,
                    html: render(st),
                    htmlMasked: ask ? render({ ...st, mask: st.cur }) : undefined,
                    watch: [["x", st.cur ?? "–"], ...extra],
                });
            g[0] = isG ? 0 : false;
            push(isG ? 4 : 0, isG ? "g[0] = mex{} = 0: tanpa langkah, nilai Grundy 0 (posisi kalah)." : "x = 0: tidak ada batu, pemain yang giliran tidak bisa melangkah dan <b>kalah</b>.", { cur: 0 }, []);
            for (let x = 1; x <= n; x++) {
                const reach = S.filter((s) => s <= x).map((s) => x - s);
                push(isG ? 4 : 2, `x = ${x}: dari sini bisa ke ${reach.length ? reach.join(", ") : "tidak ke mana pun"}.`, { cur: x, reach }, [["tujuan", reach.join(", ") || "–"]]);
                if (isG) {
                    const vals = reach.map((y) => g[y]);
                    g[x] = mex(vals);
                    push(
                        4,
                        `Nilai tujuan {${vals.join(", ")}}; bilangan terkecil yang tidak ada adalah <b>${g[x]}</b>. ${g[x] === 0 ? "Grundy 0 = posisi kalah." : "Grundy ≠ 0 = posisi menang."}`,
                        { cur: x, reach },
                        [["g[x]", g[x], true]],
                        g[x] ? "take" : "skip",
                        { type: "value", answer: String(g[x]), prompt: `Berapa g[${x}] = mex{${vals.join(", ")}}?`, hint: "mex adalah bilangan cacah terkecil (0, 1, 2, …) yang tidak muncul." },
                    );
                } else {
                    const toLose = reach.filter((y) => !g[y]);
                    g[x] = toLose.length > 0;
                    push(
                        3,
                        g[x]
                            ? `Ada langkah ke posisi kalah (${toLose.join(", ")}): lawan dipaksa ke posisi kalah, jadi x = ${x} <b>menang</b>.`
                            : `Semua langkah menuju posisi menang milik lawan: x = ${x} <b>kalah</b>.`,
                        { cur: x, reach },
                        [["hasil", g[x] ? "menang" : "kalah", true]],
                        g[x] ? "take" : "skip",
                        { type: "choice", options: ["Menang", "Kalah"], answer: g[x] ? 0 : 1, prompt: `Dari x = ${x} bisa ke ${reach.join(", ")}. Posisi x menang atau kalah?`, hint: "Posisi menang jika ada SATU langkah ke posisi kalah." },
                    );
                }
            }
            const lose = g.map((v, i) => ((isG ? v === 0 : !v) ? i : null)).filter((v) => v !== null);
            push(-1, isG ? `Selesai. Untuk beberapa tumpukan sekaligus, XOR-kan nilai Grundy setiap tumpukan: hasil 0 berarti pemain pertama kalah (teorema Sprague–Grundy).` : `Posisi kalah: ${lose.join(", ")}. Perhatikan polanya berulang; banyak soal permainan diselesaikan dengan menemukan pola dari tabel kecil seperti ini.`, {}, [], "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            build();
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [sIn, nIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });

    // ════════════════════════════ Convex hull (monotone chain) ════════════════════════════
    V.register("hull", (root) => {
        const sh = V.shell(root, {
            title: "Convex Hull: Monotone Chain Andrew",
            controls: `
                <button class="btn btn-sm" data-rand>Acak titik</button>
                <button class="btn btn-sm btn-primary" data-reset>Contoh awal</button>`,
            legend: [
                ["Titik yang diperiksa", "#f59e0b", "rgba(245,158,11,.4)"],
                ["Rantai di stack", "#22c55e"],
                ["Dibuang (belok kanan/lurus)", "#ef4444"],
                ["Hull akhir", "#4f5fd6"],
            ],
        });
        const code = V.codePanel(
            sh.side,
            `
// cross(O, A, B) > 0  <=>  O -> A -> B belok kiri
sort(p.begin(), p.end());                 // urut x, lalu y          //@0
vector<P> h;
for (int pass = 0; pass < 2; pass++) {    // bawah, lalu atas        //@1
    int awal = h.size();
    for (P q : p) {                                                  //@2
        while (h.size() >= awal + 2 &&                               //@3
               cross(h[h.size() - 2], h.back(), q) <= 0)             //@3
            h.pop_back();                 // bukan belok kiri: buang //@4
        h.push_back(q);                                              //@5
    }
    h.pop_back();                         // ujung dipakai rantai lain //@6
    reverse(p.begin(), p.end());
}`,
        );
        const watch = V.watchPanel(sh.side);
        const SAMPLE = [[1, 1], [3, 0], [6, 1], [8, 3], [7, 6], [4, 7], [1, 5], [3, 3], [5, 4], [5, 2], [2, 3], [6, 5]];
        let pts = SAMPLE;

        function build() {
            const P = pts.map(([x, y], i) => ({ x, y, id: String.fromCharCode(65 + i) })).sort((a, b) => a.x - b.x || a.y - b.y);
            const W = 560;
            const H = 330;
            const X = (p) => 40 + p.x * 58;
            const Y = (p) => H - 30 - p.y * 38;
            const cross = (o, a, b) => (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
            const frames = [];
            let h = [];
            let checks = 0;
            const render = (st = {}) => {
                let svg = `<svg viewBox="0 0 ${W} ${H}" class="hl-svg">`;
                for (let gx = 0; gx <= 8; gx++) svg += `<line x1="${X({ x: gx })}" y1="${Y({ y: 0 })}" x2="${X({ x: gx })}" y2="${Y({ y: 7 })}" class="hl-grid"/>`;
                for (let gy = 0; gy <= 7; gy++) svg += `<line x1="${X({ x: 0 })}" y1="${Y({ y: gy })}" x2="${X({ x: 8 })}" y2="${Y({ y: gy })}" class="hl-grid"/>`;
                if (st.final) {
                    svg += `<polygon points="${st.final.map((p) => `${X(p)},${Y(p)}`).join(" ")}" class="hl-poly"/>`;
                } else {
                    for (let i = 0; i + 1 < h.length; i++) svg += `<line x1="${X(h[i])}" y1="${Y(h[i])}" x2="${X(h[i + 1])}" y2="${Y(h[i + 1])}" class="hl-chain"/>`;
                    if (st.drop) svg += `<line x1="${X(st.drop[0])}" y1="${Y(st.drop[0])}" x2="${X(st.drop[1])}" y2="${Y(st.drop[1])}" class="hl-drop"/><line x1="${X(st.drop[1])}" y1="${Y(st.drop[1])}" x2="${X(st.drop[2])}" y2="${Y(st.drop[2])}" class="hl-drop"/>`;
                    if (st.try && h.length) svg += `<line x1="${X(h[h.length - 1])}" y1="${Y(h[h.length - 1])}" x2="${X(st.try)}" y2="${Y(st.try)}" class="hl-try"/>`;
                }
                for (const p of P) {
                    const c = ["hl-pt"];
                    if (st.try === p) c.push("cur");
                    else if (st.final ? st.final.includes(p) : h.includes(p)) c.push("on");
                    if (st.drop && st.drop[1] === p) c.push("drop");
                    svg += `<g class="${c.join(" ")}"><circle cx="${X(p)}" cy="${Y(p)}" r="7"/><text x="${X(p) + 10}" y="${Y(p) - 9}">${p.id}</text></g>`;
                }
                return svg + "</svg>";
            };
            const push = (line, text, st, extra = [], mark) =>
                frames.push({ line, text, mark, html: render(st), watch: [["stack", h.map((p) => p.id).join(" ") || "–"], ["cek cross", checks], ...extra] });
            push(0, `Urutkan ${P.length} titik menurut x lalu y: ${P.map((p) => p.id).join(", ")}. Titik paling kiri dan paling kanan pasti ada di hull.`, {});
            const lists = [P, [...P].reverse()];
            const res = [];
            for (let pass = 0; pass < 2; pass++) {
                h = [];
                push(1, pass === 0 ? "<b>Rantai bawah</b>: telusuri dari kiri ke kanan. Rantai yang benar selalu belok kiri." : "<b>Rantai atas</b>: telusuri dari kanan ke kiri dengan aturan yang sama.", {});
                for (const q of lists[pass]) {
                    while (h.length >= 2) {
                        const a = h[h.length - 2];
                        const b = h[h.length - 1];
                        const c = cross(a, b, q);
                        checks++;
                        if (c > 0) {
                            push(3, `cross(${a.id}, ${b.id}, ${q.id}) = ${c} &gt; 0: belok kiri, ${b.id} aman.`, { try: q }, [["cross", c]]);
                            break;
                        }
                        push(4, `cross(${a.id}, ${b.id}, ${q.id}) = ${c} ≤ 0: ${c === 0 ? "segaris" : "belok kanan"}, ${b.id} tidak mungkin di hull. <b>Buang ${b.id}</b>.`, { try: q, drop: [a, b, q] }, [["cross", c]], "skip");
                        h.pop();
                    }
                    h.push(q);
                    push(5, `Masukkan ${q.id} ke stack.`, { try: q }, [], "take");
                }
                const last = h.pop();
                push(6, `Rantai ${pass === 0 ? "bawah" : "atas"} selesai. Buang ujungnya (${last.id}) karena akan menjadi awal rantai berikutnya.`, {});
                res.push(...h);
            }
            push(-1, `Hull: <b>${res.map((p) => p.id).join(" → ")}</b> (${res.length} titik, berlawanan arah jarum jam). Setiap titik masuk dan keluar stack paling banyak sekali per rantai, jadi setelah sort total O(N).`, { final: res }, [["hull", res.length]], "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch);
        });
        sh.head.querySelector("[data-rand]").onclick = () => {
            const seen = new Set();
            pts = [];
            while (pts.length < 11) {
                const x = Math.floor(Math.random() * 9);
                const y = Math.floor(Math.random() * 8);
                if (!seen.has(x * 10 + y)) seen.add(x * 10 + y), pts.push([x, y]);
            }
            build();
        };
        sh.head.querySelector("[data-reset]").onclick = () => {
            pts = SAMPLE;
            build();
        };
        build();
    });
})();
