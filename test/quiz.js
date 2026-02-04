
function parseCSV(csvText) {
  const rows = [];
  let row = [];
  let field = "";
  let inQuotes = false;

  for (let i = 0; i < csvText.length; i++) {
    const char = csvText[i];
    const next = csvText[i + 1];

    if (char === '"' && inQuotes && next === '"') {
      field += '"';
      i++;
    } else if (char === '"') {
      inQuotes = !inQuotes;
    } else if (char === ',' && !inQuotes) {
      row.push(field);
      field = "";
    } else if ((char === '\n' || char === '\r') && !inQuotes) {
      if (char === '\r' && next === '\n') i++;
      row.push(field);
      field = "";
      if (row.some(cell => cell.trim() !== "")) rows.push(row);
      row = [];
    } else {
      field += char;
    }
  }

  if (field.length > 0 || row.length > 0) {
    row.push(field);
    if (row.some(cell => cell.trim() !== "")) rows.push(row);
  }

  return rows;
}

async function loadCSVRecords() {
  const resp = await fetch('questions.csv', { cache: 'no-store' });
  if (!resp.ok) throw new Error(`Failed to load CSV: ${resp.status} ${resp.statusText}`);

  const text = await resp.text();
  const parsed = parseCSV(text);
  const header = parsed.shift();

  if (!header) throw new Error('CSV header missing.');

  // Build a header index map so QuestionNumber can be removed later.
  const idx = {};
  header.forEach((h, i) => { idx[h.trim()] = i; });

  const required = ['Section','QuestionText','OptionA','OptionB','OptionC','CorrectOption'];
  required.forEach(col => {
    if (idx[col] === undefined) throw new Error(`CSV missing required column: ${col}`);
  });

  // Return an array of records in file order (data-row index = array index)
  const records = parsed
    .filter(r => r.some(cell => (cell || '').trim() !== ''))
    .map(r => ({
      section: (r[idx['Section']] || '').trim(),
      correct: (r[idx['CorrectOption']] || '').trim().toLowerCase(),
    }));

  return records;
}

async function gradeSelectedQuiz() {
  if (!window.quizConfig || !Array.isArray(window.quizConfig.rowIds)) {
    alert('Quiz configuration missing. Reload the quiz');
    return;
  }

  const rowIds = window.quizConfig.rowIds;

  let records;
  try {
    records = await loadCSVRecords();
  } catch (e) {
    console.error(e);
    alert('Could not load answers from CSV. Check the console.');
    return;
  }

  let score = 0;
  const total = rowIds.length;

  for (let i = 0; i < rowIds.length; i++) {
    const rowId = rowIds[i];
    const rec = records[rowId];
    if (!rec) continue;

    const correct = rec.correct;
    // Radio group names are q0, q1, q2... based on rendered order (NOT question number)
    const selected = document.querySelector(`input[name="q${i}"]:checked`);
    if (!selected) continue;

    const chosen = (selected.value || '').trim().toLowerCase();
    if (chosen === correct) score++;
  }

  const percentage = total === 0 ? 0 : (score / total) * 100;

  // Store results for module-complete page
  localStorage.setItem('finalScore', String(score));
  localStorage.setItem('finalTotal', String(total));
  localStorage.setItem('finalPercentage', percentage.toFixed(2));
  localStorage.setItem('finalSection', window.quizConfig.section || '');

  window.location.href = 'module-complete.php';
}

document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('submit-quiz');
  if (!btn) {
    console.error('Submit button not found (id="submit-quiz").');
    return;
  }
  btn.addEventListener('click', gradeSelectedQuiz);
});
