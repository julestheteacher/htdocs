<?php
include '../../index_includes/is_auth.php';
include '../../index_includes/check.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interactive Quiz</title>
<link rel="stylesheet" href="/Revision_site/global_includes/styles.css">
<script src="8-1-quiz.js"></script>
<link rel="icon" type="image/x-icon" href="favicon.ico">    

</head>
<body>
<div class="container">
  <aside class="sidebar">    
    <?php include 'php_includes/8-1-menu.php'; ?>
  </aside>
  <!-- Main Content -->
  <main class="content">
    <h1><i class="fas fa-question-circle"></i> Comprehensive Knowledge Quiz</h1>
    <p>Answer all 20 questions to test your understanding of cybersecurity vulnerabilities:</p>
    <form id="quiz-form">
    <?php include '8-1-get-questions.php'; ?>

    <button type="button" id="submit-quiz">Submit Quiz</button>
    </form>
    <script src="8-1-quiz.js"></script>
    </main>
</div>  
</body>
</html>
