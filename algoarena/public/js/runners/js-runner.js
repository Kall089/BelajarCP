/**
 * Worker penjalan kode JavaScript.
 * Satu worker = satu eksekusi, sehingga infinite loop cukup diatasi dengan terminate() dari halaman.
 *
 * Helper yang tersedia untuk kode siswa:
 *   readLine()  → baris input berikutnya (string, "" jika habis)
 *   readInts()  → baris berikutnya sebagai array angka
 *   lines       → semua baris input
 *   input       → seluruh input sebagai string
 *   print(...)  → sama dengan console.log
 *   require("fs").readFileSync(0, "utf8") → seluruh input (gaya Node.js)
 */
self.onmessage = (e) => {
    const { code, input } = e.data;
    const out = [];
    let size = 0;
    const LIMIT = 5_000_000;

    const write = (s) => {
        size += s.length;
        if (size > LIMIT) throw new Error("Output terlalu besar (> 5 MB)");
        out.push(s);
    };
    const fmt = (x) => {
        if (typeof x === "string") return x;
        if (typeof x === "bigint") return x.toString();
        if (typeof x === "object" && x !== null) {
            try {
                return JSON.stringify(x);
            } catch (err) {
                return String(x);
            }
        }
        return String(x);
    };
    const log = (...args) => write(args.map(fmt).join(" ") + "\n");

    const lines = String(input).replace(/\r/g, "").split("\n");
    if (lines.length && lines[lines.length - 1] === "") lines.pop();
    let ptr = 0;
    const readLine = () => (ptr < lines.length ? lines[ptr++] : "");
    const readInts = () => {
        const s = readLine().trim();
        return s === "" ? [] : s.split(/\s+/).map(Number);
    };

    const fakeConsole = { log, info: log, warn: log, error: log, debug: log };
    const fakeProcess = { stdout: { write: (s) => write(String(s)) }, argv: [], env: {} };
    const fakeRequire = (name) => {
        if (name === "fs") return { readFileSync: () => String(input) };
        if (name === "readline")
            throw new Error(
                'Modul "readline" tidak didukung. Gunakan readLine() atau require("fs").readFileSync(0, "utf8").',
            );
        throw new Error(`Modul "${name}" tidak tersedia di judge.`);
    };

    const t0 = performance.now();
    try {
        const fn = new Function(
            "console",
            "process",
            "require",
            "readLine",
            "readInts",
            "lines",
            "input",
            "print",
            `"use strict";\n${code}`,
        );
        fn(fakeConsole, fakeProcess, fakeRequire, readLine, readInts, lines.slice(), String(input), log);
        self.postMessage({ status: "ok", output: out.join(""), time: performance.now() - t0 });
    } catch (err) {
        const head = err && err.name ? `${err.name}: ${err.message}` : String(err);
        // Ambil lokasi di kode siswa (new Function menambah 3 baris di depan kode)
        const where = [];
        for (const line of String((err && err.stack) || "").split("\n")) {
            const m = line.match(/<anonymous>:(\d+):(\d+)/);
            if (m) where.push(`    di baris ${Math.max(1, m[1] - 3)}, kolom ${m[2]}`);
        }
        let msg = [head, ...where.slice(0, 3)].join("\n");
        if (err instanceof RangeError && /call stack/i.test(err.message)) {
            msg += "\n\nTips: rekursi terlalu dalam. Coba ubah DFS menjadi versi iteratif dengan stack.";
        }
        self.postMessage({ status: "re", output: out.join(""), error: msg, time: performance.now() - t0 });
    }
};
