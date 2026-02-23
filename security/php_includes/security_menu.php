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
?><H1><a href="<?= $homeLink ?>">Home</a></H1>
    <h1><a href="security-index.php">Security Home</a></h1>
    <h1></h1>

    <ul>
      <li><a href="8-1/8-1-index.php"><i class="fas fa-cogs"></i>8.1 Security Risks</a></li>
      <li><a href="8-2/8-2-index.php"><i class="fas fa-cogs"></i>8.2 Types of threats and vulnerabilities</a></li>
      <li><a href="8-3/8-3-index.php"><i class="fas fa-cogs"></i>8.3 Threat Mitigation</a></li>
      <li><a href="8-4/8-4-index.php"><i class="fas fa-cogs"></i>8.4 CIA Triad</a></li>
      <li><a href="../quiz/quiz.php"><i class="fas fa-cogs"></i>Test Yourself</a></li>

    </ul>