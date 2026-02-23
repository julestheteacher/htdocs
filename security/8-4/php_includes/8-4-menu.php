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
    <h2><a href="8-4-index.php">8.4 CIA Triad and IAAA - Home</a></h2>
      <ul>

      <li><a href="8-4-1-cia.php"><i class="fas fa-cogs"></i>8.4.1 CIA Triad</a></li>
      <li><a href="8-4-2-iaaa.php"><i class="fas fa-cogs"></i>8.4.2 IAAA</a></li>
      <li><a href="../quiz/quiz.php"><i class="fas fa-cogs"></i>Test Yourself</a></li>
      </UL>

