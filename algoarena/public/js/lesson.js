(() => {
    "use strict";

    const $ = (s, el = document) => el.querySelector(s);
    const $$ = (s, el = document) => [...el.querySelectorAll(s)];

    // ─────────────── Daftar isi otomatis + penanda bagian aktif ───────────────
    const toc = $("ul[data-toc]");
    const sections = $$("section[data-toc]");
    if (toc) {
        // Judul level (Dasar/Menengah/Lanjut) menjadi pengelompok daftar isi
        let level = 0;
        toc.innerHTML = $$("[data-toc-group], section[data-toc]")
            .map((s) => {
                if (s.dataset.tocGroup) {
                    level = +s.dataset.level || 0;
                    return `<li class="toc-group l${level}"><a href="#${s.id}">${s.dataset.tocGroup}</a></li>`;
                }
                if (s.dataset.level === "0") level = 0;
                const dot = level ? `<span class="toc-dot l${level}"></span>` : "";
                return `<li><a href="#${s.id}" data-toc-link="${s.id}">${dot}${s.dataset.toc}</a></li>`;
            })
            .join("");
        const links = $$("[data-toc-link]");
        // Bagian aktif = bagian terakhir yang judulnya sudah melewati bagian atas layar
        const update = () => {
            let current = sections[0]?.id;
            for (const s of sections) {
                if (s.getBoundingClientRect().top < 140) current = s.id;
            }
            if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 4)
                current = sections[sections.length - 1]?.id;
            links.forEach((a) => a.classList.toggle("active", a.dataset.tocLink === current));
        };
        window.addEventListener("scroll", update, { passive: true });
        update();
    }

    // Rel level di hero hanya relevan jika materi punya penanda level
    const rail = $("[data-level-rail]");
    if (rail && !$("#dasar")) rail.hidden = true;

    // ─────────────── Tab kode C++ / JavaScript / Python ───────────────
    let preferred = "cpp";
    try {
        // C++ menjadi bahasa utama: pilihan lama dipindahkan sekali ke C++.
        if (!localStorage.getItem("aa:cppFirst")) {
            localStorage.setItem("aa:cppFirst", "true");
            localStorage.setItem("aa:lang", JSON.stringify("cpp"));
        }
        preferred = JSON.parse(localStorage.getItem("aa:lang")) || "cpp";
    } catch (e) {}
    $$("[data-code-tabs]").forEach((box) => {
        const tabs = $$(".code-tab", box);
        const select = (lang) => {
            if (!tabs.some((t) => t.dataset.lang === lang)) lang = tabs[0]?.dataset.lang;
            tabs.forEach((t) => t.classList.toggle("active", t.dataset.lang === lang));
            $$("pre", box).forEach((p) => (p.hidden = p.dataset.lang !== lang));
        };
        tabs.forEach((t) =>
            t.addEventListener("click", () => {
                select(t.dataset.lang);
                try {
                    localStorage.setItem("aa:lang", JSON.stringify(t.dataset.lang));
                } catch (e) {}
            }),
        );
        $("[data-copy]", box).addEventListener("click", async (e) => {
            const code = $("pre:not([hidden]) code", box).textContent;
            try {
                await navigator.clipboard.writeText(code);
                e.target.textContent = "Tersalin ✓";
                setTimeout(() => (e.target.textContent = "Salin"), 1500);
            } catch (err) {}
        });
        select(preferred);
    });

    // ─────────────── Mode baca kode layar penuh ───────────────
    const closeFull = () => {
        $$(".is-fullcode").forEach((el) => {
            el.classList.remove("is-fullcode");
            const b = $("[data-fullcode]", el);
            if (b) b.textContent = "Layar penuh";
        });
        document.documentElement.classList.remove("has-fullcode");
    };
    $$("[data-fullcode]").forEach((btn) =>
        btn.addEventListener("click", () => {
            const box = btn.closest("[data-code-tabs], [data-walkthrough]");
            if (box.classList.contains("is-fullcode")) return closeFull();
            closeFull();
            box.classList.add("is-fullcode");
            document.documentElement.classList.add("has-fullcode");
            btn.textContent = "Tutup (Esc)";
        }),
    );
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeFull();
    });

    if (window.hljs) $$(".code-tabs code, pre.snippet code").forEach((el) => hljs.highlightElement(el));

    // ─────────────── Kuis ───────────────
    $$("[data-quiz]").forEach((quiz) => {
        const answer = +quiz.dataset.answer;
        const feedback = $(".quiz-feedback", quiz);
        $$(".quiz-option", quiz).forEach((opt, i) =>
            opt.addEventListener("click", () => {
                $$(".quiz-option", quiz).forEach((o) => o.classList.remove("correct", "wrong"));
                const right = i === answer;
                opt.classList.add(right ? "correct" : "wrong");
                if (!right) $$(".quiz-option", quiz)[answer].classList.add("correct");
                feedback.innerHTML = (right ? "<b>Tepat!</b> " : "<b>Belum tepat.</b> ") + quiz.dataset.explain;
                feedback.hidden = false;
            }),
        );
    });

    // ─────────────── Tandai selesai ───────────────
    const btn = $("[data-complete]");
    if (btn) {
        btn.addEventListener("click", async () => {
            const done = btn.dataset.done === "1";
            btn.disabled = true;
            try {
                const res = await fetch(btn.dataset.url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ undo: done }),
                });
                const data = await res.json();
                btn.dataset.done = data.done ? "1" : "0";
                btn.textContent = data.done ? "Batalkan" : "Tandai Selesai";
                btn.className = "btn " + (data.done ? "btn-outline" : "btn-primary");
                $("[data-complete-box]").classList.toggle("is-done", data.done);
                $("[data-complete-title]").textContent = data.done
                    ? "Materi ini sudah kamu selesaikan."
                    : "Sudah memahami materi ini?";
                $("[data-complete-sub]").textContent = data.done
                    ? "Kamu tetap bisa mengulasnya kapan saja."
                    : "Tandai selesai agar progresmu tercatat di peta belajar.";
            } finally {
                btn.disabled = false;
            }
        });
    }
})();
