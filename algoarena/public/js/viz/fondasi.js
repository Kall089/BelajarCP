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
})();
