/**
 * AlgoArena Viz Core v2
 *
 * Mesin visualisasi berbasis "frame": setiap algoritma menghasilkan array frame
 * (snapshot keadaan + baris pseudocode + penjelasan), lalu Player memutarnya.
 *
 * Properti frame yang dikenali:
 *   text   : penjelasan langkah (HTML)
 *   line   : indeks baris pseudocode yang menyala
 *   mark   : penanda peristiwa di timeline (key | discover | relax | take | skip | done)
 *   ask    : pertanyaan Mode Tebak sebelum frame ini ditampilkan
 *            { type: "node" | "value" | "choice", prompt, answer, accept?, options?, hint?,
 *              context?, mask?: [r, c], maskBadge?: id, maskKey?: key, maskNode?: id }
 */
window.Viz = (() => {
    "use strict";

    const registry = {};
    const SVG_NS = "http://www.w3.org/2000/svg";
    const W = 640;
    const H = 380;
    const R = 20;

    const esc = (s) =>
        String(s ?? "").replace(
            /[&<>"']/g,
            (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c],
        );
    const stripTags = (s) =>
        String(s ?? "")
            .replace(/<[^>]*>/g, "")
            .replace(/&lt;/g, "<")
            .replace(/&gt;/g, ">")
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'")
            .replace(/&amp;/g, "&");
    const clamp = (x, a, b) => Math.max(a, Math.min(b, x));
    const norm = (x) =>
        String(x ?? "")
            .trim()
            .toLowerCase()
            .replace(/\s+/g, " ");

    function svg(tag, attrs = {}, parent) {
        const el = document.createElementNS(SVG_NS, tag);
        for (const [k, v] of Object.entries(attrs)) el.setAttribute(k, v);
        if (parent) parent.appendChild(el);
        return el;
    }

    function html(str) {
        const t = document.createElement("template");
        t.innerHTML = str.trim();
        return t.content.firstElementChild;
    }

    const COMP_COLORS = [
        ["rgba(139,92,246,.38)", "#a78bfa"],
        ["rgba(34,211,238,.28)", "#22d3ee"],
        ["rgba(236,72,153,.30)", "#f472b6"],
        ["rgba(245,158,11,.30)", "#fbbf24"],
        ["rgba(34,197,94,.30)", "#4ade80"],
        ["rgba(99,102,241,.36)", "#818cf8"],
        ["rgba(248,113,113,.30)", "#f87171"],
        ["rgba(45,212,191,.30)", "#2dd4bf"],
    ];

    /** Warna bertingkat untuk "lapis" (misalnya jarak BFS): cyan → ungu → pink. */
    const LEVEL_COLORS = [
        ["rgba(34,211,238,.40)", "#67e8f9"],
        ["rgba(56,189,248,.36)", "#7dd3fc"],
        ["rgba(99,102,241,.40)", "#a5b4fc"],
        ["rgba(139,92,246,.42)", "#c4b5fd"],
        ["rgba(192,38,211,.38)", "#f0abfc"],
        ["rgba(236,72,153,.40)", "#f9a8d4"],
        ["rgba(244,63,94,.40)", "#fda4af"],
    ];

    // ═════════════════════════ Ikon ═════════════════════════
    const ICONS = {
        first: '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M6 5h2v14H6zM20 5v14L9 12z"/></svg>',
        prev: '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M15 5v14L4 12z"/><rect x="17" y="5" width="2.5" height="14"/></svg>',
        play: '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7 4v16l13-8z"/></svg>',
        pause: '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>',
        next: '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M9 5v14l11-7z"/><rect x="4.5" y="5" width="2.5" height="14"/></svg>',
        last: '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M16 5h2v14h-2zM4 5v14l11-7z"/></svg>',
        full: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg>',
        target: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/></svg>',
        log: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
    };

    // ═════════════════════════ Shell (kerangka kartu visualisasi) ═════════════════════════
    function shell(root, { title, controls = "", legend = [], practice = true }) {
        root.innerHTML = `
            <div class="viz-head">
                <div class="viz-title"><i class="live"></i><span>${title}</span></div>
                <div class="viz-controls-top">${controls}</div>
                <button class="viz-icon-btn" data-fullscreen title="Layar penuh (cocok untuk proyektor)">${ICONS.full}</button>
            </div>
            <div class="viz-body">
                <div class="viz-stage" data-stage></div>
                <div class="viz-side" data-side></div>
            </div>
            <div class="viz-caption" data-caption-box>
                <span class="badge" data-badge>Langkah 1</span>
                <div class="text" data-caption></div>
            </div>
            <div class="viz-prompt" data-prompt hidden></div>
            <div class="viz-player">
                <div class="player-btns">
                    <button class="player-btn" data-act="first" title="Ke awal (Home)">${ICONS.first}</button>
                    <button class="player-btn" data-act="prev" title="Mundur (←)">${ICONS.prev}</button>
                    <button class="player-btn play" data-act="play" title="Putar / jeda (Spasi)">${ICONS.play}</button>
                    <button class="player-btn" data-act="next" title="Maju (→)">${ICONS.next}</button>
                    <button class="player-btn" data-act="last" title="Ke akhir (End)">${ICONS.last}</button>
                </div>
                <div class="timeline">
                    <div class="timeline-track">
                        <div class="timeline-marks" data-marks></div>
                        <input type="range" min="0" max="0" value="0" data-slider aria-label="Langkah">
                    </div>
                    <span class="step-count" data-count>0 / 0</span>
                </div>
                ${
                    practice
                        ? `<button class="practice-toggle" data-practice title="Mode Tebak: tebak langkah berikutnya sebelum ditampilkan">
                               ${ICONS.target}<span>Mode Tebak</span><b data-score hidden>0/0</b>
                           </button>`
                        : ""
                }
                <label class="speed">Kecepatan
                    <select data-speed>
                        <option value="0.5">0.5×</option>
                        <option value="1" selected>1×</option>
                        <option value="1.5">1.5×</option>
                        <option value="2">2×</option>
                        <option value="3">3×</option>
                    </select>
                </label>
            </div>
            ${
                legend.length
                    ? `<div class="viz-legend">${legend
                          .map(([label, color, fill]) => `<span><i style="border-color:${color};background:${fill || "transparent"}"></i>${label}</span>`)
                          .join("")}</div>`
                    : ""
            }
            <details class="viz-log" data-log-wrap>
                <summary>${ICONS.log}<span>Jejak langkah</span><small data-log-count></small></summary>
                <ol class="viz-log-list" data-log></ol>
            </details>`;
        root.tabIndex = 0;
        root.querySelector("[data-fullscreen]").addEventListener("click", () => {
            if (document.fullscreenElement) document.exitFullscreen();
            else if (root.requestFullscreen) root.requestFullscreen().catch(() => {});
        });
        return {
            root,
            head: root.querySelector(".viz-controls-top"),
            stage: root.querySelector("[data-stage]"),
            side: root.querySelector("[data-side]"),
            caption: root.querySelector("[data-caption]"),
            captionBox: root.querySelector("[data-caption-box]"),
            badge: root.querySelector("[data-badge]"),
        };
    }

    // ═════════════════════════ Player ═════════════════════════
    class Player {
        constructor(sh, render) {
            this.sh = sh;
            this.render = render;
            this.frames = [];
            this.i = 0;
            this.timer = null;
            this.practice = false;
            this.score = { ok: 0, total: 0 };
            this.answered = new Set();
            this.pending = null;
            this.attempts = 0;

            const root = sh.root;
            const q = (s) => root.querySelector(s);
            this.slider = q("[data-slider]");
            this.count = q("[data-count]");
            this.playBtn = q('[data-act="play"]');
            this.speed = q("[data-speed]");
            this.marks = q("[data-marks]");
            this.log = q("[data-log]");
            this.logWrap = q("[data-log-wrap]");
            this.logCount = q("[data-log-count]");
            this.promptEl = q("[data-prompt]");
            this.practiceBtn = q("[data-practice]");
            this.scoreEl = q("[data-score]");

            q('[data-act="first"]').onclick = () => this.go(0, true);
            q('[data-act="prev"]').onclick = () => this.go(this.i - 1, true);
            q('[data-act="next"]').onclick = () => {
                this.pause();
                this.step();
            };
            q('[data-act="last"]').onclick = () => this.go(this.frames.length - 1, true);
            this.playBtn.onclick = () => this.toggle();
            this.slider.oninput = () => this.go(+this.slider.value, true);
            this.speed.onchange = () => this.timer && (this.pause(), this.play());
            if (this.practiceBtn) this.practiceBtn.onclick = () => this.setPractice(!this.practice);
            this.log.addEventListener("click", (e) => {
                const li = e.target.closest("li[data-i]");
                if (li) this.go(+li.dataset.i, true);
            });
            this.logWrap.addEventListener("toggle", () => this.highlightLog(true));

            root.addEventListener("keydown", (e) => {
                if (e.target.closest("input, select, textarea")) return;
                if (e.key === "ArrowRight") {
                    e.preventDefault();
                    this.pause();
                    this.step();
                } else if (e.key === "ArrowLeft") {
                    e.preventDefault();
                    this.go(this.i - 1, true);
                } else if (e.key === "Home") {
                    e.preventDefault();
                    this.go(0, true);
                } else if (e.key === "End") {
                    e.preventDefault();
                    this.go(this.frames.length - 1, true);
                } else if (e.key === " ") {
                    e.preventDefault();
                    this.toggle();
                }
            });
        }

        load(frames) {
            this.pause();
            this.closePrompt();
            this.frames = frames;
            this.answered = new Set();
            this.slider.max = Math.max(0, frames.length - 1);
            this.renderMarks();
            this.renderLog();
            this.go(0);
        }

        go(i, user = false) {
            if (user) {
                if (this.timer) this.pause();
                this.closePrompt();
            }
            if (!this.frames.length) return;
            this.i = clamp(i, 0, this.frames.length - 1);
            const f = this.frames[this.i];
            this.slider.value = this.i;
            this.count.textContent = `${this.i + 1} / ${this.frames.length}`;
            this.sh.badge.textContent = `Langkah ${this.i + 1}`;
            this.sh.caption.innerHTML = f.text || "";
            this.sh.captionBox.dataset.mark = f.mark || "";
            this.render(f, this.i);
            this.highlightLog();
        }

        /** Maju satu langkah. Di Mode Tebak, berhenti dulu jika langkah berikutnya punya pertanyaan. */
        step() {
            if (this.pending !== null) return false;
            const t = this.i + 1;
            if (t >= this.frames.length) return false;
            const f = this.frames[t];
            if (this.practice && f.ask && !this.answered.has(t)) {
                this.askFor(t);
                return false;
            }
            this.go(t);
            return true;
        }

        play() {
            if (this.pending !== null) return;
            if (this.i >= this.frames.length - 1) this.go(0);
            this.playBtn.innerHTML = ICONS.pause;
            this.playBtn.classList.add("playing");
            const delay = 1150 / +this.speed.value;
            this.timer = setInterval(() => {
                if (this.pending !== null || this.i >= this.frames.length - 1) return this.pause();
                this.step();
            }, delay);
        }

        pause() {
            clearInterval(this.timer);
            this.timer = null;
            this.playBtn.innerHTML = ICONS.play;
            this.playBtn.classList.remove("playing");
        }

        toggle() {
            this.timer ? this.pause() : this.play();
        }

        // ── Mode Tebak ──
        setPractice(on) {
            this.practice = on;
            this.practiceBtn?.classList.toggle("active", on);
            this.sh.root.classList.toggle("practice", on);
            this.score = { ok: 0, total: 0 };
            this.answered = new Set();
            this.updateScore();
            if (!on && this.pending !== null) {
                this.closePrompt();
                this.go(this.i);
            }
        }

        askFor(t) {
            this.pause();
            const f = this.frames[t];
            const ask = f.ask;
            this.pending = t;
            this.attempts = 0;
            if (ask.type !== "node") {
                // Tampilkan keadaan langkah berikutnya, tetapi jawabannya disamarkan.
                this.render({ ...f, masked: true }, t);
                this.sh.caption.innerHTML = ask.context || "Perhatikan bagian yang ditandai <b>?</b>";
                this.sh.badge.textContent = `Langkah ${t + 1}`;
            }
            const p = this.promptEl;
            p.innerHTML = `
                <div class="prompt-q"><div><b>Tebak dulu!</b> ${ask.prompt}</div></div>
                <div class="prompt-a">
                    ${ask.type === "value" ? '<input class="prompt-input" data-in autocomplete="off" spellcheck="false" placeholder="jawabanmu"><button class="btn btn-sm btn-primary" data-check>Cek</button>' : ""}
                    ${ask.type === "choice" ? ask.options.map((o, k) => `<button class="btn btn-sm prompt-opt" data-opt="${k}">${esc(o)}</button>`).join("") : ""}
                    ${ask.type === "node" ? '<span class="prompt-hint">Klik simpulnya langsung pada graph</span>' : ""}
                    <button class="btn btn-sm btn-ghost" data-reveal>Tunjukkan jawaban</button>
                </div>
                <div class="prompt-fb" data-fb></div>`;
            p.hidden = false;
            this.sh.root.classList.add("asking");
            const input = p.querySelector("[data-in]");
            if (input) {
                input.focus({ preventScroll: true });
                input.addEventListener("keydown", (e) => {
                    e.stopPropagation();
                    if (e.key === "Enter") {
                        e.preventDefault();
                        this.answer(input.value);
                    }
                });
                p.querySelector("[data-check]").onclick = () => this.answer(input.value);
            }
            p.querySelectorAll("[data-opt]").forEach((b) => (b.onclick = () => this.answer(+b.dataset.opt)));
            p.querySelector("[data-reveal]").onclick = () => this.reveal();
        }

        answer(val) {
            if (this.pending === null) return false;
            const t = this.pending;
            const ask = this.frames[t].ask;
            const ok = [ask.answer, ...(ask.accept || [])].some((a) => norm(a) === norm(val));
            if (ok) {
                this.score.ok++;
                this.score.total++;
                this.answered.add(t);
                this.closePrompt();
                this.go(t);
                this.flash("ok");
            } else {
                this.attempts++;
                const fb = this.promptEl.querySelector("[data-fb]");
                fb.className = "prompt-fb bad";
                fb.innerHTML =
                    this.attempts >= 2 && ask.hint ? `Belum tepat. <b>Petunjuk:</b> ${ask.hint}` : "Belum tepat, coba lagi.";
                this.promptEl.classList.remove("shake");
                void this.promptEl.offsetWidth;
                this.promptEl.classList.add("shake");
            }
            this.updateScore();
            return true;
        }

        /** Dipanggil visualizer saat simpul diklik. Mengembalikan true jika klik dipakai untuk menjawab. */
        answerNode(id) {
            if (this.pending === null || this.frames[this.pending].ask.type !== "node") return false;
            this.answer(id);
            return true;
        }

        reveal() {
            if (this.pending === null) return;
            const t = this.pending;
            this.score.total++;
            this.answered.add(t);
            this.closePrompt();
            this.go(t);
            this.flash("reveal");
            this.updateScore();
        }

        closePrompt() {
            this.pending = null;
            this.promptEl.hidden = true;
            this.promptEl.innerHTML = "";
            this.sh.root.classList.remove("asking");
        }

        flash(kind) {
            const box = this.sh.captionBox;
            box.classList.remove("flash-ok", "flash-reveal");
            void box.offsetWidth;
            box.classList.add(kind === "ok" ? "flash-ok" : "flash-reveal");
        }

        updateScore() {
            if (!this.scoreEl) return;
            this.scoreEl.hidden = !this.practice;
            this.scoreEl.textContent = `${this.score.ok}/${this.score.total}`;
        }

        // ── Timeline & jejak langkah ──
        renderMarks() {
            const n = this.frames.length;
            this.marks.innerHTML = this.frames
                .map((f, i) =>
                    f.mark ? `<i class="mk mk-${f.mark}" style="left:${n > 1 ? (i / (n - 1)) * 100 : 0}%" title="Langkah ${i + 1}"></i>` : "",
                )
                .join("");
        }

        renderLog() {
            this.logCount.textContent = `${this.frames.length} langkah`;
            this.log.innerHTML = this.frames
                .map((f, i) => `<li data-i="${i}" class="${f.mark ? "lg-" + f.mark : ""}"><span>${i + 1}</span>${esc(stripTags(f.text))}</li>`)
                .join("");
        }

        highlightLog(force = false) {
            const prev = this.log.querySelector("li.on");
            if (prev) prev.classList.remove("on");
            const cur = this.log.children[this.i];
            if (!cur) return;
            cur.classList.add("on");
            if (this.logWrap.open || force) {
                const box = this.log;
                const top = cur.offsetTop - box.clientHeight / 2;
                box.scrollTop = Math.max(0, top);
            }
        }
    }

    // ═════════════════════════ Panel samping ═════════════════════════
    const KEYWORDS =
        /\b(untuk|setiap|selama|jika|maka|lain|kembalikan|lewati|dari|ke|dan|atau|tidak|masukkan|ambil|hapus|isi|ulangi|selesai|fungsi|tambahkan|kurangi|hasil|urutkan|gabungkan|sampai)\b/g;

    function panel(side, title, hint, inner) {
        const box = html(`<div class="viz-panel"><h4><span>${title}</span><small>${hint || ""}</small></h4>${inner}</div>`);
        side.appendChild(box);
        return box;
    }

    function pseudoPanel(side, title, lines) {
        const box = panel(side, title, "", '<div class="pseudo"></div>');
        const pre = box.querySelector(".pseudo");
        pre.innerHTML = lines.map((l) => `<div>${esc(l).replace(KEYWORDS, '<span class="kw">$1</span>') || " "}</div>`).join("");
        const rows = [...pre.children];
        return {
            el: box,
            set(line) {
                rows.forEach((r, i) => r.classList.toggle("on", i === line));
                const on = rows[line];
                if (on) pre.scrollTop = Math.max(0, on.offsetTop - pre.clientHeight / 2);
            },
        };
    }

    // Kata kunci C++ untuk pewarnaan cadangan (jika highlight.js tidak termuat)
    const CPP_KW =
        /\b(int|long|bool|char|void|double|auto|const|return|for|while|if|else|continue|break|true|false|vector|pair|queue|priority_queue|stack|string|struct|using|namespace|sizeof)\b/g;

    function highlightCpp(line) {
        if (window.hljs) {
            try {
                return hljs.highlight(line, { language: "cpp", ignoreIllegals: true }).value;
            } catch (e) {}
        }
        const [code, comment] = line.split(/(?=\/\/)/);
        return esc(code).replace(CPP_KW, '<span class="kw">$1</span>') + (comment ? `<span class="hljs-comment">${esc(comment)}</span>` : "");
    }

    /**
     * Panel kode C++ yang barisnya ikut menyala sesuai langkah animasi.
     * Tandai baris dengan komentar penanda di ujung baris: "//@3" artinya baris ini
     * menyala saat frame.line === 3 ("//@3,4" untuk beberapa langkah). Penanda tidak ditampilkan.
     */
    function codePanel(side, code, title = "Kode C++", hint = "baris yang sedang dijalankan menyala") {
        const box = panel(side, title, hint, '<div class="pseudo cpp hljs"></div>');
        const pre = box.querySelector(".pseudo");
        const map = {};
        const lines = code.replace(/^\n+|\s+$/g, "").split("\n").map((raw, i) =>
            raw.replace(/\s*\/\/@([\d,]+)\s*$/, (_, ks) => {
                ks.split(",").forEach((k) => (map[k] = map[k] || []).push(i));
                return "";
            }),
        );
        pre.innerHTML = lines.map((l) => `<div>${highlightCpp(l) || " "}</div>`).join("");
        const rows = [...pre.children];
        return {
            el: box,
            set(line) {
                const on = new Set(map[line] || []);
                rows.forEach((r, i) => r.classList.toggle("on", on.has(i)));
                const first = rows[(map[line] || [])[0]];
                if (first) pre.scrollTop = Math.max(0, first.offsetTop - pre.clientHeight / 3);
            },
        };
    }

    function dsPanel(side, title, hint = "", opts = {}) {
        const box = panel(side, title, hint, `<div class="ds-items ${opts.vertical ? "ds-vertical" : ""}"></div>`);
        const items = box.querySelector(".ds-items");
        let last = "";
        return {
            el: box,
            set(list = []) {
                const sig = JSON.stringify(list);
                if (sig === last) return;
                last = sig;
                items.classList.toggle("is-empty", list.length === 0);
                items.innerHTML = list
                    .map((it) => {
                        const o = typeof it === "object" ? it : { label: it };
                        const bar = o.bar !== undefined ? `<i class="ds-bar" style="width:${Math.round(clamp(o.bar, 0, 1) * 100)}%"></i>` : "";
                        return `<span class="ds-item ${o.cls || ""}">${bar}<b>${esc(o.label)}</b>${o.sub !== undefined ? `<small>${esc(o.sub)}</small>` : ""}</span>`;
                    })
                    .join("");
            },
        };
    }

    function arrayPanel(side, title, hint = "") {
        const box = panel(side, title, hint, '<div class="dist-table"></div>');
        const grid = box.querySelector(".dist-table");
        return {
            el: box,
            set(cells = [], maskKey) {
                grid.innerHTML = cells
                    .map((c) => {
                        const masked = maskKey !== undefined && maskKey !== null && String(c.key) === String(maskKey);
                        const val = masked ? "?" : c.val;
                        return `<div class="dist-cell ${masked ? "ask" : c.cls || ""} ${val === "∞" ? "inf" : ""}"><small>${esc(c.key)}</small><b>${esc(val)}</b></div>`;
                    })
                    .join("");
            },
        };
    }

    function htmlPanel(side, title, hint = "") {
        const box = panel(side, title, hint, "<div></div>");
        const body = box.lastElementChild;
        return {
            el: box,
            set(h) {
                if (body._h !== h) {
                    body.innerHTML = h;
                    body._h = h;
                }
            },
        };
    }

    /** Panel variabel ala debugger. entries: [[nama, nilai, rahasia?], ...] */
    function watchPanel(side, title = "Variabel", hint = "nilai saat ini") {
        const box = panel(side, title, hint, '<div class="watch"></div>');
        const body = box.querySelector(".watch");
        let prev = {};
        return {
            el: box,
            set(entries = [], masked = false) {
                if (!entries || !entries.length) {
                    body.innerHTML = '<span class="muted">–</span>';
                    prev = {};
                    return;
                }
                body.innerHTML = entries
                    .map(([k, v, secret]) => {
                        const hide = masked && secret;
                        const chg = !hide && prev[k] !== undefined && String(prev[k]) !== String(v);
                        return `<div class="watch-row ${chg ? "chg" : ""} ${hide ? "ask" : ""}"><span>${esc(k)}</span><b>${esc(hide ? "?" : v)}</b></div>`;
                    })
                    .join("");
                prev = Object.fromEntries(entries.map(([k, v]) => [k, v]));
            },
        };
    }

    // ═════════════════════════ Kontrol umum ═════════════════════════
    const segmented = (name, options, active) =>
        `<div class="segmented" data-${name}>${options
            .map(([v, label]) => `<button data-v="${v}" class="${v === active ? "active" : ""}">${label}</button>`)
            .join("")}</div>`;

    function bindSegmented(head, name, cb) {
        head.querySelectorAll(`[data-${name}] button`).forEach((b) =>
            b.addEventListener("click", () => {
                head.querySelectorAll(`[data-${name}] button`).forEach((x) => x.classList.toggle("active", x === b));
                cb(b.dataset.v);
            }),
        );
    }

    // ═════════════════════════ Graph: pembangkit & layout ═════════════════════════
    const ek = (u, v, directed) => (directed ? `${u}>${v}` : `${Math.min(u, v)}-${Math.max(u, v)}`);

    function rand(a, b) {
        return a + Math.floor(Math.random() * (b - a + 1));
    }

    /** Layout force-directed sederhana (Fruchterman-Reingold) agar graph acak tetap rapi. */
    function forceLayout(n, edges, w = W, h = H, pad = 46) {
        if (n === 1) return [null, { id: 1, x: w / 2, y: h / 2 }];
        const pos = Array.from({ length: n + 1 }, (_, i) => {
            const a = (i / n) * Math.PI * 2;
            return { x: w / 2 + Math.cos(a) * w * 0.3 + Math.random() * 10, y: h / 2 + Math.sin(a) * h * 0.3 + Math.random() * 10 };
        });
        const k = Math.sqrt((w * h) / n) * 0.75;
        let t = w / 8;
        for (let it = 0; it < 320; it++) {
            const disp = pos.map(() => ({ x: 0, y: 0 }));
            for (let i = 1; i <= n; i++) {
                for (let j = i + 1; j <= n; j++) {
                    const dx = pos[i].x - pos[j].x;
                    const dy = pos[i].y - pos[j].y;
                    const d = Math.max(0.01, Math.hypot(dx, dy));
                    const f = (k * k) / d;
                    disp[i].x += (dx / d) * f;
                    disp[i].y += (dy / d) * f;
                    disp[j].x -= (dx / d) * f;
                    disp[j].y -= (dy / d) * f;
                }
            }
            for (const e of edges) {
                const dx = pos[e.u].x - pos[e.v].x;
                const dy = pos[e.u].y - pos[e.v].y;
                const d = Math.max(0.01, Math.hypot(dx, dy));
                const f = (d * d) / k;
                disp[e.u].x -= (dx / d) * f;
                disp[e.u].y -= (dy / d) * f;
                disp[e.v].x += (dx / d) * f;
                disp[e.v].y += (dy / d) * f;
            }
            for (let i = 1; i <= n; i++) {
                const d = Math.max(0.01, Math.hypot(disp[i].x, disp[i].y));
                pos[i].x += (disp[i].x / d) * Math.min(d, t);
                pos[i].y += (disp[i].y / d) * Math.min(d, t);
                pos[i].x += (w / 2 - pos[i].x) * 0.01;
                pos[i].y += (h / 2 - pos[i].y) * 0.01;
            }
            t *= 0.985;
        }
        const xs = pos.slice(1).map((p) => p.x);
        const ys = pos.slice(1).map((p) => p.y);
        const [minX, maxX, minY, maxY] = [Math.min(...xs), Math.max(...xs), Math.min(...ys), Math.max(...ys)];
        const sx = (w - pad * 2) / Math.max(1, maxX - minX);
        const sy = (h - pad * 2 - 20) / Math.max(1, maxY - minY);
        return pos.map((p, i) => (i === 0 ? null : { id: i, x: pad + (p.x - minX) * sx, y: pad + 20 + (p.y - minY) * sy }));
    }

    /** Layout berlapis untuk DAG (kiri → kanan sesuai panjang jalur terpanjang). */
    function layeredLayout(n, edges, w = W, h = H, pad = 50) {
        const indeg = new Array(n + 1).fill(0);
        const adj = Array.from({ length: n + 1 }, () => []);
        edges.forEach((e) => {
            adj[e.u].push(e.v);
            indeg[e.v]++;
        });
        const layer = new Array(n + 1).fill(0);
        const q = [];
        for (let i = 1; i <= n; i++) if (!indeg[i]) q.push(i);
        for (let hd = 0; hd < q.length; hd++) {
            const u = q[hd];
            for (const v of adj[u]) {
                layer[v] = Math.max(layer[v], layer[u] + 1);
                if (--indeg[v] === 0) q.push(v);
            }
        }
        const maxL = Math.max(0, ...layer.slice(1));
        const groups = {};
        for (let i = 1; i <= n; i++) (groups[layer[i]] ||= []).push(i);
        const pos = [null];
        for (const [l, ids] of Object.entries(groups)) {
            ids.forEach((id, j) => {
                pos[id] = {
                    id,
                    x: pad + (maxL ? (l / maxL) * (w - pad * 2) : (w - pad * 2) / 2),
                    y: pad + 16 + ((j + 1) / (ids.length + 1)) * (h - pad * 2 - 16) + (l % 2 && ids.length > 1 ? 14 : 0),
                };
            });
        }
        return pos;
    }

    /** Layout pohon berakar: akar di atas, kedalaman menentukan y, urutan daun menentukan x. */
    function treeLayout(n, edges, root = 1, w = W, h = H, pad = 40) {
        const adj = Array.from({ length: n + 1 }, () => []);
        edges.forEach((e) => {
            adj[e.u].push(e.v);
            adj[e.v].push(e.u);
        });
        adj.forEach((l) => l.sort((a, b) => a - b));
        const depth = new Array(n + 1).fill(-1);
        const x = new Array(n + 1).fill(0);
        let leaf = 0;
        let maxD = 0;
        const place = (u, d) => {
            // DFS iteratif agar aman untuk pohon yang dalam
            const stack = [[u, d, 0]];
            depth[u] = d;
            const kids = {};
            while (stack.length) {
                const top = stack[stack.length - 1];
                const [v, dv] = top;
                maxD = Math.max(maxD, dv);
                const next = adj[v].find((c) => depth[c] === -1);
                if (next !== undefined) {
                    depth[next] = dv + 1;
                    (kids[v] ||= []).push(next);
                    stack.push([next, dv + 1, 0]);
                } else {
                    stack.pop();
                    const ch = kids[v] || [];
                    x[v] = ch.length ? (x[ch[0]] + x[ch[ch.length - 1]]) / 2 : leaf++;
                }
            }
        };
        place(root, 0);
        for (let i = 1; i <= n; i++) if (depth[i] === -1) place(i, 0);
        const sx = (w - pad * 2) / Math.max(1, leaf - 1);
        const sy = (h - pad * 2 - 24) / Math.max(1, maxD);
        return Array.from({ length: n + 1 }, (_, i) =>
            i === 0
                ? null
                : { id: i, x: leaf > 1 ? pad + x[i] * sx : w / 2, y: pad + 24 + depth[i] * (maxD ? sy : 0) },
        );
    }

    function randomGraph({ n = 7, m = 9, directed = false, weighted = false, connected = true, dag = false, maxW = 9 }) {
        const seen = new Set();
        const edges = [];
        const add = (u, v) => {
            if (u === v) return false;
            if (dag && u > v) [u, v] = [v, u];
            const key = directed || dag ? `${u}>${v}` : ek(u, v, false);
            const rev = directed || dag ? `${v}>${u}` : key;
            if (seen.has(key) || seen.has(rev)) return false;
            seen.add(key);
            edges.push({ u, v, w: weighted ? rand(1, maxW) : 1 });
            return true;
        };
        if (connected) for (let i = 2; i <= n; i++) add(rand(1, i - 1), i);
        let guard = 0;
        while (edges.length < m && guard++ < 500) add(rand(1, n), rand(1, n));
        let nodes;
        if (dag) {
            const perm = Array.from({ length: n }, (_, i) => i + 1).sort(() => Math.random() - 0.5);
            edges.forEach((e) => {
                e.u = perm[e.u - 1];
                e.v = perm[e.v - 1];
            });
            nodes = layeredLayout(n, edges);
        } else {
            nodes = forceLayout(n, edges);
        }
        return { n, nodes, edges, directed: directed || dag, weighted };
    }

    /** Graph dari daftar sisi (dipakai juga untuk visualisasi contoh soal). */
    function graphFromEdges(n, edges, { directed = false, weighted = false, layered = false, tree = false, root = 1 } = {}) {
        const list = edges.map(([u, v, w]) => ({ u, v, w: w ?? 1 }));
        const nodes = tree ? treeLayout(n, list, root) : layered ? layeredLayout(n, list) : forceLayout(n, list);
        return { n, nodes, edges: list, directed, weighted };
    }

    function adjacency(g) {
        const adj = Array.from({ length: g.n + 1 }, () => []);
        for (const e of g.edges) {
            adj[e.u].push({ v: e.v, w: e.w });
            if (!g.directed) adj[e.v].push({ v: e.u, w: e.w });
        }
        adj.forEach((list) => list.sort((a, b) => a.v - b.v));
        return adj;
    }

    // ═════════════════════════ Graph: renderer SVG interaktif ═════════════════════════
    const MARKER_COLORS = { default: "var(--edge)", active: "#f59e0b", tree: "#8b5cf6", path: "#22c55e", skip: "#ef4444", new: "#22d3ee" };
    const NODE_GRADS = {
        base: ["var(--node-a)", "var(--node-b)"],
        queued: ["#1d7189", "#0b2731"],
        current: ["#fff3c4", "#f59e0b"],
        visited: ["#8d72ee", "#382679"],
        done: ["#a184ff", "#4e30b6"],
        path: ["#86efac", "#15803d"],
    };

    function graphView(stage, opts = {}) {
        stage.innerHTML = "";
        stage.classList.add("graph-stage");
        const root = svg("svg", { class: "graph", viewBox: `0 0 ${W} ${H}` }, stage);
        const defs = svg("defs", {}, root);
        for (const [name, color] of Object.entries(MARKER_COLORS)) {
            const m = svg(
                "marker",
                { id: `aa-arrow-${name}`, viewBox: "0 0 10 10", refX: "8.5", refY: "5", markerWidth: "6.5", markerHeight: "6.5", orient: "auto-start-reverse" },
                defs,
            );
            svg("path", { d: "M0 0 10 5 0 10z", style: `fill:${color}` }, m);
        }
        for (const [name, [a, b]] of Object.entries(NODE_GRADS)) {
            const g = svg("radialGradient", { id: `aa-ng-${name}`, cx: "38%", cy: "30%", r: "78%" }, defs);
            svg("stop", { offset: "0%", style: `stop-color:${a}` }, g);
            svg("stop", { offset: "100%", style: `stop-color:${b}` }, g);
        }
        const glow = svg("filter", { id: "aa-glow", x: "-80%", y: "-80%", width: "260%", height: "260%" }, defs);
        svg("feGaussianBlur", { stdDeviation: "3.5", result: "b" }, glow);
        const merge = svg("feMerge", {}, glow);
        svg("feMergeNode", { in: "b" }, merge);
        svg("feMergeNode", { in: "SourceGraphic" }, merge);

        const gEdges = svg("g", { class: "g-edges" }, root);
        const gWeights = svg("g", { class: "g-weights" }, root);
        const gFx = svg("g", { class: "g-fx" }, root);
        const gNodes = svg("g", { class: "g-nodes" }, root);

        let graph = null;
        let edgeEls = {};
        let nodeEls = {};
        let clickCb = null;
        let lastFrame = {};
        let mode = "move";
        let pick = null;
        let drag = null;
        const maxNodes = opts.maxNodes || 12;

        const hintEl = html('<div class="viz-hint"></div>');
        stage.appendChild(hintEl);
        const setHint = (t) => {
            hintEl.textContent = t || "";
            hintEl.hidden = !t;
        };

        let tools = null;
        if (opts.editable) {
            tools = html(`
                <div class="graph-tools" role="toolbar" aria-label="Alat edit graph">
                    <button data-gmode="move" class="active" title="Geser: seret simpul untuk merapikan">✥<span>Geser</span></button>
                    <button data-gmode="node" title="Tambah simpul: klik area kosong">＋<span>Simpul</span></button>
                    <button data-gmode="edge" title="Tambah / hapus sisi: klik dua simpul">⟷<span>Sisi</span></button>
                    <button data-gmode="erase" title="Hapus: klik simpul atau sisi">⌫<span>Hapus</span></button>
                    <label class="gt-weight" data-gweight hidden>bobot <input type="number" min="1" max="99" value="5"></label>
                    <button data-gclear title="Hapus semua sisi">Kosongkan</button>
                </div>`);
            stage.appendChild(tools);
            tools.querySelectorAll("[data-gmode]").forEach((b) => b.addEventListener("click", () => setMode(b.dataset.gmode)));
            tools.querySelector("[data-gclear]").addEventListener("click", () => {
                if (!graph || !graph.edges.length) return;
                graph.edges = [];
                changed();
            });
        }

        const MODE_HINT = {
            move: opts.hint || "Seret simpul untuk merapikan graph",
            node: `Klik area kosong untuk menambah simpul (maks. ${maxNodes})`,
            edge: "Klik dua simpul untuk menambah sisi · klik pasangan yang sama untuk menghapusnya",
            erase: "Klik simpul atau sisi untuk menghapusnya",
        };
        setHint(MODE_HINT.move);

        function setMode(m) {
            mode = m;
            pick = null;
            stage.dataset.gmode = m;
            tools?.querySelectorAll("[data-gmode]").forEach((b) => b.classList.toggle("active", b.dataset.gmode === m));
            setHint(MODE_HINT[m]);
            update(lastFrame);
        }

        function geom(u, v, curve = 0) {
            const a = graph.nodes[u];
            const b = graph.nodes[v];
            const dx = b.x - a.x;
            const dy = b.y - a.y;
            const d = Math.hypot(dx, dy) || 1;
            const nx = -dy / d;
            const ny = dx / d;
            const cx = (a.x + b.x) / 2 + nx * curve;
            const cy = (a.y + b.y) / 2 + ny * curve;
            const sd = Math.hypot(cx - a.x, cy - a.y) || 1;
            const ed = Math.hypot(b.x - cx, b.y - cy) || 1;
            const trimEnd = graph.directed ? R + 3 : R;
            const sx = a.x + ((cx - a.x) / sd) * R;
            const sy = a.y + ((cy - a.y) / sd) * R;
            const tx = b.x - ((b.x - cx) / ed) * trimEnd;
            const ty = b.y - ((b.y - cy) / ed) * trimEnd;
            const mx = 0.25 * sx + 0.5 * cx + 0.25 * tx;
            const my = 0.25 * sy + 0.5 * cy + 0.25 * ty;
            const off = curve ? 0 : 13;
            return {
                trimmed: `M${sx} ${sy} Q${cx} ${cy} ${tx} ${ty}`,
                full: `M${a.x} ${a.y} Q${cx} ${cy} ${b.x} ${b.y}`,
                lx: mx + nx * off,
                ly: my + ny * off,
            };
        }

        const hasTwin = (u, v) => graph.directed && graph.edges.some((f) => f.u === v && f.v === u);

        function makeNode(i) {
            const grp = svg("g", { class: "g-node" }, gNodes);
            grp.dataset.id = i;
            svg("circle", { class: "halo", r: R + 10 }, grp);
            svg("circle", { class: "ring", r: R + 5 }, grp);
            svg("circle", { class: "body", r: R }, grp);
            svg("circle", { class: "shine", r: R - 3, cy: -2 }, grp);
            const label = svg("text", { class: "label" }, grp);
            label.textContent = i;
            const badge = svg("g", { class: "g-badge", transform: "translate(0 -35)" }, grp);
            const br = svg("rect", { x: -16, y: -10, width: 32, height: 20, rx: 8 }, badge);
            const bt = svg("text", {}, badge);
            badge.style.display = "none";
            return { grp, badge, br, bt };
        }

        function draw(g) {
            graph = g;
            pick = null;
            gEdges.innerHTML = "";
            gWeights.innerHTML = "";
            gFx.innerHTML = "";
            gNodes.innerHTML = "";
            edgeEls = {};
            nodeEls = {};
            if (tools) tools.querySelector("[data-gweight]").hidden = !g.weighted;
            for (const e of g.edges) {
                const key = ek(e.u, e.v, g.directed);
                const grp = svg("g", { class: "g-edge" }, gEdges);
                grp.dataset.key = key;
                const hit = svg("path", { class: "g-hit" }, grp);
                const line = svg("path", { class: "g-line" }, grp);
                let weight = null;
                let wr = null;
                let wt = null;
                if (g.weighted) {
                    weight = svg("g", { class: "g-weight" }, gWeights);
                    wr = svg("rect", { width: 24, height: 20, rx: 7 }, weight);
                    wt = svg("text", {}, weight);
                    wt.textContent = e.w;
                }
                edgeEls[key] = { e, grp, hit, line, weight, wr, wt };
            }
            for (let i = 1; i <= g.n; i++) nodeEls[i] = makeNode(i);
            position();
            lastFrame = {};
            update({});
        }

        function position() {
            if (!graph) return;
            for (const [id, el] of Object.entries(nodeEls)) {
                const p = graph.nodes[id];
                el.grp.setAttribute("transform", `translate(${p.x} ${p.y})`);
            }
            for (const el of Object.values(edgeEls)) {
                const { e } = el;
                const gm = geom(e.u, e.v, hasTwin(e.u, e.v) ? 26 : 0);
                el.line.setAttribute("d", graph.directed ? gm.trimmed : gm.full);
                el.hit.setAttribute("d", gm.full);
                if (el.weight) {
                    const w = Math.max(24, String(el.wt.textContent).length * 8 + 12);
                    el.wr.setAttribute("x", gm.lx - w / 2);
                    el.wr.setAttribute("y", gm.ly - 10);
                    el.wr.setAttribute("width", w);
                    el.wt.setAttribute("x", gm.lx);
                    el.wt.setAttribute("y", gm.ly);
                }
            }
        }

        function update(f = {}) {
            lastFrame = f;
            if (!graph) return;
            const nodes = f.nodes || {};
            const badges = f.badges || {};
            const colors = f.colors || {};
            const maskBadge = f.masked && f.ask ? f.ask.maskBadge : undefined;
            for (const [id, el] of Object.entries(nodeEls)) {
                const col = colors[id];
                el.grp.setAttribute("class", `g-node ${nodes[id] || ""}${col ? " colored" : ""}${+id === pick ? " pick" : ""}`);
                if (col) {
                    el.grp.style.setProperty("--node-color", col[0]);
                    el.grp.style.setProperty("--node-stroke", col[1]);
                }
                let b = badges[id];
                if (maskBadge !== undefined && +id === +maskBadge) b = { text: "?", cls: "ask" };
                if (b === undefined || b === null || b === "") {
                    el.badge.style.display = "none";
                } else {
                    const o = typeof b === "object" ? b : { text: b };
                    el.badge.style.display = "";
                    el.badge.setAttribute("class", `g-badge ${o.cls || ""}`);
                    el.bt.textContent = o.text;
                    const w = Math.max(26, String(o.text).length * 8 + 12);
                    el.br.setAttribute("x", -w / 2);
                    el.br.setAttribute("width", w);
                }
            }
            const edges = f.edges || {};
            for (const [key, el] of Object.entries(edgeEls)) {
                const cls = edges[key] || "";
                el.grp.setAttribute("class", `g-edge ${cls}`);
                if (el.weight) el.weight.setAttribute("class", `g-weight ${cls}`);
                if (el.wt) {
                    // f.wlabels: teks label sisi per frame (mis. "aliran/kapasitas")
                    const txt = String(f.wlabels && f.wlabels[key] !== undefined ? f.wlabels[key] : el.e.w);
                    if (el.wt.textContent !== txt) {
                        el.wt.textContent = txt;
                        const w = Math.max(24, txt.length * 8 + 12);
                        const cx = +el.wt.getAttribute("x");
                        el.wr.setAttribute("x", cx - w / 2);
                        el.wr.setAttribute("width", w);
                    }
                }
                if (graph.directed) {
                    const state = ["active", "path", "tree", "skip", "new"].find((s) => cls.includes(s)) || "default";
                    el.line.setAttribute("marker-end", `url(#aa-arrow-${state})`);
                }
            }
            gFx.innerHTML = "";
            if (f.flow && !f.masked) spawnFlow(f.flow[0], f.flow[1], f.flowCls || "");
            (f.pulse || []).forEach(spawnRipple);
        }

        /** Titik cahaya yang bergerak dari u ke v: menunjukkan arah penjelajahan. */
        function spawnFlow(u, v, cls) {
            if (!graph.nodes[u] || !graph.nodes[v]) return;
            const gm = geom(u, v, hasTwin(u, v) ? 26 : 0);
            const trail = svg("path", { class: `g-flow-trail ${cls}`, d: gm.trimmed }, gFx);
            const len = trail.getTotalLength ? trail.getTotalLength() : 100;
            trail.style.strokeDasharray = `${len}`;
            trail.style.strokeDashoffset = `${len}`;
            requestAnimationFrame(() => {
                trail.style.transition = "stroke-dashoffset .7s cubic-bezier(.45,0,.2,1), opacity .4s .7s";
                trail.style.strokeDashoffset = "0";
                trail.style.opacity = "0";
            });
            const dot = svg("circle", { class: `g-flow ${cls}`, r: 6 }, gFx);
            const anim = svg(
                "animateMotion",
                { dur: "0.7s", begin: "indefinite", fill: "freeze", path: gm.trimmed, keyPoints: "0;1", keyTimes: "0;1", calcMode: "spline", keySplines: "0.45 0 0.2 1" },
                dot,
            );
            try {
                anim.beginElement();
            } catch (e) {
                dot.remove();
            }
        }

        function spawnRipple(id) {
            const p = graph.nodes[id];
            if (!p) return;
            const c = svg("circle", { class: "g-ripple", cx: p.x, cy: p.y, r: R }, gFx);
            const a1 = svg("animate", { attributeName: "r", from: R, to: R + 26, dur: "0.8s", begin: "indefinite", fill: "freeze" }, c);
            const a2 = svg("animate", { attributeName: "opacity", from: "0.9", to: "0", dur: "0.8s", begin: "indefinite", fill: "freeze" }, c);
            try {
                a1.beginElement();
                a2.beginElement();
            } catch (e) {
                c.remove();
            }
        }

        // ── Interaksi: geser simpul & edit graph ──
        function toSvg(evt) {
            const m = root.getScreenCTM();
            if (!m) return { x: 0, y: 0 };
            const p = new DOMPoint(evt.clientX, evt.clientY).matrixTransform(m.inverse());
            return { x: p.x, y: p.y };
        }

        function changed() {
            const g = graph;
            draw(g);
            if (opts.onEdit) opts.onEdit(g);
        }

        function addNode(x, y) {
            if (graph.n >= maxNodes) return setHint(`Maksimal ${maxNodes} simpul agar visualisasi tetap jelas`);
            graph.n++;
            graph.nodes[graph.n] = { id: graph.n, x: clamp(x, R + 6, W - R - 6), y: clamp(y, R + 32, H - R - 6) };
            changed();
        }

        function pickNode(id) {
            if (pick === null) {
                pick = id;
                setHint(`Simpul ${id} dipilih. Klik simpul tujuan untuk ${graph.directed ? "membuat sisi berarah" : "menghubungkannya"}`);
                return update(lastFrame);
            }
            if (pick === id) {
                pick = null;
                setHint(MODE_HINT.edge);
                return update(lastFrame);
            }
            const u = pick;
            pick = null;
            const idx = graph.edges.findIndex((e) =>
                graph.directed ? e.u === u && e.v === id : (e.u === u && e.v === id) || (e.u === id && e.v === u),
            );
            if (idx >= 0) {
                graph.edges.splice(idx, 1);
            } else {
                const input = tools?.querySelector("[data-gweight] input");
                const w = graph.weighted ? clamp(parseInt(input?.value, 10) || 1, 1, 99) : 1;
                graph.edges.push({ u, v: id, w });
            }
            setHint(MODE_HINT.edge);
            changed();
        }

        function removeNode(id) {
            if (graph.n <= 1) return setHint("Graph harus punya minimal 1 simpul");
            graph.edges = graph.edges
                .filter((e) => e.u !== id && e.v !== id)
                .map((e) => ({ ...e, u: e.u > id ? e.u - 1 : e.u, v: e.v > id ? e.v - 1 : e.v }));
            graph.nodes.splice(id, 1);
            graph.nodes.forEach((p, i) => p && (p.id = i));
            graph.n--;
            changed();
        }

        function removeEdge(key) {
            graph.edges = graph.edges.filter((e) => ek(e.u, e.v, graph.directed) !== key);
            changed();
        }

        root.addEventListener("pointerdown", (e) => {
            if (!graph) return;
            const nodeG = e.target.closest(".g-node");
            const edgeG = e.target.closest(".g-edge");
            if (nodeG) {
                const id = +nodeG.dataset.id;
                if (mode === "erase") return removeNode(id);
                if (mode === "edge") return pickNode(id);
                drag = { id, x0: e.clientX, y0: e.clientY, moved: false };
                root.setPointerCapture(e.pointerId);
                e.preventDefault();
                return;
            }
            if (edgeG && mode === "erase") return removeEdge(edgeG.dataset.key);
            if (mode === "node") {
                const p = toSvg(e);
                addNode(p.x, p.y);
            }
        });
        root.addEventListener("pointermove", (e) => {
            if (!drag) return;
            if (!drag.moved && Math.hypot(e.clientX - drag.x0, e.clientY - drag.y0) < 5) return;
            drag.moved = true;
            stage.classList.add("dragging-node");
            const p = toSvg(e);
            const node = graph.nodes[drag.id];
            node.x = clamp(p.x, R + 6, W - R - 6);
            node.y = clamp(p.y, R + 32, H - R - 6);
            position();
            gFx.innerHTML = "";
        });
        const endDrag = () => {
            if (!drag) return;
            const { id, moved } = drag;
            drag = null;
            stage.classList.remove("dragging-node");
            if (!moved && clickCb) clickCb(id);
        };
        root.addEventListener("pointerup", endDrag);
        root.addEventListener("pointercancel", () => {
            drag = null;
            stage.classList.remove("dragging-node");
        });

        return {
            draw,
            update,
            setMode,
            setHint,
            onNodeClick(cb) {
                clickCb = cb;
            },
            get graph() {
                return graph;
            },
        };
    }

    // ═════════════════════════ Tabel DP ═════════════════════════
    function tableView(stage, cfg) {
        const { rows, cols, rowHead = [], colHead = [], corner = "", cell = 46, colHeadBig = false, rowHeadBig = false } = cfg;
        let heatOn = !!cfg.heat;
        stage.innerHTML = "";
        stage.classList.remove("graph-stage");
        const wrap = html('<div class="dp-wrap"></div>');
        stage.appendChild(wrap);
        const hasRowHead = rowHead.length > 0;
        const hasColHead = colHead.length > 0;
        const grid = html(
            `<div class="dp-grid" style="--cell:${cell}px; grid-template-columns: repeat(${cols + (hasRowHead ? 1 : 0)}, var(--cell))"></div>`,
        );
        wrap.appendChild(grid);
        const cells = [];
        const rowHeads = [];
        const colHeads = [];
        if (hasColHead) {
            if (hasRowHead) grid.appendChild(html(`<div class="dp-cell head">${esc(corner)}</div>`));
            for (let c = 0; c < cols; c++) {
                const h = html(`<div class="dp-cell head ${colHeadBig ? "big" : ""}">${esc(colHead[c] ?? "")}</div>`);
                grid.appendChild(h);
                colHeads.push(h);
            }
        }
        for (let r = 0; r < rows; r++) {
            if (hasRowHead) {
                const h = html(`<div class="dp-cell head ${rowHeadBig ? "big" : ""}">${esc(rowHead[r] ?? "")}</div>`);
                grid.appendChild(h);
                rowHeads.push(h);
            }
            cells.push([]);
            for (let c = 0; c < cols; c++) {
                const el = html('<div class="dp-cell is-empty"></div>');
                el.dataset.r = r;
                el.dataset.c = c;
                grid.appendChild(el);
                cells[r].push(el);
            }
        }
        const arrows = svg("svg", { class: "dp-arrows" }, wrap);
        const defs = svg("defs", {}, arrows);
        for (const [id, color] of [
            ["aa-dp-a", "#22d3ee"],
            ["aa-dp-b", "#ec4899"],
            ["aa-dp-g", "#22c55e"],
        ]) {
            const m = svg("marker", { id, viewBox: "0 0 10 10", refX: "8", refY: "5", markerWidth: "6", markerHeight: "6", orient: "auto-start-reverse" }, defs);
            svg("path", { d: "M0 0 10 5 0 10z", style: `fill:${color}` }, m);
        }
        const arrowLayer = svg("g", {}, arrows);
        let clickCb = null;
        let last = null;
        grid.addEventListener("click", (e) => {
            const el = e.target.closest(".dp-cell:not(.head)");
            if (el && clickCb) clickCb(+el.dataset.r, +el.dataset.c);
        });

        const center = (el) => ({ x: el.offsetLeft + el.offsetWidth / 2, y: el.offsetTop + el.offsetHeight / 2 });

        function update(f = {}) {
            last = f;
            const vals = f.vals || [];
            const cls = f.cls || {};
            const mask = f.masked && f.ask && f.ask.mask ? f.ask.mask.join(",") : null;
            let max = 0;
            if (heatOn) for (const row of vals) for (const v of row || []) if (typeof v === "number" && isFinite(v)) max = Math.max(max, v);
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const el = cells[r][c];
                    const key = `${r},${c}`;
                    let v = vals[r] ? vals[r][c] : null;
                    let k = cls[key] || "";
                    if (key === mask) {
                        v = "?";
                        k += " ask";
                    }
                    const text = v === null || v === undefined ? "" : String(v);
                    const special = /current|dep|path|block|base|ask/.test(k);
                    el.className = `dp-cell ${text === "" && !k.includes("block") ? "is-empty" : "filled"} ${k}${clickCb ? " clickable" : ""}`;
                    if (heatOn && !special && typeof v === "number" && isFinite(v) && max > 0) {
                        const h = v / max;
                        el.style.background = `rgba(139, 92, 246, ${(0.05 + h * 0.5).toFixed(3)})`;
                        el.style.borderColor = `rgba(167, 139, 250, ${(0.12 + h * 0.5).toFixed(3)})`;
                    } else {
                        el.style.background = "";
                        el.style.borderColor = "";
                    }
                    if (el.textContent !== text) {
                        el.textContent = text;
                        if (text !== "" && k.includes("current")) {
                            el.classList.remove("pop");
                            void el.offsetWidth;
                            el.classList.add("pop");
                        }
                    }
                }
            }
            rowHeads.forEach((h, i) => h.classList.toggle("hl", f.hlRow === i));
            colHeads.forEach((h, i) => h.classList.toggle("hl", f.hlCol === i || (f.hlCols || []).includes(i)));

            arrowLayer.innerHTML = "";
            arrows.setAttribute("width", wrap.scrollWidth);
            arrows.setAttribute("height", wrap.scrollHeight);
            if (f.pathLine && f.pathLine.length > 1) {
                const pts = f.pathLine
                    .filter(([r, c]) => cells[r] && cells[r][c])
                    .map(([r, c]) => center(cells[r][c]))
                    .map((p) => `${p.x},${p.y}`)
                    .join(" ");
                svg("polyline", { class: "dp-path-line", points: pts }, arrowLayer);
            }
            for (const a of f.arrows || []) {
                const from = cells[a.from[0]]?.[a.from[1]];
                const to = cells[a.to[0]]?.[a.to[1]];
                if (!from || !to) continue;
                const p = center(from);
                const q = center(to);
                const dx = q.x - p.x;
                const dy = q.y - p.y;
                const d = Math.hypot(dx, dy) || 1;
                const x1 = p.x + (dx / d) * cell * 0.25;
                const y1 = p.y + (dy / d) * cell * 0.25;
                const x2 = q.x - (dx / d) * cell * 0.42;
                const y2 = q.y - (dy / d) * cell * 0.42;
                const bend = a.bend ?? (Math.abs(dy) < 2 || Math.abs(dx) < 2 ? 0 : 0.15);
                const cx = (x1 + x2) / 2 - (y2 - y1) * bend;
                const cy = (y1 + y2) / 2 + (x2 - x1) * bend;
                const color = a.green ? "g" : a.alt ? "b" : "a";
                svg("path", { d: `M${x1} ${y1} Q${cx} ${cy} ${x2} ${y2}`, class: `dp-arrow ${color}`, "marker-end": `url(#aa-dp-${color})` }, arrowLayer);
                if (a.label) {
                    const t = svg("text", { class: `dp-arrow-label ${color}`, x: cx, y: cy - 5 }, arrowLayer);
                    t.textContent = a.label;
                }
            }
        }

        return {
            update,
            onCellClick(cb) {
                clickCb = cb;
            },
            setHeat(on) {
                heatOn = on;
                if (last) update(last);
            },
        };
    }

    // ═════════════════════════ Util input ═════════════════════════
    function parseList(str, { min = 1, max = 99, limit = 8 } = {}) {
        return String(str)
            .split(/[\s,]+/)
            .map(Number)
            .filter((x) => Number.isInteger(x) && x >= min && x <= max)
            .slice(0, limit);
    }

    function clampInt(v, a, b, d) {
        const n = parseInt(v, 10);
        return Number.isFinite(n) ? Math.max(a, Math.min(b, n)) : d;
    }

    function register(name, factory) {
        registry[name] = factory;
    }

    function mountAll() {
        document.querySelectorAll("[data-viz]").forEach((el) => {
            const f = registry[el.dataset.viz];
            if (!f || el.dataset.mounted) return;
            el.dataset.mounted = "1";
            el.classList.add("viz");
            try {
                f(el);
            } catch (err) {
                console.error(err);
                el.innerHTML = '<p class="empty">Visualisasi gagal dimuat.</p>';
            }
        });
    }

    document.addEventListener("DOMContentLoaded", mountAll);

    return {
        register,
        mountAll,
        shell,
        Player,
        pseudoPanel,
        codePanel,
        dsPanel,
        arrayPanel,
        htmlPanel,
        watchPanel,
        segmented,
        bindSegmented,
        graphView,
        tableView,
        randomGraph,
        graphFromEdges,
        forceLayout,
        layeredLayout,
        treeLayout,
        adjacency,
        ek,
        esc,
        stripTags,
        svg,
        html,
        rand,
        clamp,
        parseList,
        clampInt,
        COMP_COLORS,
        LEVEL_COLORS,
        W,
        H,
    };
})();
