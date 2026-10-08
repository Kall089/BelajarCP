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

    // ════════════════════════════ Fenwick tree: query prefix dan update titik ════════════════════════════
    V.register("fenwick", (root) => {
        const N = 8;
        const sh = V.shell(root, {
            title: "Fenwick Tree (BIT)",
            controls: `
                <label class="viz-input">a[1..8] <input data-arr value="3, 1, 4, 1, 5, 9, 2, 6" style="width:150px"></label>
                <label class="viz-input">query r <input class="short" data-r value="7"></label>
                <label class="viz-input">tambah i v <input class="short" data-iv value="3 5" style="width:52px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sedang dikunjungi", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Sudah dikunjungi", "#22c55e", "rgba(34,197,94,.18)"],
                ["Elemen yang dicakup", "#22d3ee", "rgba(34,211,238,.16)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const rIn = sh.head.querySelector("[data-r]");
        const ivIn = sh.head.querySelector("[data-iv]");
        const code = V.codePanel(
            sh.side,
            `
int lowbit(int i) { return i & (-i); }      // bit 1 terendah  //@0
void tambah(int i, long long v) {                              //@1
    for (; i <= n; i += lowbit(i)) t[i] += v;                  //@2
}
long long prefix(int i) {                                      //@3
    long long s = 0;
    for (; i > 0; i -= lowbit(i)) s += t[i];                   //@4
    return s;                                                  //@5
}`,
        );
        const watch = V.watchPanel(sh.side);
        const lowbit = (i) => i & -i;
        const bin = (i) => i.toString(2).padStart(4, "0");

        function render(a, t, st) {
            const levels = [8, 4, 2, 1];
            let h = `<div class="fw-grid" style="grid-template-columns: 64px repeat(${N}, minmax(0, 1fr))">`;
            levels.forEach((lb, r) => {
                h += `<div class="fw-lbl" style="grid-row:${r + 1}">panjang ${lb}</div>`;
                for (let i = lb; i <= N; i += 2 * lb) {
                    const cls = ["fw-node"];
                    if (st.cur === i) cls.push("path");
                    else if (st.done && st.done.includes(i)) cls.push("done");
                    h += `<div class="${cls.join(" ")}" style="grid-row:${r + 1}; grid-column:${i - lb + 2} / ${i + 2}"><b>${t[i]}</b><small>t[${i}]${lb > 1 ? ` · a[${i - lb + 1}..${i}]` : ""}</small></div>`;
                }
            });
            h += `<div class="fw-lbl" style="grid-row:5">a[i]</div>`;
            for (let i = 1; i <= N; i++) {
                const cov = st.cur && i > st.cur - lowbit(st.cur) && i <= st.cur;
                h += `<div class="fw-cell ${cov ? "hl" : ""} ${st.point === i ? "pt" : ""}" style="grid-row:5; grid-column:${i + 1}">${a[i]}</div>`;
            }
            h += `<div class="fw-lbl" style="grid-row:6">i (biner)</div>`;
            for (let i = 1; i <= N; i++) h += `<div class="fw-idx" style="grid-row:6; grid-column:${i + 1}"><b>${i}</b><small>${bin(i)}</small></div>`;
            return `${h}</div>`;
        }

        function build() {
            const parsed = V.parseList(arrIn.value, { min: -99, max: 99, limit: N });
            const base = parsed.length === N ? parsed : [3, 1, 4, 1, 5, 9, 2, 6];
            arrIn.value = base.join(", ");
            const r = V.clampInt(rIn.value, 1, N, 7);
            rIn.value = r;
            const iv = ivIn.value.trim().split(/[\s,]+/).map(Number);
            const ui = V.clampInt(iv[0], 1, N, 3);
            const uv = Number.isFinite(iv[1]) ? Math.max(-99, Math.min(99, Math.round(iv[1]))) : 5;
            ivIn.value = `${ui} ${uv}`;
            const a = [0, ...base];
            const t = new Array(N + 1).fill(0);
            for (let i = 1; i <= N; i++) for (let j = i; j <= N; j += lowbit(j)) t[j] += a[i];
            const frames = [];
            const push = (line, text, st = {}, fx = {}) => frames.push({ line, text, html: render(a, t, st), watch: fx.watch || [["n", N]], ...fx });
            push(0, `Setiap <code>t[i]</code> menyimpan jumlah <b>lowbit(i)</b> elemen yang berakhir di i, dengan lowbit(i) = nilai bit 1 terendah dari i. Contoh: 6 = 0110, bit terendahnya 2, jadi <code>t[6] = a[5] + a[6]</code>. Indeks ganjil hanya menyimpan dirinya sendiri.`);
            const query = (rr, label) => {
                let i = rr;
                let sum = 0;
                const done = [];
                push(3, `${label}: hitung <code>prefix(${rr})</code> = a[1] + … + a[${rr}]. Mulai dari i = ${rr}.`, { done }, { watch: [["i", i], ["biner", bin(i)], ["s", sum]] });
                while (i > 0) {
                    sum += t[i];
                    push(4, `Tambahkan <code>t[${i}] = ${t[i]}</code> (mencakup a[${i - lowbit(i) + 1}..${i}]), s = ${sum}. Lalu <code>i −= lowbit(${i}) = ${lowbit(i)}</code>: bit 1 terendah dihapus, ${bin(i)} → ${bin(i - lowbit(i))}.`, { cur: i, done: done.slice() }, {
                        watch: [["i", i], ["biner", bin(i)], ["lowbit(i)", lowbit(i)], ["s", sum]],
                    });
                    done.push(i);
                    i -= lowbit(i);
                }
                const direct = a.slice(1, rr + 1).reduce((x, y) => x + y, 0);
                push(5, `i = 0, selesai: <code>prefix(${rr}) = ${sum}</code> hanya dengan ${done.length} langkah (paling banyak log₂ N). Cek langsung: ${direct}.`, { done }, { mark: "key", watch: [["s", sum], ["langkah", done.length]] });
            };
            query(r, "Pertanyaan");
            let i = ui;
            const done = [];
            push(1, `Sekarang <code>tambah(${ui}, ${uv})</code>: a[${ui}] bertambah ${uv}. Semua t[j] yang rentangnya memuat ${ui} harus ikut diperbarui.`, { point: ui }, { watch: [["i", i], ["v", uv]] });
            a[ui] += uv;
            while (i <= N) {
                t[i] += uv;
                push(2, `<code>t[${i}] += ${uv}</code> → ${t[i]} (rentang a[${i - lowbit(i) + 1}..${i}] memuat ${ui}). Lalu <code>i += lowbit(${i}) = ${lowbit(i)}</code>: ${bin(i)} → ${bin(i + lowbit(i))}.`, { cur: i, done: done.slice(), point: ui }, { watch: [["i", i], ["biner", bin(i)], ["lowbit(i)", lowbit(i)]] });
                done.push(i);
                i += lowbit(i);
            }
            push(2, `i = ${i} &gt; ${N}, selesai. Hanya ${done.length} simpul yang berubah: ${done.join(", ")}.`, { done, point: ui }, { mark: "key" });
            query(r, "Tanya lagi");
            push(-1, "Update dan query sama-sama O(log N). Jumlah rentang l..r = prefix(r) − prefix(l − 1).", {}, { mark: "done" });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [arrIn, rIn, ivIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
