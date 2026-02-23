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

<h1><a href="<?= $homeLink ?>">Home</a></h1>
<h1><a href="../security-index.php">Security Home</a></h1>

    <h2>8.2 Technical Threats</h2>
      <ul>

      <li><a href="8-2-1-technical.php"><i class="fas fa-cogs"></i>8.2.1 Technical Threats</a></li>
      <li><a href="8-2-2-techvul.php"><i class="fas fa-cogs"></i>8.2.2 Technical Vulnerabilities</a></li>
      <li><a href="8-2-3-human.php">  <i class="fas fa-cogs"></i>8.2.3 Human Threats</a></li>
      <li><a href="8-2-4-physical.php"> <i class="fas fa-cogs"></i>8.2.4 Physical Threats</a></li>
      <li><a href="8-2-5-impact.php"> <i class="fas fa-cogs"></i>8.2.5 Impact</a></li>
      <li><a href="../quiz/quiz.php"><i class="fas fa-cogs"></i>Test Yourself</a></li>
      </UL>

