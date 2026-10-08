/* Visualizer track Struktur Data: array selisih, stack monoton, Fenwick tree, segment tree */
(() => {
    "use strict";

    const V = window.Viz;

    /** Baris sel bergaya mq-* (dipakai bersama visual deque monoton). */
    const row = (label, n, fn) =>
        `<div class="mq-row"><span class="mq-lbl">${label}</span>${Array.from({ length: n }, (_, k) => fn(k + 1)).join("")}</div>`;

    // ════════════════════════════ Array selisih: banyak penambahan rentang ════════════════════════════
    V.register("diffarray", (root) => {
        const sh = V.shell(root, {
            title: "Array Selisih: Tambah Rentang dalam O(1)",
            controls: `
                <label class="viz-input">N <input class="short" data-n value="8"></label>
                <label class="viz-input">Operasi l r v <input data-ops value="2 5 3; 4 8 2; 1 3 -1" style="width:170px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Rentang operasi", "#22d3ee", "rgba(34,211,238,.16)"],
                ["d[l] += v", "#22c55e", "rgba(34,197,94,.18)"],
                ["d[r+1] −= v", "#ef4444", "rgba(239,68,68,.16)"],
                ["Sedang dijumlah", "#f59e0b", "rgba(245,158,11,.18)"],
            ],
        });
        const nIn = sh.head.querySelector("[data-n]");
        const opsIn = sh.head.querySelector("[data-ops]");
        const code = V.codePanel(
            sh.side,
            `
vector<long long> d(n + 2, 0);                //@0
for (int j = 0; j < m; j++) {                 //@1
    d[l[j]] += v[j];          // mulai        //@1
    d[r[j] + 1] -= v[j];      // berhenti     //@2
}
long long jalan = 0;                          //@3
for (int i = 1; i <= n; i++) {                //@3
    jalan += d[i];                            //@4
    a[i] = jalan;                             //@4
}`,
        );
        const opsPanel = V.dsPanel(sh.side, "Operasi", "tambah v pada l..r");
        const watch = V.watchPanel(sh.side);

        function build() {
            const n = V.clampInt(nIn.value, 3, 12, 8);
            nIn.value = n;
            let ops = opsIn.value
                .split(/[;\n]/)
                .map((t) => t.trim().split(/[\s,]+/).map(Number))
                .filter((x) => x.length === 3 && x.every(Number.isFinite))
                .map(([l, r, v]) => [Math.max(1, Math.min(n, Math.round(l))), Math.max(1, Math.min(n, Math.round(r))), Math.max(-9, Math.min(9, Math.round(v)))])
                .map(([l, r, v]) => (l <= r ? [l, r, v] : [r, l, v]))
                .slice(0, 5);
            if (!ops.length) ops = [[2, 5, 3], [4, 8, 2], [1, 3, -1]];
            opsIn.value = ops.map((o) => o.join(" ")).join("; ");
            const d = new Array(n + 2).fill(0);
            const a = new Array(n + 1).fill(null);
            const frames = [];
            const render = (st) => {
                const cell = (j, val, extra = "") => {
                    const cls = ["mq-cell", extra];
                    if (st.range && j >= st.range[0] && j <= st.range[1]) cls.push("win");
                    return `<div class="${cls.join(" ")}">${val}</div>`;
                };
                const dCls = (j) => (st.plus === j ? "front" : st.minus === j ? "gone" : st.cur === j ? "cur" : "");
                return `<div class="mq-wrap">
                    ${row("i", n + 1, (j) => `<div class="mq-cell idx">${j}</div>`)}
                    ${row("d[i]", n + 1, (j) => cell(j, d[j], dCls(j)))}
                    ${row("a[i]", n + 1, (j) => (j > n ? '<div class="mq-cell mq-na"></div>' : cell(j, a[j] === null ? "·" : a[j], `${a[j] === null ? "mq-na" : ""} ${st.cur === j ? "cur" : ""}`)))}
                    <p class="mq-note">${st.note || "d[n + 1] hanya penampung tanda berhenti dan tidak pernah dibaca."}</p>
                </div>`;
            };
            const opsList = (done, curIdx) => ops.map((o, k) => ({ label: `${o[0]}..${o[1]}`, sub: `${o[2] > 0 ? "+" : ""}${o[2]}`, cls: k === curIdx ? "hot" : k < done ? "done" : "" }));
            const push = (line, text, st = {}, fx = {}) => {
                let htmlMasked;
                if (fx.ask && st.cur) {
                    const keep = a[st.cur];
                    a[st.cur] = "?";
                    htmlMasked = render(st);
                    a[st.cur] = keep;
                }
                frames.push({ line, text, html: render(st), htmlMasked, ops: opsList(st.done ?? 0, st.op), watch: fx.watch || [["operasi", ops.length], ["N", n]], ...fx });
            };
            push(0, `Ada ${ops.length} operasi "tambah v ke semua elemen l..r". Cara langsung memakai O(panjang rentang) per operasi. Array selisih <code>d</code> mencatat <b>perubahan</b> antar elemen bertetangga, sehingga setiap operasi cukup mengubah 2 sel.`);
            ops.forEach(([l, r, v], k) => {
                d[l] += v;
                push(1, `Operasi ${k + 1}: tambah <b>${v}</b> pada ${l}..${r}. Mulai dari elemen ${l}, semua nilai naik ${v}: <code>d[${l}] += ${v}</code> → ${d[l]}.`, { range: [l, r], plus: l, op: k, done: k }, { watch: [["l", l], ["r", r], ["v", v]] });
                d[r + 1] -= v;
                push(2, `Setelah elemen ${r}, kenaikan itu harus berhenti: <code>d[${r + 1}] −= ${v}</code> → ${d[r + 1]}. Hanya dua sel yang berubah, apa pun panjang rentangnya.`, { range: [l, r], minus: r + 1, op: k, done: k + 1 }, { watch: [["l", l], ["r", r], ["v", v]] });
            });
            let run = 0;
            push(3, "Semua operasi tercatat. Sekarang bangun array akhir dengan <b>prefix sum</b> dari d: setiap elemen = elemen sebelumnya + perubahannya.", { done: ops.length }, { mark: "key" });
            for (let i = 1; i <= n; i++) {
                run += d[i];
                a[i] = run;
                push(4, `<code>a[${i}] = a[${i - 1}] + d[${i}] = ${run - d[i]} + ${d[i]} = ${run}</code>.`, { cur: i, done: ops.length }, {
                    watch: [["i", i], ["d[i]", d[i]], ["jalan", run]],
                    ask: i === Math.ceil(n / 2) ? { type: "value", answer: run, prompt: `Berapa <code>a[${i}]</code>?`, hint: `a[${i - 1}] + d[${i}]` } : undefined,
                });
            }
            const direct = new Array(n + 1).fill(0);
            ops.forEach(([l, r, v]) => {
                for (let i = l; i <= r; i++) direct[i] += v;
            });
            const ok = direct.slice(1).every((x, i) => x === a[i + 1]);
            push(-1, `Selesai: O(M + N) untuk M operasi dan N elemen, bukan O(M · N). ${ok ? "Hasilnya sama persis dengan menambahkan satu per satu." : ""}`, { done: ops.length, note: "Gabungkan dengan prefix sum dari a untuk menjawab jumlah rentang setelahnya." }, { mark: "done" });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            opsPanel.set(f.ops);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [nIn, opsIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });

    // ════════════════════════════ Stack monoton: elemen lebih besar berikutnya ════════════════════════════
    V.register("monostack", (root) => {
        const sh = V.shell(root, {
            title: "Stack Monoton: Elemen Lebih Besar Berikutnya",
            controls: `
                <label class="viz-input">Array <input data-arr value="4, 2, 1, 5, 3, 3, 6, 2" style="width:180px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Elemen baru (i)", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Puncak stack", "#22c55e", "rgba(34,197,94,.18)"],
                ["Dikeluarkan (jawaban ditemukan)", "#ef4444", "rgba(239,68,68,.16)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const code = V.codePanel(
            sh.side,
            `
stack<int> st;          // indeks yang belum dapat jawaban  //@0
for (int i = 1; i <= n; i++) {                             //@1
    while (!st.empty() && a[st.top()] < a[i]) {            //@2
        jawab[st.top()] = a[i];                            //@3
        st.pop();                                          //@3
    }
    st.push(i);                                            //@4
}
// yang tersisa di stack tidak punya jawaban: -1           //@5`,
        );
        const watch = V.watchPanel(sh.side);

        function build() {
            const parsed = V.parseList(arrIn.value, { min: 0, max: 99, limit: 12 });
            const arr = parsed.length >= 2 ? parsed : [4, 2, 1, 5, 3, 3, 6, 2];
            arrIn.value = arr.join(", ");
            const n = arr.length;
            const a = [null, ...arr];
            const ans = new Array(n + 1).fill(null);
            const st = [];
            const frames = [];
            const render = (s, ansView = ans) => {
                const cls = (j) => {
                    const c = ["mq-cell"];
                    if (st.includes(j)) c.push("inq");
                    if (st[st.length - 1] === j && s.cur !== j) c.push("front");
                    if (s.cur === j) c.push("cur");
                    if (s.gone === j) c.push("gone");
                    return c.join(" ");
                };
                const items = st.map((j, k) => `<span class="it ${k === st.length - 1 ? "front" : ""} ${j === s.pushed ? "new" : ""}"><small>j = ${j}</small><b>${a[j]}</b></span>`).join("");
                const gone = s.gone ? `<span class="it gone"><small>j = ${s.gone}</small><b>${a[s.gone]}</b></span>` : "";
                return `<div class="mq-wrap">
                    ${row("i", n, (j) => `<div class="mq-cell idx">${j}</div>`)}
                    ${row("a[i]", n, (j) => `<div class="${cls(j)}">${a[j]}</div>`)}
                    ${row("jawab", n, (j) => `<div class="mq-cell ${ansView[j] === null ? "mq-na" : ""} ${s.gone === j ? "gone" : ""}">${ansView[j] === null ? "·" : ansView[j]}</div>`)}
                    <div class="mq-dq-wrap"><span class="mq-lbl">stack</span><div class="mq-dq">${items}${gone}${!items && !gone ? '<span class="mq-empty">kosong</span>' : ""}</div></div>
                    <p class="mq-note">dasar (kiri) → puncak (kanan) · nilai di stack selalu tidak naik dari dasar ke puncak</p>
                </div>`;
            };
            const push = (line, text, s = {}, fx = {}) => {
                let htmlMasked;
                if (fx.ask && s.gone) {
                    const hidden = ans.slice();
                    hidden[s.gone] = "?";
                    htmlMasked = render(s, hidden);
                }
                frames.push({ line, text, html: render(s), htmlMasked, watch: [["i", s.cur ?? "–"], ["isi stack", st.length], ["puncak", st.length ? `${st[st.length - 1]} (a = ${a[st[st.length - 1]]})` : "–"]], ...fx });
            };
            push(0, `Untuk setiap elemen, cari elemen <b>pertama di kanannya yang lebih besar</b>. Cara naif melihat ke kanan satu per satu: O(N²). Stack menyimpan elemen yang <b>masih menunggu</b> jawabannya.`);
            for (let i = 1; i <= n; i++) {
                push(1, `Elemen baru: <code>a[${i}] = ${a[i]}</code>. Siapa saja yang sedang menunggu dan lebih kecil dari ${a[i]}? Mereka akhirnya menemukan jawaban.`, { cur: i });
                while (st.length && a[st[st.length - 1]] < a[i]) {
                    const j = st.pop();
                    ans[j] = a[i];
                    push(3, `<code>a[${j}] = ${a[j]} &lt; ${a[i]}</code>: elemen lebih besar pertama di kanan indeks ${j} adalah <b>${a[i]}</b>. Keluarkan ${j} dari stack.`, { cur: i, gone: j }, {
                        mark: "discover",
                        ask: { type: "value", answer: a[i], prompt: `Berapa <code>jawab[${j}]</code>?`, hint: "Elemen baru yang membuatnya keluar dari stack." },
                    });
                }
                if (st.length) push(2, `Puncak stack <code>a[${st[st.length - 1]}] = ${a[st[st.length - 1]]}</code> tidak lebih kecil dari ${a[i]}, berhenti. Elemen di bawahnya juga pasti tidak lebih kecil (stack tidak naik).`, { cur: i }, { mark: "skip" });
                st.push(i);
                push(4, `Masukkan ${i} ke stack: ia juga menunggu jawabannya.`, { pushed: i });
            }
            const rest = st.slice();
            rest.forEach((j) => (ans[j] = -1));
            push(5, `Selesai. Indeks ${rest.join(", ")} tidak pernah dikeluarkan: tidak ada elemen lebih besar di kanannya, jawabannya −1. Setiap indeks masuk sekali dan keluar paling banyak sekali, total <b>O(N)</b>.`, {}, { mark: "done" });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        arrIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        build();
    });
})();
