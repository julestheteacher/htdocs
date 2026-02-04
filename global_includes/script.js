function checkQuiz(formId, correctAnswer) {
  const answer = document.querySelector(`#${formId} input[name="q1"]:checked`);
  const result = document.getElementById(`${formId}-result`);
  if (!answer) {
    result.textContent = "Please select an answer.";
    return;
  }
  result.textContent = answer.value === correctAnswer ? "✅ Correct!" : "❌ Incorrect. Try again!";
}

function saveReflection(key, textareaId) {
  const reflection = document.getElementById(textareaId).value.trim();
  const result = document.getElementById(`${textareaId}-result`);
  if (reflection) {
    localStorage.setItem(key, reflection);
    result.textContent = "Reflection saved!";
  } else {
    result.textContent = "Please write your reflection.";
  }
}

function loadSummary() {
  const list = document.getElementById('summary-list');
  list.innerHTML = '';
  const keys = ['technicalReflection', 'humanReflection', 'physicalReflection'];
  keys.forEach(key => {
    const value = localStorage.getItem(key);
    if (value) {
      const li = document.createElement('li');
      li.textContent = `${key.replace('Reflection','')}: ${value}`;
      list.appendChild(li);
    }
  });
}



function displayFinalResult() {
  const score = localStorage.getItem('finalScore');
  const percentage = localStorage.getItem('finalPercentage');
  const resultElement = document.getElementById('final-result');
  const certElement = document.getElementById('certificate');
  const certScore = document.getElementById('cert-score');

  if (score && percentage) {
    resultElement.textContent = `Your final score is ${score} (${percentage}%).`;
    if (percentage >= 50) {
      certElement.style.display = 'block';
      certScore.textContent = `${score} (${percentage}%)`;
    }
  } else {
    resultElement.textContent = 'No quiz data found. Please complete the quiz first.';
  }
}

function downloadCertificate() {
  const score = localStorage.getItem('finalScore');
  const percentage = localStorage.getItem('finalPercentage');
  const certificateContent = `
    Certificate of Completion
    --------------------------
    Congratulations!
    You have successfully completed the Cybersecurity Module.
    Score: ${score} (${percentage}%)
  `;
  const blob = new Blob([certificateContent], { type: 'text/plain' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'Cybersecurity_Certificate.txt';
  link.click();
}

window.onload = displayFinalResult;

function gradeLongQuiz() {
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
    window.location.href = 'module-complete.html';
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

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('show');
}

function toggleSubmenu(event) {
  event.preventDefault();
  const parent = event.target.closest('.has-submenu');
  parent.classList.toggle('open');
}