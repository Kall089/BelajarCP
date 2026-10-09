#include <bits/stdc++.h>
using namespace std;

int main()
{
    int r, c;
    cin >> r >> c;

    vector<vector<char>> st(r, vector<char>(c));
    for (int i = 0; i < r; i++)
    {
        for (int j = 0; j < c; j++)
        {
            cin >> st[i][j];
        }
    }

    vector<vector<int>> dp(r, vector<int>(c));

    if (st[0][0] != '#')
    {
        dp[0][0] = 1;
    }

    for (int i = 0; i < r; i++)
    {
        for (int j = 0; j < c; j++)
        {
            if (i > 0 && st[i][j] != '#')
            {
                dp[i][j] += dp[i - 1][j];
            }
            if (j > 0 && st[i][j] != '#')
            {
                dp[i][j] += dp[i][j - 1];
            }
        }
    }

    cout << dp[r - 1][c - 1];
}