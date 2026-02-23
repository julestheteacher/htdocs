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
<h2>8.1 Security Risks</h2>
     
<ul>
    <li><a href="8-1-1-confidential.php"><i class="fas fa-cogs"></i>8.1.1 Confidential Information</a></li>
    <li><a href="8-1-2-whyimportant.php"><i class="fas fa-cogs"></i>8.1.2 Why Important</a></li>
    <li><a href="8-1-3-impact.php"><i class="fas fa-cogs"></i>8.1.3 Impact on Orgs etc</a></li>

    <li><a href="../quiz/quiz.php"><i class="fas fa-cogs"></i>Test Yourself</a></li>
</ul>

