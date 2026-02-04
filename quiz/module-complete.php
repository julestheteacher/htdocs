<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Module Complete</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --ok:#15803d;      /* green-700 */
      --bad:#b91c1c;     /* red-700 */
      --muted:#6b7280;   /* gray-500 */
      --brand:#e14586;   /* theme pink */
      --bg:#ffffff;
      --chip:#f3f4f6;    /* gray-100 */
    }
    body { font-family: system-ui, Arial, sans-serif; background:#fafafa; margin:2rem; }
    .card { max-width: 860px; margin: 0 auto; padding: 1.5rem 1.75rem; border: 1px solid #eee; border-radius: 14px; background: var(--bg); }
    h1 { margin: 0 0 .5rem; }
    .lead { font-size: 1.1rem; margin-bottom: .25rem; }
    .muted { color: var(--muted); }
    .stat { display:inline-block; padding:.2rem .55rem; border-radius:999px; background:var(--chip); margin-right:.5rem; }
    .pass { color: var(--ok); font-weight: 700; }
    .fail { color: var(--bad); font-weight: 700; }
    .actions { margin-top: 1rem; display: flex; gap: .75rem; flex-wrap: wrap; }
    .btn { display: inline-block; padding: .6rem 1rem; border-radius: 8px; text-decoration: none; color: #fff; background: var(--brand); }
    .btn.secondary { background: #6b7280; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Test Complete</h1>
    <p id="headline" class="lead">Loading your results…</p>

    <p class="muted">
      <span id="score"     class="stat"></span>
      <span id="percent"   class="stat"></span>
      <span id="section"   class="stat"></span>
      <span id="threshold" class="stat"></span>
    </p>

    <div class="actions">
      <a class="btn" href="review.php">Review answers</a>
      <a class="btn secondary" href="quiz.php">Change section / count</a>
      <a class="btn secondary" href="quiz.php">Retry Quiz</a>
    </div>
  </div>

  <script>
    (function(){
      // === Static pass mark ===
      const PASS_THRESHOLD = 70; // percent

      const read = (k) => window.localStorage.getItem(k);
      const toNum = (v, d=0) => {
        const n = Number(v);
        return Number.isFinite(n) ? n : d;
      };

      // Read stored summary
      const score = toNum(read('finalScore'), 0);
      const total = toNum(read('finalTotal'), 0);
      const storedPct = toNum(read('finalPercentage'), NaN);
      const pct = Number.isFinite(storedPct) ? storedPct : (total ? (score/total*100) : 0);
      const sec = read('finalSection') || '';

      // Decide pass (recompute to avoid any mismatch)
      const pass = (pct >= PASS_THRESHOLD);

      // Headline + chips
      const headline = document.getElementById('headline');
      headline.innerHTML = pass
        ? '🎉 <span class="pass">You passed</span> this quiz.'
        : '❗ <span class="fail">You did not meet the pass mark</span>.';

      document.getElementById('score').textContent     = `Score: ${score}/${total}`;
      document.getElementById('percent').textContent   = `Percentage: ${pct.toFixed(2)}%`;
      document.getElementById('section').textContent   = sec ? `Section: ${sec}` : 'Section: (not specified)';
      document.getElementById('threshold').textContent = `Pass mark: ${PASS_THRESHOLD}%`;
    })();
  </script>
</body>
</html>