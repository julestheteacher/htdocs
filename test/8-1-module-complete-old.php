<?php
include '../../index_includes/is_auth.php';
include '../../index_includes/check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Physical Vulnerabilities</title>
<link rel="stylesheet" href="/Revision_site/global_includes/styles.css">
<link rel="icon" type="image/x-icon" href="favicon.ico">

<style>
  /* Keep certificate hidden until the script verifies eligibility */
  #certificate { display: none; }
</style>

<script>
    window.onload = displayFinalResult;

    function displayFinalResult() 
    {
      const scoreRaw = localStorage.getItem('finalScore');
      const percentageRaw = localStorage.getItem('finalPercentage');

      const resultElement = document.getElementById('final-result');
      const certElement = document.getElementById('certificate');
      const certScore = document.getElementById('cert-score');
      const practiceElement = document.getElementById('practice-message');

      // Ensure certificate and practice blocks start hidden/safe
      if (certElement) certElement.style.display = 'none';
      if (practiceElement) practiceElement.style.display = 'none';

      // Parse as numbers safely
      const score = Number(scoreRaw);
      const percentage = Number(percentageRaw);

      if (!Number.isFinite(score) || !Number.isFinite(percentage)) {
        resultElement.textContent = 'No quiz data found. Please complete the quiz first.';
        return;
      }

      // Always show the final score summary
      resultElement.textContent = `Your final score is ${score} (${percentage}%).`;

      // Threshold: 70% or higher gets the certificate, otherwise practice
      if (percentage >= 70) {
        if (certElement) {
          certElement.style.display = 'block';
          if (certScore) certScore.textContent = `${score} (${percentage}%)`;
        }
        if (practiceElement) practiceElement.style.display = 'none';
      } else {
        if (practiceElement) {
          practiceElement.style.display = 'block';
          // (Optional) 
        }
        if (certElement) certElement.style.display = 'none';
      }
    }

</script>

</head>


<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <?php include 'php_includes/8-1-menu.php'; ?>
    </aside>

    <main class="content">
      <h1>Module Complete</h1>

      <section class="score-section">
        <h2>Your Performance</h2>

        <div id="score-box" class="score-box">
          <p id="final-result" class="score-text"></p>
        </div>

       
        <div id="practice-message" class="practice">
          <p></p>
          <h2>Further Practice Recommended</h2>
          <p>
          <p>You scored below 70%. Further practice needed</p>
          <!-- <a class="btn" href="8-1-review.html">Review the Module</a> -->
          </p>
        </div>

        <div id="certificate" class="certificate">
          <h2>🏆 Certificate of Completion</h2>
          <p>
          <p>Congratulations! You have successfully completed 8-1 Security Risks</p>
          <p>Your Score: <span id="cert-score"></span></p>
          <button onclick="downloadCertificate()">Download Certificate</button>
          </p>
        </div>

      </section>
    </main>
  </div>
</body>


</body>
</html>



