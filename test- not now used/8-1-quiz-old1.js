function parseCSV(csvText) {
  const rows = [];
  let row = [];
  let field = "";
  let inQuotes = false;

  for (let i = 0; i < csvText.length; i++) {
    const char = csvText[i];
    const next = csvText[i + 1];

    if (char === '"' && inQuotes && next === '"') {
      // Escaped quote
      field += '"';
      i++;
    } else if (char === '"') {
      inQuotes = !inQuotes;
    } else if (char === "," && !inQuotes) {
      row.push(field);
      field = "";
    } else if ((char === "\n" || char === "\r") && !inQuotes) {
      // End of line (support \r\n and \n)
      if (char === "\r" && next === "\n") i++;
      row.push(field);
      field = "";

      // Only push non-empty rows
      if (row.some(cell => cell.trim() !== "")) rows.push(row);

      row = [];
    } else {
      field += char;
    }
  }

  // Last field
  if (field.length > 0 || row.length > 0) {
    row.push(field);
    if (row.some(cell => cell.trim() !== "")) rows.push(row);
  }

  return rows;
}

async function loadAnswersFromCSV() {
  const response = await fetch("8-1-questions.csv", { cache: "no-store" });
  if (!response.ok) {
    throw new Error(`Failed to load CSV: ${response.status} ${response.statusText}`);
  }

  const text = await response.text();
  const parsed = parseCSV(text);

  // Remove header row
  const header = parsed.shift();
  if (!header || header.length < 7) {
    throw new Error("CSV header missing or incorrect format.");
  }

  // Build answers object: { q1: 'a', q2: 'b', ... }
  const answers = {};
  for (const cols of parsed) {
    // Expected columns:
    // 0 Section, 1 QuestionNumber, 2 QuestionText, 3 A, 4 B, 5 C, 6 CorrectOption
    if (cols.length < 7) continue;

    const num = (cols[1] ?? "").trim();
    const correct = (cols[6] ?? "").trim().toLowerCase();

    if (!num) continue;
    if (!["a", "b", "c"].includes(correct)) continue;

    answers["q" + num] = correct;
  }

  return answers;
}

async function gradeQuiz() {
  let answers;

  try {
    answers = await loadAnswersFromCSV();
  } catch (err) {
    console.error(err);
    alert("Could not load quiz answers. Check the console for details.");
    return;
  }

  const keys = Object.keys(answers);
  if (keys.length === 0) {
    alert("No answers were loaded from the CSV. Check CSV formatting.");
    return;
  }

  let score = 0;

  for (const q of keys) {
    const selected = document.querySelector(`input[name="${q}"]:checked`);
    if (!selected) continue;

    const chosen = (selected.value || "").trim().toLowerCase();
    if (chosen === answers[q]) score++;
  }

  const total = keys.length;
  const percentage = (score / total) * 100;

  // Save for module-complete page
  localStorage.setItem("finalScore", String(score));
  localStorage.setItem("finalTotal", String(total));
  localStorage.setItem("finalPercentage", percentage.toFixed(2));

  // Redirect
  window.location.href = "8-1-module-complete.php";
}

// Bind safely after DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("submit-quiz");
  if (!btn) {
    console.error('Submit button not found. Add id="submit-quiz" to your button.');
    return;
  }
  btn.addEventListener("click", gradeQuiz);
});
