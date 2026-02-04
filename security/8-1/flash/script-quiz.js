

window.onload = displayFinalResult;

function gradeLongQuiz8_2() {
  let score = 0;
  const answers = {
    q1: 'B', q2: 'A', q3: 'A', q4: 'A', q5: 'A',
    q6: 'B', q7: 'A', q8: 'A', q9: 'A', q10: 'A',
    q11: 'C', q12: 'B', q13: 'A', q14: 'C', q15: 'B',
    q16: 'A', q17: 'C', q18: 'B', q19: 'A', q20: 'C'
  };

  for (let q in answers) {
    const selected = document.querySelector(`input[name="${q}"]:checked`);
    if (selected && selected.value === answers[q]) {
      score++;
    }
  }

  const total = Object.keys(answers).length;
  const percentage = (score / total) * 100;

  // Save score in localStorage
  localStorage.setItem('finalScore', score);
  localStorage.setItem('finalPercentage', percentage.toFixed(2));

  // Show message before redirect
  document.getElementById('quiz-score').textContent =
    `You scored ${score} out of ${total} (${percentage.toFixed(2)}%). Redirecting...`;

  // Redirect after 2 seconds
  setTimeout(() => {
    window.location.href = '8-2-module-complete.html';
  }, 2000);
}

document.addEventListener('DOMContentLoaded', () => {
  const headers = document.querySelectorAll('.accordion-header');
  headers.forEach(header => {
    header.addEventListener('click', () => {
      const content = header.nextElementSibling;
      const isOpen = content.style.display === 'block';
      document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');
      content.style.display = isOpen ? 'none' : 'block';
    });
  });
});

function displayFinalResult() {
  const score = localStorage.getItem('finalScore');
  const percentage = localStorage.getItem('finalPercentage');
    if (score !== null && percentage !== null) {
    document.getElementById('final-result').textContent =
        `You scored ${score} out of 20 (${percentage}%).`;
    } else {
    document.getElementById('final-result').textContent =
        'No quiz results found.';
    }
}  

document.getElementById('submit-quiz').addEventListener('click', gradeLongQuiz);

