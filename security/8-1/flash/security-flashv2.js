
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Flashcards (Minimal, Button Load)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root{
      --border:#d0d7de;
      --btn-bg:#f7f7f7;
      --btn-bg-hover:#efefef;
    }
    body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;margin:1.5rem}
    h1{margin:0 0 1rem 0;font-size:1.3rem}
    .controls{margin-bottom:1rem;display:flex;gap:.6rem;align-items:center;flex-wrap:wrap}
    .controls button{
      padding:.55rem .9rem;border:1px solid var(--border);background:var(--btn-bg);
      border-radius:8px;cursor:pointer;transition:.15s background-color,.15s border-color;
    }
    .controls button:hover{background:var(--btn-bg-hover)}
    .controls button:disabled{opacity:.6;cursor:not-allowed}
    #status{margin:.5rem 0 1rem 0;color:#555}
    .card{border:1px solid var(--border);border-radius:12px;padding:1rem;max-width:880px;}
    .front{font-weight:700;font-size:1.05rem}
    .back{margin-top:.75rem;color:#222;display:none;white-space:pre-wrap}
    .actions{margin-top:1rem;display:flex;gap:.5rem;flex-wrap:wrap}
    .score{margin-top:1rem;font-weight:600}
    .nav{margin-top:.75rem;display:flex;gap:.5rem;flex-wrap:wrap}
    /* fully hide native input while keeping it operable */
    .visually-hidden {
      position: absolute !important;
      width: 1px !important; height: 1px !important;
      padding: 0 !important; margin: -1px !important;
      overflow: hidden !important; clip: rect(0 0 0 0) !important;
      white-space: nowrap !important; border: 0 !important;
    }
    .hidden{display:none}
  </style>
</head>
<body>
  <h1>Flashcards from CSV</h1>

  <div class="controls">
    <!-- Visible button (identical style to others) -->
    <button id="loadCsvBtn">Load CSV</button>
    <!-- Hidden file input that the button will trigger -->
    <input type="file" id="csvInput" accept=".csv" class="visually-hidden" />
    <!-- Other buttons (disabled until CSV loads) -->
    <button id="resetBtn" disabled>Reset Progress</button>
  </div>

  <div id="status">No CSV loaded.</div>

  <div id="cardArea" class="card hidden">
    <div class="front" id="question"></div>
    <div class="back" id="answer"></div>

    <div class="actions">
      <button id="revealBtn">Reveal Answer</button>
      <button id="correctBtn" disabled>✔ Correct</button>
      <button id="incorrectBtn" disabled>✘ Incorrect</button>
    </div>

    <div class="nav">
      <button id="prevBtn" disabled>⟵ Previous</button>
      <button id="nextBtn" disabled>Next ⟶</button>
    </div>

    <div class="score" id="score">Score: 0 correct out of 0</div>
  </div>

  <script>
    // ---------- Wire the visible button to the hidden input ----------
    const loadCsvBtn = document.getElementById('loadCsvBtn');
    const csvInput   = document.getElementById('csvInput');

    loadCsvBtn.addEventListener('click', () => {
      // IMPORTANT: this must be a direct response to a user click
      csvInput.click();
    });

    // ---------- Simple CSV parser ----------
    function parseCSV(text) {
      const rows = [];
      let i = 0, field = '', row = [], inQuotes = false;

      while (i < text.length) {
        const char = text[i];
        if (inQuotes) {
          if (char === '"') {
            if (text[i + 1] === '"') { field += '"'; i += 2; continue; }
            inQuotes = false; i++; continue;
          } else { field += char; i++; continue; }
        } else {
          if (char === '"') { inQuotes = true; i++; continue; }
          if (char === ',') { row.push(field); field = ''; i++; continue; }
          if (char === '\n') { row.push(field); field = ''; rows.push(row); row = []; i++; continue; }
          if (char === '\r') { i++; continue; }
          field += char; i++;
        }
      }
      if (field.length > 0 || row.length > 0) { row.push(field); rows.push(row); }
      return rows;
    }

    // ---------- App state ----------
    let cards = [];         // [{ id, question, answer }]
    let current = 0;
    let correctCount = 0;
    let answered = [];

    // ---------- DOM ----------
    const status     = document.getElementById('status');
    const cardArea   = document.getElementById('cardArea');
    const questionEl = document.getElementById('question');
    const answerEl   = document.getElementById('answer');
    const revealBtn  = document.getElementById('revealBtn');
    const correctBtn = document.getElementById('correctBtn');
    const incorrectBtn = document.getElementById('incorrectBtn');
    const prevBtn    = document.getElementById('prevBtn');
    const nextBtn    = document.getElementById('nextBtn');
    const scoreEl    = document.getElementById('score');
    const resetBtn   = document.getElementById('resetBtn');

    function updateScore() {
      const totalAnswered = answered.filter(x => x !== null).length;
      scoreEl.textContent = `Score: ${correctCount} correct out of ${totalAnswered} (total cards: ${cards.length})`;
    }

    function showCard(index) {
      const c = cards[index];
      questionEl.textContent = c.question;
      answerEl.textContent = c.answer;
      answerEl.style.display = 'none';

      revealBtn.disabled = false;
      correctBtn.disabled = true;
      incorrectBtn.disabled = true;

      prevBtn.disabled = index <= 0;
      nextBtn.disabled = index >= cards.length - 1;

      status.textContent = `Card ${index + 1} of ${cards.length}`;
      cardArea.classList.remove('hidden');
    }

    function resetProgress() {
      correctCount = 0;
      answered = Array(cards.length).fill(null);
      current = 0;
      updateScore();
      showCard(0);
    }

    // ---------- File input change: read & parse CSV ----------
    csvInput.addEventListener('change', async (e) => {
      const file = e.target.files && e.target.files[0];
      if (!file) {
        status.textContent = 'No file selected.';
        return;
      }

      try {
        const text = await file.text();
        const rows = parseCSV(text);

        if (rows.length < 2) {
          status.textContent = 'CSV seems empty or lacks data rows.';
          cardArea.classList.add('hidden');
          return;
        }

        const header = rows[0].map(h => h.trim().toLowerCase());
        const idxId = header.indexOf('id');
        const idxQ  = header.indexOf('question');
        const idxA  = header.indexOf('answer');

        if (idxQ === -1 || idxA === -1) {
          status.textContent = 'CSV must contain headers: "question" and "answer" (optional "id").';
          cardArea.classList.add('hidden');
          return;
        }

        cards = rows.slice(1)
          .filter(r => r.length >= Math.max(idxQ, idxA) + 1)
          .map(r => ({
            id: idxId !== -1 ? r[idxId] : '',
            question: r[idxQ],
            answer: r[idxA]
          }));

        if (!cards.length) {
          status.textContent = 'No valid flashcard rows found.';
          cardArea.classList.add('hidden');
          return;
        }

        answered = Array(cards.length).fill(null);
        correctCount = 0;
        current = 0;

        status.textContent = `Loaded ${cards.length} flashcards from "${file.name}".`;
        resetBtn.disabled = false;
        updateScore();
        showCard(current);
      } catch (err) {
        console.error(err);
        status.textContent = 'Failed to read the file. Try a different CSV.';
      } finally {
        // Clear the input so selecting the same file again will retrigger change
        e.target.value = '';
      }
    });

    // ---------- Card actions ----------
    revealBtn.addEventListener('click', () => {
      answerEl.style.display = 'block';
      correctBtn.disabled = false;
      incorrectBtn.disabled = false;
      revealBtn.disabled = true;
    });

    correctBtn.addEventListener('click', () => {
      if (answered[current] === null) {
        answered[current] = true;
        correctCount += 1;
        updateScore();
      }
      correctBtn.disabled = true;
      incorrectBtn.disabled = true;
      revealBtn.disabled = false;
    });

    incorrectBtn.addEventListener('click', () => {
      if (answered[current] === null) {
        answered[current] = false;
        updateScore();
      }
      correctBtn.disabled = true;
      incorrectBtn.disabled = true;
      revealBtn.disabled = false;
    });

    prevBtn.addEventListener('click', () => {
      if (current > 0) { current -= 1; showCard(current); }
    });

    nextBtn.addEventListener('click', () => {
      if (current < cards.length - 1) { current += 1; showCard(current); }
    });

    resetBtn.addEventListener('click', resetProgress);
  </script>

  <!-- Example CSV format:
  id,question,answer
  1,What is the capital of France?,Paris
  2,2 + 2 = ?,4
  3,HTML stands for?,HyperText Markup Language
  -->
</body>
