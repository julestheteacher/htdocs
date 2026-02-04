// security/8-1-script-quiz.js
window.onload = displayFinalResult;

function gradeLongQuiz8_1() {
  let score = 0;
  const answers = {
    q1: "a", // Salaries and benefits
    q2: "a", // Client details
    q3: "a", // Usernames and passwords
    q4: "a", // Prevent competitors from offering higher wages
    q5: "a", // Protect privacy and prevent competitors contacting them
    q6: "a", // Prevent competitors from copying designs
    q7: "b", // Financial loss and legal action
    q8: "a", // Fines and refunds
    q9: "a"  // Legal action and lawsuits
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
  //document.getElementById('quiz-score').textContent =
   // `You scored ${score} out of ${total} (${percentage.toFixed(2)}%). Redirecting...`;

  // Redirect after 2 seconds
  setTimeout(() => {
    window.location.href = '8-1-module-complete.php';
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

document.getElementById('submit-quiz').addEventListener('click', gradeLongQuiz8_1);