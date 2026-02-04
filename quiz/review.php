<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Review Answers</title>
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
    .card { max-width: 980px; margin: 0 auto; padding: 1.5rem 1.75rem; border: 1px solid #eee; border-radius: 14px; background: var(--bg); }
    h1 { margin: 0 0 .5rem; }
    .muted { color: var(--muted); }
    .stat { display:inline-block; padding:.2rem .55rem; border-radius:999px; background:var(--chip); margin-right:.5rem; }
    .grid { margin-top: 1rem; }
    .q {
      padding: .9rem 1rem;
      border: 1px solid #eee; border-radius: 10px; background: #fff;
      margin: .6rem 0;
    }
    .q h3 { margin: 0 0 .6rem; font-size: 1rem; }
    .row { display: flex; gap: 1rem; flex-wrap: wrap; }
    .answer {
      display: inline-flex; align-items: center; gap: .5rem;
      padding: .25rem .55rem; border-radius: 999px; background: var(--chip);
    }
    .correct .label::before {
      content: "✔";
      color: var(--ok);
      font-weight: 900;
      margin-right: .35rem;
    }
    .incorrect .label::before {
      content: "✖";
      color: var(--bad);
      font-weight: 900;
      margin-right: .35rem;
    }
    .label { font-weight: 700; }
    .actions { margin-top: 1rem; display: flex; gap: .75rem; flex-wrap: wrap; }
    .btn { display: inline-block; padding: .6rem 1rem; border-radius: 8px; text-decoration: none; color: #fff; background: var(--brand); }
    .btn.secondary { background: #6b7280; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Review Answers</h1>
    <p class="muted">
      <span id="score"     class="stat"></span>
      <span id="percent"   class="stat"></span>
      <span id="section"   class="stat"></span>
      <span id="threshold" class="stat"></span>
    </p>

    <div id="list" class="grid" aria-live="polite"></div>

    <div class="actions">
      <a class="btn secondary" href="module-complete.php">Back to results</a>
      <a class="btn" href="quiz.php">Take another quiz</a>
    </div>
  </div>

  <script>
    (function(){
      const PASS_THRESHOLD = 70; // keep static here too

      const read = (k) => window.localStorage.getItem(k);
      const toNum = (v, d=0) => {
        const n = Number(v);
        return Number.isFinite(n) ? n : d;
      };

      // Summary chips
      const score = toNum(read('finalScore'), 0);
      const total = toNum(read('finalTotal'), 0);
      const storedPct = toNum(read('finalPercentage'), NaN);
      const pct = Number.isFinite(storedPct) ? storedPct : (total ? (score/total*100) : 0);
      const sec = read('finalSection') || '';

      document.getElementById('score').textContent     = `Score: ${score}/${total}`;
      document.getElementById('percent').textContent   = `Percentage: ${pct.toFixed(2)}%`;
      document.getElementById('section').textContent   = sec ? `Section: ${sec}` : 'Section: (not specified)';
      document.getElementById('threshold').textContent = `Pass mark: ${PASS_THRESHOLD}%`;

      // Review items
      const list = document.getElementById('list');
      let review = [];
      try {
        const raw = read('finalReview');
        review = raw ? JSON.parse(raw) : [];
      } catch (e) { review = []; }

      if (!Array.isArray(review) || review.length === 0) {
        list.innerHTML = '<p class="muted">No review data available. Please complete a quiz first.</p>';
        return;
      }

      const frag = document.createDocumentFragment();

      review.forEach(item => {
        const wrapper = document.createElement('div');
        wrapper.className = 'q';

        const isCorrect = !!item.isCorrect;

        // Your answer chip
        const yourChip = document.createElement('div');
        yourChip.className = `answer ${isCorrect ? 'correct' : 'incorrect'}`;
        yourChip.innerHTML =
          `<span class="label">${isCorrect ? 'Correct' : 'Your answer'}</span>
           <span>${item.chosenText ? item.chosenText : '<em>Not answered</em>'}</span>`;

        // Correct answer chip (always green tick)
        const correctChip = document.createElement('div');
        correctChip.className = 'answer correct';
        correctChip.innerHTML =
          `<span class="label">Correct answer</span>
           <span>${item.correctText ? item.correctText : '(not available)'}</span>`;

        const title = document.createElement('h3');
        title.textContent = `${item.index}. ${item.question || '(Question text unavailable)'}`;

        const row = document.createElement('div');
        row.className = 'row';
        row.appendChild(yourChip);
        row.appendChild(correctChip);

        wrapper.appendChild(title);
        wrapper.appendChild(row);
        frag.appendChild(wrapper);
      });

      list.replaceChildren(frag);
    })();
  </script>
</body>
</html>
