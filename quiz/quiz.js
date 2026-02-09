// === Static pass mark ===
const PASS_THRESHOLD = 70;

// --- CSV parsing ---
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
            if (row.some(c => c.trim() !== "")) rows.push(row);
            row = [];
        } else {
            field += char;
        }
    }

    if (field.length > 0 || row.length > 0) {
        row.push(field);
        if (row.some(c => c.trim() !== "")) rows.push(row);
    }

    return rows;
}

async function loadCSVRecords() {
    const resp = await fetch(window.quizConfig.csvFile, { cache: "no-store" });

    if (!resp.ok) {
        alert("❌ Could not load CSV file: " + window.quizConfig.csvFile);
        throw new Error("CSV load failed at line 47");
    }

    const text = await resp.text();
    const parsed = parseCSV(text);

    const header = parsed.shift();
    const map = {};
    header.forEach((h, i) => (map[h] = i));

    const records = parsed.map(r => ({
        section: (r[map["Section"]] ?? "").trim(),
        criteria: (r[map["Criteria"]] ?? "").trim(),
        topic: (r[map["Topic1"]] ?? "").trim(),
        correct: (r[map["CorrectOption"]] ?? "").trim().toLowerCase()
    }));

    return records;
}

async function gradeSelectedQuiz() {
    const rowIds = window.quizConfig.rowIds;
    let records;

    try {
        records = await loadCSVRecords();
    } catch {
        alert("Could not load CSV.");
        return;
    }

    let score = 0;
    const total = rowIds.length;
    const review = [];

    for (let i = 0; i < rowIds.length; i++) {
        const rowId = rowIds[i];
        const rec = records[rowId];
        if (!rec) continue;

        const correct = rec.correct;
        const selected = document.querySelector(`input[name="q${i}"]:checked`);
        const chosen = selected?.value?.toLowerCase() ?? "";

        const isCorrect = chosen === correct;
        if (isCorrect) score++;

        review.push({
            index: i + 1,
            rowId,
            chosen,
            correct,
            isCorrect
        });
    }

    const percentage = total === 0 ? 0 : (score / total) * 100;
    const pass = percentage >= PASS_THRESHOLD;

    localStorage.setItem("finalScore", score);
    localStorage.setItem("finalTotal", total);
    localStorage.setItem("finalPercentage", percentage.toFixed(2));
    localStorage.setItem("finalSection", window.quizConfig.section);
    localStorage.setItem("finalPass", pass ? "1" : "0");
    localStorage.setItem("finalReview", JSON.stringify(review));

    window.location.href = "module-complete.php";
}

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("submit-quiz");
    if (btn) btn.addEventListener("click", gradeSelectedQuiz);
});