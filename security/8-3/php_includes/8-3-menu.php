<?php
// Read cookie set from index.php
$course = $_COOKIE['course'] ?? null;

// Map course -> home page
$courseMap = [
    'dss' => '../../dss_index.php',
    'dsd' => '../dsd_index.php',
    'marketing' => '../marketing_index.php'
];

// Default home page if no cookie
$homeLink = "../index.php";

if ($course && isset($courseMap[$course])) {
    $homeLink = $courseMap[$course];
}
?>

        
    <H1><a href="<?= $homeLink ?>">Home</a></H1>
    <h1><a href="../security-index.php">Security Home</a></h1>
    <h2>8.3 Mitigation Techniques</h2>
      <ul>

      <li><a href="8-3-1-mitigation.php"><i class="fas fa-cogs"></i>8.3.1 Mitigation Techniques</a></li>
      <li><a href="8-3-2-processes.php"><i class="fas fa-cogs"></i>8.3.2 Process and Procedures</a></li>
      <li><a href="../quiz/quiz.php"><i class="fas fa-cogs"></i>8.3.3 Quiz</a></li>
      </UL>

