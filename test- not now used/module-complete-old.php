<?php
include '../../index_includes/is_auth.php';
include '../../index_includes/check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Module Complete</title>
  <link rel="stylesheet" href="/Revision_site/global_includes/styles.css">
</head>
<body>

<div class="container">
  <aside class="sidebar">    
    <?php include 'php_includes/8-1-menu.php'; ?>
  </aside>
<main class="content">
  <h1>Test Complete</h1>

  <div id="score-output" class="score-box">Loading your results…</div>

  <!-- This will be filled by JS -->
  <div id="result-message"></div>

  <div id="certificate-box" class="score-box hidden">
    🎉 <strong>Congratulations!</strong><br>
    You passed this quiz.<br><br>
  </div>

  <p id="debug" class="muted"></p>

  <div class="actions">
    <a href="8-1-quiz.php">Retry Quiz</a>
  </div>
</div>

<script>
(function () {
  const scoreBox   = document.getElementById("score-output");
  const messageBox = document.getElementById("result-message");
  const certBox    = document.getElementById("certificate-box");
  const debug      = document.getElementById("debug");
  const title      = document.getElementById("module-title");

  // Read values saved by 8-1-quiz.js
  const scoreRaw = localStorage.getItem("finalScore");
  const totalRaw = localStorage.getItem("finalTotal");
  const pctRaw   = localStorage.getItem("finalPercentage");
  const section  = localStorage.getItem("finalSection");

  // If you want the section to show in the heading:
  if (section) title.textContent = `Section ${section} – Quiz Complete`;

  // Convert safely to numbers
  const score = scoreRaw !== null ? parseInt(scoreRaw, 10) : null;
  const total = totalRaw !== null ? parseInt(totalRaw, 10) : null;
  const pct   = pctRaw   !== null ? parseFloat(pctRaw)     : null;

  // Validate
  if (Number.isFinite(score) && Number.isFinite(total) && total > 0 && Number.isFinite(pct)) {

    // ✅ Show correct score + correct total + correct percentage
    scoreBox.innerHTML =
      `You scored <strong>${score}</strong> out of <strong>${total}</strong> ` +
      `(<strong>${pct.toFixed(2)}%</strong>).`;

    // ✅ Pass/Fail at 70%
    if (pct >= 70) {
      messageBox.innerHTML = `<p class="pass">Pass ✅ Great job!</p>`;
      certBox.classList.remove("hidden");
    } else {
      messageBox.innerHTML =
        `<p class="fail">Not yet ❌ You scored below 70%. Please review the module and try again.</p>`;
      certBox.classList.add("hidden");
    }

    debug.textContent = ""; // clear debug
  } else {
    // If the page is opened directly without taking the quiz first
    scoreBox.textContent = "No quiz results found.";
    messageBox.innerHTML = `<p class="fail">No results to display. Please complete the quiz first.</p>`;
    debug.textContent =
      "Debug: finalScore/finalTotal/finalPercentage not found or invalid. " +
      "Complete the quiz, then redirect here.";
  }
})();
localStorage.clear();
</script>

</body>
</html>