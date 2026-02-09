// === Static pass mark ===
const PASS_THRESHOLD = 70;

// --- CSV parsing ---
function parseCSV(csvText){
    const rows = [];
    let row = [], field = "", inQuotes = false;

    for (let i=0; i<csvText.length; i++){
        const char = csvText[i], next = csvText[i+1];

        if (char === '"' && inQuotes && next === '"'){
            field += '"'; i++;
        } else if (char === '"'){
            inQuotes = !inQuotes;
        } else if (char === ',' && !inQuotes){
            row.push(field); field = "";
        } else if ((char === '\n' || char === '\r') && !inQuotes){
            if (char === '\r' && next === '\n') i++;
            row.push(field);
            if (row.some(c=>c.trim()!=="")) rows.push(row);
            row=[]; field="";
        } else {
            field += char;
        }
    }

    if (field.length>0 || row.length>0){
        row.push(field);
        if (row.some(c=>c.trim()!=="")) rows.push(row);
    }

    return rows;
}

async function loadCSVRecords(){
    const resp = await fetch(window.quizConfig.csvFile, {cache:"no-store"});
    if (!resp.ok) throw new Error("CSV load failed");
    const text = await resp.text();
    const parsed = parseCSV(text);
    const header = parsed.shift();

    const map = {};
    header.forEach((h,i)=> map[h]=i);

    return parsed.map(r=>({
        section:  (r[map["Section"]] ?? "").trim(),
        criteria: (r[map["Criteria"]] ?? "").trim(),
        topic:    (r[map["Topic1"]] ?? "").trim(),
        text:     (r[map["QuestionText"]] ?? "").trim(),
        A:        (r[map["OptionA"]] ?? "").trim(),
        B:        (r[map["OptionB"]] ?? "").trim(),
        C:        (r[map["OptionC"]] ?? "").trim(),
        D:        (r[map["OptionD"]] ?? "").trim(),
        correct:  (r[map["CorrectOption"]] ?? "").trim().toLowerCase(),
        description: map["Description"] !== undefined 
            ? (r[map["Description"]] ?? "").trim()
            : ""
    }));
}

async function gradeSelectedQuiz(){

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

    for (let i=0; i<rowIds.length; i++){
        const rowId = rowIds[i];
        const rec = records[rowId];
        if (!rec) continue;

        const selected = document.querySelector(`input[name="q${i}"]:checked`);
        const chosen = selected?.value?.toLowerCase() ?? "";

        const isCorrect = chosen === rec.correct;
        if (isCorrect) score++;

        review.push({
            number: i+1,
            rowId,
            question: rec.text,
            options: {A:rec.A, B:rec.B, C:rec.C, D:rec.D},
            chosen,
            correct: rec.correct,
            description: rec.description
        });
    }

    const percentage = total===0 ? 0 : (score / total * 100).toFixed(2);

    // Save results for review page
    localStorage.setItem("reviewSet", JSON.stringify(review));
    localStorage.setItem("reviewScore", score);
    localStorage.setItem("reviewTotal", total);
    localStorage.setItem("reviewPct", percentage);

    // Display score on SAME PAGE
    const container = document.getElementById("quiz-result");
    container.innerHTML = `
        <div class="question" style="text-align:center;">
            <h2>Your Score</h2>
            <h3>You scored <strong>${score}</strong> out of <strong>${total}</strong> (${percentage}%).</h3>
            <a href="review-answers.php" class="review-button">Review Answers</a>
        </div>
    `;

    // Hide the submit button so quiz is not re-submitted
    document.getElementById("submit-quiz").style.display = "none";
}

document.addEventListener("DOMContentLoaded",()=>{
    const btn = document.getElementById("submit-quiz");
    if (btn) btn.addEventListener("click", gradeSelectedQuiz);
});