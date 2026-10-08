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

    // ════════════════════════════ Trie ════════════════════════════
    V.register("trie", (root) => {
        const sh = V.shell(root, {
            title: "Trie: Pohon Awalan",
            controls: `
                <label class="viz-input">Kata <input data-w value="bola, bolu, bot, buku, bus" style="width:170px"></label>
                <label class="viz-input">Awalan <input class="short" data-p value="bo" style="width:52px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Simpul saat ini", "#f59e0b", "rgba(245,158,11,.3)"],
                ["Jalur kata/awalan", "#22d3ee", "rgba(34,211,238,.2)"],
                ["Simpul baru", "#22c55e", "rgba(34,197,94,.25)"],
                ["Akhir kata (cincin tebal)", "#8b5cf6"],
            ],
        });
        const wIn = sh.head.querySelector("[data-w]");
        const pIn = sh.head.querySelector("[data-p]");
        const code = V.codePanel(
            sh.side,
            `
void sisip(const string& w) {                         //@1
    int v = 0;                       // akar          //@1
    for (char c : w) {                                //@2
        int x = c - 'a';
        if (!anak[v][x]) anak[v][x] = jumlahSimpul++; //@3
        v = anak[v][x];                               //@2
        cnt[v]++;      // satu kata lagi lewat sini   //@4
    }
    akhir[v]++;                                       //@5
}
int hitungAwalan(const string& p) {                   //@6
    int v = 0;
    for (char c : p) {
        v = anak[v][c - 'a'];                         //@6
        if (v == 0) return 0;     // jalan buntu      //@7
    }
    return cnt[v];                                    //@8
}`,
        );
        const watch = V.watchPanel(sh.side);

        function build() {
            let words = wIn.value
                .split(/[\s,;]+/)
                .map((w) => w.replace(/[^a-z]/gi, "").toLowerCase().slice(0, 6))
                .filter(Boolean)
                .slice(0, 6);
            if (!words.length) words = ["bola", "bolu", "bot", "buku", "bus"];
            wIn.value = words.join(", ");
            const pref = (pIn.value.replace(/[^a-z]/gi, "").toLowerCase().slice(0, 6)) || "bo";
            pIn.value = pref;
            // bangun struktur akhir untuk tata letak tetap
            const nodes = [{ id: 0, ch: "", parent: -1, depth: 0, kids: {} }];
            for (const w of words) {
                let v = 0;
                for (const c of w) {
                    if (nodes[v].kids[c] === undefined) {
                        nodes[v].kids[c] = nodes.length;
                        nodes.push({ id: nodes.length, ch: c, parent: v, depth: nodes[v].depth + 1, kids: {} });
                    }
                    v = nodes[v].kids[c];
                }
            }
            let leaf = 0;
            const place = (v) => {
                const ks = Object.keys(nodes[v].kids).sort();
                if (!ks.length) {
                    nodes[v].x = leaf++;
                    return;
                }
                ks.forEach((c) => place(nodes[v].kids[c]));
                const xs = ks.map((c) => nodes[nodes[v].kids[c]].x);
                nodes[v].x = (Math.min(...xs) + Math.max(...xs)) / 2;
            };
            place(0);
            const maxDepth = Math.max(...nodes.map((n) => n.depth));
            const W = 560;
            const gapX = leaf > 1 ? (W - 80) / (leaf - 1) : 0;
            const H = maxDepth * 62 + 70;
            const px = (n) => (leaf > 1 ? 40 + n.x * gapX : W / 2);
            const py = (n) => 34 + n.depth * 62;

            const exists = new Set([0]);
            const cnt = new Array(nodes.length).fill(0);
            const end = new Array(nodes.length).fill(0);
            const frames = [];
            const render = (st) => {
                let svg = `<svg viewBox="0 0 ${W} ${H}" class="tr-svg">`;
                for (const n of nodes) {
                    if (n.parent < 0 || !exists.has(n.id)) continue;
                    const p = nodes[n.parent];
                    const onPath = st.path && st.path.includes(n.id);
                    svg += `<line x1="${px(p)}" y1="${py(p)}" x2="${px(n)}" y2="${py(n)}" class="tr-edge ${onPath ? "on" : ""}"/>`;
                    svg += `<text x="${(px(p) + px(n)) / 2 + (px(n) >= px(p) ? 9 : -9)}" y="${(py(p) + py(n)) / 2}" class="tr-ch ${onPath ? "on" : ""}">${n.ch}</text>`;
                }
                for (const n of nodes) {
                    if (!exists.has(n.id)) continue;
                    const cls = ["tr-node"];
                    if (st.cur === n.id) cls.push("cur");
                    else if (st.fresh === n.id) cls.push("fresh");
                    else if (st.path && st.path.includes(n.id)) cls.push("on");
                    if (end[n.id]) cls.push("end");
                    svg += `<g class="${cls.join(" ")}" transform="translate(${px(n)} ${py(n)})"><circle r="17"/><text>${n.id === 0 ? "akar" : cnt[n.id]}</text></g>`;
                }
                return `${svg}</svg><p class="mq-note">angka di simpul = cnt (banyak kata yang melewati simpul itu); huruf ada di sisi</p>`;
            };
            const push = (line, text, st, w) => frames.push({ line, text, html: render(st), watch: w });
            push(1, `Trie menyimpan banyak kata dengan <b>berbagi awalan</b>. Setiap sisi adalah satu huruf, setiap jalur dari akar adalah sebuah awalan. Sisipkan ${words.length} kata satu per satu.`, {}, [["kata", words.length], ["simpul", 1]]);
            for (const w of words) {
                let v = 0;
                const path = [0];
                push(1, `Sisipkan "<b>${w}</b>": mulai dari akar.`, { cur: 0, path }, [["kata", w], ["simpul", exists.size]]);
                for (let i = 0; i < w.length; i++) {
                    const c = w[i];
                    const nx = nodes[v].kids[c];
                    const isNew = !exists.has(nx);
                    exists.add(nx);
                    v = nx;
                    path.push(v);
                    cnt[v]++;
                    push(isNew ? 3 : 4, isNew ? `Belum ada sisi '${c}' dari simpul ini: <b>buat simpul baru</b>, lalu cnt = ${cnt[v]}.` : `Sisi '${c}' sudah ada (awalan "${w.slice(0, i + 1)}" dipakai bersama): ikuti, cnt naik menjadi ${cnt[v]}.`, { cur: v, path: path.slice(), fresh: isNew ? v : undefined }, [["kata", w], ["awalan", w.slice(0, i + 1)], ["cnt", cnt[v]], ["simpul", exists.size]]);
                }
                end[v]++;
                push(5, `Kata "${w}" berakhir di sini: tandai akhir kata (cincin tebal).`, { cur: v, path: path.slice() }, [["kata", w], ["simpul", exists.size]]);
            }
            let v = 0;
            const path = [0];
            let ok = true;
            push(6, `Berapa kata yang diawali "<b>${pref}</b>"? Telusuri hurufnya dari akar.`, { cur: 0, path }, [["awalan", pref]]);
            for (const c of pref) {
                const nx = nodes[v].kids[c];
                if (nx === undefined) {
                    push(7, `Tidak ada sisi '${c}': tidak ada kata yang diawali "${pref}". Jawaban <b>0</b>.`, { cur: v, path: path.slice() }, [["awalan", pref], ["jawaban", 0]]);
                    ok = false;
                    break;
                }
                v = nx;
                path.push(v);
                push(6, `Ikuti sisi '${c}'.`, { cur: v, path: path.slice() }, [["awalan", pref], ["cnt", cnt[v]]]);
            }
            if (ok) {
                const list = words.filter((w) => w.startsWith(pref));
                frames.push({
                    line: 8,
                    text: `Sampai di ujung awalan: <code>cnt = ${cnt[v]}</code> kata (${list.join(", ")}). Waktu hanya O(panjang awalan), tidak bergantung pada banyaknya kata di kamus.`,
                    html: render({ cur: v, path: path.slice() }),
                    watch: [["awalan", pref], ["jawaban", cnt[v]]],
                    mark: "done",
                });
            }
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [wIn, pIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
