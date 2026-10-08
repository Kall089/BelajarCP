/**
 * Walkthrough kode: program C++ lengkap yang dijelaskan langkah demi langkah.
 * Mode "Satu per satu": kode di kiri, satu kartu penjelasan di kanan, baris terkait menyala.
 * Mode "Baca berurutan": setiap langkah tampil bersama potongan kodenya, dari atas ke bawah.
 */
(() => {
    "use strict";

    const $ = (s, el = document) => el.querySelector(s);
    const $$ = (s, el = document) => [...el.querySelectorAll(s)];
    const esc = (s) => String(s).replace(/[&<>]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;" })[c]);

    /** Pecah HTML hasil highlight per baris tanpa merusak <span> yang melintasi baris. */
    function splitLines(html) {
        const lines = [];
        const open = [];
        let cur = "";
        const re = /(<span[^>]*>)|(<\/span>)|(\n)|([^<\n]+)|(<)/g;
        let m;
        while ((m = re.exec(html))) {
            if (m[1]) {
                open.push(m[1]);
                cur += m[1];
            } else if (m[2]) {
                open.pop();
                cur += m[2];
            } else if (m[3]) {
                lines.push(cur + "</span>".repeat(open.length));
                cur = open.join("");
            } else {
                cur += m[0];
            }
        }
        lines.push(cur);
        return lines;
    }

    function highlight(code) {
        if (window.hljs) {
            try {
                return hljs.highlight(code, { language: "cpp", ignoreIllegals: true }).value;
            } catch (e) {}
        }
        return esc(code);
    }

    const lineHtml = (html, n) =>
        `<span class="wl" data-n="${n}"><span class="wl-n">${n}</span><span class="wl-c">${html || " "}</span></span>`;

    function setup(root) {
        const codeEl = $("[data-wt-code]", root);
        const wrap = $("[data-wt-code-wrap]", root);
        const raw = codeEl.textContent;
        const lines = splitLines(highlight(raw));
        codeEl.innerHTML = lines.map((l, i) => lineHtml(l, i + 1)).join("");
        codeEl.classList.add("hljs");
        const lineEls = $$(".wl", codeEl);
        const steps = $$(".wt-step", root);
        const prev = $("[data-wt-prev]", root);
        const next = $("[data-wt-next]", root);
        const counter = $("[data-wt-counter]", root);
        const bar = $("[data-wt-bar]", root);
        let current = 0;

        // Potongan kode untuk mode "Baca berurutan"
        steps.forEach((s) => {
            const from = +s.dataset.from;
            const to = +s.dataset.to;
            const snip = document.createElement("pre");
            snip.className = "wt-snippet hljs";
            snip.innerHTML = lines
                .slice(from - 1, to)
                .map((l, i) => lineHtml(l, from + i))
                .join("");
            $(".wt-step-head", s).after(snip);
        });

        function light(i, scroll) {
            const from = +steps[i].dataset.from;
            const to = +steps[i].dataset.to;
            lineEls.forEach((el, k) => el.classList.toggle("on", k + 1 >= from && k + 1 <= to));
            root.classList.add("has-focus");
            if (!scroll) return;
            const first = lineEls[from - 1];
            const last = lineEls[to - 1];
            const top = first.offsetTop;
            const height = last.offsetTop + last.offsetHeight - top;
            // Tempatkan blok yang menyala di sepertiga atas panel kode
            const target = height > wrap.clientHeight * 0.8 ? top - 12 : top - Math.max(16, (wrap.clientHeight - height) / 3);
            wrap.scrollTo({ top: Math.max(0, target), behavior: "smooth" });
        }

        function show(i, { scroll = true, focusStep = false } = {}) {
            current = Math.max(0, Math.min(steps.length - 1, i));
            steps.forEach((s, k) => s.classList.toggle("active", k === current));
            counter.textContent = `Langkah ${current + 1} dari ${steps.length}`;
            bar.style.width = `${((current + 1) / steps.length) * 100}%`;
            prev.disabled = current === 0;
            next.textContent = current === steps.length - 1 ? "Ulangi dari awal ↺" : "Berikutnya →";
            light(current, scroll);
            if (focusStep && root.dataset.mode === "all") {
                steps[current].scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }

        prev.addEventListener("click", () => show(current - 1));
        next.addEventListener("click", () => show(current === steps.length - 1 ? 0 : current + 1));

        // Klik baris kode → lompat ke langkah yang menjelaskan baris itu
        codeEl.addEventListener("click", (e) => {
            const line = e.target.closest(".wl");
            if (!line) return;
            const n = +line.dataset.n;
            const k = steps.findIndex((s) => n >= +s.dataset.from && n <= +s.dataset.to);
            if (k >= 0) show(k, { scroll: false, focusStep: true });
        });

        steps.forEach((s, k) =>
            s.addEventListener("click", () => {
                if (root.dataset.mode === "all" && current !== k) show(k);
            }),
        );

        $$("[data-wt-mode]", root).forEach((b) =>
            b.addEventListener("click", () => {
                root.dataset.mode = b.dataset.wtMode;
                $$("[data-wt-mode]", root).forEach((x) => x.classList.toggle("active", x === b));
                show(current, { scroll: true });
            }),
        );

        root.addEventListener("keydown", (e) => {
            if (e.target.closest("input, textarea, select")) return;
            if (e.key === "ArrowRight") {
                e.preventDefault();
                show(current + 1);
            } else if (e.key === "ArrowLeft") {
                e.preventDefault();
                show(current - 1);
            }
        });
        // Klik di dalam komponen agar panah keyboard langsung berfungsi
        root.addEventListener("pointerdown", (e) => {
            if (!e.target.closest("button, a, summary, input, textarea")) root.focus({ preventScroll: true });
        });

        $("[data-wt-copy]", root).addEventListener("click", async (e) => {
            try {
                await navigator.clipboard.writeText(raw);
                e.target.textContent = "Tersalin ✓";
                setTimeout(() => (e.target.textContent = "Salin kode"), 1500);
            } catch (err) {}
        });

        show(0, { scroll: false });
    }

    const init = () => $$("[data-walkthrough]").forEach(setup);
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
    else init();
})();
