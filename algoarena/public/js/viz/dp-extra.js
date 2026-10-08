/* Visualizer DP lanjutan: deque monoton untuk optimasi transisi, dan DP peluang (jumlah dadu) */
(() => {
    "use strict";

    const V = window.Viz;

    // ════════════════════════════ Deque monoton: dp[i] = c[i] + min(dp[i-K..i-1]) ════════════════════════════
    V.register("monoqueue", (root) => {
        const sh = V.shell(root, {
            title: "DP + Deque Monoton (Lompat Batu)",
            controls: `
                <label class="viz-input">Biaya batu <input data-arr value="4, 7, 2, 9, 5, 1, 8, 3" style="width:170px"></label>
                <label class="viz-input">K <input class="short" data-k value="3"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Jendela i−K..i−1", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Sedang dihitung", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Depan deque = minimum", "#22c55e", "rgba(34,197,94,.18)"],
                ["Dibuang", "#ef4444", "rgba(239,68,68,.16)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const kIn = sh.head.querySelector("[data-k]");
        const code = V.codePanel(
            sh.side,
            `
dp[1] = c[1];                                        //@0
dq.push_back(1);                                     //@0
for (int i = 2; i <= n; i++) {                       //@1
    while (dq.front() < i - k) dq.pop_front();       //@2
    dp[i] = c[i] + dp[dq.front()];                   //@3
    while (!dq.empty() && dp[dq.back()] >= dp[i])    //@4
        dq.pop_back();                               //@4
    dq.push_back(i);                                 //@5
}
cout << dp[n];                                       //@6`,
        );
        const watch = V.watchPanel(sh.side);

        function render(c, dp, st) {
            const n = c.length - 1;
            const cells = (label, fn) =>
                `<div class="mq-row"><span class="mq-lbl">${label}</span>${Array.from({ length: n }, (_, k) => fn(k + 1)).join("")}</div>`;
            const cls = (j) => {
                const out = ["mq-cell"];
                if (st.i && j >= Math.max(1, st.i - st.k) && j < st.i) out.push("win");
                if (j === st.i) out.push("cur");
                if (st.dq.includes(j)) out.push("inq");
                if (st.dq[0] === j && st.i) out.push("front");
                if (st.gone === j) out.push("gone");
                return out.join(" ");
            };
            const dq = st.dq
                .map((j, idx) => {
                    let c2 = "it";
                    if (idx === 0) c2 += " front";
                    if (j === st.pushed) c2 += " new";
                    return `<span class="${c2}"><small>j = ${j}</small><b>${dp[j]}</b></span>`;
                })
                .join("");
            const gone = st.gone ? `<span class="it gone"><small>j = ${st.gone}</small><b>${dp[st.gone]}</b></span>` : "";
            return `<div class="mq-wrap">
                ${cells("i", (j) => `<div class="mq-cell idx">${j}</div>`)}
                ${cells("c[i]", (j) => `<div class="${cls(j)}">${c[j]}</div>`)}
                ${cells("dp[i]", (j) => `<div class="${cls(j)} ${dp[j] === null ? "mq-na" : ""}">${dp[j] === null ? "·" : dp[j]}</div>`)}
                <div class="mq-dq-wrap"><span class="mq-lbl">deque</span><div class="mq-dq">${st.side === "front" ? gone + dq : dq + (st.side === "back" ? gone : "")}${!dq && !gone ? '<span class="mq-empty">kosong</span>' : ""}</div></div>
                <p class="mq-note">depan (kiri) → belakang (kanan) · isi: indeks j dengan dp[j] naik</p>
            </div>`;
        }

        function build() {
            const a = V.parseList(arrIn.value, { min: 0, max: 99, limit: 12 });
            const arr = a.length >= 2 ? a : [4, 7, 2, 9, 5, 1, 8, 3];
            arrIn.value = arr.join(", ");
            const n = arr.length;
            const K = V.clampInt(kIn.value, 1, n, 3);
            kIn.value = K;
            const c = [null, ...arr];
            const dp = new Array(n + 1).fill(null);
            const dq = [];
            const frames = [];
            const push = (line, text, st = {}, fx = {}) => {
                let masked;
                if (fx.ask) {
                    const hidden = dp.slice();
                    hidden[st.i] = "?";
                    masked = render(c, hidden, { k: K, dq: dq.slice(), ...st });
                }
                frames.push({
                    line,
                    text,
                    html: render(c, dp.slice(), { k: K, dq: dq.slice(), ...st }),
                    htmlMasked: masked,
                    watch: [["i", st.i ?? "–"], ["K", K], ["jendela", st.i ? `${Math.max(1, st.i - K)}..${st.i - 1}` : "–"], ["isi deque", dq.length]],
                    ...fx,
                });
            };
            push(-1, `Katak mulai di batu 1 dan harus mendarat di batu ${n}, melompat 1 sampai <b>${K}</b> batu. Biayanya jumlah c pada batu tempat ia mendarat. <code>dp[i] = c[i] + min(dp[i−K..i−1])</code>. Cara langsung O(N·K); deque monoton membuatnya O(N).`);
            dp[1] = c[1];
            dq.push(1);
            push(0, `Base case: <code>dp[1] = c[1] = ${c[1]}</code>. Masukkan indeks 1 ke deque.`, { pushed: 1 });
            for (let i = 2; i <= n; i++) {
                push(1, `Hitung <b>dp[${i}]</b>. Calon sebelumnya: batu ${Math.max(1, i - K)} sampai ${i - 1} (jendela biru).`, { i });
                while (dq[0] < i - K) {
                    const j = dq.shift();
                    push(2, `Indeks ${j} sudah di luar jendela (${j} &lt; ${i} − ${K}): buang dari <b>depan</b>. Ia tidak akan pernah terjangkau lagi.`, { i, gone: j, side: "front" }, { mark: "skip" });
                }
                const best = dq[0];
                dp[i] = c[i] + dp[best];
                push(3, `Depan deque selalu minimum jendela: <code>dp[${best}] = ${dp[best]}</code>. Jadi <code>dp[${i}] = ${c[i]} + ${dp[best]} = ${dp[i]}</code>.`, { i }, {
                    ask: { type: "value", answer: dp[i], prompt: `Berapa <code>dp[${i}]</code>?`, hint: `c[${i}] + dp[depan deque]` },
                });
                while (dq.length && dp[dq[dq.length - 1]] >= dp[i]) {
                    const j = dq.pop();
                    push(4, `<code>dp[${j}] = ${dp[j]} ≥ dp[${i}] = ${dp[i]}</code> dan ${j} lebih tua: ${j} keluar jendela lebih dulu dan tidak lebih kecil, jadi tidak akan pernah menjadi minimum lagi. Buang dari <b>belakang</b>.`, { i, gone: j, side: "back" }, { mark: "skip" });
                }
                dq.push(i);
                push(5, `Masukkan ${i} ke belakang. Isi deque tetap naik: [${dq.map((j) => dp[j]).join(", ")}].`, { i, pushed: i });
            }
            push(6, `Jawaban <b>dp[${n}] = ${dp[n]}</b>. Setiap indeks masuk sekali dan keluar paling banyak sekali, jadi total <b>O(N)</b>.`, {}, { mark: "done" });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [arrIn, kIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });

    // ════════════════════════════ DP peluang: distribusi jumlah dadu ════════════════════════════
    V.register("dice", (root) => {
        const sh = V.shell(root, {
            title: "DP Peluang: Jumlah Beberapa Dadu",
            controls: V.segmented(
                "dadu",
                [
                    ["2", "2 dadu"],
                    ["3", "3 dadu"],
                    ["4", "4 dadu"],
                ],
                "2",
            ),
            legend: [
                ["Sedang dihitung", "#f59e0b", "rgba(245,158,11,.25)"],
                ["Sumber (k − 1 dadu)", "#22d3ee", "rgba(34,211,238,.25)"],
                ["Paling mungkin", "#22c55e", "rgba(34,197,94,.25)"],
            ],
        });
        const code = V.codePanel(
            sh.side,
            `
// cara[k][s] = banyak hasil k dadu yang jumlahnya s
cara[0][0] = 1;                                         //@0
for (int k = 1; k <= n; k++)                            //@1
    for (int s = k; s <= 6 * k; s++)                    //@2
        for (int d = 1; d <= 6; d++)   // dadu ke-k = d //@3
            if (s - d >= 0)                             //@3
                cara[k][s] += cara[k - 1][s - d];       //@3
// P(jumlah = s) = cara[n][s] / 6^n                     //@4`,
        );
        const watch = V.watchPanel(sh.side);
        let n = 2;

        function bars(row, maxS, opts = {}) {
            const max = Math.max(1, ...row);
            let h = "";
            for (let s = 0; s <= maxS; s++) {
                const v = row[s] || 0;
                const cls = ["pb-col"];
                if (opts.cur === s) cls.push("cur");
                if (opts.src && opts.src.includes(s)) cls.push("src");
                if (opts.best && opts.best.includes(s)) cls.push("best");
                const pct = `${((v / opts.total) * 100).toFixed(1)}%`;
                const label = opts.mask === s ? "?" : opts.pct ? (maxS <= 12 || opts.best.includes(s) ? pct : "") : v;
                h += `<div class="${cls.join(" ")}"><span class="pb-bar"><i style="height:${(v / max) * 100}%"></i></span><b>${v || opts.cur === s ? label : ""}</b><small>${s}</small></div>`;
            }
            return `<div class="pb-row">${h}</div>`;
        }

        function build() {
            const maxS = 6 * n;
            const cara = Array.from({ length: n + 1 }, () => new Array(maxS + 1).fill(0));
            cara[0][0] = 1;
            const frames = [];
            const view = (k, opts, top = true) =>
                `<div class="pb-wrap">${top && k > 1 ? `<p class="pb-title">${k - 1} dadu</p>${bars(cara[k - 1], maxS, { src: opts.src })}` : ""}<p class="pb-title">${k} dadu</p>${bars(cara[k], maxS, opts)}</div>`;
            for (let s = 1; s <= 6; s++) cara[1][s] = 1;
            frames.push({
                line: 1,
                text: "Satu dadu: setiap angka 1 sampai 6 muncul dengan <b>1 cara</b> dari 6, jadi peluangnya masing-masing 1/6. Kita hitung <b>banyak cara</b> dulu (bilangan bulat), lalu bagi dengan 6<sup>k</sup> di akhir.",
                html: view(1, {}),
                watch: [["k", 1], ["total hasil", 6]],
            });
            for (let k = 2; k <= n; k++) {
                for (let s = k; s <= 6 * k; s++) {
                    const src = [];
                    let sum = 0;
                    for (let d = 1; d <= 6; d++)
                        if (s - d >= 0 && cara[k - 1][s - d]) {
                            src.push(s - d);
                            sum += cara[k - 1][s - d];
                        }
                    cara[k][s] = sum;
                    const parts = src.map((x) => cara[k - 1][x]).join(" + ");
                    const html = view(k, { cur: s, src });
                    const htmlMasked = view(k, { cur: s, src, mask: s });
                    frames.push({
                        line: 3,
                        text: `Jumlah <b>${s}</b> dengan ${k} dadu: dadu terakhir bernilai d = 1..6, sisanya ${k - 1} dadu harus berjumlah ${s} − d. <code>cara[${k}][${s}] = ${parts} = ${sum}</code>.`,
                        html,
                        htmlMasked,
                        watch: [["k", k], ["s", s], [`cara[${k}][${s}]`, sum], ["total hasil", 6 ** k]],
                        ask: s === k + 2 || s === Math.floor(7 * k / 2) ? { type: "value", answer: sum, prompt: `Berapa <code>cara[${k}][${s}]</code>?`, hint: `Jumlahkan cara[${k - 1}][${s} − d] untuk d = 1..6 (sel biru).` } : undefined,
                    });
                }
            }
            const total = 6 ** n;
            const top = Math.max(...cara[n]);
            const best = cara[n].map((v, s) => (v === top ? s : -1)).filter((s) => s >= 0);
            frames.push({
                line: 4,
                text: `Selesai. Bagi dengan 6<sup>${n}</sup> = ${total} untuk mendapat peluang. Jumlah paling mungkin: <b>${best.join(" dan ")}</b> dengan peluang ${top}/${total} ≈ ${((top / total) * 100).toFixed(1)}%. Semakin banyak dadu, bentuknya semakin mirip lonceng.`,
                html: `<div class="pb-wrap"><p class="pb-title">${n} dadu: peluang setiap jumlah</p>${bars(cara[n], maxS, { best, pct: true, total })}</div>`,
                watch: [["k", n], ["total hasil", total], ["Σ cara", cara[n].reduce((a, b) => a + b, 0)]],
                mark: "done",
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        V.bindSegmented(sh.head, "dadu", (v) => {
            n = +v;
            build();
        });
        build();
    });
})();
