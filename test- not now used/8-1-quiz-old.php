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

    <form id="final-quiz">
      <!-- Questions -->
      <div class="quiz-container">
  <h2>8.1 Quiz: Privacy and Confidentiality</h2>
  <form id="quizForm">
      <!-- 8.1.1 Questions -->
    <div class="quiz-question">
      <p>1. Which of these is an example of Human Resources confidential information?</p>
      <label><input type="radio" name="q1" value="a"> Salaries and benefits</label><br>
      <label><input type="radio" name="q1" value="b"> Intellectual property</label><br>
      <label><input type="radio" name="q1" value="c"> Client details</label>
    </div>

    <div class="quiz-question">
      <p>2. Which of these is considered commercially sensitive information?</p>
      <label><input type="radio" name="q2" value="a"> Client details</label><br>
      <label><input type="radio" name="q2" value="b"> Staff personal details</label><br>
      <label><input type="radio" name="q2" value="c"> Usernames and passwords</label>
    </div>

    <div class="quiz-question">
      <p>3. Which of these is considered access information?</p>
      <label><input type="radio" name="q3" value="a"> Usernames and passwords</label><br>
      <label><input type="radio" name="q3" value="b"> Sales numbers</label><br>
      <label><input type="radio" name="q3" value="c"> Contracts</label>
    </div>

    
    <div class="quiz-question">
      <p>4. Why must salary information be kept confidential?</p>
      <label><input type="radio" name="q4" value="a"> To prevent competitors from offering higher wages</label><br>
      <label><input type="radio" name="q4" value="b"> To allow employees to compare salaries</label><br>
      <label><input type="radio" name="q4" value="c"> To share benefits publicly</label>
    </div>

    <div class="quiz-question">
      <p>5. Why must staff details be kept confidential?</p>
      <label><input type="radio" name="q5" value="a"> To protect privacy and prevent competitors from contacting them</label><br>
      <label><input type="radio" name="q5" value="b"> To allow public networking</label><br>
      <label><input type="radio" name="q5" value="c"> To share achievements publicly</label>
    </div>

    <div class="quiz-question">
      <p>6. Why must intellectual property be kept confidential?</p>
      <label><input type="radio" name="q6" value="a"> To prevent competitors from copying designs</label><br>
      <label><input type="radio" name="q6" value="b"> To allow free sharing of ideas</label><br>
      <label><input type="radio" name="q6" value="c"> To improve collaboration with competitors</label>
    </div>

    <!-- 8.1.3 Questions -->
    <div class="quiz-question">
      <p>7. What is a potential impact of failing to maintain confidentiality?</p>
      <label><input type="radio" name="q7" value="a"> Increased trust</label><br>
      <label><input type="radio" name="q7" value="b"> Financial loss and legal action</label><br>
      <label><input type="radio" name="q7" value="c"> Improved security</label>
    </div>

    <div class="quiz-question">
      <p>8. Which of these is a financial consequence of failing to maintain confidentiality?</p>
      <label><input type="radio" name="q8" value="a"> Fines and refunds</label><br>
      <label><input type="radio" name="q8" value="b"> Increased earnings</label><br>
      <label><input type="radio" name="q8" value="c"> Free advertising</label>
    </div>

    <div class="quiz-question">
      <p>9. Which of these is a legal consequence of failing to maintain confidentiality?</p>
      <label><input type="radio" name="q9" value="a"> Legal action and lawsuits</label><br>
      <label><input type="radio" name="q9" value="b"> Improved compliance</label><br>
      <label><input type="radio" name="q9" value="c"> Increased security</label>
    </div>

    <button type="button" onclick="gradeLongQuiz8_1()">Submit Quiz</button>
    <p id="quiz-score"></p>
    <p></p>
  </form>
</div>



  </main>
</div>
</body>
</html>



