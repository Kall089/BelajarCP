/* Visualizer track String: fungsi prefiks (KMP), hash polinomial, trie */
(() => {
    "use strict";

    const V = window.Viz;
    const esc = (s) => String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;");

    // ════════════════════════════ Fungsi prefiks π ════════════════════════════
    V.register("kmp", (root) => {
        const sh = V.shell(root, {
            title: "Fungsi Prefiks (KMP)",
            controls: `
                <label class="viz-input">String <input data-s value="aabaaab" style="width:150px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["s[i] (sedang diproses)", "#f59e0b", "rgba(245,158,11,.2)"],
                ["s[k] (dibandingkan)", "#22d3ee", "rgba(34,211,238,.18)"],
                ["Awalan = akhiran (border)", "#22c55e", "rgba(34,197,94,.16)"],
                ["Tidak cocok", "#ef4444", "rgba(239,68,68,.16)"],
            ],
        });
        const sIn = sh.head.querySelector("[data-s]");
        const code = V.codePanel(
            sh.side,
            `
vector<int> pi(n, 0);                                //@0
for (int i = 1; i < n; i++) {                        //@1
    int k = pi[i - 1];      // border terpanjang lama //@1
    while (k > 0 && s[i] != s[k])                    //@2
        k = pi[k - 1];      // coba border lebih pendek //@2
    if (s[i] == s[k]) k++;                           //@3
    pi[i] = k;                                       //@4
}`,
        );
        const watch = V.watchPanel(sh.side);

        function build() {
            let s = sIn.value.replace(/[^a-z]/gi, "").toLowerCase().slice(0, 14);
            if (s.length < 2) s = "aabaaab";
            sIn.value = s;
            const n = s.length;
            const pi = new Array(n).fill(null);
            pi[0] = 0;
            const frames = [];
            const render = (st) => {
                const cell = (j) => {
                    const cls = ["mq-cell"];
                    if (st.pre && j < st.pre) cls.push("front");
                    if (st.suf && j >= st.suf[0] && j < st.suf[1]) cls.push("front");
                    if (j === st.k) cls.push(st.bad ? "gone" : "win");
                    if (j === st.i) cls.push(st.bad ? "gone" : "cur");
                    return cls.join(" ");
                };
                return `<div class="mq-wrap">
                    ${rowOf("i", n, (j) => `<div class="mq-cell idx">${j}</div>`)}
                    ${rowOf("s[i]", n, (j) => `<div class="${cell(j)}">${esc(s[j])}</div>`)}
                    ${rowOf("π[i]", n, (j) => `<div class="mq-cell ${pi[j] === null ? "mq-na" : ""} ${j === st.i && st.done ? "cur" : ""}">${pi[j] === null ? "·" : st.mask && j === st.i ? "?" : pi[j]}</div>`)}
                    <p class="mq-note">π[i] = panjang awalan terpanjang (bukan seluruh s[0..i]) yang juga akhiran s[0..i]</p>
                </div>`;
            };
            const rowOf = (label, len, fn) => `<div class="mq-row"><span class="mq-lbl">${label}</span>${Array.from({ length: len }, (_, j) => fn(j)).join("")}</div>`;
            const push = (line, text, st = {}, fx = {}) => frames.push({ line, text, html: render(st), htmlMasked: fx.ask ? render({ ...st, mask: true }) : undefined, ...fx });
            push(0, `<b>π[i]</b> menjawab: berapa panjang awalan terpanjang dari s yang juga muncul sebagai akhiran s[0..i] (tidak boleh seluruh s[0..i]). π[0] = 0. Dari π, KMP mencari pola dalam O(n + m).`, {}, { watch: [["n", n]] });
            for (let i = 1; i < n; i++) {
                let k = pi[i - 1];
                push(1, `Proses i = ${i} (huruf '${s[i]}'). Border terpanjang untuk s[0..${i - 1}] panjangnya π[${i - 1}] = ${k}${k ? `: "${s.slice(0, k)}" = "${s.slice(i - k, i)}"` : ""}. Coba perpanjang dengan membandingkan s[${i}] dan s[${k}].`, { i, k, pre: k, suf: [i - k, i] }, { watch: [["i", i], ["k", k]] });
                while (k > 0 && s[i] !== s[k]) {
                    const nk = pi[k - 1];
                    push(2, `s[${i}] = '${s[i]}' ≠ s[${k}] = '${s[k]}': border sepanjang ${k} tidak bisa diperpanjang. Lompat ke border yang lebih pendek: <code>k = π[${k - 1}] = ${nk}</code>. Tidak perlu mengulang dari awal.`, { i, k, bad: true }, { watch: [["i", i], ["k", k], ["k baru", nk]], mark: "skip" });
                    k = nk;
                    push(2, `Sekarang k = ${k}${k ? `, border "${s.slice(0, k)}"` : ""}. Bandingkan lagi s[${i}] dengan s[${k}].`, { i, k, pre: k, suf: [i - k, i] }, { watch: [["i", i], ["k", k]] });
                }
                if (s[i] === s[k]) {
                    k++;
                    push(3, `s[${i}] = s[${k - 1}] = '${s[i]}': cocok, border bertambah satu menjadi ${k}.`, { i, k: k - 1, pre: k, suf: [i - k + 1, i + 1] }, { watch: [["i", i], ["k", k]] });
                } else {
                    push(3, `s[${i}] = '${s[i]}' ≠ s[0] = '${s[0]}' dan k sudah 0: tidak ada border.`, { i, k, bad: true }, { watch: [["i", i], ["k", 0]] });
                }
                pi[i] = k;
                push(4, `<code>π[${i}] = ${k}</code>${k ? `: "${s.slice(0, k)}" adalah awalan sekaligus akhiran dari "${s.slice(0, i + 1)}"` : ""}.`, { i, done: true, pre: k, suf: [i - k + 1, i + 1] }, {
                    watch: [["i", i], ["π[i]", k]],
                    ask: i === n - 1 || i === Math.floor(n / 2) ? { type: "value", answer: k, prompt: `Berapa <code>π[${i}]</code>?`, hint: "Panjang awalan terpanjang yang sama dengan akhiran s[0..i]." } : undefined,
                });
            }
            push(-1, `Selesai: π = [${pi.join(", ")}]. Setiap langkah maju menambah k paling banyak 1, dan setiap lompatan mundur mengurangi k, jadi total O(n), meski ada <code>while</code> di dalam <code>for</code>.`, {}, { mark: "done", watch: [["n", n]] });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        sIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        build();
    });

    // ════════════════════════════ Hash polinomial & prefix hash ════════════════════════════
    V.register("hash", (root) => {
        const B = 31;
        const M = 1009;
        const sh = V.shell(root, {
            title: "Prefix Hash (B = 31, M = 1009)",
            controls: `
                <label class="viz-input">String <input data-s value="abracadabra" style="width:130px"></label>
                <label class="viz-input">A = s[a..b] <input class="short" data-a value="1 4" style="width:52px"></label>
                <label class="viz-input">B = s[c..d] <input class="short" data-b value="8 11" style="width:52px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sedang dihitung", "#f59e0b", "rgba(245,158,11,.2)"],
                ["Potongan yang ditanya", "#22d3ee", "rgba(34,211,238,.18)"],
                ["h[l] dan h[r] yang dipakai", "#22c55e", "rgba(34,197,94,.18)"],
            ],
        });
        const sIn = sh.head.querySelector("[data-s]");
        const aIn = sh.head.querySelector("[data-a]");
        const bIn = sh.head.querySelector("[data-b]");
        const code = V.codePanel(
            sh.side,
            `
// h[i] = hash s[0..i-1],  p[i] = B^i  (mod M)
h[0] = 0; p[0] = 1;                                   //@0
for (int i = 0; i < n; i++) {                         //@1
    h[i + 1] = (h[i] * B + nilai(s[i])) % M;          //@1
    p[i + 1] = p[i] * B % M;                          //@1
}
// hash s[l..r-1] dalam O(1)
long long ambil(int l, int r) {                       //@2
    return ((h[r] - h[l] * p[r - l]) % M + M) % M;    //@3
}`,
        );
        const watch = V.watchPanel(sh.side);
        const mod = (x) => ((x % M) + M) % M;

        function build() {
            let str = sIn.value.replace(/[^a-z]/gi, "").toLowerCase().slice(0, 11);
            if (str.length < 2) str = "abracadabra";
            sIn.value = str;
            const n = str.length;
            const rng = (inp, def) => {
                const v = inp.value.trim().split(/[\s,]+/).map(Number);
                let a = V.clampInt(v[0], 1, n, def[0]);
                let b = V.clampInt(v[1], 1, n, def[1]);
                if (a > b) [a, b] = [b, a];
                inp.value = `${a} ${b}`;
                return [a, b];
            };
            const A = rng(aIn, [1, Math.min(4, n)]);
            const Bq = rng(bIn, [Math.max(1, n - 3), n]);
            const val = (c) => c.charCodeAt(0) - 96;
            const h = new Array(n + 1).fill(null);
            const p = new Array(n + 1).fill(null);
            h[0] = 0;
            p[0] = 1;
            const frames = [];
            const render = (st) => {
                const col = (j, inner, extra = "") => `<div class="mq-cell ${extra}">${inner}</div>`;
                const rowOf = (label, fn) => `<div class="mq-row"><span class="mq-lbl">${label}</span>${Array.from({ length: n + 1 }, (_, j) => fn(j)).join("")}</div>`;
                const inRange = (j) => st.range && j >= st.range[0] && j <= st.range[1];
                return `<div class="mq-wrap compact">
                    ${rowOf("i", (j) => `<div class="mq-cell idx">${j}</div>`)}
                    ${rowOf("s", (j) => (j === 0 ? '<div class="mq-cell mq-na"></div>' : col(j, `${str[j - 1]}<sub class="hs-v">${val(str[j - 1])}</sub>`, `${inRange(j) ? "win" : ""} ${st.cur === j ? "cur" : ""}`)))}
                    ${rowOf("h[i]", (j) => col(j, h[j] === null ? "·" : h[j], `${h[j] === null ? "mq-na" : ""} ${st.cur === j ? "cur" : ""} ${st.ends && st.ends.includes(j) ? "front" : ""}`))}
                    ${rowOf("p[i]", (j) => col(j, p[j] === null ? "·" : p[j], `${p[j] === null ? "mq-na" : ""} ${st.pw === j ? "front" : ""}`))}
                    <p class="mq-note">nilai huruf: a = 1, b = 2, …, z = 26 (angka kecil di bawah huruf)</p>
                </div>`;
            };
            frames.push({ line: 0, text: `Ubah string menjadi bilangan dalam basis B = ${B}: "abc" ↦ 1·31² + 2·31 + 3. Agar tidak meledak, semuanya dimodulo M = ${M} (di soal sungguhan M sekitar 10<sup>9</sup>). <code>h[i]</code> = hash i huruf pertama.`, html: render({}), watch: [["B", B], ["M", M]] });
            for (let i = 0; i < n; i++) {
                h[i + 1] = mod(h[i] * B + val(str[i]));
                p[i + 1] = mod(p[i] * B);
                frames.push({
                    line: 1,
                    text: `<code>h[${i + 1}] = (h[${i}] · ${B} + ${val(str[i])}) mod ${M} = (${h[i]} · ${B} + ${val(str[i])}) mod ${M} = ${h[i + 1]}</code>. Seperti menambah satu digit di belakang bilangan.`,
                    html: render({ cur: i + 1 }),
                    watch: [["i", i + 1], ["h[i]", h[i + 1]], ["p[i]", p[i + 1]]],
                });
            }
            const take = ([a, b], label) => {
                const l = a - 1;
                const r = b;
                const v = mod(h[r] - h[l] * p[r - l]);
                frames.push({
                    line: 3,
                    text: `${label} = "${str.slice(l, r)}" (s[${a}..${b}]). <code>h[${r}] − h[${l}] · p[${r - l}] = ${h[r]} − ${h[l]} · ${p[r - l]} ≡ ${v} (mod ${M})</code>. Mengalikan h[${l}] dengan B<sup>${r - l}</sup> "menggeser" awalan yang tidak dipakai agar sejajar, lalu dikurangkan.`,
                    html: render({ range: [a, b], ends: [l, r], pw: r - l }),
                    watch: [["l", l], ["r", r], [`hash ${label}`, v]],
                });
                return v;
            };
            const hA = take(A, "A");
            const hB = take(Bq, "B");
            const sameLen = A[1] - A[0] === Bq[1] - Bq[0];
            const realSame = str.slice(A[0] - 1, A[1]) === str.slice(Bq[0] - 1, Bq[1]);
            let verdict;
            if (!sameLen) verdict = "Panjangnya berbeda, jadi pasti tidak sama (tidak perlu hash).";
            else if (hA === hB && realSame) verdict = `Hash sama (${hA}) dan potongannya memang sama. Setiap perbandingan hanya O(1).`;
            else if (hA === hB) verdict = `Hash sama (${hA}) padahal potongannya berbeda: <b>tabrakan</b>! Dengan M kecil ini sering terjadi; itulah sebabnya dipakai M ≈ 10<sup>9</sup> atau dua modulus sekaligus.`;
            else verdict = `Hash berbeda (${hA} ≠ ${hB}), jadi potongannya pasti berbeda.`;
            frames.push({ line: 2, text: verdict, html: render({ range: null }), watch: [["hash A", hA], ["hash B", hB]], mark: "done" });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [sIn, aIn, bIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
