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
    <h1><a href="business-index.php">Business Home</a></h1>

    <ul>
      <li><a href="/5-1/5-1-index.php"><i class="fas fa-cogs"></i>5.1 Different org types</a></li>
      <li><a href="/5-2/5-2-index.php"><i class="fas fa-cogs"></i>5.2 How are digi systems used</a></li>
      <li><a href="/5-3/5-3-index.php"><i class="fas fa-cogs"></i>5.3 Potential Risks</a></li>  
      <li><a href="/5-4/5-4-index.php"><i class="fas fa-cogs"></i>5.4 Change Management</a></li>
      

    <li>
      <?php if ($course === 'dss'): ?>
        <a href="/5-5/5-5-index.php">
          <i class="fas fa-cogs"></i>5.5 Skills needed for digital infrastructure
        </a>
      <?php else: ?>
        <!-- Hidden for non-DSS; or show plain text if you prefer -->
      <?php endif; ?>
    </li>

      <li><a href="/quiz/quiz.php?course=<?= $course ?>&section=business"><i class="fas fa-cogs"></i>Test Yourself</a></li>

    </ul>