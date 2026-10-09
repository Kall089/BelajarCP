/* Visualizer track Struktur Data (lanjutan): Disjoint Set Union, algoritma Mo */
(() => {
    "use strict";

    const V = window.Viz;

    // ════════════════════════════ Disjoint Set Union ════════════════════════════
    V.register("dsu", (root) => {
        const sh = V.shell(root, {
            title: "Disjoint Set Union: Hutan Penunjuk Induk",
            controls: `
                ${V.segmented("mode", [["opt", "Ukuran + kompresi"], ["naif", "Naif"]], "opt")}
                <label class="viz-input">Operasi <input data-ops value="1-2, 3-4, 1-3, 5-6, 7-8, 5-7, 1-5, ?8, 2-8" style="width:220px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Simpul yang dicari", "#f59e0b", "rgba(245,158,11,.3)"],
                ["Jalur ke akar", "#22d3ee", "rgba(34,211,238,.2)"],
                ["Akar", "#22c55e", "rgba(34,197,94,.25)"],
                ["Penunjuk baru", "#8b5cf6"],
            ],
        });
        const opsIn = sh.head.querySelector("[data-ops]");
        let mode = "opt";
        const code = V.codePanel(
            sh.side,
            `
int find(int x) {
    int r = x;
    while (p[r] != r) r = p[r];       // naik sampai akar   //@1
    while (p[x] != r) {               // kompresi jalur     //@2
        int nx = p[x];                                      //@2
        p[x] = r;                                           //@2
        x = nx;                                             //@2
    }
    return r;                                               //@1
}
bool unite(int a, int b) {
    a = find(a); b = find(b);                               //@3
    if (a == b) return false;         // sudah sekelompok   //@4
    if (sz[a] < sz[b]) swap(a, b);    // union by size      //@5
    p[b] = a;                                               //@6
    sz[a] += sz[b];                                         //@6
    return true;
}`,
        );
        const watch = V.watchPanel(sh.side);

        function parseOps(str, n) {
            const ops = [];
            for (const t of String(str).split(/[,;]+/)) {
                const q = t.match(/^\s*\?\s*(\d+)\s*$/);
                const u = t.match(/^\s*(\d+)\s*[-–]\s*(\d+)\s*$/);
                if (q && +q[1] >= 1 && +q[1] <= n) ops.push({ type: "find", x: +q[1] });
                else if (u && +u[1] >= 1 && +u[1] <= n && +u[2] >= 1 && +u[2] <= n) ops.push({ type: "unite", a: +u[1], b: +u[2] });
            }
            return ops.slice(0, 14);
        }

        function build() {
            const N = 8;
            let ops = parseOps(opsIn.value, N);
            if (!ops.length) ops = parseOps("1-2, 3-4, 1-3, 5-6, 7-8, 5-7, 1-5, ?8, 2-8", N);
            opsIn.value = ops.map((o) => (o.type === "find" ? `?${o.x}` : `${o.a}-${o.b}`)).join(", ");
            const opt = mode === "opt";
            const p = Array.from({ length: N + 1 }, (_, i) => i);
            const sz = new Array(N + 1).fill(1);
            const frames = [];
            let comps = N;
            let steps = 0;

            const depthOf = (x) => {
                let d = 0;
                while (p[x] !== x) (x = p[x]), d++;
                return d;
            };
            const render = (st = {}) => {
                // tata letak hutan: setiap pohon diletakkan berdampingan, akar di atas
                const kids = Array.from({ length: N + 1 }, () => []);
                for (let v = 1; v <= N; v++) if (p[v] !== v) kids[p[v]].push(v);
                const pos = {};
                let leaf = 0;
                let maxD = 0;
                const place = (v, d) => {
                    maxD = Math.max(maxD, d);
                    if (!kids[v].length) {
                        pos[v] = { x: leaf++, d };
                        return;
                    }
                    kids[v].sort((a, b) => a - b).forEach((c) => place(c, d + 1));
                    const xs = kids[v].map((c) => pos[c].x);
                    pos[v] = { x: (Math.min(...xs) + Math.max(...xs)) / 2, d };
                };
                for (let v = 1; v <= N; v++)
                    if (p[v] === v) {
                        place(v, 0);
                        leaf += 0.35; // jarak antar pohon
                    }
                const W = 600;
                const span = Math.max(leaf - 0.35, 1);
                const X = (v) => 40 + (pos[v].x * (W - 80)) / Math.max(span - 1, 1);
                const Y = (v) => 32 + pos[v].d * 62;
                const H = maxD * 62 + 64;
                const path = new Set(st.path || []);
                let svg = `<svg viewBox="0 0 ${W} ${H}" class="dsu-svg"><defs><marker id="dsu-ah" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse"><path d="M0 0L10 5L0 10z" class="dsu-ah"/></marker></defs>`;
                for (let v = 1; v <= N; v++) {
                    if (p[v] === v) continue;
                    const u = p[v];
                    const dx = X(u) - X(v);
                    const dy = Y(u) - Y(v);
                    const len = Math.hypot(dx, dy) || 1;
                    const x1 = X(v) + (dx / len) * 17;
                    const y1 = Y(v) + (dy / len) * 17;
                    const x2 = X(u) - (dx / len) * 19;
                    const y2 = Y(u) - (dy / len) * 19;
                    const cls = (st.fresh || []).includes(v) ? "fresh" : path.has(v) && path.has(u) ? "on" : "";
                    svg += `<line x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}" class="dsu-edge ${cls}" marker-end="url(#dsu-ah)"/>`;
                }
                for (let v = 1; v <= N; v++) {
                    const cls = ["dsu-node"];
                    if (st.cur === v) cls.push("cur");
                    else if (st.roots && st.roots.includes(v)) cls.push("root");
                    else if (path.has(v)) cls.push("on");
                    svg += `<g class="${cls.join(" ")}" transform="translate(${X(v)} ${Y(v)})"><circle r="16"/><text>${v}</text></g>`;
                }
                svg += "</svg>";
                const cells = (label, fn) => `<div class="mq-row"><span class="mq-lbl">${label}</span>${Array.from({ length: N }, (_, k) => fn(k + 1)).join("")}</div>`;
                const cc = (v) => {
                    const c = ["mq-cell"];
                    if (st.cur === v) c.push("cur");
                    else if (st.roots && st.roots.includes(v)) c.push("front");
                    else if (path.has(v)) c.push("inq");
                    if ((st.fresh || []).includes(v)) c.push("win");
                    return c.join(" ");
                };
                return `${svg}<div class="mq-wrap compact">
                    ${cells("i", (v) => `<div class="mq-cell idx">${v}</div>`)}
                    ${cells("p[i]", (v) => `<div class="${cc(v)}">${p[v]}</div>`)}
                    ${cells("sz[i]", (v) => `<div class="mq-cell ${p[v] === v ? "" : "mq-na"}">${p[v] === v ? sz[v] : "·"}</div>`)}
                    <p class="mq-note">panah menunjuk ke induk; sz hanya bermakna di akar</p>
                </div>`;
            };
            const push = (line, text, st, extra = [], mark) =>
                frames.push({
                    line,
                    text,
                    mark,
                    html: render(st),
                    watch: [["mode", opt ? "ukuran + kompresi" : "naif"], ["komponen", comps], ["langkah naik", steps], ["kedalaman maks", Math.max(...Array.from({ length: N }, (_, k) => depthOf(k + 1)))], ...extra],
                });

            // find dengan animasi: naik ke akar lalu (opsional) kompresi
            const find = (x, label) => {
                const path = [x];
                let r = x;
                push(1, `${label}: cari akar dari <b>${x}</b> dengan mengikuti penunjuk induk.`, { cur: x, path });
                while (p[r] !== r) {
                    r = p[r];
                    steps++;
                    path.push(r);
                }
                push(1, path.length > 1 ? `Jalur ${path.join(" → ")}: akarnya <b>${r}</b> (${path.length - 1} langkah naik).` : `${x} menunjuk dirinya sendiri: ${x} adalah akar.`, { cur: x, path, roots: [r] }, [["find(" + x + ")", r]]);
                if (opt && path.length > 2) {
                    const fresh = path.slice(0, -2);
                    for (const v of fresh) p[v] = r;
                    push(2, `<b>Kompresi jalur</b>: ${fresh.join(", ")} kini langsung menunjuk akar ${r}. Pencarian berikutnya dari simpul-simpul ini hanya 1 langkah.`, { cur: x, path, roots: [r], fresh }, [["find(" + x + ")", r]], "key");
                }
                return r;
            };

            push(-1, `${N} simpul, masing-masing kelompok sendiri: <code>p[i] = i</code>, <code>sz[i] = 1</code>. Setiap kelompok diwakili akarnya.${opt ? "" : " Mode naif: tanpa ukuran dan tanpa kompresi, pohon bisa menjadi rantai panjang."}`, {});
            for (const op of ops) {
                if (op.type === "find") {
                    find(op.x, `Pertanyaan ?${op.x}`);
                    continue;
                }
                const ra = find(op.a, `unite(${op.a}, ${op.b})`);
                const rb = find(op.b, `unite(${op.a}, ${op.b})`);
                if (ra === rb) {
                    push(4, `Akar ${op.a} dan ${op.b} sama (${ra}): sudah satu kelompok, tidak ada yang digabung.`, { roots: [ra] }, [], "skip");
                    continue;
                }
                let a = ra;
                let b = rb;
                if (opt && sz[a] < sz[b]) [a, b] = [b, a];
                if (!opt) [a, b] = [rb, ra]; // naif: p[find(a)] = find(b)
                p[b] = a;
                sz[a] += sz[b];
                comps--;
                push(
                    opt ? 6 : 6,
                    opt
                        ? `Gabungkan: pohon yang lebih kecil (akar ${b}) digantung di bawah yang lebih besar (akar ${a}), sehingga tinggi pohon tetap O(log N). <code>sz[${a}] = ${sz[a]}</code>.`
                        : `Naif: <code>p[${b}] = ${a}</code> tanpa melihat ukuran. Pohon bisa makin tinggi.`,
                    { roots: [a], fresh: [b] },
                    [],
                    "take",
                );
            }
            push(-1, `Selesai: ${comps} kelompok, total <b>${steps}</b> langkah naik. ${opt ? "Dengan dua optimasi, setiap operasi praktis O(1) (tepatnya O(α(N)), α ≤ 4 untuk N apa pun yang realistis)." : "Coba operasi 1-2, 1-3, 1-4, 1-5, 1-6, 1-7, 1-8, ?1 di mode naif: terbentuk rantai, dan find menjadi O(N)."}`, {}, [], "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch);
        });
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            build();
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        opsIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        build();
    });

    // ════════════════════════════ Algoritma Mo ════════════════════════════
    V.register("mo", (root) => {
        const sh = V.shell(root, {
            title: "Algoritma Mo: Banyak Nilai Berbeda di Rentang",
            controls: `
                ${V.segmented("ord", [["mo", "Urutan Mo"], ["asli", "Urutan asli"]], "mo")}
                <label class="viz-input">Array <input data-arr value="1, 3, 1, 2, 3, 3, 4, 1, 2, 4, 4, 2" style="width:200px"></label>
                <label class="viz-input">Kueri <input data-q value="0-4, 8-11, 2-5, 6-9, 1-3, 7-11" style="width:170px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Jendela [L, R]", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Ditambahkan", "#22c55e", "rgba(34,197,94,.3)"],
                ["Dibuang", "#ef4444", "rgba(239,68,68,.2)"],
                ["Kueri aktif", "#f59e0b", "rgba(245,158,11,.3)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const qIn = sh.head.querySelector("[data-q]");
        let ord = "mo";
        const code = V.codePanel(
            sh.side,
            `
// B = ukuran blok ≈ √N; kueri {l, r, id}
sort(q.begin(), q.end(), [&](const Kueri& x, const Kueri& y) {   //@1
    if (x.l / B != y.l / B) return x.l / B < y.l / B;            //@1
    return x.r < y.r;                                            //@1
});
int L = 0, R = -1, beda = 0;                                     //@0
for (const Kueri& k : q) {                                       //@2
    while (R < k.r) tambah(++R);                                 //@3
    while (L > k.l) tambah(--L);                                 //@3
    while (R > k.r) hapus(R--);                                  //@4
    while (L < k.l) hapus(L++);                                  //@4
    jawab[k.id] = beda;                                          //@5
}
// tambah(i): if (cnt[a[i]]++ == 0) beda++;
// hapus(i):  if (--cnt[a[i]] == 0) beda--;`,
        );
        const watch = V.watchPanel(sh.side);

        function build() {
            let a = V.parseList(arrIn.value, { min: 0, max: 9, limit: 16 });
            if (a.length < 4) a = [1, 3, 1, 2, 3, 3, 4, 1, 2, 4, 4, 2];
            arrIn.value = a.join(", ");
            const n = a.length;
            let qs = String(qIn.value)
                .split(/[,;]+/)
                .map((t) => t.match(/(\d+)\s*[-–]\s*(\d+)/))
                .filter(Boolean)
                .map((m) => [Math.min(+m[1], +m[2]), Math.max(+m[1], +m[2])])
                .filter(([l, r]) => r < n)
                .slice(0, 8);
            if (!qs.length) qs = [[0, Math.min(4, n - 1)], [1, Math.min(3, n - 1)]];
            qIn.value = qs.map(([l, r]) => `${l}-${r}`).join(", ");
            const B = Math.max(1, Math.round(Math.sqrt(n)));
            const qs2 = qs.map(([l, r], id) => ({ l, r, id }));
            if (ord === "mo") qs2.sort((x, y) => (Math.floor(x.l / B) !== Math.floor(y.l / B) ? Math.floor(x.l / B) - Math.floor(y.l / B) : x.r - y.r));
            const ans = new Array(qs.length).fill(null);
            const cnt = {};
            for (const v of a) cnt[v] = 0;
            let L = 0;
            let R = -1;
            let beda = 0;
            let moves = 0;
            const frames = [];
            const naive = qs.reduce((s, [l, r]) => s + (r - l + 1), 0);

            const render = (st = {}) => {
                const cells = a
                    .map((x, i) => {
                        const c = ["mo-cell"];
                        if (i >= L && i <= R) c.push("in");
                        if (st.add === i) c.push("add");
                        if (st.del === i) c.push("del");
                        if (st.q && i >= st.q.l && i <= st.q.r) c.push("q");
                        const blk = i % B === 0 && i > 0 ? " blk" : "";
                        return `<div class="${c.join(" ")}${blk}"><small>${i}</small><b>${x}</b>${i === L ? '<i class="ptr l">L</i>' : ""}${i === R ? '<i class="ptr r">R</i>' : ""}</div>`;
                    })
                    .join("");
                const vals = [...new Set(a)].sort((x, y) => x - y);
                const cntRow = vals.map((v) => `<span class="mo-cnt ${cnt[v] ? "on" : ""}"><small>${v}</small><b>${cnt[v] || 0}</b></span>`).join("");
                const qList = qs2
                    .map((q) => `<span class="mo-q ${st.q && st.q.id === q.id ? "cur" : ""} ${ans[q.id] !== null ? "done" : ""}">#${q.id + 1} [${q.l}, ${q.r}] blok ${Math.floor(q.l / B)}${ans[q.id] !== null ? ` → <b>${ans[q.id]}</b>` : ""}</span>`)
                    .join("");
                return `<div class="mo-wrap"><div class="mo-row">${cells}</div>
                    <div class="mo-line"><span class="mq-lbl">cnt</span>${cntRow}</div>
                    <div class="mo-line"><span class="mq-lbl">kueri</span><div class="mo-qs">${qList}</div></div>
                    <p class="mq-note">garis tebal = batas blok (B = ${B}); kueri diproses dari kiri ke kanan</p></div>`;
            };
            const push = (line, text, st, mark) =>
                frames.push({ line, text, mark, html: render(st), watch: [["L", L], ["R", R], ["beda", beda], ["geseran", moves], ["B", B]] });

            push(0, `Array ${n} elemen, ${qs.length} kueri "berapa nilai berbeda di [l, r]?". Menghitung ulang setiap kueri butuh total ${naive} langkah. Mo menjawab semua kueri <b>offline</b> sambil menggeser dua penunjuk.`, {});
            push(1, ord === "mo" ? `Urutkan kueri menurut <b>blok l</b> (ukuran blok B = ${B} ≈ √${n}), lalu menurut r. Di dalam satu blok, R hanya bergerak maju, dan L hanya bergoyang di dalam blok.` : `Urutan asli: kueri diproses apa adanya. Penunjuk bisa bolak-balik jauh. Bandingkan total geserannya dengan urutan Mo.`, {});
            for (const q of qs2) {
                push(2, `Kueri #${q.id + 1}: [${q.l}, ${q.r}]. Geser jendela [${L}, ${R}] menjadi [${q.l}, ${q.r}].`, { q });
                const add = (i, line) => {
                    if (cnt[a[i]]++ === 0) beda++;
                    moves++;
                    push(line, `tambah(${i}): nilai ${a[i]}, cnt menjadi ${cnt[a[i]]}${cnt[a[i]] === 1 ? ", nilai baru: <b>beda++</b>" : ""}.`, { q, add: i });
                };
                const del = (i, line) => {
                    if (--cnt[a[i]] === 0) beda--;
                    moves++;
                    push(line, `hapus(${i}): nilai ${a[i]}, cnt menjadi ${cnt[a[i]]}${cnt[a[i]] === 0 ? ", nilai hilang: <b>beda--</b>" : ""}.`, { q, del: i });
                };
                while (R < q.r) add(++R, 3);
                while (L > q.l) add(--L, 3);
                while (R > q.r) {
                    const i = R--;
                    del(i, 4);
                }
                while (L < q.l) {
                    const i = L++;
                    del(i, 4);
                }
                ans[q.id] = beda;
                push(5, `Jendela tepat [${q.l}, ${q.r}]: jawab kueri #${q.id + 1} = <b>${beda}</b>.`, { q }, "key");
            }
            const geser = (list) => {
                let l = 0;
                let r = -1;
                let m = 0;
                for (const q of list) {
                    m += Math.abs(r - q.r) + Math.abs(l - q.l);
                    l = q.l;
                    r = q.r;
                }
                return m;
            };
            const lain = ord === "mo" ? qs.map(([l, r]) => ({ l, r })) : [...qs2].sort((x, y) => (Math.floor(x.l / B) !== Math.floor(y.l / B) ? Math.floor(x.l / B) - Math.floor(y.l / B) : x.r - y.r));
            push(-1, `Selesai dengan <b>${moves}</b> geseran penunjuk; ${ord === "mo" ? "urutan asli" : "urutan Mo"} butuh <b>${geser(lain)}</b>, menghitung ulang setiap kueri ${naive}. Untuk N dan Q sampai 10<sup>5</sup>, Mo butuh sekitar (N + Q)·√N ≈ 6·10<sup>7</sup> operasi, sedangkan menghitung ulang bisa 10<sup>10</sup>.`, {}, "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch);
        });
        V.bindSegmented(sh.head, "ord", (v) => {
            ord = v;
            build();
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [arrIn, qIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
