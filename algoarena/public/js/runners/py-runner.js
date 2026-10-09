/**
 * Worker penjalan Python (Pyodide / CPython yang dikompilasi ke WebAssembly).
 * Worker ini dipakai ulang antar-eksekusi karena memuat Pyodide cukup lama (±3 detik pertama kali).
 */
let pyodide = null;
let runner = null;

const BOOT = `
import sys, io, traceback, builtins

def __aa_run(code, data):
    # stdin/stdout berupa TextIOWrapper di atas BytesIO, sama seperti judge sungguhan:
    # input(), sys.stdin.readline(), sys.stdin.read(), dan sys.stdin.buffer.read() semuanya bisa dipakai.
    sys.stdin = io.TextIOWrapper(io.BytesIO(data.encode("utf-8")), encoding="utf-8")
    out_bytes = io.BytesIO()
    out = io.TextIOWrapper(out_bytes, encoding="utf-8", newline="\n", write_through=True)
    old_out = sys.stdout
    sys.stdout = out

    def _input(prompt=""):
        line = sys.stdin.readline()
        if line == "":
            raise EOFError("EOF when reading a line")
        return line.rstrip("\\n")

    env = {"__name__": "__main__", "input": _input}
    err = None
    try:
        exec(compile(code, "solusi.py", "exec"), env)
    except SystemExit:
        pass
    except RecursionError:
        err = "RecursionError: rekursi terlalu dalam.\\nTips: gunakan versi iteratif atau sys.setrecursionlimit()."
    except BaseException:
        lines = traceback.format_exc().splitlines()
        keep = [l for l in lines if "solusi.py" in l or not l.startswith("  File")]
        err = "\\n".join(keep[-6:])
    finally:
        try:
            out.flush()
        except Exception:
            pass
        sys.stdout = old_out
        sys.setrecursionlimit(1000)
    return out_bytes.getvalue().decode("utf-8", "replace"), err
`;

self.onmessage = async (e) => {
    const { type, indexURL, code, input } = e.data;
    try {
        if (type === "init") {
            importScripts(indexURL + "pyodide.js");
            pyodide = await loadPyodide({ indexURL });
            pyodide.runPython(BOOT);
            runner = pyodide.globals.get("__aa_run");
            self.postMessage({ type: "ready" });
            return;
        }
        if (type === "run") {
            const t0 = performance.now();
            const result = runner(code, input);
            const [output, error] = result.toJs();
            result.destroy();
            self.postMessage({
                type: "result",
                status: error ? "re" : "ok",
                output,
                error: error || undefined,
                time: performance.now() - t0,
            });
        }
    } catch (err) {
        self.postMessage({
            type: type === "init" ? "fail" : "result",
            status: "re",
            output: "",
            error: String(err),
            time: 0,
        });
    }
};
