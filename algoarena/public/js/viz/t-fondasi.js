/* Visualisasi track Fondasi & STL: vector, comparator, stack/queue/deque, set/map/heap, algoritma STL, bitset */
(() => {
    "use strict";

    const V = window.Viz;
    const K = window.Kit;

    // ════════════════════════════ vector: push_back, kapasitas & realokasi ════════════════════════════
    V.register("vector-mem", (root) => {
        K.widget(root, {
            title: "vector: push_back dan Kapasitas",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">push_back sebanyak <select data-n>${[5, 9, 13, 17].map((k) => `<option ${k === 9 ? "selected" : ""}>${k}</option>`).join("")}</select></label>
                ${V.segmented("mode", [["biasa", "tanpa reserve"], ["reserve", "dengan reserve(n)"]], "biasa")}`,
            legend: [
                ["Elemen baru", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Elemen yang disalin", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Slot kosong (kapasitas cadangan)", "#9ca3af", "transparent"],
            ],
            code: `
vector<int> v;                                //@0
v.reserve(n);           // opsional            //@5
for (int i = 0; i < n; i++) {
    // di dalam push_back:
    if (v.size() == v.capacity()) {           //@1
        // 1. minta blok baru 2x lebih besar   //@2
        // 2. salin semua elemen lama          //@2
        // 3. bebaskan blok lama               //@2
    }
    v.push_back(i);   // taruh di slot kosong  //@3
}
// rata-rata salinan per push_back < 2       //@4`,
            watch: "Keadaan vector",
            build(ui) {
                const n = +K.val(ui, "n");
                const reserve = K.segVal(ui, "mode") === "reserve";
                const frames = [];
                let cap = 0;
                let data = [];
                let reallocs = 0;
                let copies = 0;
                const block = (arr, c, cls = {}, label) =>
                    K.cells(
                        Array.from({ length: Math.max(c, 1) }, (_, i) => (i < arr.length ? arr[i] : "")),
                        { cls: Object.assign(Object.fromEntries(Array.from({ length: Math.max(c, 1) }, (_, i) => [i, i < arr.length ? "" : "empty"])), cls), label, size: c > 12 ? "sm" : "" },
                    );
                const watch = (secret) => [
                    ["size()", data.length],
                    ["capacity()", cap, secret],
                    ["realokasi", reallocs],
                    ["total salinan", copies],
                    ["salinan / push", data.length ? (copies / data.length).toFixed(2) : "–"],
                ];
                const push = (line, text, html, mark, ask, htmlMasked) => frames.push({ line, text, html, mark, ask, htmlMasked, watch: watch() });
                push(0, `vector kosong: <b>size = 0</b>, <b>capacity = 0</b>. Kita akan memanggil <code>push_back</code> sebanyak ${n} kali.`, `<div class="kx-note">blok memori belum dialokasikan</div>`);
                if (reserve) {
                    cap = n;
                    reallocs = 1;
                    push(5, `<code>reserve(${n})</code>: langsung minta blok untuk ${n} elemen. Tidak akan ada realokasi lagi.`, block(data, cap, {}, "blok"), "key");
                }
                for (let i = 0; i < n; i++) {
                    if (data.length === cap) {
                        const old = data.slice();
                        const oldCap = cap;
                        const newCap = Math.max(1, cap * 2);
                        const html = (oldCap ? block(old, oldCap, {}, "blok lama") : "") + block([], newCap, {}, "blok baru");
                        push(
                            1,
                            `size = capacity = ${cap}: <b>penuh</b>. Kapasitas baru = ${oldCap ? `2 × ${oldCap}` : "1"} = <b>${newCap}</b>.`,
                            html,
                            "skip",
                            { type: "value", prompt: `Blok penuh dengan kapasitas ${cap}. Berapa kapasitas barunya?`, answer: String(newCap) },
                            (oldCap ? block(old, oldCap, {}, "blok lama") : "") + `<div class="kx-big">blok baru: kapasitas ?</div>`,
                        );
                        cap = newCap;
                        reallocs++;
                        if (old.length) {
                            copies += old.length;
                            const cls = Object.fromEntries(old.map((_, k) => [k, "in"]));
                            push(2, `Salin <b>${old.length}</b> elemen lama ke blok baru (total salinan sekarang ${copies}), lalu bebaskan blok lama.`, block(old, oldCap, cls, "blok lama") + block(old, cap, cls, "blok baru"), "discover");
                        }
                        data = old;
                    }
                    data.push(i);
                    push(3, `<code>push_back(${i})</code> → masuk ke slot ${i}. size = ${data.length}, capacity = ${cap}.`, block(data, cap, { [i]: "on" }, "blok"));
                }
                push(
                    4,
                    reserve
                        ? `Selesai: ${n} kali push_back tanpa satu pun penyalinan. Gunakan <code>reserve</code> jika ukuran akhirnya sudah diketahui.`
                        : `Selesai: ${reallocs} kali realokasi, total ${copies} salinan untuk ${n} elemen (rata-rata ${(copies / n).toFixed(2)}). Deret 1 + 2 + 4 + … selalu &lt; 2n, jadi push_back rata-rata <b>O(1)</b>.`,
                    block(data, cap, {}, "blok"),
                    "done",
                );
                return frames;
            },
        });
    });

    // ════════════════════════════ Comparator multi-kriteria ════════════════════════════
    const STUDENTS = [
        ["dina", 80, 20],
        ["budi", 90, 19],
        ["cici", 80, 18],
        ["eko", 70, 22],
        ["ani", 80, 18],
        ["fajar", 90, 21],
    ];
    V.register("cmp-sort", (root) => {
        K.widget(root, {
            title: "Comparator: Siapa yang Harus di Depan?",
            stageClass: "kx-stage",
            practice: true,
            controls: V.segmented(
                "crit",
                [
                    ["1", "nilai ↓"],
                    ["2", "nilai ↓, umur ↑"],
                    ["3", "nilai ↓, umur ↑, nama ↑"],
                ],
                "3",
            ),
            legend: [
                ["Elemen yang sedang disisipkan", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Dibandingkan", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Bagian yang sudah urut", "#22c55e", "rgba(34,197,94,.14)"],
            ],
            code: `
bool lebihDulu(const Siswa& x, const Siswa& y) {
    if (x.nilai != y.nilai)                   //@0
        return x.nilai > y.nilai;   // menurun  //@0
    if (x.umur != y.umur)                     //@1
        return x.umur < y.umur;     // menaik   //@1
    return x.nama < y.nama;         // menaik   //@2
}
// dua siswa identik → false (bukan true!)   //@3`,
            watch: "Perbandingan",
            build(ui) {
                const crit = +K.segVal(ui, "crit");
                const a = STUDENTS.map(([nama, nilai, umur]) => ({ nama, nilai, umur }));
                const frames = [];
                const label = (s) => `${s.nama}`;
                const sub = (s) => `${s.nilai} / ${s.umur}`;
                const view = (cls) =>
                    K.cells(a.map(label), { cls, sub: Object.fromEntries(a.map((s, i) => [i, sub(s)])), idx: true, size: "lg" }) +
                    `<div class="kx-note">di bawah nama: <b>nilai / umur</b></div>`;
                // cmp(x, y): x harus di depan y?  (kriteria yang dipakai bergantung pilihan)
                const cmp = (x, y) => {
                    if (x.nilai !== y.nilai) return [x.nilai > y.nilai, 0, `nilai ${x.nilai} vs ${y.nilai} berbeda`];
                    if (crit < 2) return [false, 0, `nilai sama (${x.nilai}) dan tidak ada kriteria lain`];
                    if (x.umur !== y.umur) return [x.umur < y.umur, 1, `nilai sama, umur ${x.umur} vs ${y.umur}`];
                    if (crit < 3) return [false, 1, `nilai & umur sama, tidak ada kriteria lain`];
                    if (x.nama !== y.nama) return [x.nama < y.nama, 2, `nilai & umur sama, nama "${x.nama}" vs "${y.nama}"`];
                    return [false, 3, "identik"];
                };
                const push = (line, text, cls, watch, mark, ask) => frames.push({ line, text, html: view(cls), watch, mark, ask });
                push(-1, `Urutkan 6 siswa dengan comparator <b>${["", "nilai menurun", "nilai menurun, lalu umur menaik", "nilai menurun, umur menaik, lalu nama menaik"][crit]}</b>. Kita pakai insertion sort agar setiap perbandingan terlihat.`, {}, []);
                for (let i = 1; i < a.length; i++) {
                    let j = i;
                    while (j > 0) {
                        const [res, line, why] = cmp(a[j], a[j - 1]);
                        const cls = {};
                        for (let k = 0; k < i + 1; k++) cls[k] = "ok";
                        cls[j] = "on";
                        cls[j - 1] = "in";
                        push(
                            line,
                            `lebihDulu(<b>${a[j].nama}</b>, <b>${a[j - 1].nama}</b>)? ${why} → <b>${res}</b>. ${res ? "Tukar: " + a[j].nama + " maju." : "Berhenti di sini."}`,
                            cls,
                            [
                                ["x", a[j].nama],
                                ["y", a[j - 1].nama],
                                ["hasil", String(res), true],
                            ],
                            res ? "key" : "skip",
                            { type: "choice", prompt: `Apakah <b>${a[j].nama}</b> harus di depan <b>${a[j - 1].nama}</b>?`, options: ["true", "false"], answer: res ? 0 : 1 },
                        );
                        if (!res) break;
                        [a[j], a[j - 1]] = [a[j - 1], a[j]];
                        j--;
                    }
                }
                const ties = [];
                for (let i = 1; i < a.length; i++) if (!cmp(a[i - 1], a[i])[0] && !cmp(a[i], a[i - 1])[0]) ties.push(`${a[i - 1].nama} & ${a[i].nama}`);
                push(
                    -1,
                    `Hasil: ${a.map((s) => s.nama).join(", ")}. ${
                        ties.length
                            ? `<b>Perhatian:</b> pasangan ${ties.join(", ")} dianggap <em>sama</em> oleh comparator ini, sehingga urutannya bisa berbeda di komputer lain (std::sort tidak stabil). Tambahkan kriteria berikutnya.`
                            : "Setiap pasangan punya urutan yang pasti: hasilnya selalu sama di komputer mana pun."
                    }`,
                    Object.fromEntries(a.map((_, i) => [i, "ok"])),
                    [],
                    "done",
                );
                return frames;
            },
        });
    });

    // ════════════════════════════ Stack: kurung seimbang ════════════════════════════
    V.register("stack-bracket", (root) => {
        K.widget(root, {
            title: "Stack: Memeriksa Kurung Seimbang",
            stageClass: "kx-stage",
            practice: true,
            controls: `<label class="viz-input">String <input data-s value="{[()()]}(" style="width:150px" maxlength="16"></label>
                       <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Karakter yang dibaca", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Pasangan cocok", "#22c55e", "rgba(34,197,94,.16)"],
                ["Bermasalah", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
bool seimbang(const string& s) {
    stack<char> st;                              //@0
    for (char c : s) {
        if (c == '(' || c == '[' || c == '{')    //@1
            st.push(c);                          //@1
        else {
            if (st.empty()) return false;        //@2
            if (!cocok(st.top(), c)) return false; //@3
            st.pop();                            //@4
        }
    }
    return st.empty();                           //@5
}`,
            watch: "Keadaan",
            panels: [{ key: "st", type: "html", title: "Isi stack", hint: "atas = paling baru" }],
            build(ui) {
                let s = String(K.val(ui, "s")).replace(/[^()[\]{}]/g, "").slice(0, 16);
                if (!s) s = "{[()()]}(";
                ui.head.querySelector("[data-s]").value = s;
                const pair = { ")": "(", "]": "[", "}": "{" };
                const frames = [];
                const st = [];
                const done = {};
                const view = (i, extra = {}) => K.cells(s.split(""), { cls: Object.assign({}, done, i >= 0 ? { [i]: "on" } : {}, extra), ptr: i >= 0 ? { [i]: "i" } : {}, size: "lg" });
                const stk = (cls, hide) => K.stack(st.map((c, k) => ({ label: hide && k === st.length - 1 ? "?" : c, cls: k === st.length - 1 ? (hide ? "ask" : cls || "") : "" })), { empty: "stack kosong" });
                const push = (line, text, i, extra, cls, mark, ask) =>
                    frames.push({ line, text, mark, ask, html: view(i, extra), st: stk(cls), stMasked: stk(cls, true), watch: [["i", i < 0 ? "–" : i], ["c", i >= 0 && i < s.length ? s[i] : "–"], ["ukuran stack", st.length], ["top()", st.length ? st[st.length - 1] : "–", true]] });
                push(0, `Baca string <code>${V.esc(s)}</code> dari kiri ke kanan. Kurung buka disimpan di stack; kurung tutup harus cocok dengan kurung buka <b>paling baru</b> (paling atas).`, -1);
                let ok = true;
                for (let i = 0; i < s.length; i++) {
                    const c = s[i];
                    if ("([{".includes(c)) {
                        st.push(c);
                        push(1, `<code>${c}</code> adalah kurung buka: <b>push</b> ke stack.`, i, {}, "on", "");
                    } else if (!st.length) {
                        push(2, `<code>${c}</code> adalah kurung tutup, tetapi stack <b>kosong</b>: tidak ada pasangannya. Tidak seimbang.`, i, { [i]: "bad" }, "", "skip");
                        ok = false;
                        break;
                    } else if (st[st.length - 1] !== pair[c]) {
                        push(3, `<code>${c}</code> harus menutup <code>${pair[c]}</code>, tetapi top() adalah <code>${st[st.length - 1]}</code>. Tidak cocok: tidak seimbang.`, i, { [i]: "bad" }, "bad", "skip", { type: "value", prompt: `Kurung tutup <code>${c}</code> datang. Apa isi top() stack sekarang?`, answer: st[st.length - 1] });
                        ok = false;
                        break;
                    } else {
                        // cari indeks pasangan pembuka untuk diwarnai
                        let depth = 0;
                        for (let j = i - 1; j >= 0; j--) {
                            if (")]}".includes(s[j])) depth++;
                            else if (depth) depth--;
                            else {
                                done[j] = "ok";
                                break;
                            }
                        }
                        done[i] = "ok";
                        push(3, `<code>${c}</code> cocok dengan top() <code>${pair[c]}</code>.`, i, {}, "ok", "discover", { type: "value", prompt: `Kurung tutup <code>${c}</code> datang. Apa isi top() stack sekarang?`, answer: pair[c] });
                        st.pop();
                        push(4, `<b>pop</b>: pasangan selesai, buang dari stack.`, i, {}, "", "key");
                    }
                }
                if (ok) {
                    if (st.length) push(5, `String habis tetapi stack masih berisi ${st.length} kurung buka tanpa pasangan: <b>tidak seimbang</b>.`, -1, {}, "bad", "done");
                    else push(5, `String habis dan stack kosong: semua kurung punya pasangan. <b>Seimbang!</b>`, -1, {}, "", "done");
                } else frames[frames.length - 1].mark = "done";
                return frames;
            },
        });
    });

    // ════════════════════════════ Queue & deque ════════════════════════════
    const QOPS = {
        queue: ["push A", "push B", "push C", "pop", "push D", "pop", "pop", "push E", "pop", "pop"],
        deque: ["push_back A", "push_back B", "push_front C", "push_back D", "pop_front", "push_front E", "pop_back", "pop_back", "push_front F", "pop_front"],
    };
    V.register("queue-deque", (root) => {
        K.widget(root, {
            title: "Queue (FIFO) dan Deque (dua ujung)",
            stageClass: "kx-stage",
            practice: true,
            controls: V.segmented("kind", [["queue", "queue"], ["deque", "deque"]], "queue"),
            legend: [
                ["Baru masuk", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Akan keluar", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
// queue<char> q;            deque<char> d;
q.push(x);        // ke belakang        //@0
q.pop();          // dari depan         //@1
d.push_back(x);   // ke belakang        //@2
d.push_front(x);  // ke depan           //@3
d.pop_back();     // dari belakang      //@4
d.pop_front();    // dari depan         //@5
// semua O(1)`,
            watch: "Keadaan",
            panels: [{ key: "out", type: "ds", title: "Urutan keluar", hint: "elemen yang sudah dikeluarkan" }],
            build(ui) {
                const kind = K.segVal(ui, "kind");
                const ops = QOPS[kind];
                const q = [];
                const out = [];
                const frames = [];
                const view = (cls, hide) => K.stack(q.map((x, i) => ({ label: hide && cls[i] === "bad" ? "?" : x, cls: cls[i] || "" })), { dir: "h", front: "depan ←", back: "← belakang", empty: "kosong" });
                const push = (line, text, cls, mark, ask) =>
                    frames.push({ line, text, mark, ask, html: view(cls), htmlMasked: view(cls, true), out: out.slice(), watch: [["operasi", text.replace(/<[^>]+>/g, "").split(":")[0]], ["size()", q.length], ["front()", q.length ? q[0] : "–", true], ["back()", q.length ? q[q.length - 1] : "–", true]] });
                push(-1, kind === "queue" ? "Queue seperti antrian kasir: masuk dari <b>belakang</b>, keluar dari <b>depan</b> (First In, First Out)." : "Deque (double-ended queue) bisa menambah dan membuang di <b>kedua ujung</b> dalam O(1).", {});
                for (const op of ops) {
                    const [name, x] = op.split(" ");
                    if (name === "push" || name === "push_back") {
                        q.push(x);
                        push(name === "push" ? 0 : 2, `<code>${name}(${x})</code>: ${x} masuk di belakang.`, { [q.length - 1]: "on" });
                    } else if (name === "push_front") {
                        q.unshift(x);
                        push(3, `<code>push_front(${x})</code>: ${x} menyelak ke paling depan.`, { 0: "on" });
                    } else if (name === "pop" || name === "pop_front") {
                        const ask = { type: "value", prompt: `Elemen mana yang keluar pada <code>${name}()</code>?`, answer: q[0] };
                        push(name === "pop" ? 1 : 5, `<code>${name}()</code>: yang keluar adalah elemen paling <b>depan</b>, yaitu ${q[0]}.`, { 0: "bad" }, "skip", ask);
                        out.push(q.shift());
                        push(name === "pop" ? 1 : 5, `${out[out.length - 1]} sudah keluar.`, {}, "");
                    } else if (name === "pop_back") {
                        const ask = { type: "value", prompt: "Elemen mana yang keluar pada <code>pop_back()</code>?", answer: q[q.length - 1] };
                        push(4, `<code>pop_back()</code>: yang keluar adalah elemen paling <b>belakang</b>, yaitu ${q[q.length - 1]}.`, { [q.length - 1]: "bad" }, "skip", ask);
                        out.push(q.pop());
                        push(4, `${out[out.length - 1]} sudah keluar.`, {}, "");
                    }
                }
                push(-1, `Selesai. Urutan keluar: <b>${out.join(", ")}</b>.${kind === "queue" ? " Perhatikan: sama persis dengan urutan masuk." : ""}`, {}, "done");
                return frames;
            },
        });
    });

    // ════════════════════════════ set / multiset: urutan, lower_bound, erase ════════════════════════════
    const SET_OPS = [
        ["insert", 5], ["insert", 2], ["insert", 8], ["insert", 5], ["insert", 3], ["insert", 5],
        ["lower_bound", 4], ["upper_bound", 5], ["lower_bound", 9], ["erase_it", 5], ["erase", 5], ["count", 5],
    ];
    V.register("set-ops", (root) => {
        K.widget(root, {
            title: "set & multiset: Selalu Terurut",
            stageClass: "kx-stage",
            practice: true,
            controls: V.segmented("kind", [["set", "set"], ["multiset", "multiset"]], "multiset"),
            legend: [
                ["Elemen baru / hasil pencarian", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Yang dihapus", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
s.insert(x);                       // O(log n)  //@0
auto it = s.lower_bound(x);        // pertama >= x //@1
auto it = s.upper_bound(x);        // pertama >  x //@2
s.erase(s.find(x));  // hapus SATU salinan    //@3
s.erase(x);          // hapus SEMUA salinan   //@4
s.count(x);          // banyak salinan          //@5
// it == s.end() → tidak ada                 //@6`,
            watch: "Keadaan",
            build(ui) {
                const multi = K.segVal(ui, "kind") === "multiset";
                let a = [];
                const frames = [];
                const name = multi ? "multiset" : "set";
                const view = (cls = {}, endMark) =>
                    (a.length ? K.cells(a, { cls, size: "lg" }) : `<div class="kx-empty">${name} kosong</div>`) + (endMark ? `<div class="kx-note">hasil: <b>s.end()</b> (tidak ada)</div>` : "");
                const push = (line, text, cls, extra = {}) =>
                    frames.push(Object.assign({ line, text, html: view(cls, extra.end), watch: [["size()", a.length], ["isi", `{${a.join(", ")}}`]] }, extra));
                push(-1, `${name} kosong. Setiap operasi menjaga isinya tetap <b>terurut</b>${multi ? " dan boleh ada nilai kembar" : " dan tanpa duplikat"}.`, {});
                for (const [op, x] of SET_OPS) {
                    if (op === "insert") {
                        if (!multi && a.includes(x)) {
                            push(0, `<code>insert(${x})</code>: ${x} sudah ada di set, <b>tidak ada yang berubah</b>.`, { [a.indexOf(x)]: "in" }, { mark: "skip" });
                            continue;
                        }
                        let pos = a.findIndex((y) => y > x);
                        if (pos < 0) pos = a.length;
                        a.splice(pos, 0, x);
                        push(0, `<code>insert(${x})</code>: langsung disisipkan di posisi yang benar.`, { [pos]: "on" });
                    } else if (op === "lower_bound" || op === "upper_bound") {
                        const pos = a.findIndex((y) => (op === "lower_bound" ? y >= x : y > x));
                        const ans = pos < 0 ? "end()" : String(a[pos]);
                        push(
                            op === "lower_bound" ? 1 : 2,
                            `<code>${op}(${x})</code>: elemen pertama yang ${op === "lower_bound" ? "≥" : "&gt;"} ${x} adalah <b>${pos < 0 ? "tidak ada (end)" : a[pos]}</b>.`,
                            pos < 0 ? {} : { [pos]: "on" },
                            { mark: "key", end: pos < 0, ask: { type: "value", prompt: `Berapa nilai <code>*s.${op}(${x})</code>? (tulis <b>end</b> jika tidak ada)`, answer: pos < 0 ? "end" : ans, accept: pos < 0 ? ["end()", "s.end()"] : [] } },
                        );
                        frames[frames.length - 1].htmlMasked = view({});
                    } else if (op === "erase_it") {
                        const pos = a.indexOf(x);
                        if (pos < 0) continue;
                        push(3, `<code>erase(find(${x}))</code>: menghapus <b>tepat satu</b> salinan ${x} lewat iteratornya.`, { [pos]: "bad" }, { mark: "skip" });
                        a.splice(pos, 1);
                        push(3, `Satu salinan ${x} hilang.`, {});
                    } else if (op === "erase") {
                        const cls = {};
                        a.forEach((y, i) => y === x && (cls[i] = "bad"));
                        const k = Object.keys(cls).length;
                        push(4, `<code>erase(${x})</code> dengan <b>nilai</b>: menghapus <b>semua</b> salinan ${x} (${k} buah).${multi ? " Inilah jebakan multiset!" : ""}`, cls, { mark: "skip" });
                        a = a.filter((y) => y !== x);
                        push(4, `Semua ${x} hilang.`, {});
                    } else if (op === "count") {
                        push(5, `<code>count(${x})</code> = ${a.filter((y) => y === x).length}.`, {}, { mark: "done" });
                    }
                }
                return frames;
            },
        });
    });

    // ════════════════════════════ priority_queue: binary heap ════════════════════════════
    V.register("heap-pq", (root) => {
        K.widget(root, {
            title: "priority_queue: Heap di Dalam Array",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">push <input data-vals value="5 3 8 1 9 2" style="width:130px"></label>
                ${V.segmented("kind", [["max", "max-heap (bawaan)"], ["min", "min-heap (greater)"]], "max")}
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Elemen yang bergerak", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Dibandingkan", "#22d3ee", "rgba(34,211,238,.16)"],
                ["Puncak (top)", "#22c55e", "rgba(34,197,94,.16)"],
            ],
            code: `
// anak dari i: 2i+1 dan 2i+2, ayah: (i-1)/2
void push(int x) {
    h.push_back(x);  int i = h.size() - 1;    //@0
    while (i > 0 && lebih(h[i], h[(i-1)/2])) { //@1
        swap(h[i], h[(i-1)/2]);               //@2
        i = (i - 1) / 2;                      //@2
    }
}
void pop() {
    h[0] = h.back();  h.pop_back();          //@3
    // turunkan h[0]: tukar dengan anak terbaik //@4
    // selama anak itu "lebih" dari dirinya     //@4
}`,
            watch: "Keadaan",
            build(ui) {
                const vals = K.nums(K.val(ui, "vals"), { min: 0, max: 99, limit: 9, def: [5, 3, 8, 1, 9, 2] });
                ui.head.querySelector("[data-vals]").value = vals.join(" ");
                const isMax = K.segVal(ui, "kind") === "max";
                const better = (a, b) => (isMax ? a > b : a < b);
                const h = [];
                const frames = [];
                const view = (cls = {}, hideIdx = -1) => {
                    const nodes = h.map((v, i) => ({ id: i, label: i === hideIdx ? "?" : v, parent: i ? (i - 1) >> 1 : null, cls: (cls[i] || (i === 0 ? "ok" : "")) + (i === hideIdx ? " ask" : ""), sub: `[${i}]` }));
                    const tree = h.length ? K.tree({ nodes, w: 560, h: 210, r: 19, subBelow: true }) : `<div class="kx-empty">heap kosong</div>`;
                    return tree + K.cells(h.map((v, i) => (i === hideIdx ? "?" : v)), { cls: Object.assign({ 0: "ok" }, cls), size: "sm", label: "array h" });
                };
                const push = (line, text, cls, extra = {}) =>
                    frames.push(Object.assign({ line, text, html: view(cls), watch: [["size()", h.length], ["top()", h.length ? h[0] : "–", true]] }, extra));
                push(-1, `${isMax ? "Max-heap: setiap ayah ≥ anaknya, jadi puncak = terbesar." : "Min-heap: setiap ayah ≤ anaknya, jadi puncak = terkecil."} Heap disimpan sebagai array biasa.`, {});
                for (const x of vals) {
                    h.push(x);
                    let i = h.length - 1;
                    push(0, `<code>push(${x})</code>: taruh di ujung array (indeks ${i}).`, { [i]: "on" });
                    while (i > 0) {
                        const p = (i - 1) >> 1;
                        if (better(h[i], h[p])) {
                            push(1, `${h[i]} ${isMax ? "&gt;" : "&lt;"} ayahnya ${h[p]}: harus naik.`, { [i]: "on", [p]: "in" });
                            [h[i], h[p]] = [h[p], h[i]];
                            push(2, `Tukar. ${x} sekarang di indeks ${p}.`, { [p]: "on" }, { mark: "discover" });
                            i = p;
                        } else {
                            push(1, `${h[i]} tidak ${isMax ? "lebih besar" : "lebih kecil"} dari ayahnya ${h[p]}: berhenti.`, { [i]: "on", [p]: "in" });
                            break;
                        }
                    }
                    frames[frames.length - 1].mark = frames[frames.length - 1].mark || "key";
                }
                for (let r = 0; r < Math.min(3, vals.length); r++) {
                    const top = h[0];
                    const askTop = { type: "value", prompt: `Berapa <code>top()</code> sebelum pop ke-${r + 1}?`, answer: String(top) };
                    push(3, `<code>pop()</code>: keluarkan puncak <b>${top}</b>. Elemen terakhir dipindah ke akar.`, { 0: "bad" }, { ask: askTop, htmlMasked: view({}, 0) });
                    h[0] = h[h.length - 1];
                    h.pop();
                    let i = 0;
                    if (h.length) push(3, `Elemen terakhir ${h[0]} kini di akar; turunkan sampai posisinya benar.`, { 0: "on" });
                    while (true) {
                        const l = 2 * i + 1;
                        const rr = 2 * i + 2;
                        let b = i;
                        if (l < h.length && better(h[l], h[b])) b = l;
                        if (rr < h.length && better(h[rr], h[b])) b = rr;
                        if (b === i) break;
                        push(4, `Anak terbaik dari ${h[i]} adalah ${h[b]}: tukar.`, { [i]: "on", [b]: "in" });
                        [h[i], h[b]] = [h[b], h[i]];
                        i = b;
                    }
                    push(4, `Heap kembali rapi. top() = ${h.length ? h[0] : "–"}.`, {}, { mark: "key" });
                }
                frames[frames.length - 1].mark = "done";
                frames[frames.length - 1].text += " Push dan pop hanya menelusuri satu jalur akar–daun: <b>O(log n)</b>.";
                return frames;
            },
        });
    });

    // ════════════════════════════ Algoritma STL: sort + unique + erase, next_permutation ════════════════════════════
    V.register("stl-algo", (root) => {
        const CODE_UNIQUE = `
vector<int> b = a;                       // salinan //@0
sort(b.begin(), b.end());                         //@1
// unique: tulis setiap nilai yang berbeda dari
// tetangga kirinya ke posisi w, lalu w++           //@2
auto ujung = unique(b.begin(), b.end());          //@3
b.erase(ujung, b.end());   // potong sisa sampah   //@4`;
        K.widget(root, {
            title: "Algoritma STL Langkah demi Langkah",
            stageClass: "kx-stage",
            practice: true,
            controls: `${V.segmented("mode", [["unique", "sort + unique + erase"], ["perm", "next_permutation"]], "unique")}
                <label class="viz-input">array <input data-arr value="50 10 50 30 10 30 70" style="width:150px"></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Penunjuk baca / tulis", "#f59e0b", "rgba(245,158,11,.18)"],
                ["Bagian yang sudah unik / ekor menurun", "#22c55e", "rgba(34,197,94,.14)"],
                ["Sampah / ditukar", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: CODE_UNIQUE,
            watch: "Keadaan",
            build(ui) {
                const mode = K.segVal(ui, "mode");
                const frames = [];
                if (mode === "unique") {
                    const a = K.nums(K.val(ui, "arr"), { min: 0, max: 99, limit: 10, def: [50, 10, 50, 30, 10, 30, 70] });
                    ui.head.querySelector("[data-arr]").value = a.join(" ");
                    let b = a.slice();
                    const push = (line, text, html, watch, extra = {}) => frames.push(Object.assign({ line, text, html, watch }, extra));
                    push(0, "Bekerja pada <b>salinan</b> b agar urutan asli a tidak rusak.", K.cells(a, { label: "a" }) + K.cells(b, { label: "b" }), [["n", a.length]]);
                    b.sort((x, y) => x - y);
                    push(1, "<code>sort</code>: nilai kembar kini <b>bersebelahan</b>. Tanpa ini, unique tidak bisa menemukan kembar yang berjauhan.", K.cells(b, { label: "b", cls: Object.fromEntries(b.map((v, i) => [i, i > 0 && b[i - 1] === v ? "in" : ""])) }), [["n", b.length]], { mark: "key" });
                    let w = 1;
                    for (let r = 1; r < b.length; r++) {
                        const same = b[r] === b[w - 1];
                        const cls = {};
                        for (let k = 0; k < w; k++) cls[k] = "ok";
                        cls[r] = "on";
                        push(2, same ? `b[${r}] = ${b[r]} sama dengan nilai unik terakhir (${b[w - 1]}): <b>lewati</b>.` : `b[${r}] = ${b[r]} berbeda dari ${b[w - 1]}: tulis ke posisi w = ${w}.`, K.cells(b, { label: "b", cls, ptr: Object.assign({ [r]: "baca" }, r === w ? {} : { [w]: "tulis" }) }), [["baca", r], ["tulis w", w]], {
                            mark: same ? "skip" : "",
                            ask: { type: "choice", prompt: `b[${r}] = ${b[r]}. Apakah ia ditulis ke posisi w?`, options: ["ya, tulis", "tidak, lewati"], answer: same ? 1 : 0 },
                        });
                        if (!same) {
                            b[w] = b[r];
                            w++;
                        }
                    }
                    const cls = {};
                    for (let k = 0; k < b.length; k++) cls[k] = k < w ? "ok" : "bad";
                    push(3, `<code>unique</code> selesai dan mengembalikan iterator ke posisi ${w}. Bagian sesudahnya adalah <b>sampah</b>: unique tidak menghapus apa pun!`, K.cells(b, { label: "b", cls, ptr: { [Math.min(w, b.length - 1)]: w < b.length ? "ujung" : "" } }), [["banyak unik", w]], { mark: "discover" });
                    b = b.slice(0, w);
                    push(4, `<code>erase(ujung, end)</code> memotong sampahnya. Hasil: ${w} nilai berbeda, terurut.`, K.cells(b, { label: "b", cls: Object.fromEntries(b.map((_, i) => [i, "ok"])) }), [["size()", w]], { mark: "done" });
                    return frames;
                }
                // next_permutation
                let a = K.nums(K.val(ui, "arr"), { min: 0, max: 99, limit: 8, def: [1, 3, 5, 4, 2] });
                if (a.length < 2 || a.join() === "50,10,50,30,10,30,70") a = [1, 3, 5, 4, 2];
                ui.head.querySelector("[data-arr]").value = a.join(" ");
                const push = (line, text, cls, watch, extra = {}) => frames.push(Object.assign({ line, text, html: K.cells(a, { cls, size: "lg", ptr: extra.ptr || {} }), watch }, extra));
                push(-1, `Cari permutasi <b>berikutnya</b> dari [${a.join(", ")}] dalam urutan kamus (leksikografis).`, {}, []);
                let i = a.length - 2;
                while (i >= 0 && a[i] >= a[i + 1]) i--;
                const tail = {};
                for (let k = i + 1; k < a.length; k++) tail[k] = "ok";
                if (i < 0) {
                    push(1, "Seluruh array menurun: ini permutasi <b>terakhir</b>. next_permutation mengembalikan false dan membaliknya menjadi yang pertama.", tail, [["i", "tidak ada"]], { mark: "done" });
                    return frames;
                }
                push(1, `Dari kanan, ekor <b>[${a.slice(i + 1).join(", ")}]</b> menurun: ekor ini sudah "maksimal". Yang harus naik adalah a[${i}] = <b>${a[i]}</b>, elemen pertama sebelum ekor.`, Object.assign({}, tail, { [i]: "on" }), [["i", i]], {
                    ptr: { [i]: "i" },
                    ask: { type: "value", prompt: "Elemen mana (nilainya) yang harus dinaikkan?", answer: String(a[i]) },
                    mark: "key",
                });
                let j = a.length - 1;
                while (a[j] <= a[i]) j--;
                push(2, `Di ekor, cari elemen <b>terkecil yang lebih besar</b> dari ${a[i]}: dari kanan, yang pertama &gt; ${a[i]} adalah a[${j}] = <b>${a[j]}</b>.`, Object.assign({}, tail, { [i]: "on", [j]: "bad" }), [["i", i], ["j", j]], { ptr: { [i]: "i", [j]: "j" } });
                [a[i], a[j]] = [a[j], a[i]];
                push(3, `Tukar a[${i}] dan a[${j}]. Ekor masih menurun.`, Object.assign({}, tail, { [i]: "on", [j]: "bad" }), [["i", i], ["j", j]], { ptr: { [i]: "i", [j]: "j" } });
                const head = a.slice(0, i + 1);
                const rev = a.slice(i + 1).reverse();
                a = head.concat(rev);
                push(4, `Balik ekornya menjadi menaik (paling kecil). Hasil: <b>[${a.join(", ")}]</b>, permutasi tepat berikutnya.`, tail, [["hasil", a.join(" ")]], { mark: "done" });
                return frames;
            },
            render(f, ui) {
                const mode = K.segVal(ui, "mode");
                if (ui._mode !== mode) {
                    ui._mode = mode;
                    const box = ui.side.querySelector(".viz-panel");
                    const fresh = V.codePanel(
                        document.createElement("div"),
                        mode === "unique"
                            ? CODE_UNIQUE
                            : `
// next_permutation(a.begin(), a.end()):
int i = n - 2;
while (i >= 0 && a[i] >= a[i+1]) i--;   // ekor menurun //@1
if (i < 0) { reverse(a); return false; }              //@1
int j = n - 1;
while (a[j] <= a[i]) j--;     // terkecil yg > a[i]  //@2
swap(a[i], a[j]);                                     //@3
reverse(a.begin() + i + 1, a.end());                  //@4
return true;`,
                    );
                    box.replaceWith(fresh.el);
                    ui.code = fresh;
                }
                ui.code.set(f.line);
            },
        });
    });

    // ════════════════════════════ bitset: subset sum dengan geser + OR ════════════════════════════
    V.register("bitset-shift", (root) => {
        K.widget(root, {
            title: "bitset: Subset Sum dengan Satu Geseran",
            stageClass: "kx-stage",
            practice: true,
            controls: `
                <label class="viz-input">barang <input data-items value="2 3 5" style="width:100px"></label>
                <label class="viz-input">batas S <select data-s>${[7, 10, 12, 15].map((k) => `<option ${k === 10 ? "selected" : ""}>${k}</option>`).join("")}</select></label>
                <button class="btn btn-sm btn-primary" data-apply>Terapkan</button>`,
            legend: [
                ["Bit menyala (jumlah bisa dibentuk)", "#2f5bd3", "rgba(47,91,211,.14)"],
                ["Bit baru dari geseran", "#22c55e", "rgba(34,197,94,.16)"],
                ["Terbuang (lewat batas)", "#ef4444", "rgba(239,68,68,.14)"],
            ],
            code: `
bitset<S + 1> dp;
dp[0] = 1;              // jumlah 0 selalu bisa //@0
for (int x : barang)
    dp |= dp << x;      // geser semua, lalu OR //@1,2
// dp[s] == 1  ⇔  s bisa dibentuk           //@3
cout << dp.count();     // banyak jumlah yang bisa //@3`,
            watch: "Keadaan",
            build(ui) {
                const items = K.nums(K.val(ui, "items"), { min: 1, max: 9, limit: 5, def: [2, 3, 5] });
                ui.head.querySelector("[data-items]").value = items.join(" ");
                const S = +K.val(ui, "s");
                const frames = [];
                let dp = new Array(S + 1).fill(0);
                dp[0] = 1;
                const bits = (arr, cls = {}, label, hide = -1) =>
                    K.line(
                        label,
                        `<div class="kx-bits">${arr.map((b, i) => `<span class="${i === hide ? "on" : b ? "one" : ""} ${cls[i] || ""}" title="${i}">${i === hide ? "?" : b}</span>`).join("")}</div>`,
                    );
                const idx = K.line("s =", `<div class="kx-bits">${dp.map((_, i) => `<span style="border:none;background:none;font-size:10px">${i}</span>`).join("")}</div>`);
                const set = (arr) => arr.map((b, i) => (b ? i : null)).filter((x) => x !== null);
                const push = (line, text, html, extra = {}) => frames.push(Object.assign({ line, text, html: idx + html, watch: [["jumlah yang bisa", `{${set(dp).join(", ")}}`], ["dp.count()", set(dp).length]] }, extra));
                push(0, `Bit ke-s menyala jika jumlah s bisa dibentuk. Awalnya hanya <b>s = 0</b> (tidak mengambil apa pun). Batas S = ${S}.`, bits(dp, {}, "dp"));
                for (const x of items) {
                    const sh = new Array(S + 1).fill(0);
                    const lost = [];
                    dp.forEach((b, i) => {
                        if (!b) return;
                        if (i + x <= S) sh[i + x] = 1;
                        else lost.push(i + x);
                    });
                    const cls = {};
                    sh.forEach((b, i) => b && !dp[i] && (cls[i] = "ok"));
                    push(1, `<code>dp &lt;&lt; ${x}</code>: <b>semua</b> bit bergeser ${x} langkah sekaligus = "ambil barang ${x}". ${lost.length ? `Jumlah ${lost.join(", ")} melewati batas dan terbuang.` : ""}`, bits(dp, {}, "dp") + bits(sh, cls, `dp << ${x}`), { mark: "discover" });
                    const nd = dp.map((b, i) => b | sh[i]);
                    const fresh = nd.map((b, i) => (b && !dp[i] ? i : -1)).filter((i) => i >= 0);
                    const zeros = nd.map((b, i) => (b ? -1 : i)).filter((i) => i >= 0);
                    // Bergantian: tanyakan bit yang baru menyala, atau bit yang tetap mati
                    const askOn = (frames.length % 2 === 0 && fresh.length) || !zeros.length;
                    const hide = askOn ? (fresh.length ? fresh[fresh.length - 1] : -1) : zeros[Math.floor(zeros.length / 2)];
                    const newCls = Object.fromEntries(fresh.map((i) => [i, "ok"]));
                    dp = nd;
                    push(2, `<code>dp |= …</code>: gabungkan "tidak ambil" dan "ambil". Jumlah baru: <b>${fresh.join(", ") || "tidak ada"}</b>.`, bits(dp, newCls, "dp"), {
                        mark: "key",
                        ask: hide >= 0 ? { type: "choice", prompt: `Setelah OR, apakah bit s = ${hide} menyala?`, options: ["ya (1)", "tidak (0)"], answer: dp[hide] ? 0 : 1 } : undefined,
                        htmlMasked: idx + bits(dp, {}, "dp", hide),
                    });
                }
                push(3, `Selesai: ${set(dp).length} jumlah bisa dibentuk. Setiap barang hanya butuh <b>satu</b> operasi geser dan satu OR, masing-masing O(S / 64).`, bits(dp, {}, "dp"), { mark: "done" });
                return frames;
            },
        });
    });
})();
