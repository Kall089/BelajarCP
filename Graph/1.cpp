#include <bits/stdc++.h>
using namespace std;

int main() {
    ios_base::sync_with_stdio(false);
    cin.tie(NULL);

    int n, m;
    cin >> n >> m;

    vector<vector<int>> adj(n + 1);
    vector<vector<bool>> mat(n + 1, vector<bool> (m + 1, false));

    for (int i = 0; i < m; i++) {
        int u, v;
        cin >> u >> v;

        adj[u].push_back(v);
        adj[v].push_back(u);
        mat[v][u] = mat[u][v] = true;
    }

    for (int u = 1; u <= n; u++) {
        sort(adj[u].begin(), adj[u].end());
        cout << u << ":";
        for (int v : adj[u]) {
            cout << " " << v;
        }
        cout << "\n";
    }

    int q;
    cin >> q;

    while (q--) {
        int u, v;
        cin >> u >> v;  
        if (mat[u][v]) {
            cout << "YA" << '\n';
        } else {
            cout << "TIDAK" << "\n";
        }
    }
}