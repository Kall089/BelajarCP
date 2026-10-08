<?php

/*
 * Starter & solusi C++ untuk soal yang ditulis sebelum C++ menjadi bahasa utama.
 * Soal baru menulis 'cpp' langsung di 'starter' dan 'solutions' miliknya.
 * Semua kode harus lolos C++14 (GCC 6.3): tanpa structured binding.
 */

$graphStarter = <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1); // adj[u] = daftar tetangga u
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    // Tulis solusimu di sini

    return 0;
}
CPP;

$gridStarter = <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<string> grid(R);
    for (int i = 0; i < R; i++) cin >> grid[i];

    // Tulis solusimu di sini

    return 0;
}
CPP;

$twoStringsStarter = <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string a, b;
    cin >> a >> b;
    int n = a.size(), m = b.size();

    // Tulis solusimu di sini

    return 0;
}
CPP;

return [
    'derajat-simpul' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        // Tulis solusimu di sini
    }

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> deg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        deg[u]++; // sisi u-v menambah derajat u
        deg[v]++; // ...dan juga derajat v (tak berarah)
    }

    for (int i = 1; i <= n; i++) {
        cout << deg[i] << (i < n ? ' ' : '\n');
    }
    return 0;
}
CPP,
    ],

    'daftar-tetangga' => [
        'starter' => $graphStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v); // jalan dua arah: catat di kedua ujung
        adj[v].push_back(u);
    }

    for (int i = 1; i <= n; i++) {
        sort(adj[i].begin(), adj[i].end()); // tetangga dari nomor terkecil
        cout << i << ':';
        for (int v : adj[i]) cout << ' ' << v;
        cout << '\n';
    }
    return 0;
}
CPP,
    ],

    'jarak-pertemanan' => [
        'starter' => $graphStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    vector<int> dist(n + 1, -1); // -1 = belum ditemukan
    queue<int> q;
    dist[1] = 0;
    q.push(1);
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        for (int v : adj[u]) {
            if (dist[v] == -1) {
                dist[v] = dist[u] + 1; // satu lapis lebih jauh dari u
                q.push(v);
            }
        }
    }

    for (int i = 1; i <= n; i++) {
        cout << dist[i] << (i < n ? ' ' : '\n');
    }
    return 0;
}
CPP,
    ],

    'labirin' => [
        'starter' => $gridStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<string> grid(R);
    for (int i = 0; i < R; i++) cin >> grid[i];

    int sr = 0, sc = 0, er = 0, ec = 0;
    for (int r = 0; r < R; r++) {
        for (int c = 0; c < C; c++) {
            if (grid[r][c] == 'S') { sr = r; sc = c; }
            if (grid[r][c] == 'E') { er = r; ec = c; }
        }
    }

    const int dr[4] = {-1, 1, 0, 0};
    const int dc[4] = {0, 0, -1, 1};
    vector<vector<int>> dist(R, vector<int>(C, -1));
    queue<pair<int, int>> q;
    dist[sr][sc] = 0;
    q.push({sr, sc});

    while (!q.empty()) {
        int r = q.front().first, c = q.front().second;
        q.pop();
        for (int k = 0; k < 4; k++) {
            int nr = r + dr[k], nc = c + dc[k];
            if (nr < 0 || nr >= R || nc < 0 || nc >= C) continue; // keluar grid
            if (grid[nr][nc] == '#' || dist[nr][nc] != -1) continue; // tembok / sudah dikunjungi
            dist[nr][nc] = dist[r][c] + 1;
            q.push({nr, nc});
        }
    }

    cout << dist[er][ec] << '\n';
    return 0;
}
CPP,
    ],

    'hitung-pulau' => [
        'starter' => $gridStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int R, C;
vector<string> grid;
vector<vector<bool>> seen;

void dfs(int r, int c) {
    if (r < 0 || r >= R || c < 0 || c >= C) return; // keluar peta
    if (grid[r][c] != '#' || seen[r][c]) return;    // air atau sudah ditandai
    seen[r][c] = true;
    dfs(r - 1, c);
    dfs(r + 1, c);
    dfs(r, c - 1);
    dfs(r, c + 1);
}

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    cin >> R >> C;
    grid.resize(R);
    for (int i = 0; i < R; i++) cin >> grid[i];
    seen.assign(R, vector<bool>(C, false));

    int pulau = 0;
    for (int r = 0; r < R; r++) {
        for (int c = 0; c < C; c++) {
            if (grid[r][c] == '#' && !seen[r][c]) {
                pulau++;   // daratan baru yang belum dijelajahi = pulau baru
                dfs(r, c); // tandai seluruh pulau ini
            }
        }
    }

    cout << pulau << '\n';
    return 0;
}
CPP,
    ],

    'komponen-terhubung' => [
        'starter' => $graphStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;
        adj[u].push_back(v);
        adj[v].push_back(u);
    }

    vector<bool> seen(n + 1, false);
    int kelompok = 0, terbesar = 0;
    for (int s = 1; s <= n; s++) {
        if (seen[s]) continue;
        kelompok++; // s belum pernah dikunjungi: kelompok baru
        int ukuran = 0;
        stack<int> st; // DFS iteratif sambil menghitung ukuran
        st.push(s);
        seen[s] = true;
        while (!st.empty()) {
            int u = st.top();
            st.pop();
            ukuran++;
            for (int v : adj[u]) {
                if (!seen[v]) {
                    seen[v] = true;
                    st.push(v);
                }
            }
        }
        terbesar = max(terbesar, ukuran);
    }

    cout << kelompok << ' ' << terbesar << '\n';
    return 0;
}
CPP,
    ],

    'ongkos-kirim' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<pair<int, int>>> adj(n + 1); // {tetangga, ongkos}
    for (int i = 0; i < m; i++) {
        int u, v, w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        adj[v].push_back({u, w});
    }

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<pair<int, int>>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v, w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w});
        adj[v].push_back({u, w});
    }

    vector<long long> dist(n + 1, INF);
    // min-heap berisi {jarak, simpul}: yang jaraknya terkecil keluar duluan
    priority_queue<pair<long long, int>, vector<pair<long long, int>>, greater<pair<long long, int>>> pq;
    dist[1] = 0;
    pq.push({0, 1});
    while (!pq.empty()) {
        long long d = pq.top().first;
        int u = pq.top().second;
        pq.pop();
        if (d > dist[u]) continue; // data usang, u sudah final
        for (auto &e : adj[u]) {
            int v = e.first, w = e.second;
            if (dist[u] + w < dist[v]) { // relaksasi
                dist[v] = dist[u] + w;
                pq.push({dist[v], v});
            }
        }
    }

    cout << (dist[n] == INF ? -1 : dist[n]) << '\n';
    return 0;
}
CPP,
    ],

    'rute-tercepat' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, s;
    cin >> n >> m >> s;
    vector<vector<pair<int, int>>> adj(n + 1); // {tujuan, waktu}
    for (int i = 0; i < m; i++) {
        int u, v, w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w}); // satu arah: hanya dari u ke v
    }

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long INF = LLONG_MAX / 4;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m, s;
    cin >> n >> m >> s;
    vector<vector<pair<int, int>>> adj(n + 1);
    for (int i = 0; i < m; i++) {
        int u, v, w;
        cin >> u >> v >> w;
        adj[u].push_back({v, w}); // satu arah: hanya dari u ke v
    }

    vector<long long> dist(n + 1, INF);
    priority_queue<pair<long long, int>, vector<pair<long long, int>>, greater<pair<long long, int>>> pq;
    dist[s] = 0;
    pq.push({0, s});
    while (!pq.empty()) {
        long long d = pq.top().first;
        int u = pq.top().second;
        pq.pop();
        if (d > dist[u]) continue;
        for (auto &e : adj[u]) {
            if (d + e.second < dist[e.first]) {
                dist[e.first] = d + e.second;
                pq.push({dist[e.first], e.first});
            }
        }
    }

    for (int i = 1; i <= n; i++) {
        cout << (dist[i] == INF ? -1 : dist[i]) << (i < n ? ' ' : '\n');
    }
    return 0;
}
CPP,
    ],

    'urutan-mapel' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1);
    vector<int> indeg(n + 1, 0); // banyak prasyarat setiap materi
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        indeg[b]++;
    }

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<vector<int>> adj(n + 1);
    vector<int> indeg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        indeg[b]++;
    }

    // Kahn dengan min-heap: selalu ambil materi bebas bernomor terkecil
    priority_queue<int, vector<int>, greater<int>> pq;
    for (int i = 1; i <= n; i++) {
        if (indeg[i] == 0) pq.push(i);
    }
    vector<int> urutan;
    while (!pq.empty()) {
        int u = pq.top();
        pq.pop();
        urutan.push_back(u);
        for (int v : adj[u]) {
            if (--indeg[v] == 0) pq.push(v); // semua prasyarat v sudah selesai
        }
    }

    if ((int)urutan.size() < n) {
        cout << "MUSTAHIL\n"; // ada siklus: sebagian materi tidak pernah bebas
    } else {
        for (int i = 0; i < n; i++) cout << urutan[i] << (i + 1 < n ? ' ' : '\n');
    }
    return 0;
}
CPP,
    ],

    'waktu-proyek' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> t(n + 1);
    for (int i = 1; i <= n; i++) cin >> t[i];
    vector<vector<int>> adj(n + 1);
    vector<int> indeg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        indeg[b]++;
    }

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<int> t(n + 1);
    for (int i = 1; i <= n; i++) cin >> t[i];
    vector<vector<int>> adj(n + 1);
    vector<int> indeg(n + 1, 0);
    for (int i = 0; i < m; i++) {
        int a, b;
        cin >> a >> b;
        adj[a].push_back(b);
        indeg[b]++;
    }

    vector<long long> mulai(n + 1, 0), selesai(n + 1, 0);
    queue<int> q;
    for (int i = 1; i <= n; i++) {
        if (indeg[i] == 0) q.push(i);
    }
    long long jawaban = 0;
    while (!q.empty()) {
        int u = q.front();
        q.pop();
        selesai[u] = mulai[u] + t[u];
        jawaban = max(jawaban, selesai[u]);
        for (int v : adj[u]) {
            mulai[v] = max(mulai[v], selesai[u]); // tunggu prasyarat paling lama
            if (--indeg[v] == 0) q.push(v);
        }
    }

    cout << jawaban << '\n';
    return 0;
}
CPP,
    ],

    'fibonacci-modulo' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    int n;
    cin >> n;

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    int n;
    cin >> n;

    long long a = 0; // F(i)
    long long b = 1; // F(i + 1)
    for (int i = 0; i < n; i++) {
        long long c = (a + b) % MOD; // F(i + 2)
        a = b;
        b = c;
    }

    cout << a << '\n';
    return 0;
}
CPP,
    ],

    'lompatan-katak' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> h(n);
    for (int i = 0; i < n; i++) cin >> h[i];

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> h(n);
    for (int i = 0; i < n; i++) cin >> h[i];

    // dp[i] = energi minimum untuk sampai di batu i
    vector<long long> dp(n, 0);
    for (int i = 1; i < n; i++) {
        dp[i] = dp[i - 1] + abs(h[i] - h[i - 1]); // datang dari i-1
        if (i >= 2) {
            dp[i] = min(dp[i], dp[i - 2] + abs(h[i] - h[i - 2])); // atau dari i-2
        }
    }

    cout << dp[n - 1] << '\n';
    return 0;
}
CPP,
    ],

    'naik-tangga' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<bool> rusak(n + 1, false);
    for (int i = 0; i < m; i++) {
        int x;
        cin >> x;
        rusak[x] = true;
    }

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, m;
    cin >> n >> m;
    vector<bool> rusak(n + 1, false);
    for (int i = 0; i < m; i++) {
        int x;
        cin >> x;
        rusak[x] = true;
    }

    // dp[i] = banyak cara berdiri di anak tangga i
    vector<long long> dp(n + 1, 0);
    dp[0] = 1; // satu cara: belum melangkah
    for (int i = 1; i <= n; i++) {
        if (rusak[i]) continue; // tidak boleh diinjak, tetap 0
        dp[i] = dp[i - 1];
        if (i >= 2) dp[i] = (dp[i] + dp[i - 2]) % MOD;
    }

    cout << dp[n] << '\n';
    return 0;
}
CPP,
    ],

    'koin-minimum' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int k, x;
    cin >> k >> x;
    vector<int> koin(k);
    for (int i = 0; i < k; i++) cin >> koin[i];

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const int INF = 1e9;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int k, x;
    cin >> k >> x;
    vector<int> koin(k);
    for (int i = 0; i < k; i++) cin >> koin[i];

    // dp[v] = koin minimum untuk membentuk nominal v
    vector<int> dp(x + 1, INF);
    dp[0] = 0;
    for (int v = 1; v <= x; v++) {
        for (int c : koin) {
            if (c <= v && dp[v - c] != INF) {
                dp[v] = min(dp[v], dp[v - c] + 1); // pakai satu koin c, sisanya v - c
            }
        }
    }

    cout << (dp[x] == INF ? -1 : dp[x]) << '\n';
    return 0;
}
CPP,
    ],

    'jalur-grid' => [
        'starter' => $gridStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

const long long MOD = 1000000007;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<string> grid(R);
    for (int i = 0; i < R; i++) cin >> grid[i];

    // dp[i][j] = banyak jalur dari (0,0) ke (i,j)
    vector<vector<long long>> dp(R, vector<long long>(C, 0));
    for (int i = 0; i < R; i++) {
        for (int j = 0; j < C; j++) {
            if (grid[i][j] == '#') continue; // terhalang: 0 jalur
            if (i == 0 && j == 0) {
                dp[i][j] = 1;
                continue;
            }
            long long atas = i > 0 ? dp[i - 1][j] : 0;
            long long kiri = j > 0 ? dp[i][j - 1] : 0;
            dp[i][j] = (atas + kiri) % MOD;
        }
    }

    cout << dp[R - 1][C - 1] << '\n';
    return 0;
}
CPP,
    ],

    'koleksi-koin' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<vector<int>> a(R, vector<int>(C));
    for (int i = 0; i < R; i++)
        for (int j = 0; j < C; j++) cin >> a[i][j];

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int R, C;
    cin >> R >> C;
    vector<vector<int>> a(R, vector<int>(C));
    for (int i = 0; i < R; i++)
        for (int j = 0; j < C; j++) cin >> a[i][j];

    // dp[i][j] = koin maksimum yang terkumpul saat tiba di (i,j)
    vector<vector<long long>> dp(R, vector<long long>(C, 0));
    for (int i = 0; i < R; i++) {
        for (int j = 0; j < C; j++) {
            long long terbaik = 0;
            if (i > 0 && j > 0) terbaik = max(dp[i - 1][j], dp[i][j - 1]);
            else if (i > 0) terbaik = dp[i - 1][j]; // kolom 0: hanya dari atas
            else if (j > 0) terbaik = dp[i][j - 1]; // baris 0: hanya dari kiri
            dp[i][j] = terbaik + a[i][j];
        }
    }

    cout << dp[R - 1][C - 1] << '\n';
    return 0;
}
CPP,
    ],

    'ransel-pendaki' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, W;
    cin >> n >> W;
    vector<int> w(n), v(n);
    for (int i = 0; i < n; i++) cin >> w[i] >> v[i];

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n, W;
    cin >> n >> W;
    vector<int> w(n), v(n);
    for (int i = 0; i < n; i++) cin >> w[i] >> v[i];

    // dp[c] = nilai maksimum dengan kapasitas c (memakai barang yang sudah diproses)
    vector<long long> dp(W + 1, 0);
    for (int i = 0; i < n; i++) {
        // loop MUNDUR agar setiap barang hanya dipakai sekali
        for (int c = W; c >= w[i]; c--) {
            dp[c] = max(dp[c], dp[c - w[i]] + v[i]);
        }
    }

    cout << dp[W] << '\n';
    return 0;
}
CPP,
    ],

    'bagi-dua-adil' => [
        'starter' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    int total = 0;
    for (int i = 0; i < n; i++) {
        cin >> a[i];
        total += a[i];
    }

    // Tulis solusimu di sini

    return 0;
}
CPP,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    int n;
    cin >> n;
    vector<int> a(n);
    int total = 0;
    for (int i = 0; i < n; i++) {
        cin >> a[i];
        total += a[i];
    }

    // bisa[s] = apakah ada sekumpulan kantong berjumlah tepat s
    vector<bool> bisa(total + 1, false);
    bisa[0] = true;
    for (int x : a) {
        for (int s = total; s >= x; s--) { // mundur: knapsack 0/1
            if (bisa[s - x]) bisa[s] = true;
        }
    }

    // Bagian pertama sedekat mungkin ke total / 2
    for (int s = total / 2; s >= 0; s--) {
        if (bisa[s]) {
            cout << total - 2 * s << '\n';
            break;
        }
    }
    return 0;
}
CPP,
    ],

    'lcs-dna' => [
        'starter' => $twoStringsStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string a, b;
    cin >> a >> b;
    int n = a.size(), m = b.size();

    // dp[i][j] = LCS dari i huruf pertama a dan j huruf pertama b
    vector<vector<int>> dp(n + 1, vector<int>(m + 1, 0));
    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= m; j++) {
            if (a[i - 1] == b[j - 1]) {
                dp[i][j] = dp[i - 1][j - 1] + 1; // huruf sama: perpanjang diagonal
            } else {
                dp[i][j] = max(dp[i - 1][j], dp[i][j - 1]); // buang salah satu huruf
            }
        }
    }

    cout << dp[n][m] << '\n';
    return 0;
}
CPP,
    ],

    'jarak-edit' => [
        'starter' => $twoStringsStarter,
        'solution' => <<<'CPP'
#include <bits/stdc++.h>
using namespace std;

int main() {
    ios::sync_with_stdio(false);
    cin.tie(nullptr);

    string a, b;
    cin >> a >> b;
    int n = a.size(), m = b.size();

    // dp[i][j] = edit distance dari i huruf pertama a ke j huruf pertama b
    vector<vector<int>> dp(n + 1, vector<int>(m + 1, 0));
    for (int i = 0; i <= n; i++) dp[i][0] = i; // hapus semua
    for (int j = 0; j <= m; j++) dp[0][j] = j; // sisip semua

    for (int i = 1; i <= n; i++) {
        for (int j = 1; j <= m; j++) {
            if (a[i - 1] == b[j - 1]) {
                dp[i][j] = dp[i - 1][j - 1]; // huruf sama, tanpa operasi
            } else {
                dp[i][j] = 1 + min({dp[i - 1][j - 1],  // ganti
                                    dp[i - 1][j],      // hapus a[i-1]
                                    dp[i][j - 1]});    // sisip b[j-1]
            }
        }
    }

    cout << dp[n][m] << '\n';
    return 0;
}
CPP,
    ],
];
