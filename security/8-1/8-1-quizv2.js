// Load CSV answers and grade quiz
async function loadAnswers() {
    const response = await fetch("8-1-questions.csv");
    const text = await response.text();

    const lines = text.trim().split("\n");
    lines.shift(); // remove header

    const answers = {};

    lines.forEach(line => {
        const cols = line.split(",");
        const num = cols[1];
        const correct = cols[6];
        answers["q" + num] = correct;
    });

    return answers;
}

async function gradeQuiz() {
    const answers = await loadAnswers();
    let score = 0;

    for (let q in answers) {
        const selected = document.querySelector(`input[name="${q}"]:checked`);
        if (selected && selected.value === answers[q]) {
            score++;
        }
    }

    const total = Object.keys(answers).length;
    const percentage = (score / total) * 100;

    localStorage.setItem('finalScore', score);
    localStorage.setItem('finalPercentage', percentage.toFixed(2));

    window.location.href = "8-1-module-completev2.php";
}

document.getElementById("submit-quiz")
    .addEventListener("click", gradeQuiz);

    window.onload = displayFinalResult;