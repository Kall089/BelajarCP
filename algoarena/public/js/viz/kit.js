/**
 * AlgoArena Viz Kit
 *
 * Komponen bersama untuk visualisasi materi baru, dibangun di atas Viz core (shell + Player).
 *
 *   Kit.widget(root, cfg)  → kartu visualisasi lengkap (judul, kontrol, panggung, panel kode, variabel, player)
 *     cfg.title, cfg.controls (HTML), cfg.legend, cfg.practice (Mode Tebak), cfg.code (C++ dengan penanda //@k),
 *     cfg.watch (judul panel variabel, atau false), cfg.panels [{key, type: "html"|"ds"|"array", title, hint, vertical}],
 *     cfg.build(ui) → array frame, cfg.render(f, ui) opsional.
 *   Frame: { text, line, mark, ask, html (isi panggung), htmlMasked, watch: [[nama, nilai, rahasia?]], <key panel>: nilai,
 *            <key panel>Masked: nilai saat Mode Tebak menyamarkan jawaban }
 *
 *   Pembantu HTML: Kit.cells, Kit.bars, Kit.stack, Kit.line, Kit.table, Kit.tree, Kit.plot, Kit.chip
 */
window.Kit = (() => {
    "use strict";

    const V = window.Viz;
    const esc = V.esc;

    // ═════════════════════════ Widget standar ═════════════════════════
    function widget(root, cfg) {
        const sh = V.shell(root, {
            title: cfg.title,
            controls: cfg.controls || "",
            legend: cfg.legend || [],
            practice: !!cfg.practice,
        });
        if (cfg.stageClass) sh.stage.classList.add(...cfg.stageClass.split(" "));
        const ui = { sh, head: sh.head, stage: sh.stage, side: sh.side, panels: {} };
        if (cfg.code && typeof cfg.code !== "function") {
            ui.code = V.codePanel(sh.side, cfg.code, cfg.codeTitle || "Kode C++");
            ui._codeSrc = cfg.code;
        }
        if (cfg.watch !== false) ui.watch = V.watchPanel(sh.side, cfg.watch || "Variabel");
        for (const p of cfg.panels || []) {
            if (p.type === "ds") ui.panels[p.key] = V.dsPanel(sh.side, p.title, p.hint || "", { vertical: p.vertical });
            else if (p.type === "array") ui.panels[p.key] = V.arrayPanel(sh.side, p.title, p.hint || "");
            else ui.panels[p.key] = V.htmlPanel(sh.side, p.title, p.hint || "");
        }
        let lastHtml = null;
        const player = new V.Player(sh, (f) => {
            const h = f.masked && f.htmlMasked !== undefined ? f.htmlMasked : f.html;
            if (h !== undefined && h !== lastHtml) {
                sh.stage.innerHTML = h;
                lastHtml = h;
            }
            if (ui.code) ui.code.set(f.line);
            if (ui.watch) ui.watch.set(f.watch, f.masked);
            for (const [k, panel] of Object.entries(ui.panels)) {
                const v = f.masked && f[k + "Masked"] !== undefined ? f[k + "Masked"] : f[k];
                panel.set(v === undefined ? (panel.el.querySelector(".ds-items") ? [] : "") : v);
            }
            if (cfg.render) cfg.render(f, ui);
        });
        ui.player = player;
        const rebuild = () => {
            lastHtml = null;
            try {
                player.load(cfg.build(ui));
            } catch (err) {
                console.error(err);
            }
        };
        ui.rebuild = rebuild;
        if (typeof cfg.code === "function") {
            const inner = cfg.build;
            cfg.build = (u) => {
                swapCode(u, cfg.code(u), cfg.codeTitle || "Kode C++");
                return inner(u);
            };
        }
        sh.head.querySelectorAll("[data-apply]").forEach((b) => (b.onclick = rebuild));
        sh.head.querySelectorAll("input").forEach((el) => el.addEventListener("keydown", (e) => e.key === "Enter" && rebuild()));
        sh.head.querySelectorAll("select").forEach((el) => (el.onchange = rebuild));
        sh.head.querySelectorAll(".segmented").forEach((seg) =>
            seg.querySelectorAll("button").forEach((b) =>
                b.addEventListener("click", () => {
                    seg.querySelectorAll("button").forEach((x) => x.classList.toggle("active", x === b));
                    rebuild();
                }),
            ),
        );
        rebuild();
        return ui;
    }

    /** Ganti isi panel kode (misalnya saat mode visualisasi berubah). */
    function swapCode(ui, code, title = "Kode C++") {
        if (ui._codeSrc === code) return;
        ui._codeSrc = code;
        const fresh = V.codePanel(document.createElement("div"), code, title);
        if (ui.code) ui.code.el.replaceWith(fresh.el);
        else ui.side.prepend(fresh.el);
        ui.code = fresh;
    }

    /** Nilai tombol aktif pada kontrol segmented: segVal(ui, "mode") untuk <div class="segmented" data-mode>. */
    const segVal = (ui, name) => ui.head.querySelector(`[data-${name}] button.active`)?.dataset.v;
    const val = (ui, name) => ui.head.querySelector(`[data-${name}]`)?.value ?? "";

    // ═════════════════════════ Pembantu HTML ═════════════════════════
    /**
     * Deretan sel array.
     * opts: cls {i: kelas}, ptr {i: label | [label]}, idx true/false/array label, base (indeks awal),
     *       sub {i: teks kecil di bawah}, mask {i: true} (nilai disamarkan "?"), label (judul baris), size ("sm" | "lg")
     */
    function cells(values, opts = {}) {
        const { cls = {}, ptr = {}, idx = true, base = 0, sub = {}, mask = {}, label, size = "" } = opts;
        const body = values
            .map((v, i) => {
                const p = ptr[i];
                const ptrs = p === undefined ? [] : Array.isArray(p) ? p : [p];
                const ix = idx === false ? "" : Array.isArray(idx) ? idx[i] : i + base;
                const hidden = mask[i];
                return `<div class="kx-cell ${cls[i] || ""} ${hidden ? "ask" : ""}">${ix !== "" ? `<small>${esc(ix)}</small>` : ""}<b>${esc(hidden ? "?" : v)}</b>${
                    sub[i] !== undefined ? `<em>${esc(sub[i])}</em>` : ""
                }${ptrs.length ? `<i class="kx-ptr">${ptrs.map((t) => `<span>${esc(t)}</span>`).join("")}</i>` : ""}</div>`;
            })
            .join("");
        const row = `<div class="kx-row ${size ? "kx-" + size : ""} ${Object.keys(ptr).length ? "has-ptr" : ""}">${body}</div>`;
        return label !== undefined ? line(label, row) : row;
    }

    /** Baris berlabel: label di kiri, isi di kanan. */
    const line = (label, inner, cls = "") => `<div class="kx-line ${cls}"><span class="kx-lab">${label}</span><div class="kx-line-body">${inner}</div></div>`;

    /** Diagram batang. opts: cls {i}, max, labels [], h (tinggi px), ptr {i: label} */
    function bars(values, opts = {}) {
        const { cls = {}, labels, h = 150, ptr = {} } = opts;
        const max = opts.max ?? Math.max(1, ...values.map((v) => Math.abs(v)));
        return `<div class="kx-bars" style="--h:${h}px">${values
            .map((v, i) => {
                const hh = Math.max(4, Math.round((Math.abs(v) / max) * (h - 26)));
                return `<div class="kx-bar ${cls[i] || ""}"><b>${esc(v)}</b><i style="height:${hh}px"></i><small>${esc(labels ? labels[i] : i)}</small>${
                    ptr[i] !== undefined ? `<em>${esc(ptr[i])}</em>` : ""
                }</div>`;
            })
            .join("")}</div>`;
    }

    /**
     * Tumpukan / antrian kotak.
     * items: [nilai | {label, cls, sub}], opts: dir "v" (stack, atas = akhir) | "h" (antrian kiri→kanan),
     * front/back: teks penanda ujung, empty: teks saat kosong, title
     */
    function stack(items, opts = {}) {
        const { dir = "v", front, back, empty = "kosong", title } = opts;
        const list = items.map((it) => (typeof it === "object" && it !== null ? it : { label: it }));
        const shown = dir === "v" ? [...list].reverse() : list;
        const inner = shown.length
            ? shown
                  .map((o) => `<div class="kx-box ${o.cls || ""}"><b>${esc(o.label)}</b>${o.sub !== undefined ? `<small>${esc(o.sub)}</small>` : ""}</div>`)
                  .join("")
            : `<div class="kx-empty">${esc(empty)}</div>`;
        return `<div class="kx-stackwrap ${dir === "v" ? "kx-v" : "kx-h"}">${title ? `<div class="kx-stack-title">${title}</div>` : ""}${
            dir === "h" && front ? `<span class="kx-end">${front}</span>` : ""
        }<div class="kx-stack">${inner}</div>${dir === "h" && back ? `<span class="kx-end">${back}</span>` : ""}${
            dir === "v" && front ? `<span class="kx-end">${front}</span>` : ""
        }</div>`;
    }

    /** Tabel sederhana. rows: array of array. opts: head [], cls {"r,c": kelas}, rowCls {r: kelas} */
    function table(rows, opts = {}) {
        const { head, cls = {}, rowCls = {}, mask = {} } = opts;
        const th = head ? `<tr>${head.map((h) => `<th>${h}</th>`).join("")}</tr>` : "";
        const tb = rows
            .map(
                (r, i) =>
                    `<tr class="${rowCls[i] || ""}">${r
                        .map((c, j) => {
                            const k = `${i},${j}`;
                            return `<td class="${cls[k] || ""} ${mask[k] ? "ask" : ""}">${mask[k] ? "?" : c}</td>`;
                        })
                        .join("")}</tr>`,
            )
            .join("");
        return `<div class="kx-tablewrap"><table class="kx-table">${th}${tb}</table></div>`;
    }

    const chip = (text, cls = "") => `<span class="kx-chip ${cls}">${text}</span>`;

    /**
     * Pohon berakar (SVG).
     * spec: { nodes: [{id, label, sub, cls, parent, edgeCls, edgeLabel}], w, h, r (jari-jari), order: "given" }
     * Anak diurutkan sesuai urutan kemunculan di array nodes. Simpul tanpa parent = akar (boleh lebih dari satu).
     */
    function tree(spec) {
        const { nodes, w = 600, h = 300, r = 17, pad = 28, wide = false } = spec;
        if (!nodes.length) return `<div class="kx-empty">pohon kosong</div>`;
        const byId = new Map(nodes.map((n) => [String(n.id), n]));
        const kids = new Map();
        const roots = [];
        for (const n of nodes) {
            const p = n.parent === undefined || n.parent === null ? null : String(n.parent);
            if (p === null || !byId.has(p)) roots.push(n);
            else {
                if (!kids.has(p)) kids.set(p, []);
                kids.get(p).push(n);
            }
        }
        const pos = new Map();
        let leaf = 0;
        let maxD = 0;
        const place = (n, d) => {
            maxD = Math.max(maxD, d);
            const ch = kids.get(String(n.id)) || [];
            if (!ch.length) {
                pos.set(String(n.id), { x: leaf++, d });
                return;
            }
            ch.forEach((c) => place(c, d + 1));
            const xs = ch.map((c) => pos.get(String(c.id)).x);
            pos.set(String(n.id), { x: (xs[0] + xs[xs.length - 1]) / 2, d });
        };
        roots.forEach((rt) => {
            place(rt, 0);
            leaf += 0.6;
        });
        const span = Math.max(1, leaf - 1.6);
        const sx = (w - pad * 2) / Math.max(1, span);
        const sy = (h - pad * 2 - (spec.subBelow ? 14 : 0)) / Math.max(1, maxD);
        const P = (id) => {
            const p = pos.get(String(id));
            return { x: span < 0.5 ? w / 2 : pad + p.x * sx, y: pad + p.d * (maxD ? sy : 0) };
        };
        let edges = "";
        let labels = "";
        for (const n of nodes) {
            if (n.parent === undefined || n.parent === null || !byId.has(String(n.parent))) continue;
            const a = P(n.parent);
            const b = P(n.id);
            edges += `<line class="kx-tedge ${n.edgeCls || ""}" x1="${a.x}" y1="${a.y}" x2="${b.x}" y2="${b.y}"/>`;
            if (n.edgeLabel !== undefined)
                labels += `<text class="kx-tedge-label ${n.edgeCls || ""}" x="${(a.x + b.x) / 2 + (b.x < a.x ? -8 : 8)}" y="${(a.y + b.y) / 2}">${esc(n.edgeLabel)}</text>`;
        }
        let body = "";
        for (const n of nodes) {
            const p = P(n.id);
            const lab = String(n.label ?? n.id);
            const rw = wide || lab.length > 3 ? Math.max(r, lab.length * 4.4 + 8) : r;
            const shape =
                rw > r
                    ? `<rect x="${-rw}" y="${-r}" width="${rw * 2}" height="${r * 2}" rx="${r * 0.7}"/>`
                    : `<circle r="${r}"/>`;
            body += `<g class="kx-tnode ${n.cls || ""}" transform="translate(${p.x} ${p.y})">${shape}<text>${esc(lab)}</text>${
                n.sub !== undefined ? `<text class="kx-tsub" y="${r + 13}">${esc(n.sub)}</text>` : ""
            }${n.tag !== undefined ? `<text class="kx-ttag" y="${-r - 6}">${esc(n.tag)}</text>` : ""}</g>`;
        }
        return `<svg class="kx-tree" viewBox="0 0 ${w} ${h}" preserveAspectRatio="xMidYMid meet">${edges}${labels}${body}</svg>`;
    }

    /**
     * Graph umum (SVG) dengan posisi simpul yang ditentukan pemanggil.
     * spec: { nodes: [{id, x, y, label, cls, sub, tag, color}], edges: [{u, v, cls, label, bend}], w, h, r, directed }
     * Sisi dua arah (u→v dan v→u) otomatis dilengkungkan agar tidak bertumpuk. color: warna komponen (kelas "col").
     */
    function graph(spec) {
        const { nodes, edges = [], w = 600, h = 300, r = 17, directed = true } = spec;
        const pos = new Map(nodes.map((n) => [String(n.id), n]));
        const has = new Set(edges.map((e) => `${e.u}>${e.v}`));
        const f = (x) => Math.round(x * 10) / 10;
        let ge = "";
        let gl = "";
        for (const e of edges) {
            const a = pos.get(String(e.u));
            const b = pos.get(String(e.v));
            if (!a || !b) continue;
            const cls = e.cls || "";
            if (String(e.u) === String(e.v)) {
                ge += `<path class="kx-gedge ${cls}" d="M${f(a.x - 7)} ${f(a.y - r + 2)} C${f(a.x - 22)} ${f(a.y - r - 30)} ${f(a.x + 22)} ${f(a.y - r - 30)} ${f(a.x + 7)} ${f(a.y - r + 2)}"/>`;
                if (e.label !== undefined) gl += `<text class="kx-gedge-label ${cls}" x="${f(a.x)}" y="${f(a.y - r - 26)}">${esc(e.label)}</text>`;
                continue;
            }
            const dx = b.x - a.x;
            const dy = b.y - a.y;
            const L = Math.hypot(dx, dy) || 1;
            const nx = -dy / L;
            const ny = dx / L;
            const bend = e.bend ?? (directed && has.has(`${e.v}>${e.u}`) ? 16 : 0);
            const mx = (a.x + b.x) / 2 + nx * bend;
            const my = (a.y + b.y) / 2 + ny * bend;
            const s1 = Math.hypot(mx - a.x, my - a.y) || 1;
            const sx = a.x + ((mx - a.x) / s1) * r;
            const sy = a.y + ((my - a.y) / s1) * r;
            const e1 = Math.hypot(b.x - mx, b.y - my) || 1;
            const ux = (b.x - mx) / e1;
            const uy = (b.y - my) / e1;
            const ex = b.x - ux * (r + 1);
            const ey = b.y - uy * (r + 1);
            const lx = directed ? ex - ux * 7 : ex;
            const ly = directed ? ey - uy * 7 : ey;
            ge += `<path class="kx-gedge ${cls}" d="M${f(sx)} ${f(sy)} Q${f(mx)} ${f(my)} ${f(lx)} ${f(ly)}"/>`;
            if (directed) {
                const bx = ex - ux * 10;
                const by = ey - uy * 10;
                ge += `<polygon class="kx-garrow ${cls}" points="${f(ex)},${f(ey)} ${f(bx - uy * 5)},${f(by + ux * 5)} ${f(bx + uy * 5)},${f(by - ux * 5)}"/>`;
            }
            if (e.label !== undefined) {
                const t = e.at ?? 0.5;
                const qx = (1 - t) * (1 - t) * a.x + 2 * (1 - t) * t * mx + t * t * b.x;
                const qy = (1 - t) * (1 - t) * a.y + 2 * (1 - t) * t * my + t * t * b.y;
                const off = e.off ?? 9;
                const tx = qx + nx * (Math.sign(bend) || 1) * off;
                const ty = qy + ny * (Math.sign(bend) || 1) * off + 4;
                gl += `<text class="kx-gedge-label ${cls}" x="${f(tx)}" y="${f(ty)}">${esc(e.label)}</text>`;
            }
        }
        let body = "";
        for (const n of nodes) {
            const lab = String(n.label ?? n.id);
            const rw = lab.length > 3 ? Math.max(r, lab.length * 4.4 + 8) : r;
            const shape = rw > r ? `<rect x="${-rw}" y="${-r}" width="${rw * 2}" height="${r * 2}" rx="${r * 0.7}"/>` : `<circle r="${r}"/>`;
            const style = n.color ? ` style="--c:${n.color}"` : "";
            body += `<g class="kx-tnode ${n.color ? "col" : ""} ${n.cls || ""}"${style} transform="translate(${f(n.x)} ${f(n.y)})">${shape}<text>${esc(lab)}</text>${
                n.sub !== undefined ? `<text class="kx-tsub" y="${r + 13}">${esc(n.sub)}</text>` : ""
            }${n.tag !== undefined ? `<text class="kx-ttag" y="${-r - 6}">${esc(n.tag)}</text>` : ""}</g>`;
        }
        return `<svg class="kx-graph" viewBox="0 0 ${w} ${h}" preserveAspectRatio="xMidYMid meet">${ge}${gl}${body}</svg>`;
    }

    /** Posisi simpul pada lingkaran (untuk graph kecil tanpa struktur khusus). */
    function circle(ids, w = 600, h = 300, pad = 40) {
        const cx = w / 2;
        const cy = h / 2;
        const rx = w / 2 - pad;
        const ry = h / 2 - pad;
        return ids.map((id, i) => {
            const t = -Math.PI / 2 + (2 * Math.PI * i) / Math.max(1, ids.length);
            return { id, x: cx + rx * Math.cos(t), y: cy + ry * Math.sin(t) };
        });
    }

    /**
     * Bidang koordinat (SVG) untuk geometri / garis.
     * spec: { xr: [x0, x1], yr: [y0, y1], w, h, grid: true,
     *         points: [{x, y, label, cls}], segs: [{a: [x, y], b: [x, y], cls}], polys: [{pts: [[x, y]], cls}],
     *         lines: [{m, c, cls, label}] (y = m x + c), vlines: [{x, cls, label}], curve: {f, cls} }
     */
    function plot(spec) {
        const { xr, yr, w = 600, h = 320, pad = 30 } = spec;
        const X = (x) => pad + ((x - xr[0]) / (xr[1] - xr[0])) * (w - 2 * pad);
        const Y = (y) => h - pad - ((y - yr[0]) / (yr[1] - yr[0])) * (h - 2 * pad);
        let g = "";
        if (spec.grid !== false) {
            const stepX = spec.stepX || niceStep(xr[1] - xr[0]);
            const stepY = spec.stepY || niceStep(yr[1] - yr[0]);
            for (let x = Math.ceil(xr[0] / stepX) * stepX; x <= xr[1] + 1e-9; x += stepX)
                g += `<line class="kx-grid ${Math.abs(x) < 1e-9 ? "axis" : ""}" x1="${X(x)}" y1="${Y(yr[0])}" x2="${X(x)}" y2="${Y(yr[1])}"/><text class="kx-axis-t" x="${X(x)}" y="${h - pad + 14}">${+x.toFixed(2)}</text>`;
            for (let y = Math.ceil(yr[0] / stepY) * stepY; y <= yr[1] + 1e-9; y += stepY)
                g += `<line class="kx-grid ${Math.abs(y) < 1e-9 ? "axis" : ""}" x1="${X(xr[0])}" y1="${Y(y)}" x2="${X(xr[1])}" y2="${Y(y)}"/><text class="kx-axis-t" x="${pad - 6}" y="${Y(y) + 4}" text-anchor="end">${+y.toFixed(2)}</text>`;
        }
        for (const p of spec.polys || []) g += `<polygon class="kx-poly ${p.cls || ""}" points="${p.pts.map(([x, y]) => `${X(x)},${Y(y)}`).join(" ")}"/>`;
        if (spec.curve) {
            const pts = [];
            for (let i = 0; i <= 120; i++) {
                const x = xr[0] + ((xr[1] - xr[0]) * i) / 120;
                const y = spec.curve.f(x);
                if (Number.isFinite(y)) pts.push(`${X(x)},${Y(Math.max(yr[0] - 1e9, Math.min(yr[1] + 1e9, y)))}`);
            }
            g += `<polyline class="kx-curve ${spec.curve.cls || ""}" points="${pts.join(" ")}"/>`;
        }
        for (const l of spec.lines || []) {
            const x0 = xr[0];
            const x1 = xr[1];
            g += `<line class="kx-seg ${l.cls || ""}" x1="${X(x0)}" y1="${Y(l.m * x0 + l.c)}" x2="${X(x1)}" y2="${Y(l.m * x1 + l.c)}"/>`;
            if (l.label) g += `<text class="kx-plabel ${l.cls || ""}" x="${X(x1) - 4}" y="${Y(l.m * x1 + l.c) - 6}" text-anchor="end">${esc(l.label)}</text>`;
        }
        for (const v of spec.vlines || []) {
            g += `<line class="kx-vline ${v.cls || ""}" x1="${X(v.x)}" y1="${Y(yr[0])}" x2="${X(v.x)}" y2="${Y(yr[1])}"/>`;
            if (v.label) g += `<text class="kx-plabel ${v.cls || ""}" x="${X(v.x) + 4}" y="${pad + 10}">${esc(v.label)}</text>`;
        }
        for (const s of spec.segs || [])
            g += `<line class="kx-seg ${s.cls || ""}" x1="${X(s.a[0])}" y1="${Y(s.a[1])}" x2="${X(s.b[0])}" y2="${Y(s.b[1])}" ${s.arrow ? 'marker-end="url(#kx-arrow)"' : ""}/>`;
        for (const p of spec.points || []) {
            g += `<g class="kx-pt ${p.cls || ""}" transform="translate(${X(p.x)} ${Y(p.y)})"><circle r="${p.r || 5}"/>${
                p.label !== undefined ? `<text x="8" y="-8">${esc(p.label)}</text>` : ""
            }</g>`;
        }
        return `<svg class="kx-plot" viewBox="0 0 ${w} ${h}" preserveAspectRatio="xMidYMid meet"><defs><marker id="kx-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse"><path d="M0 0 10 5 0 10z" fill="currentColor"/></marker></defs>${g}</svg>`;
    }

    function niceStep(range) {
        const raw = range / 8;
        const p = Math.pow(10, Math.floor(Math.log10(raw)));
        const m = raw / p;
        return (m < 1.5 ? 1 : m < 3.5 ? 2 : m < 7.5 ? 5 : 10) * p;
    }

    /** Daftar bilangan dari teks input, dengan batas. */
    const nums = (str, { min = -99, max = 99, limit = 10, def = [] } = {}) => {
        const a = String(str)
            .split(/[\s,;]+/)
            .filter((x) => x !== "")
            .map(Number)
            .filter((x) => Number.isInteger(x) && x >= min && x <= max)
            .slice(0, limit);
        return a.length ? a : def;
    };

    const bin = (x, w) => (x >>> 0).toString(2).padStart(w, "0");

    return { widget, swapCode, segVal, val, cells, line, bars, stack, table, chip, tree, graph, circle, plot, nums, bin, esc };
})();
