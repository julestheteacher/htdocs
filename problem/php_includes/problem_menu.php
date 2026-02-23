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
<h1><a href="../problem/problem-index.php">Problem Solving Home</a></h1>
     
<ul>
    <li><a href="1-1-index.php"><i class="fas fa-cogs"></i>1-1 Computational Thinking</a></li>
    <li><a href="1-2-index.php"><i class="fas fa-cogs"></i>1.2 Strategies</a></li>
    <li><a href="1-3-index.php"><i class="fas fa-cogs"></i>1.3 Problem Solving</a></li>

    <li><a href="../quiz/quiz.php?course=<?= $course ?>&section=Problem+Solving"><i class="fas fa-cogs"></i>Test Yourself</a></li>
</ul>

