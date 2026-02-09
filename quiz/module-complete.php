<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Quiz Complete</title>

<link rel="stylesheet" href="quiz-layout.css">

<style>
.result-shell {
    max-width:600px;
    margin:3rem auto;
    text-align:center;
}

.result-card {
    background:#f8dfea;
    border-radius:16px;
    padding:2rem;
    border:2px solid #e4b9c9;
    box-shadow:0 4px 16px rgba(0,0,0,0.1);
}

.result-card h1 {
    margin-bottom:1rem;
}

.result-buttons a {
    display:inline-block;
    margin:0.6rem;
    padding:0.75rem 1.4rem;
    background:#e89cb5;
    color:white;
    border-radius:12px;
    text-decoration:none;
    font-size:1.1rem;
    transition:0.15s ease;
}

.result-buttons a:hover {
    background:#d37997;
}

.result-buttons a:active {
    transform:scale(0.95);
    background:#b85d7e;
}

.score-output {
    font-size:1.4rem;
    margin:1rem 0;
    font-weight:bold;
}
</style>

</head>
<body>

<div class="result-shell">
<div class="result-card">
<h1>You've completed the quiz!</h1>

<p id="scoreArea" class="score-output">Loading results…</p>

<div class="result-buttons">
<a href="review.php"> Review Answers</a>
<a href="quiz.php">Change Section / Count</a>

</div>
</div>
</div>

<script>
// display score
document.getElementById("scoreArea").innerText =
    `You scored ${localStorage.getItem("finalScore")} out of ${localStorage.getItem("finalTotal")} (${localStorage.getItem("finalPercentage")}%)`;

// retry
document.getElementById("retryQuizBtn").addEventListener("click", e=>{
    e.preventDefault();

    const course  = localStorage.getItem("lastCourse");
    const section = localStorage.getItem("lastSection");
    const filter  = localStorage.getItem("lastFilter");
    const value   = localStorage.getItem("lastValue");
    const count   = localStorage.getItem("lastCount");

});
</script>

</body>
</html>