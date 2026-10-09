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
    V.register("interval", (root) => {
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
})();
