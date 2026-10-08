(() => {
    "use strict";

    const P = window.PROBLEM;
    const $ = (s, el = document) => el.querySelector(s);
    const $$ = (s, el = document) => [...el.querySelectorAll(s)];
    const esc = (s) =>
        String(s ?? "").replace(
            /[&<>"']/g,
            (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[c],
        );
    const store = {
        get(k, d) {
            try {
                return JSON.parse(localStorage.getItem(k)) ?? d;
            } catch (e) {
                return d;
            }
        },
        set(k, v) {
            try {
                localStorage.setItem(k, JSON.stringify(v));
            } catch (e) {}
        },
    };
    const csrf = $('meta[name="csrf-token"]').content;
    const VERDICT = {
        AC: "Accepted",
        WA: "Wrong Answer",
        TLE: "Time Limit Exceeded",
        RE: "Runtime Error",
        CE: "Compile Error",
    };
    const LANG_HINT = {
        cpp: "Baca input dengan <code>cin</code>, cetak dengan <code>cout</code> · dikompilasi g++ di server",
        javascript: "<code>readLine()</code> / <code>readInts()</code> untuk membaca input",
        python: "<code>input()</code> atau <code>sys.stdin</code> untuk membaca input",
    };
    const TAB_SIZE = { cpp: 4, javascript: 2, python: 4 };
    const SERVER = new Set(P.serverLanguages || []);
    const onServer = (lang) => SERVER.has(lang);
    const normalize = (s) =>
        String(s ?? "")
            .replace(/\r/g, "")
            .split("\n")
            .map((l) => l.replace(/\s+$/, ""))
            .join("\n")
            .replace(/\s+$/, "");

    // ═════════════════════ Tab ═════════════════════
    function switchTab(attr, panelAttr, name) {
        $$(`[${attr}]`).forEach((t) => t.classList.toggle("active", t.getAttribute(attr) === name));
        $$(`[${panelAttr}]`).forEach((p) => p.classList.toggle("active", p.getAttribute(panelAttr) === name));
    }
    $$("[data-tab]").forEach((t) =>
        t.addEventListener("click", () => switchTab("data-tab", "data-panel", t.dataset.tab)),
    );
    $$("[data-ctab]").forEach((t) =>
        t.addEventListener("click", () => switchTab("data-ctab", "data-cpanel", t.dataset.ctab)),
    );
    const showConsole = (name) => switchTab("data-ctab", "data-cpanel", name);

    $$("[data-copy-text]").forEach((b) =>
        b.addEventListener("click", async () => {
            try {
                await navigator.clipboard.writeText(b.dataset.copyText);
                b.textContent = "tersalin ✓";
                setTimeout(() => (b.textContent = "salin"), 1400);
            } catch (e) {}
        }),
    );

    // ═════════════════════ Editor Monaco ═════════════════════
    const MONACO_BASE = "https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.52.2/min";
    const langSelect = $("[data-lang-select]");
    const langs = Object.keys(P.languages);
    const codeKey = (lang) => `aa:code:${P.userId}:${P.slug}:${lang}`;

    // C++ menjadi bahasa utama: pengguna lama dipindahkan sekali ke C++.
    if (P.languages.cpp && !store.get("aa:cppFirst", false)) {
        store.set("aa:cppFirst", true);
        store.set("aa:lang", "cpp");
    }
    let language = store.get("aa:lang", langs[0]);
    if (!P.languages[language] || !P.starter[language]) language = langs.find((l) => P.starter[l]) || langs[0];
    langSelect.value = language;
    let editor = null;
    const models = {};

    window.MonacoEnvironment = {
        getWorkerUrl: () =>
            `data:text/javascript;charset=utf-8,${encodeURIComponent(
                `self.MonacoEnvironment={baseUrl:'${MONACO_BASE}/'};importScripts('${MONACO_BASE}/vs/base/worker/workerMain.js');`,
            )}`,
    };
    require.config({ paths: { vs: `${MONACO_BASE}/vs` } });
    require(["vs/editor/editor.main"], () => {
        monaco.editor.defineTheme("algoarena", {
            base: "vs-dark",
            inherit: true,
            rules: [
                { token: "comment", foreground: "6b7591", fontStyle: "italic" },
                { token: "keyword", foreground: "c4b5fd" },
                { token: "number", foreground: "fbbf24" },
                { token: "string", foreground: "86efac" },
                { token: "identifier", foreground: "e6e9f2" },
                { token: "type", foreground: "67e8f9" },
                { token: "keyword.directive", foreground: "f9a8d4" },
            ],
            colors: {
                "editor.background": "#141924",
                "editor.foreground": "#e6e9f2",
                "editorLineNumber.foreground": "#3d4760",
                "editorLineNumber.activeForeground": "#a78bfa",
                "editor.lineHighlightBackground": "#1a2030",
                "editor.selectionBackground": "#8b5cf650",
                "editorCursor.foreground": "#a78bfa",
                "editorIndentGuide.background1": "#232b3d",
                "editorWidget.background": "#1a2030",
                "editorSuggestWidget.background": "#1a2030",
                "editorSuggestWidget.selectedBackground": "#2b3550",
                "scrollbarSlider.background": "#2b344880",
            },
        });
        monaco.editor.defineTheme("algoarena-light", {
            base: "vs",
            inherit: true,
            rules: [
                { token: "comment", foreground: "6e7781", fontStyle: "italic" },
                { token: "keyword", foreground: "cf222e" },
                { token: "number", foreground: "0550ae" },
                { token: "string", foreground: "0a3069" },
                { token: "type", foreground: "953800" },
                { token: "keyword.directive", foreground: "116329" },
            ],
            colors: {
                "editor.background": "#fbfaf7",
                "editor.foreground": "#24292f",
                "editorLineNumber.foreground": "#a9a597",
                "editorLineNumber.activeForeground": "#2f5bd3",
                "editor.lineHighlightBackground": "#f1efe9",
                "editor.selectionBackground": "#2f5bd333",
                "editorCursor.foreground": "#2f5bd3",
            },
        });
        const themeName = () => (document.documentElement.dataset.theme === "dark" ? "algoarena" : "algoarena-light");
        document.addEventListener("aa:theme", () => monaco.editor.setTheme(themeName()));
        for (const lang of langs) {
            const saved = store.get(codeKey(lang), null);
            models[lang] = monaco.editor.createModel(saved ?? P.starter[lang] ?? "", lang);
            models[lang].onDidChangeContent(() => {
                store.set(codeKey(lang), models[lang].getValue());
                if (lang === "cpp") monaco.editor.setModelMarkers(models[lang], "judge", []);
            });
        }
        editor = monaco.editor.create($("[data-editor]"), {
            model: models[language],
            theme: themeName(),
            fontFamily: "JetBrains Mono, Consolas, monospace",
            fontLigatures: false,
            fontSize: 14,
            lineHeight: 22,
            minimap: { enabled: false },
            automaticLayout: true,
            scrollBeyondLastLine: false,
            padding: { top: 12, bottom: 12 },
            tabSize: TAB_SIZE[language] || 4,
            renderLineHighlight: "all",
            smoothScrolling: true,
            cursorBlinking: "smooth",
            bracketPairColorization: { enabled: true },
            guides: { bracketPairs: true },
        });
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter, () => submit());
        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.Quote, () => runSamples());
        $("[data-editor-loading]").hidden = true;
    });

    const getCode = () => (editor ? editor.getValue() : (models[language]?.getValue() ?? P.starter[language]));

    langSelect.addEventListener("change", () => {
        language = langSelect.value;
        store.set("aa:lang", language);
        if (editor) {
            editor.setModel(models[language]);
            editor.updateOptions({ tabSize: TAB_SIZE[language] || 4 });
        }
        updateRuntimeStatus();
        if (language === "python") python.ensure().catch(() => {});
    });

    $("[data-reset-code]").addEventListener("click", () => {
        if (!confirm("Kembalikan kode ke template awal? Kodemu saat ini akan hilang.")) return;
        models[language]?.setValue(P.starter[language] ?? "");
    });

    /** Tandai baris error kompilasi langsung di editor (garis bergelombang merah). */
    function markCompileErrors(text) {
        if (!window.monaco || !models.cpp) return;
        const markers = [];
        const re = /solusi\.cpp:(\d+):(\d+):\s*(fatal error|error|warning|note):\s*(.+)/g;
        let m;
        while ((m = re.exec(text)) && markers.length < 20) {
            if (m[3] === "note") continue;
            const line = +m[1];
            markers.push({
                startLineNumber: line,
                startColumn: +m[2],
                endLineNumber: line,
                endColumn: models.cpp.getLineMaxColumn(Math.min(line, models.cpp.getLineCount())),
                message: m[4],
                severity: m[3] === "warning" ? monaco.MarkerSeverity.Warning : monaco.MarkerSeverity.Error,
            });
        }
        monaco.editor.setModelMarkers(models.cpp, "judge", markers);
    }

    function compileErrorHtml(text) {
        const first = /solusi\.cpp:(\d+):\d+:\s*(?:fatal )?error/.exec(text);
        return `<div class="ce-box">
                <div class="ce-head"><b>Compile Error</b>${first ? `<span>cek baris <b>${first[1]}</b></span>` : ""}</div>
                <p>Kodemu belum bisa dikompilasi oleh g++. Baca pesan error dari atas: baris pertama biasanya penyebab utamanya. Baris yang bermasalah juga ditandai merah di editor.</p>
                <pre class="err">${esc(text)}</pre>
            </div>`;
    }

    // ═════════════════════ Runner ═════════════════════
    const runtimeEl = $("[data-runtime]");
    function setRuntime(state, text) {
        runtimeEl.className = "runtime-status " + state;
        runtimeEl.querySelector("span").textContent = text;
    }
    function updateRuntimeStatus() {
        const hint = $("[data-lang-hint]");
        if (hint) hint.innerHTML = LANG_HINT[language] || "";
        if (onServer(language)) setRuntime("ready", `${P.languages[language]} · judge server siap`);
        else if (language === "javascript") setRuntime("ready", "JavaScript siap");
        else if (python.ready) setRuntime("ready", "Python (Pyodide) siap");
        else setRuntime("loading", "Memuat Python… (±5 detik pertama kali)");
    }

    /** Jalankan JS di worker baru; terminate jika melewati batas waktu. */
    function runJs(code, input, limit) {
        return new Promise((resolve) => {
            const w = new Worker(P.runners.js);
            const timer = setTimeout(() => {
                w.terminate();
                resolve({ status: "tle", output: "", time: limit });
            }, limit + 250);
            w.onmessage = (e) => {
                clearTimeout(timer);
                w.terminate();
                const r = e.data;
                if (r.time > limit) r.status = "tle";
                resolve(r);
            };
            w.onerror = (e) => {
                clearTimeout(timer);
                w.terminate();
                resolve({ status: "re", output: "", error: e.message || "Error tidak dikenal", time: 0 });
            };
            w.postMessage({ code, input });
        });
    }

    const python = {
        worker: null,
        ready: false,
        booting: null,
        ensure() {
            if (this.booting) return this.booting;
            this.ready = false;
            updateRuntimeStatus();
            this.worker = new Worker(P.runners.py);
            this.booting = new Promise((resolve, reject) => {
                this.worker.onmessage = (e) => {
                    if (e.data.type === "ready") {
                        this.ready = true;
                        updateRuntimeStatus();
                        resolve();
                    } else if (e.data.type === "fail") {
                        setRuntime("", "Gagal memuat Python. Periksa koneksi internet.");
                        this.booting = null;
                        reject(new Error(e.data.error));
                    }
                };
            });
            this.worker.postMessage({ type: "init", indexURL: P.pyodide });
            return this.booting;
        },
        reset() {
            this.worker?.terminate();
            this.worker = null;
            this.booting = null;
            this.ready = false;
        },
        async run(code, input, limit) {
            await this.ensure();
            return new Promise((resolve) => {
                const timer = setTimeout(() => {
                    // Python tidak bisa dihentikan dari dalam: matikan worker lalu siapkan ulang
                    this.reset();
                    this.ensure().catch(() => {});
                    resolve({ status: "tle", output: "", time: limit });
                }, limit + 400);
                this.worker.onmessage = (e) => {
                    if (e.data.type !== "result") return;
                    clearTimeout(timer);
                    const r = e.data;
                    if (r.time > limit) r.status = "tle";
                    resolve(r);
                };
                this.worker.postMessage({ type: "run", code, input });
            });
        },
    };

    async function postJson(url, body) {
        const res = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json", Accept: "application/json", "X-CSRF-TOKEN": csrf },
            body: JSON.stringify(body),
        });
        if (res.status === 429) throw new Error("Terlalu sering mengirim. Tunggu sebentar lalu coba lagi.");
        if (res.status === 419) throw new Error("Sesi kedaluwarsa. Muat ulang halaman.");
        if (res.status === 503) {
            const d = await res.json().catch(() => ({}));
            throw new Error(d.message || "Judge server sedang tidak tersedia.");
        }
        if (res.status === 422) {
            const d = await res.json().catch(() => ({}));
            throw new Error(Object.values(d.errors || {})[0]?.[0] || "Data tidak valid.");
        }
        if (!res.ok) throw new Error("HTTP " + res.status);
        return res.json();
    }

    /** Kompilasi sekali di server lalu jalankan beberapa input. */
    function runOnServer(code, inputs) {
        return postJson(P.runUrl, { code, inputs });
    }

    function execute(code, input) {
        const limit = P.timeLimits[language];
        return language === "python" ? python.run(code, input, limit) : runJs(code, input, limit);
    }

    if (language === "python") python.ensure().catch(() => {});
    updateRuntimeStatus();

    // ═════════════════════ Jalankan tes contoh ═════════════════════
    const runBtn = $("[data-run]");
    const submitBtn = $("[data-submit]");
    let busy = false;

    const setBusy = (on, btn) => {
        busy = on;
        runBtn.disabled = on;
        submitBtn.disabled = on;
        btn?.classList.toggle("loading", on);
    };

    const verdictOf = (r, expected) =>
        r.status === "tle"
            ? "TLE"
            : r.status === "re"
              ? "RE"
              : normalize(r.output) === normalize(expected)
                ? "AC"
                : "WA";

    async function runSamples() {
        if (busy) return;
        setBusy(true, runBtn);
        showConsole("samples");
        const view = $("[data-sample-view]");
        const code = getCode();
        const results = [];
        try {
            if (onServer(language)) {
                view.innerHTML = '<p class="muted working" style="margin:0">Mengompilasi di server…</p>';
                const data = await runOnServer(
                    code,
                    P.samples.map((s) => s.input),
                );
                if (data.compileError !== undefined) {
                    view.innerHTML = compileErrorHtml(data.compileError);
                    markCompileErrors(data.compileError);
                    setBusy(false, runBtn);
                    return;
                }
                data.results.forEach((r, i) => {
                    const s = P.samples[i];
                    results.push({ ...r, input: s.input, expected: s.output, verdict: verdictOf(r, s.output) });
                });
            } else {
                view.innerHTML = '<p class="muted working" style="margin:0">Menjalankan tes contoh…</p>';
                for (const s of P.samples) {
                    const r = await execute(code, s.input);
                    results.push({ ...r, input: s.input, expected: s.output, verdict: verdictOf(r, s.output) });
                }
            }
        } catch (e) {
            view.innerHTML = `<p class="muted">Gagal menjalankan: ${esc(e.message)}</p>`;
            setBusy(false, runBtn);
            return;
        }
        renderSamples(results);
        setBusy(false, runBtn);
    }

    function renderSamples(results) {
        const view = $("[data-sample-view]");
        const allOk = results.every((r) => r.verdict === "AC");
        const passed = results.filter((r) => r.verdict === "AC").length;
        let active = Math.max(
            0,
            results.findIndex((r) => r.verdict !== "AC"),
        );
        const draw = () => {
            const r = results[active];
            view.innerHTML = `
                <div class="run-summary">
                    <h3 class="verdict verdict-${allOk ? "AC" : results.find((x) => x.verdict !== "AC").verdict}">${allOk ? "Semua tes contoh lolos ✓" : VERDICT[results[active].verdict]}</h3>
                    <span class="muted">${passed}/${results.length} contoh benar${allOk ? " · Lanjut <b>Submit</b> untuk diuji tes tersembunyi" : ""}</span>
                </div>
                <div class="case-tabs">${results
                    .map(
                        (x, i) =>
                            `<button class="case-tab ${x.verdict} ${i === active ? "active" : ""}" data-case="${i}"><i></i>Contoh ${i + 1}</button>`,
                    )
                    .join("")}</div>
                <div class="io-block"><small>Input</small><pre>${esc(r.input.trimEnd())}</pre></div>
                <div class="io-block"><small>Output kamu · ${Math.round(r.time)} ms</small><pre class="${r.verdict === "AC" ? "good" : "bad"}">${esc((r.output || "").trimEnd()) || '<span class="faint">(kosong)</span>'}</pre></div>
                ${r.verdict === "AC" ? "" : `<div class="io-block"><small>Output yang diharapkan</small><pre class="good">${esc(r.expected)}</pre></div>`}
                ${r.error ? `<div class="io-block"><small>Error</small><pre class="err">${esc(r.error)}</pre></div>` : ""}
                ${r.verdict === "TLE" ? `<div class="io-block"><small>Info</small><pre class="err">Melebihi batas waktu ${P.timeLimits[language]} ms. Mungkin ada infinite loop, program menunggu input yang tidak ada, atau algoritmanya terlalu lambat.</pre></div>` : ""}`;
            $$("[data-case]", view).forEach((b) =>
                b.addEventListener("click", () => {
                    active = +b.dataset.case;
                    draw();
                }),
            );
        };
        draw();
    }

    $("[data-run-custom]").addEventListener("click", async () => {
        if (busy) return;
        const btn = $("[data-run-custom]");
        setBusy(true, btn);
        const input = $("[data-custom-input]").value + "\n";
        const wrap = $("[data-custom-out-wrap]");
        const out = $("[data-custom-out]");
        wrap.hidden = false;
        $("[data-custom-label]").textContent = onServer(language) ? "Mengompilasi…" : "Menjalankan…";
        out.className = "";
        out.textContent = "";
        let r;
        try {
            if (onServer(language)) {
                const data = await runOnServer(getCode(), [input]);
                if (data.compileError !== undefined) {
                    markCompileErrors(data.compileError);
                    r = { status: "ce", output: "", error: data.compileError, time: 0 };
                } else {
                    r = data.results[0];
                }
            } else {
                r = await execute(getCode(), input);
            }
        } catch (e) {
            r = { status: "re", error: e.message, output: "", time: 0 };
        }
        const label = {
            tle: "Time Limit Exceeded",
            re: "Runtime Error",
            ce: "Compile Error",
        }[r.status];
        $("[data-custom-label]").textContent = label || `Output · ${Math.round(r.time)} ms`;
        out.className = r.status === "ok" ? "" : "err";
        out.textContent = (r.output || "") + (r.error ? (r.output ? "\n" : "") + r.error : "") || "(tidak ada output)";
        setBusy(false, btn);
    });

    // ═════════════════════ Submit ═════════════════════
    async function submit() {
        if (busy) return;
        const code = getCode();
        if (!code.trim()) return toast("Kode masih kosong.", "bad");
        setBusy(true, submitBtn);
        showConsole("result");
        const view = $("[data-result-view]");

        if (onServer(language)) {
            view.innerHTML = `
                <div class="run-summary"><h3 class="working">Menilai di server…</h3><span class="muted">kompilasi g++ lalu menjalankan semua tes</span></div>
                <div class="judge-progress"><i></i></div>`;
            try {
                const data = await postJson(P.submitUrl, { language, code });
                finishSubmit(data);
            } catch (e) {
                view.innerHTML = `<p class="muted">Gagal menilai: ${esc(e.message)}</p>`;
            }
            return setBusy(false, submitBtn);
        }

        view.innerHTML = '<p class="muted" style="margin:0">Mengambil data tes…</p>';
        let tests;
        try {
            const res = await fetch(P.testsUrl, { headers: { Accept: "application/json" } });
            tests = await res.json();
        } catch (e) {
            view.innerHTML = '<p class="muted">Gagal mengambil data tes. Periksa koneksi.</p>';
            return setBusy(false, submitBtn);
        }

        view.innerHTML = `
            <div class="run-summary"><h3>Menilai…</h3><span class="muted" data-progress>0/${tests.length} tes</span></div>
            <div class="test-grid">${tests.map((t, i) => `<div class="test-box pending" data-box="${i}" style="animation-delay:${i * 25}ms"><b>…</b>#${i + 1}</div>`).join("")}</div>`;

        const results = [];
        for (let i = 0; i < tests.length; i++) {
            const box = $(`[data-box="${i}"]`, view);
            box.className = "test-box running";
            let r;
            try {
                r = await execute(code, tests[i].input);
            } catch (e) {
                r = { status: "re", output: "", error: e.message, time: 0 };
            }
            results.push({
                id: tests[i].id,
                status: r.status,
                output: (r.output || "").slice(0, 2_000_000),
                time: Math.round(r.time || 0),
                error: r.error ? String(r.error).slice(0, 1900) : null,
            });
            box.className = `test-box ${r.status === "tle" ? "TLE" : r.status === "re" ? "RE" : "pending"}`;
            box.innerHTML = `<b>${r.status === "tle" ? "TLE" : r.status === "re" ? "RE" : "✓"}</b>${Math.round(r.time || 0)} ms`;
            $("[data-progress]", view).textContent = `${i + 1}/${tests.length} tes dijalankan`;
        }

        try {
            finishSubmit(await postJson(P.submitUrl, { language, code, results }));
        } catch (e) {
            view.insertAdjacentHTML("afterbegin", `<p class="muted">Gagal mengirim hasil: ${esc(e.message)}</p>`);
        }
        setBusy(false, submitBtn);
    }

    function finishSubmit(data) {
        renderVerdict(data);
        addSubmission(data);
        unlockEditorial();
        if (data.verdict === "CE") markCompileErrors(data.firstFail?.error || "");
        if (data.verdict === "AC") celebrate(data);
    }

    function renderVerdict(d) {
        const view = $("[data-result-view]");
        const ff = d.firstFail;
        let detail = "";
        if (ff && ff.verdict === "CE") {
            detail = compileErrorHtml(ff.error || "");
        } else if (ff) {
            detail = ff.sample
                ? `<div class="io-block"><small>Tes #${ff.n} (contoh), input</small><pre>${esc((ff.input || "").trimEnd())}</pre></div>
                   <div class="io-block"><small>Output kamu</small><pre class="bad">${esc((ff.output || "").trimEnd()) || '<span class="faint">(kosong)</span>'}</pre></div>
                   <div class="io-block"><small>Output yang diharapkan</small><pre class="good">${esc(ff.expected)}</pre></div>`
                : `<div class="io-block"><small>Tes #${ff.n} (tersembunyi)</small><pre>${
                      ff.verdict === "WA"
                          ? "Output berbeda dari jawaban. Coba pikirkan kasus khusus: N = 1, tidak ada sisi, graph tidak terhubung, nilai maksimum (perlu long long?)…"
                          : ff.verdict === "TLE"
                            ? "Program terlalu lambat untuk input besar. Periksa kompleksitas algoritmamu."
                            : "Program error saat dijalankan."
                  }</pre></div>`;
            if (ff.error)
                detail += `<div class="io-block"><small>Error</small><pre class="err">${esc(ff.error)}</pre></div>`;
        }
        view.innerHTML = `
            <div class="score-big ${d.verdict === "AC" ? "ac" : ""}">
                <div class="num">${d.score}<small>/100</small></div>
                <div>
                    <div class="verdict verdict-${d.verdict}" style="font-size:18px">${esc(d.verdictLabel)}</div>
                    <div class="muted" style="font-size:13px">${d.passed}/${d.total} tes lolos${d.verdict === "CE" ? "" : ` · waktu terlama ${d.time} ms`} · ${esc(d.language)} · nilai terbaik ${d.best}</div>
                </div>
            </div>
            ${
                d.tests.length
                    ? `<div class="test-grid">${d.tests
                          .map(
                              (t, i) =>
                                  `<div class="test-box ${t.verdict}" style="animation-delay:${i * 30}ms" title="${t.sample ? "Tes contoh" : "Tes tersembunyi"}"><b>${t.verdict}</b>#${t.n} · ${t.time}ms</div>`,
                          )
                          .join("")}</div>`
                    : ""
            }
            ${detail}`;
    }

    function addSubmission(d) {
        const list = $("[data-sub-list]");
        $("[data-sub-empty]")?.remove();
        list.insertAdjacentHTML(
            "afterbegin",
            `<div class="sub-item">
                <div>
                    <span class="verdict verdict-${d.verdict}">${esc(d.verdictLabel)}</span>
                    <small>${esc(d.createdAt)} · ${esc(d.language)} · ${d.passed}/${d.total} tes · ${d.time} ms</small>
                </div>
                <span class="score-chip ${d.score === 100 ? "full" : "part"}">${d.score}</span>
                <button class="btn btn-sm btn-ghost" data-load-sub="/submisi/${d.id}">Lihat kode</button>
            </div>`,
        );
        const count = $("[data-sub-count]");
        count.textContent = +count.textContent + 1;
    }

    async function unlockEditorial() {
        const box = $("[data-editorial]");
        if (!box.hidden && box.children.length) return;
        try {
            const res = await fetch(P.editorialUrl, { headers: { Accept: "text/html" } });
            if (!res.ok) return;
            box.innerHTML = await res.text();
            box.hidden = false;
            $("[data-editorial-locked]").hidden = true;
            setupCodeTabs(box);
        } catch (e) {}
    }

    /** Tab bahasa + highlight untuk blok kode yang dimuat belakangan. */
    function setupCodeTabs(root) {
        const lang = store.get("aa:lang", "cpp");
        $$("[data-code-tabs]", root).forEach((box) => {
            const tabs = $$(".code-tab", box);
            const select = (l) => {
                tabs.forEach((t) => t.classList.toggle("active", t.dataset.lang === l));
                $$("pre", box).forEach((p) => (p.hidden = p.dataset.lang !== l));
            };
            tabs.forEach((t) => t.addEventListener("click", () => select(t.dataset.lang)));
            $("[data-copy]", box).addEventListener("click", () =>
                navigator.clipboard.writeText($("pre:not([hidden]) code", box).textContent),
            );
            select(tabs.some((t) => t.dataset.lang === lang) ? lang : tabs[0]?.dataset.lang);
            if (window.hljs) $$("code", box).forEach((c) => hljs.highlightElement(c));
        });
    }

    document.addEventListener("click", async (e) => {
        const btn = e.target.closest("[data-load-sub]");
        if (!btn) return;
        if (!confirm("Muat kode submisi ini ke editor? Kode di editor saat ini akan diganti.")) return;
        const res = await fetch(btn.dataset.loadSub, { headers: { Accept: "application/json" } });
        const d = await res.json();
        if (!P.languages[d.language]) return toast("Bahasa submisi ini tidak tersedia lagi.", "bad");
        langSelect.value = d.language;
        langSelect.dispatchEvent(new Event("change"));
        models[d.language]?.setValue(d.code);
        toast("Kode submisi dimuat ke editor.");
    });

    runBtn.addEventListener("click", runSamples);
    submitBtn.addEventListener("click", submit);
    document.addEventListener("keydown", (e) => {
        if (!(e.ctrlKey || e.metaKey)) return;
        if (e.key === "Enter") {
            e.preventDefault();
            submit();
        } else if (e.key === "'") {
            e.preventDefault();
            runSamples();
        }
    });

    // ═════════════════════ AC: modal & confetti ═════════════════════
    const modal = $("[data-modal]");
    function celebrate(d) {
        stopwatch.stop();
        $("[data-modal-text]").textContent =
            `Semua ${d.total} tes lolos dalam waktu terlama ${d.time} ms. Kerja bagus!`;
        modal.hidden = false;
        const colors = ["#8b5cf6", "#22d3ee", "#22c55e", "#f59e0b", "#ec4899"];
        if (!matchMedia("(prefers-reduced-motion: reduce)").matches) {
            for (let i = 0; i < 90; i++) {
                const c = document.createElement("i");
                c.className = "confetti";
                c.style.left = innerWidth / 2 + "px";
                c.style.top = innerHeight / 3 + "px";
                c.style.background = colors[i % colors.length];
                c.style.setProperty("--dx", Math.random() * 900 - 450 + "px");
                c.style.setProperty("--dy", Math.random() * 520 + 80 + "px");
                c.style.setProperty("--r", Math.random() * 720 - 360 + "deg");
                document.body.appendChild(c);
                setTimeout(() => c.remove(), 1600);
            }
        }
    }
    modal.addEventListener("click", (e) => e.target === modal && (modal.hidden = true));
    $("[data-modal-editorial]").addEventListener("click", () => {
        modal.hidden = true;
        switchTab("data-tab", "data-panel", "editorial");
    });
    document.addEventListener("keydown", (e) => e.key === "Escape" && (modal.hidden = true));

    // ═════════════════════ Stopwatch ═════════════════════
    const stopwatch = (() => {
        const el = $("[data-stopwatch]");
        const t0 = Date.now();
        const tick = () => {
            const s = Math.floor((Date.now() - t0) / 1000);
            el.textContent = `⏱ ${String(Math.floor(s / 60)).padStart(2, "0")}:${String(s % 60).padStart(2, "0")}`;
        };
        const h = setInterval(tick, 1000);
        return { stop: () => clearInterval(h) };
    })();

    // ═════════════════════ Panel yang bisa digeser ═════════════════════
    const workspace = $("[data-workspace]");
    const left = $("[data-pane-left]");
    const consolePane = $("[data-console]");
    const layout = store.get("aa:layout", { left: 42, console: 36 });
    const apply = () => {
        left.style.flexBasis = layout.left + "%";
        consolePane.style.flexBasis = layout.console + "%";
    };
    apply();
    function drag(g, onMove) {
        g.addEventListener("pointerdown", (e) => {
            e.preventDefault();
            g.setPointerCapture(e.pointerId);
            document.body.classList.add("dragging");
            const move = (ev) => {
                onMove(ev);
                apply();
            };
            g.addEventListener("pointermove", move);
            g.addEventListener(
                "pointerup",
                () => {
                    g.removeEventListener("pointermove", move);
                    document.body.classList.remove("dragging");
                    store.set("aa:layout", layout);
                },
                { once: true },
            );
        });
    }
    drag($("[data-gutter-x]"), (e) => {
        const r = workspace.getBoundingClientRect();
        layout.left = Math.min(70, Math.max(22, ((e.clientX - r.left) / r.width) * 100));
    });
    drag($("[data-gutter-y]"), (e) => {
        const r = $("[data-pane-right]").getBoundingClientRect();
        layout.console = Math.min(75, Math.max(14, ((r.bottom - e.clientY) / r.height) * 100));
    });

    // ═════════════════════ Toast ═════════════════════
    let toastTimer;
    function toast(msg, type = "ok") {
        const t = $("[data-toast]");
        t.textContent = msg;
        t.className = "toast show " + type;
        t.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove("show"), 2600);
    }
})();
