// === Static pass mark ===
const PASS_THRESHOLD = 70; // percent

// ------------------------------
// CSV parsing (unchanged logic)
// ------------------------------
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

  const idx = {};
  header.forEach((h, i) => { idx[h.trim()] = i; });

  const required = ['Section','QuestionText','OptionA','OptionB','OptionC','CorrectOption'];
  required.forEach(col => {
    if (idx[col] === undefined) throw new Error(`CSV missing required column: ${col}`);
  });

  // Keep only what we need for grading (display text comes from the DOM)
  const records = parsed
    .filter(r => r.some(cell => (cell || '').trim() !== ''))
    .map(r => ({
      section: (r[idx['Section']] || '').trim(),
      correct: (r[idx['CorrectOption']] || '').trim().toLowerCase(),
    }));

  return records;
}

// ------------------------------
// Grading + Review builder
// ------------------------------
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
  const review = []; // [{ index, rowId, question, chosen, chosenText, correct, correctText, isCorrect }]

  for (let i = 0; i < rowIds.length; i++) {
    const rowId = rowIds[i];
    const rec = records[rowId];
    if (!rec) continue;

    const correct = (rec.correct || '').trim().toLowerCase();

    // Prefer node by data-rowid, else fallback to nth .question
    const qNode = document.querySelector(`.question[data-rowid="${rowId}"]`) ||
                  document.querySelectorAll('.question')[i];

    // Question text: remove "1. " prefix from <h3> ("1. Question text")
    const qTextRaw = qNode?.querySelector('h3')?.textContent || '';
    const questionText = qTextRaw.replace(/^\s*\d+\.\s*/, '').trim();

    // Collect option texts from labels next to radio inputs
    const optionEntries = Array.from(qNode?.querySelectorAll('label > input') || []).map(inp => {
      const key = (inp.value || '').trim().toLowerCase(); // 'a' | 'b' | 'c'
      const text = (inp.parentElement?.textContent || '').trim();
      return { key, text };
    });

    // Selected choice (by rendered order: q0, q1, ...)
    const selected = document.querySelector(`input[name="q${i}"]:checked`);
    const chosen = (selected?.value || '').trim().toLowerCase();
    const chosenText = optionEntries.find(o => o.key === chosen)?.text ?? null;
    const correctText = optionEntries.find(o => o.key === correct)?.text ?? null;

    const isCorrect = chosen !== '' && chosen === correct;
    if (isCorrect) score++;

    review.push({
      index: i + 1,
      rowId,
      question: questionText,
      chosen: chosen || null,
      chosenText,
      correct,
      correctText,
      isCorrect
    });
  }

  const percentage = total === 0 ? 0 : (score / total) * 100;
  const pass = percentage >= PASS_THRESHOLD;

  // Persist results for the results & review pages
  localStorage.setItem('finalScore', String(score));
  localStorage.setItem('finalTotal', String(total));
  localStorage.setItem('finalPercentage', percentage.toFixed(2));
  localStorage.setItem('finalSection', window.quizConfig.section || '');
  localStorage.setItem('finalPass', pass ? '1' : '0'); // normalized
  // We no longer store threshold; we use static 70% on the pages
  localStorage.setItem('finalReview', JSON.stringify(review));

  // Go to results
  window.location.href = 'module-complete.php';
}

// Wire up submit
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('submit-quiz');
  if (!btn) {
    console.error('Submit button not found (id="submit-quiz").');
    return;
  }
  btn.addEventListener('click', gradeSelectedQuiz);
});