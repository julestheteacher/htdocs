<!DOCTYPE html>
<html>
<head>
<title>Review Your Answers</title>
<link rel="stylesheet" href="quiz-layout.css">
</head>
<body>

<h1>Review Your Answers</h1>

<div class="quiz-shell">
    <div class="quiz-layout">
        <div class="quiz-right" style="width:100%;">

<div id="review-area"></div>

        </div>
    </div>
</div>

<script>
// Load stored review set
const reviewSet = JSON.parse(localStorage.getItem("reviewSet") ?? "[]");
const score     = localStorage.getItem("reviewScore");
const total     = localStorage.getItem("reviewTotal");
const pct       = localStorage.getItem("reviewPct");

const area = document.getElementById("review-area");

area.innerHTML = `
    <div class="question" style="text-align:center;">
        <h2>Your Score</h2>
        <p><strong>${score}</strong> out of <strong>${total}</strong> (${pct}%)</p>
    </div>
`;

reviewSet.forEach(q=>{
    area.innerHTML += `
        <div class="question">
            <h3>${q.number}. ${q.question}</h3>

            <p><strong>Your answer:</strong> ${q.chosen.toUpperCase()}</p>
            <p><strong>Correct answer:</strong> ${q.correct.toUpperCase()}</p>

            ${q.description ? `<div class="hint"><em>${q.description}</em></div>` : ""}
        </div>
    `;
});

area.innerHTML += `
    <div style="text-align:center; margin-top:2rem;">
        <a href="quiz.php">Back to Quiz</a>
    </div>
`
</script>

</body>
</html>