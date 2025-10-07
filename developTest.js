// デバッグしやすい合計例

function sumToN(n = 100) {
    let sum = 0;
    for (let i = 1; i <= n; i++) {
        sum += i;

        // VSCodeデバッガーの主要ポイントで一時停止
        if (i === 1 || i === Math.floor(n / 2) || i === n) {
        // 次の行にブレークポイントを設定するか、この文で実行を一時停止
        debugger;
        // また、結果が明確に確認できるよう、簡潔な進行状況行も出力する
        console.log(`[step] i=${i}, sum=${sum}`);
        }
    }
    return sum;
}

// `node developTest.js` または VSCode デバッグ経由で直接実行時にも動作
if (require.main === module) {
    const n = 100;
    const result = sumToN(n);
    debugger; // ★ ここで停止すれば `result` が見える
    console.log(`\n=== RESULT === sum(1..${n}) = ${result}`);
}

module.exports = { sumToN };