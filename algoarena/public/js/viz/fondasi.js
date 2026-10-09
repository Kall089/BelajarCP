/* Visualizer track Fondasi: backtracking N-Queens dan sliding window (two pointers) */
(() => {
    "use strict";

    const V = window.Viz;

    // ════════════════════════════ Backtracking: N-Queens ════════════════════════════
    V.register("nqueen", (root) => {
        const sh = V.shell(root, {
            title: "Backtracking: Menaruh N Ratu",
            controls: `<label class="viz-input">N = <select data-n>${[4, 5, 6].map((k) => `<option ${k === 4 ? "selected" : ""}>${k}</option>`).join("")}</select></label>`,
            legend: [
                ["Petak yang sedang dicoba", "#f59e0b", "#f59e0b"],
                ["Diserang ratu lain", "#ef4444", "rgba(239,68,68,.16)"],
                ["Ratu terpasang", "#8b5cf6", "rgba(139,92,246,.3)"],
                ["Solusi lengkap", "#22c55e", "rgba(34,197,94,.25)"],
            ],
            practice: false,
        });
        const nSel = sh.head.querySelector("[data-n]");
        const code = V.codePanel(
            sh.side,
            `
// kol[c], d1[r+c], d2[r-c+n] = sudah ada ratu?
void solve(int r) {                              //@0
    if (r == n) {                                //@1
        jawaban++;   // semua baris terisi       //@1
        return;                                  //@1
    }
    for (int c = 0; c < n; c++) {                //@2
        if (kol[c] || d1[r + c] || d2[r - c + n])//@3
            continue;    // diserang, lewati     //@3
        kol[c] = d1[r + c] = d2[r - c + n] = 1;  //@4
        solve(r + 1);                            //@5
        kol[c] = d1[r + c] = d2[r - c + n] = 0;  //@6
    }
}`,
        );
        const watch = V.watchPanel(sh.side, "Keadaan", "kedalaman rekursi = baris");
        const sols = V.htmlPanel(sh.side, "Solusi ditemukan");

        function boardHtml(n, queens, cur, status) {
            const attacked = (r, c) => queens.some(([qr, qc]) => qr !== r && (qc === c || qr + qc === r + c || qr - qc === r - c));
            let h = `<div class="nq-board" style="--n:${n}">`;
            for (let r = 0; r < n; r++) {
                for (let c = 0; c < n; c++) {
                    const q = queens.some(([qr, qc]) => qr === r && qc === c);
                    let cls = (r + c) % 2 ? "dark" : "";
                    if (cur && cur[0] === r && cur[1] === c) cls += status === "bad" ? " try bad" : " try";
                    else if (!q && r >= queens.length && attacked(r, c) && r === (cur ? cur[0] : -1)) cls += " hit";
                    if (q) cls += status === "sol" ? " queen sol" : " queen";
                    h += `<div class="nq-cell ${cls}">${q ? "♛" : cur && cur[0] === r && cur[1] === c ? "?" : ""}</div>`;
                }
            }
            return h + "</div>";
        }

        function build() {
            const n = +nSel.value;
            const frames = [];
            const queens = [];
            const found = [];
            let calls = 0;
            const col = new Array(n).fill(false);
            const d1 = new Array(2 * n).fill(false);
            const d2 = new Array(2 * n + 1).fill(false);
            const push = (line, text, cur, status, mark) =>
                frames.push({
                    line,
                    text,
                    mark,
                    board: boardHtml(n, queens, cur, status),
                    watch: [["baris r", cur ? cur[0] : queens.length], ["ratu terpasang", queens.length], ["pemanggilan solve", calls]],
                    sols: found.length ? found.map((s) => `<code>${s}</code>`).join(" ") : '<span class="muted">belum ada</span>',
                });
            push(-1, `Taruh <b>${n}</b> ratu di papan ${n}×${n} sehingga tidak ada dua ratu saling menyerang (sebaris, sekolom, atau sediagonal). Setiap baris pasti berisi tepat satu ratu, jadi kita isi baris demi baris.`);
            const limit = n <= 4 ? 400 : 260;
            const solve = (r) => {
                calls++;
                if (frames.length > limit) return;
                if (r === n) {
                    found.push(queens.map(([, c]) => c + 1).join(""));
                    push(1, `Semua ${n} baris terisi: <b>solusi ke-${found.length}</b> ditemukan! Kembali (backtrack) untuk mencari solusi lain.`, null, "sol", "done");
                    return;
                }
                push(0, `<code>solve(${r})</code>: cari kolom untuk ratu di baris ${r}.`, null);
                for (let c = 0; c < n; c++) {
                    if (frames.length > limit) return;
                    if (col[c] || d1[r + c] || d2[r - c + n]) {
                        push(3, `Petak (${r}, ${c}) diserang ratu lain: <b>lewati</b>. Inilah pemangkasan, cabang ini tidak dijelajahi sama sekali.`, [r, c], "bad", "skip");
                        continue;
                    }
                    col[c] = d1[r + c] = d2[r - c + n] = true;
                    queens.push([r, c]);
                    push(4, `Petak (${r}, ${c}) aman: <b>taruh ratu</b>, tandai kolom dan kedua diagonalnya.`, null, "", "key");
                    solve(r + 1);
                    if (frames.length > limit) return;
                    queens.pop();
                    col[c] = d1[r + c] = d2[r - c + n] = false;
                    push(6, `Kembali ke baris ${r}: <b>angkat</b> ratu dari (${r}, ${c}) dan coba kolom berikutnya.`, [r, c], "", "discover");
                }
            };
            solve(0);
            push(
                -1,
                frames.length > limit
                    ? `Animasi dihentikan di sini agar tidak terlalu panjang. Solusi yang sudah ditemukan: ${found.length}.`
                    : `Selesai: <b>${found.length}</b> solusi dengan ${calls} pemanggilan <code>solve</code>. Tanpa pemangkasan, kita harus mencoba ${n}<sup>${n}</sup> = ${n ** n} susunan.`,
                null,
                "",
                "done",
            );
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.board;
            code.set(f.line);
            watch.set(f.watch);
            sols.set(f.sols);
        });
        nSel.onchange = build;
        build();
    });

    // ════════════════════════════ Two pointers: sliding window ════════════════════════════
    V.register("window", (root) => {
        const sh = V.shell(root, {
            title: "Two Pointers: Jendela Geser Terpanjang",
            controls: `
                <label class="viz-input">Array <input data-arr value="2, 1, 3, 2, 4, 1, 1, 3" style="width:170px"></label>
                <label class="viz-input">K <input class="short" data-k value="7"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Jendela [l, r]", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Elemen baru (r)", "#f59e0b", "#f59e0b"],
                ["Elemen dibuang (l)", "#ef4444", "rgba(239,68,68,.16)"],
                ["Jendela terbaik", "#22c55e", "rgba(34,197,94,.2)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const kIn = sh.head.querySelector("[data-k]");
        const code = V.codePanel(
            sh.side,
            `
// subarray terpanjang, jumlah <= K, a[i] >= 0
int l = 0, best = 0;                         //@0
long long sum = 0;                           //@0
for (int r = 0; r < n; r++) {                //@1
    sum += a[r];          // perlebar kanan  //@2
    while (sum > K) {                        //@3
        sum -= a[l];      // persempit kiri  //@4
        l++;                                 //@4
    }
    best = max(best, r - l + 1);             //@5
}`,
        );
        const watch = V.watchPanel(sh.side);

        function arrHtml(a, l, r, cls = {}, best) {
            return `<div class="win-row">${a
                .map((x, i) => {
                    let c = "";
                    if (best && i >= best[0] && i <= best[1]) c = "best";
                    if (l !== null && i >= l && i <= r) c = "in";
                    if (cls[i]) c += " " + cls[i];
                    const marks = `${i === l ? '<i class="ptr l">l</i>' : ""}${i === r ? '<i class="ptr r">r</i>' : ""}`;
                    return `<div class="win-cell ${c}"><small>${i}</small><b>${x}</b>${marks}</div>`;
                })
                .join("")}</div>`;
        }

        function build() {
            const a = V.parseList(arrIn.value, { min: 0, max: 99, limit: 12 });
            const arr = a.length ? a : [2, 1, 3, 2, 4, 1, 1, 3];
            arrIn.value = arr.join(", ");
            const K = V.clampInt(kIn.value, 0, 999, 7);
            kIn.value = K;
            const frames = [];
            let l = 0;
            let sum = 0;
            let best = 0;
            let bestRange = null;
            const push = (line, text, r, cls, mark) =>
                frames.push({
                    line,
                    text,
                    mark,
                    html: arrHtml(arr, r === null ? null : l, r, cls, bestRange),
                    watch: [["l", l], ["r", r === null ? "–" : r], ["sum", sum], ["K", K], ["best", best]],
                });
            push(0, `Cari subarray (bagian berurutan) <b>terpanjang</b> yang jumlahnya ≤ <b>${K}</b>. Dua penunjuk l dan r hanya pernah bergerak maju.`, null);
            for (let r = 0; r < arr.length; r++) {
                sum += arr[r];
                push(2, `Geser r ke ${r}: tambah a[${r}] = ${arr[r]}, sum = ${sum}.`, r, { [r]: "new" });
                while (sum > K) {
                    push(3, `sum = ${sum} &gt; ${K}: jendela terlalu berat.`, r, {}, "skip");
                    sum -= arr[l];
                    const old = l;
                    l++;
                    push(4, `Buang a[${old}] = ${arr[old]} dari kiri, l = ${l}, sum = ${sum}.`, r, { [old]: "out" });
                }
                if (r - l + 1 > best) {
                    best = r - l + 1;
                    bestRange = [l, r];
                    push(5, `Jendela [${l}, ${r}] valid dengan panjang <b>${best}</b>: rekor baru!`, r, {}, "key");
                } else push(5, `Jendela [${l}, ${r}] valid, panjang ${r - l + 1} (rekor tetap ${best}).`, r);
            }
            push(-1, `Selesai: panjang terbaik <b>${best}</b>. Setiap elemen masuk sekali (r) dan keluar paling banyak sekali (l), jadi total <b>O(N)</b> walaupun ada loop di dalam loop.`, null, {}, "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [arrIn, kIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });

    // ════════════════════════════ Greedy: jadwal rapat ════════════════════════════
    V.register("jadwal", (root) => {
        const KEYS = {
            selesai: { label: "selesai paling awal", key: (r) => r.e, tag: (r) => `selesai ${r.e}` },
            mulai: { label: "mulai paling awal", key: (r) => r.s, tag: (r) => `mulai ${r.s}` },
            pendek: { label: "paling pendek", key: (r) => r.e - r.s, tag: (r) => `panjang ${r.e - r.s}` },
        };
        const sh = V.shell(root, {
            title: "Greedy: Jadwal Rapat Terbanyak",
            controls: `
                ${V.segmented("key", [["selesai", "Selesai awal"], ["mulai", "Mulai awal"], ["pendek", "Terpendek"]], "selesai")}
                <label class="viz-input">Rapat <input data-iv value="0-11, 1-5, 4-7, 6-10, 10-13, 11-14, 12-16, 14-17" style="width:230px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Sedang dipertimbangkan", "#f59e0b", "rgba(245,158,11,.3)"],
                ["Diambil", "#22c55e", "rgba(34,197,94,.3)"],
                ["Dilewati (bentrok)", "#ef4444", "rgba(239,68,68,.18)"],
                ["Penyebab bentrok", "#8b5cf6"],
            ],
        });
        const ivIn = sh.head.querySelector("[data-iv]");
        let mode = "selesai";
        const code = V.codePanel(
            sh.side,
            `
// rapat[i] = {mulai, selesai}
sort(rapat.begin(), rapat.end(), [&](auto& a, auto& b) {   //@1
    return kunci(a) < kunci(b);                            //@1
});
vector<pair<int, int>> dipilih;                            //@0
for (auto [s, e] : rapat) {                                //@2
    bool bentrok = false;                                  //@3
    for (auto [s2, e2] : dipilih)                          //@3
        if (s < e2 && s2 < e) bentrok = true;              //@3
    if (!bentrok) dipilih.push_back({s, e});   // ambil    //@4,5
}
cout << dipilih.size() << '\\n';                            //@6`,
        );
        const watch = V.watchPanel(sh.side);

        // Jawaban optimal untuk pembanding: greedy selesai paling awal.
        function optimal(rs) {
            let bebas = -Infinity;
            let n = 0;
            for (const r of [...rs].sort((a, b) => a.e - b.e || a.s - b.s)) if (r.s >= bebas) (n++, (bebas = r.e));
            return n;
        }

        function build() {
            let rs = String(ivIn.value)
                .split(/[,;]+/)
                .map((t) => t.match(/(\d+)\s*[-–:]\s*(\d+)/))
                .filter(Boolean)
                .map((m) => ({ s: Math.min(+m[1], 30), e: Math.min(+m[2], 30) }))
                .filter((r) => r.s < r.e)
                .slice(0, 10);
            if (rs.length < 2) rs = [[0, 11], [1, 5], [4, 7], [6, 10], [10, 13], [11, 14], [12, 16], [14, 17]].map(([s, e]) => ({ s, e }));
            rs.forEach((r, i) => (r.id = String.fromCharCode(65 + i)));
            ivIn.value = rs.map((r) => `${r.s}-${r.e}`).join(", ");
            const K = KEYS[mode];
            const best = optimal(rs);
            const T = Math.max(...rs.map((r) => r.e));
            const W = 600;
            const L = 74;
            const RH = 30;
            const x = (t) => L + ((W - L - 92) * t) / T;   // sisakan ruang kanan untuk label

            const render = (order, st) => {
                const rows = order.length;
                const top = 46;
                const H = top + rows * RH + 34;
                let svg = `<svg viewBox="0 0 ${W} ${H}" class="iv-svg">`;
                const step = T > 20 ? 4 : 2;
                for (let t = 0; t <= T; t += step) {
                    svg += `<line x1="${x(t)}" y1="${top - 8}" x2="${x(t)}" y2="${top + rows * RH}" class="iv-grid"/>`;
                    svg += `<text x="${x(t)}" y="${top + rows * RH + 16}" class="iv-tick">${t}</text>`;
                }
                // lajur ruangan: rapat yang sudah diambil
                svg += `<text x="8" y="22" class="iv-lane">ruangan</text>`;
                svg += `<rect x="${x(0)}" y="10" width="${x(T) - x(0)}" height="22" rx="5" class="iv-room"/>`;
                for (const r of st.taken) svg += `<rect x="${x(r.s) + 1}" y="12" width="${x(r.e) - x(r.s) - 2}" height="18" rx="4" class="iv-bar take"/><text x="${(x(r.s) + x(r.e)) / 2}" y="22" class="iv-id on">${r.id}</text>`;
                if (st.bebas !== undefined && st.bebas > -Infinity && mode !== "pendek")
                    svg += `<line x1="${x(st.bebas)}" y1="6" x2="${x(st.bebas)}" y2="${top + rows * RH}" class="iv-free"/><text x="${x(st.bebas) + 4}" y="${top - 12}" class="iv-free-t">bebas ${st.bebas}</text>`;
                order.forEach((r, i) => {
                    const y = top + i * RH;
                    const cls = st.cls[r.id] || "";
                    svg += `<text x="8" y="${y + RH / 2}" class="iv-lbl ${cls}">${r.id} [${r.s}, ${r.e})</text>`;
                    svg += `<rect x="${x(r.s)}" y="${y + 6}" width="${x(r.e) - x(r.s)}" height="${RH - 12}" rx="5" class="iv-bar ${cls}"/>`;
                    if (st.tags) svg += `<text x="${x(r.e) + 6}" y="${y + RH / 2}" class="iv-tag">${K.tag(r)}</text>`;
                });
                return `${svg}</svg>`;
            };

            const frames = [];
            const taken = [];
            let bebas = -Infinity;
            const cls = {};
            const w = (extra = []) => [["strategi", K.label], ["diambil", taken.length], ...extra];
            frames.push({
                line: 0,
                text: `Satu ruangan, ${rs.length} permintaan rapat. Pilih sebanyak mungkin rapat yang tidak saling bertabrakan. Strategi: ambil rapat dengan <b>${K.label}</b> lebih dulu, asalkan tidak bentrok.`,
                html: render(rs, { taken: [], cls: {} }),
                watch: w(),
            });
            const order = [...rs].sort((a, b) => K.key(a) - K.key(b) || a.e - b.e || a.s - b.s);
            frames.push({
                line: 1,
                text: `Urutkan menurut <b>${K.label}</b>. Setelah itu setiap rapat cukup dipertimbangkan sekali, dari atas ke bawah.`,
                html: render(order, { taken: [], cls: {}, tags: true }),
                watch: w(),
            });
            for (const r of order) {
                const clash = taken.filter((t) => r.s < t.e && t.s < r.e);
                frames.push({
                    line: 2,
                    text: `Pertimbangkan rapat <b>${r.id} [${r.s}, ${r.e})</b>.`,
                    html: render(order, { taken: [...taken], cls: { ...cls, [r.id]: "cur" }, bebas, tags: true }),
                    watch: w([["rapat", `${r.id} [${r.s}, ${r.e})`]]),
                });
                const ok = clash.length === 0;
                const blame = {};
                clash.forEach((t) => (blame[t.id] = "take blame"));
                const masked = render(order, { taken: [...taken], cls: { ...cls, [r.id]: "cur" }, bebas, tags: true });
                if (ok) {
                    taken.push(r);
                    cls[r.id] = "take";
                    if (mode !== "pendek") bebas = Math.max(bebas, r.e);
                } else cls[r.id] = "skip";
                const why = ok
                    ? mode === "selesai"
                        ? `Mulai ${r.s} ≥ ${taken.length > 1 ? `selesainya rapat terakhir (${taken[taken.length - 2].e})` : "awal hari"}: tidak bentrok, <b>ambil</b>. Ruangan sekarang bebas mulai menit ${r.e}, sedini mungkin.`
                        : `Tidak bentrok dengan rapat yang sudah diambil: <b>ambil</b>.`
                    : `Bentrok dengan ${clash.map((t) => `${t.id} [${t.s}, ${t.e})`).join(" dan ")}: <b>lewati</b>.`;
                frames.push({
                    line: ok ? 4 : 5,
                    text: why,
                    mark: ok ? "take" : "skip",
                    html: render(order, { taken: [...taken], cls: { ...cls, ...blame }, bebas, tags: true }),
                    htmlMasked: masked,
                    watch: [["strategi", K.label], ["diambil", taken.length, true], ["rapat", `${r.id} [${r.s}, ${r.e})`], ["keputusan", ok ? "ambil" : "lewati", true]],
                    ask: {
                        type: "choice",
                        options: ["Ambil", "Lewati"],
                        answer: ok ? 0 : 1,
                        prompt: `Rapat <b>${r.id} [${r.s}, ${r.e})</b>: diambil atau dilewati?`,
                        hint: "Bandingkan dengan rapat hijau di lajur ruangan. [a, b) dan [c, d) bentrok jika a < d dan c < b.",
                        context: `Rapat ${r.id} sedang dipertimbangkan.`,
                    },
                });
            }
            const verdict =
                taken.length === best
                    ? mode === "selesai"
                        ? `Selesai: <b>${taken.length}</b> rapat, dan ini memang paling banyak. Memilih yang selesai paling awal menyisakan waktu sebanyak mungkin untuk rapat berikutnya.`
                        : `Selesai: <b>${taken.length}</b> rapat, kebetulan optimal untuk data ini. Coba data lain; strategi ini tidak selalu benar.`
                    : `Selesai: hanya <b>${taken.length}</b> rapat, padahal bisa <b>${best}</b>. Strategi "${K.label}" <b>salah</b>: ${mode === "mulai" ? "rapat yang mulai paling awal bisa sangat panjang dan menutup banyak rapat lain" : "rapat pendek bisa berada di tengah dan memotong dua rapat sekaligus"}.`;
            frames.push({
                line: 6,
                text: verdict,
                mark: "done",
                html: render(order, { taken: [...taken], cls: { ...cls }, bebas, tags: true }),
                watch: w([["optimal", best]]),
            });
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.masked && f.htmlMasked ? f.htmlMasked : f.html;
            code.set(f.line);
            watch.set(f.watch, f.masked);
        });
        V.bindSegmented(sh.head, "key", (v) => {
            mode = v;
            build();
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        ivIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        build();
    });

    // ════════════════════════════ Kompresi koordinat + lower_bound ════════════════════════════
    V.register("kompresi", (root) => {
        const sh = V.shell(root, {
            title: "Kompresi Koordinat dengan sort, unique, lower_bound",
            controls: `<label class="viz-input">Array <input data-arr value="1000, 7, 1000000000, 7, 42, 1000" style="width:230px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Rentang pencarian [lo, hi)", "#22d3ee", "rgba(34,211,238,.16)"],
                ["mid", "#f59e0b", "rgba(245,158,11,.3)"],
                ["Elemen yang sedang dikompres", "#8b5cf6", "rgba(139,92,246,.2)"],
                ["Hasil", "#22c55e", "rgba(34,197,94,.25)"],
            ],
        });
        const arrIn = sh.head.querySelector("[data-arr]");
        const code = V.codePanel(
            sh.side,
            `
vector<int> v = a;                                       //@0
sort(v.begin(), v.end());                                //@1
v.erase(unique(v.begin(), v.end()), v.end());            //@2
for (int i = 0; i < n; i++)                              //@3
    b[i] = lower_bound(v.begin(), v.end(), a[i]) - v.begin();  //@3
// di dalam lower_bound: posisi pertama dengan v[pos] >= x
int lo = 0, hi = v.size();                               //@4
while (lo < hi) {                                        //@5
    int mid = (lo + hi) / 2;                             //@5
    if (v[mid] < x) lo = mid + 1; else hi = mid;         //@6
}
return lo;                                               //@7`,
        );
        const watch = V.watchPanel(sh.side);

        function build() {
            let a = String(arrIn.value)
                .split(/[\s,;]+/)
                .map(Number)
                .filter((x) => Number.isInteger(x) && x >= 0 && x <= 1e9)
                .slice(0, 8);
            if (a.length < 2) a = [1000, 7, 1000000000, 7, 42, 1000];
            arrIn.value = a.join(", ");
            const frames = [];
            let v = [];
            let stage = "salin";
            const b = new Array(a.length).fill(null);
            const row = (label, arr, cls = () => "", extra = () => "") =>
                `<div class="mq-row"><span class="mq-lbl">${label}</span>${arr.map((x, i) => `<div class="mq-cell kc ${cls(i)}">${x === null ? "·" : x}${extra(i)}</div>`).join("")}</div>`;
            const render = (st = {}) => `<div class="mq-wrap kc-wrap">
                    ${row("i", a.map((_, i) => i), () => "idx")}
                    ${row("a[i]", a, (i) => (st.i === i ? "cur" : ""))}
                    ${v.length ? row(stage === "unik" ? "v (unik)" : "v", v, (i) => [st.lo !== undefined && i >= st.lo && i < st.hi ? "inq" : "", st.mid === i ? "front" : "", st.found === i ? "win" : "", st.dup && st.dup.includes(i) ? "gone" : ""].join(" "), (i) => (stage === "unik" ? `<small>${i}</small>` : "")) : ""}
                    ${row("b[i]", b, (i) => (b[i] !== null ? "win" : ""))}
                    <p class="mq-note">nilai asli boleh sampai 10<sup>9</sup>; setelah dikompres semuanya 0..${Math.max(0, new Set(a).size - 1)} dan urutannya tetap</p>
                </div>`;
            const push = (line, text, st, w = [], mark) => frames.push({ line, text, mark, html: render(st), watch: w });
            v = [...a];
            push(0, `Nilai sampai 10<sup>9</sup> tidak bisa langsung dipakai sebagai indeks array. Kompresi koordinat mengganti setiap nilai dengan <b>peringkatnya</b>, tanpa mengubah urutan relatif. Mulai dengan menyalin a ke v.`, {});
            v.sort((x, y) => x - y);
            push(1, `Urutkan v: O(N log N).`, {});
            const dup = v.map((x, i) => (i > 0 && v[i - 1] === x ? i : -1)).filter((i) => i >= 0);
            if (dup.length) push(2, `<code>unique</code> menggeser nilai kembar (bersebelahan karena sudah terurut) ke belakang; <code>erase</code> membuangnya.`, { dup });
            v = [...new Set(v)];
            stage = "unik";
            push(2, `Tersisa ${v.length} nilai berbeda. Indeks di v adalah peringkat baru.`, {}, [["v.size()", v.length]]);
            for (let i = 0; i < a.length; i++) {
                const x = a[i];
                let lo = 0;
                let hi = v.length;
                push(4, `Kompres a[${i}] = ${x}: cari posisi pertama di v yang ≥ ${x} dengan binary search.`, { i, lo, hi }, [["x", x], ["lo", lo], ["hi", hi]]);
                while (lo < hi) {
                    const mid = (lo + hi) >> 1;
                    const go = v[mid] < x;
                    push(5, `mid = ${mid}, v[mid] = ${v[mid]} ${go ? "&lt;" : "≥"} ${x}: ${go ? "jawaban di kanan, lo = mid + 1" : "mid bisa jadi jawaban, hi = mid"}.`, { i, lo, hi, mid }, [["x", x], ["lo", lo], ["hi", hi], ["mid", mid]]);
                    if (go) lo = mid + 1;
                    else hi = mid;
                }
                b[i] = lo;
                push(7, `lo = hi = ${lo}: <b>b[${i}] = ${lo}</b>.`, { i, found: lo }, [["x", x], ["b[i]", lo]], "take");
            }
            push(-1, `Selesai: ${a.join(", ")} menjadi <b>${b.join(", ")}</b>. Sekarang nilai bisa dipakai sebagai indeks array frekuensi, Fenwick tree, dan sebagainya. Untuk mengembalikan nilai asli: <code>v[b[i]]</code>.`, {}, [], "done");
            player.load(frames);
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            code.set(f.line);
            watch.set(f.watch);
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        arrIn.addEventListener("keydown", (e) => e.key === "Enter" && build());
        build();
    });

    // ════════════════════════════ Manipulasi bit ════════════════════════════
    V.register("bits", (root) => {
        const sh = V.shell(root, {
            title: "Bit: Operator dan Enumerasi Subset",
            controls: `
                ${V.segmented("mode", [["op", "Operator"], ["subset", "Semua subset"]], "op")}
                <label class="viz-input" data-opin>a <input class="short" data-a value="44"></label>
                <label class="viz-input" data-opin>b <input class="short" data-b value="27"></label>
                <label class="viz-input" data-subin hidden>Berat <input data-w value="3, 5, 6, 2" style="width:100px"></label>
                <label class="viz-input" data-subin hidden>K <input class="short" data-k value="8"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Bit 1", "#22c55e", "rgba(34,197,94,.3)"],
                ["Bit yang sedang dibahas", "#f59e0b", "rgba(245,158,11,.35)"],
                ["Subset yang memenuhi", "#4f5fd6"],
            ],
        });
        let mode = "op";
        const aIn = sh.head.querySelector("[data-a]");
        const bIn = sh.head.querySelector("[data-b]");
        const wIn = sh.head.querySelector("[data-w]");
        const kIn = sh.head.querySelector("[data-k]");
        const codeBox = document.createElement("div");
        sh.side.appendChild(codeBox);
        const CODES = {
            op: `
int a, b;
a & b      // AND: 1 jika keduanya 1                  //@1
a | b      // OR : 1 jika salah satu 1               //@2
a ^ b      // XOR: 1 jika berbeda                     //@3
a << 1     // geser kiri = kali 2                     //@4
a >> 1     // geser kanan = bagi 2 (dibulatkan ke bawah) //@5
(a >> i) & 1        // bit ke-i dari a                //@6
a & -a     // bit 1 terendah (lowbit)                 //@7
__builtin_popcount(a)  // banyak bit 1                //@8`,
            subset: `
int jumlah = 0;
for (int mask = 0; mask < (1 << n); mask++) {     //@1
    int s = 0;
    for (int i = 0; i < n; i++)                   //@2
        if (mask >> i & 1) s += w[i];  // barang i dipilih //@2
    if (s == K) jumlah++;                         //@3
}
// 2^n subset × n bit = O(2^n · n); n ≤ 20 masih cepat //@4`,
        };
        let code = null;
        const watch = V.watchPanel(sh.side);
        const setCode = () => {
            codeBox.innerHTML = "";
            code = V.codePanel(codeBox, CODES[mode]);
        };

        const bitsRow = (label, x, width, hi = -1, extra = "") =>
            `<div class="bt-row"><span class="bt-lbl">${label}</span>${Array.from({ length: width }, (_, k) => {
                const i = width - 1 - k;
                const on = (x >> i) & 1;
                return `<span class="bt-bit ${on ? "on" : ""} ${i === hi ? "hi" : ""}"><small>${i}</small>${on}</span>`;
            }).join("")}<span class="bt-val">= ${x}${extra}</span></div>`;

        function buildOp() {
            const a = V.clampInt(aIn.value, 0, 255, 44);
            const b = V.clampInt(bIn.value, 0, 255, 27);
            aIn.value = a;
            bIn.value = b;
            const W = 9;
            const frames = [];
            const push = (line, text, rows, w) => frames.push({ line, text, html: `<div class="bt-wrap">${rows.join("")}</div>`, watch: w });
            const base = [bitsRow("a", a, W), bitsRow("b", b, W)];
            push(-1, `Setiap bilangan bulat disimpan sebagai deretan bit. Bit ke-i bernilai 2<sup>i</sup>: ${a} = ${[...a.toString(2)].reverse().map((c, i) => (c === "1" ? 2 ** i : null)).filter((x) => x !== null).reverse().join(" + ") || 0}.`, base, [["a", a], ["b", b]]);
            push(1, `<b>a &amp; b</b>: bit hasil 1 hanya jika bit a <em>dan</em> bit b sama-sama 1.`, [...base, bitsRow("a & b", a & b, W)], [["a & b", a & b]]);
            push(2, `<b>a | b</b>: bit hasil 1 jika <em>salah satu</em> bernilai 1.`, [...base, bitsRow("a | b", a | b, W)], [["a | b", a | b]]);
            push(3, `<b>a ^ b</b>: bit hasil 1 jika keduanya <em>berbeda</em>. Sifat penting: x ^ x = 0 dan x ^ 0 = x.`, [...base, bitsRow("a ^ b", a ^ b, W)], [["a ^ b", a ^ b]]);
            push(4, `<b>a &lt;&lt; 1</b>: semua bit bergeser satu ke kiri, sama dengan dikali 2. <code>1 &lt;&lt; k</code> = 2<sup>k</sup>.`, [bitsRow("a", a, W), bitsRow("a << 1", (a << 1) & 511, W)], [["a << 1", a << 1]]);
            push(5, `<b>a &gt;&gt; 1</b>: bergeser ke kanan, bit terendah hilang: dibagi 2 dibulatkan ke bawah.`, [bitsRow("a", a, W), bitsRow("a >> 1", a >> 1, W)], [["a >> 1", a >> 1]]);
            const hiBit = a ? Math.floor(Math.log2(a)) : 0;
            const i = Math.min(3, hiBit);
            push(6, `Bit ke-${i} dari a: geser a ke kanan ${i} kali, lalu ambil bit terendah dengan &amp; 1. Hasil: <b>${(a >> i) & 1}</b>.`, [bitsRow("a", a, W, i), bitsRow(`a >> ${i}`, a >> i, W, 0)], [["(a >> " + i + ") & 1", (a >> i) & 1]]);
            push(7, `<b>a &amp; -a</b> menyisakan bit 1 terendah saja (dipakai di Fenwick tree). Untuk a = ${a}: ${a & -a}.`, [bitsRow("a", a, W), bitsRow("a & -a", a & -a, W)], [["lowbit", a & -a]]);
            const pc = a.toString(2).split("").filter((c) => c === "1").length;
            push(8, `<b>popcount</b>: banyak bit 1 di a = ${pc}. Di C++: <code>__builtin_popcount</code> (int) atau <code>__builtin_popcountll</code> (long long).`, [bitsRow("a", a, W)], [["popcount(a)", pc]]);
            return frames;
        }

        function buildSubset() {
            let w = V.parseList(wIn.value, { min: 1, max: 20, limit: 4 });
            if (!w.length) w = [3, 5, 6, 2];
            wIn.value = w.join(", ");
            const K = V.clampInt(kIn.value, 0, 80, 8);
            kIn.value = K;
            const n = w.length;
            const frames = [];
            const found = [];
            frames.push({ line: 1, text: `${n} barang berbobot ${w.join(", ")}. Setiap subset bisa ditulis sebagai bilangan <code>mask</code> dari 0 sampai 2<sup>${n}</sup> − 1 = ${(1 << n) - 1}: bit ke-i bernilai 1 berarti barang i dipilih. Cari subset berjumlah tepat ${K}.`, html: `<div class="bt-wrap">${bitsRow("mask", 0, n)}</div>`, watch: [["subset", 1 << n]] });
            for (let mask = 0; mask < 1 << n; mask++) {
                let s = 0;
                const pick = [];
                for (let i = 0; i < n; i++) if ((mask >> i) & 1) (s += w[i]), pick.push(`w[${i}]`);
                const ok = s === K;
                if (ok) found.push(mask);
                const items = w.map((x, i) => `<span class="bt-item ${(mask >> i) & 1 ? "on" : ""}"><small>barang ${i}</small>${x}</span>`).join("");
                frames.push({
                    line: ok ? 3 : 2,
                    mark: ok ? "key" : undefined,
                    text: `mask = ${mask} = ${mask.toString(2).padStart(n, "0")}<sub>2</sub>: ${pick.length ? pick.join(" + ") : "tidak ada barang"} = ${s}${ok ? ` = K: <b>subset cocok</b>.` : "."}`,
                    html: `<div class="bt-wrap">${bitsRow("mask", mask, n, -1, ok ? " cocok" : "")}<div class="bt-items">${items}</div><p class="mq-note">jumlah = ${s}; ditemukan sejauh ini: ${found.length ? found.map((m) => m.toString(2).padStart(n, "0")).join(", ") : "belum ada"}</p></div>`,
                    watch: [["mask", mask], ["jumlah", s], ["cocok", found.length]],
                });
            }
            frames.push({ line: 4, mark: "done", text: `Selesai: <b>${found.length}</b> subset berjumlah ${K}. Total ${1 << n} subset diperiksa; untuk n = 20 itu sekitar sejuta, masih sangat cepat.`, html: frames[frames.length - 1].html, watch: [["cocok", found.length]] });
            return frames;
        }

        function build() {
            sh.head.querySelectorAll("[data-opin]").forEach((el) => (el.hidden = mode !== "op"));
            sh.head.querySelectorAll("[data-subin]").forEach((el) => (el.hidden = mode !== "subset"));
            setCode();
            player.load(mode === "op" ? buildOp() : buildSubset());
        }

        const player = new V.Player(sh, (f) => {
            sh.stage.innerHTML = f.html;
            if (code) code.set(f.line);
            watch.set(f.watch);
        });
        V.bindSegmented(sh.head, "mode", (v) => {
            mode = v;
            build();
        });
        sh.head.querySelector("[data-apply]").onclick = build;
        [aIn, bIn, wIn, kIn].forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && build()));
        build();
    });
})();
