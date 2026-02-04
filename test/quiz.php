<?php


$csvFile = __DIR__ . DIRECTORY_SEPARATOR . 'questions.csv';

function removeZeroWidthSpaces(string $text): string {
    // Pattern matches all listed zero-width characters
    $pattern = '/[\x{200B}\x{200C}\x{200D}\x{FEFF}]/u';
    return preg_replace($pattern, '', $text);
}

if (!file_exists($csvFile)) {
    http_response_code(500);
    echo "CSV file not found:questions.csv";
    exit;
}

// Read CSV using fgetcsv for correct handling of commas/quotes
$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle);
if ($header === false) {
    http_response_code(500);
    echo "CSV header missing.";
    exit;
}

// Map columns by header names so QuestionNumber can be removed in future
$headerMap = [];
foreach ($header as $i => $name) {
    $headerMap[removeZeroWidthSpaces(trim($name))] = removeZeroWidthSpaces($i);
}

$required = ['Section','QuestionText','OptionA','OptionB','OptionC','OptionD','CorrectOption','Description'];
foreach ($required as $col) {
    if (!array_key_exists($col, $headerMap)) {
        http_response_code(500);
        echo "CSV is missing required column: {$col}";
        exit;
    }
}

$rows = [];
$sections = [];
$rowId = 0; // data-row index after the header
while (($data = fgetcsv($handle)) !== false) {
    // Skip empty lines
    if (count($data) < 2) continue;

    $section = trim($data[$headerMap['Section']] ?? '');
    $qtext   = trim($data[$headerMap['QuestionText']] ?? '');

    if ($section === '' || $qtext === '') {
        $rowId++;
        continue;
    }

    $rows[] = [
        'rowId' => $rowId,
        'Section' => $section,
        'QuestionText' => $qtext,
        'OptionA' => $data[$headerMap['OptionA']] ?? '',
        'OptionB' => $data[$headerMap['OptionB']] ?? '',
        'OptionC' => $data[$headerMap['OptionC']] ?? '',
        'OptionD' => $data[$headerMap['OptionD']] ?? '',
        // CorrectOption is intentionally not used here (handled in JS)
    ];

    $sections[$section] = true;
    $rowId++;
}
fclose($handle);

$sectionList = array_keys($sections);
sort($sectionList);

// Read user selection
$selectedSection = isset($_GET['section']) ? trim($_GET['section']) : '';
$requestedCount  = isset($_GET['count']) ? (int)$_GET['count'] : 0;

// Filter questions by section
$filtered = [];
if ($selectedSection !== '') {
    foreach ($rows as $r) {
        if ($r['Section'] === $selectedSection) $filtered[] = $r;
    }
}

$maxCount = count($filtered);
if ($requestedCount < 1) $requestedCount = 0;
if ($requestedCount > $maxCount) $requestedCount = $maxCount;

// If valid selection, pick N random questions from the chosen section
$quizQuestions = [];
if ($selectedSection !== '' && $requestedCount > 0) {
    // Shuffle the filtered list and take first N
    shuffle($filtered);
    $quizQuestions = array_slice($filtered, 0, $requestedCount);
}

$randomSection = null;
$randomQuestions = [];

if(isset($_GET['section'])){
  if($_GET['section'] == "rand_s"){
    $randomSection = $sectionList[random_int(0, sizeof($sectionList)-1)];
    header('Location: quiz.php?section='.htmlspecialchars($randomSection).'&count='.htmlspecialchars($_GET['count']));
  } elseif($_GET['section'] == "rand_q"){
    $requestedCount = isset($_GET['count']) ? (int) $_GET['count'] : 10;
    if ($requestedCount < 1) {
        $requestedCount = 10; // fallback if user input is less than 1
    }

    $allQuestions = [];
    foreach ($sectionList as $sec) {
        foreach ($rows as $r) {
            if ($r['Section'] === $sec) {
                $allQuestions[] = $r;
            }
        }
    }

    $maxCount = sizeof($allQuestions);

    shuffle($allQuestions);
    $quizQuestions = array_slice($allQuestions, 0, $requestedCount);
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Interactive Quiz</title>
   <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="container">
      <aside class="sidebar">    
      <?php include '../index_includes/index_menu.php'; ?>
      </aside>
<main class="content">
<div class="wrap">
  <h1>Interactive Quiz</h1>
  <p>Choose the section and the number of questions. </p>

  <form method="get" class="controls" aria-label="Quiz settings">
    <div>
      <label for="section">Section</label>
      <select id="section" name="section" required>
        <option value="" disabled 
        <?php echo ($selectedSection === '' ? 'selected' : ''); ?>>Select a section…</option>
        <option <?php echo ($selectedSection === 'rand_s' ? 'selected' : ''); ?> value="rand_s">Randomly pick a section…</option>
        <option <?php echo ($selectedSection === 'rand_q' ? 'selected' : ''); ?> value="rand_q">Randomly pick questions…</option>
        <?php foreach ($sectionList as $sec): ?>
          <option value="<?php echo htmlspecialchars($sec); ?>" <?php echo ($sec === $selectedSection ? 'selected' : ''); ?>>
            <?php echo htmlspecialchars($sec); ?><?=
              (explode('.', $sec)[0] == "7" && explode('.', $sec)[1] == "3") ? " - Digital Environments - Networking" : " - Unknown"
            ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div><br><br>

    <div>
      <label for="count">Number of questions</label>
      <input id="count" name="count" type="number" min="1" value="<?php echo ($requestedCount > 0 ? $requestedCount : ''); ?>" placeholder="e.g. 5" required />
      <?php if ($selectedSection !== ''): ?>
        <div class="hint">Available in <?php echo htmlspecialchars($selectedSection == "rand_q" ? "total" : ($selectedSection)); ?>.<br>Number of questions in section: <?php echo $maxCount; ?></div>
      <?php else: ?>
        <div class="hint">Pick a section first to see available questions.</div>
      <?php endif; ?>
    </div>

    <div>
      <button type="submit">Load Quiz</button>
    </div>
  </form>

  <?php if ($selectedSection !== '' && $requestedCount > 0 && count($quizQuestions) === 0): ?>
    <p class="error">No questions found for that section. Please choose another section.</p>
  <?php endif; ?>

  <?php if (count($quizQuestions) > 0): ?>
    <hr />
    <h2><?php echo htmlspecialchars($selectedSection == "rand_q" ? "Randomly Generated" : ('Section '.$selectedSection)); ?> Quiz</h2>
    <p>Answer all questions then submit.</p>

    <form id="quiz-form" onsubmit="return false;">
      <?php foreach ($quizQuestions as $i => $q): ?>
        <div class="question" data-rowid="<?php echo (int)$q['rowId']; ?>">
          <h3><?php echo ($i+1) . '. ' . htmlspecialchars($q['QuestionText']); ?></h3>

          <div class="option">
            <label><input type="radio" name="q<?php echo $i; ?>" value="a" /> <?php echo htmlspecialchars($q['OptionA']); ?></label>
          </div>
          <div class="option">
            <label><input type="radio" name="q<?php echo $i; ?>" value="b" /> <?php echo htmlspecialchars($q['OptionB']); ?></label>
          </div>
          <div class="option">
            <label><input type="radio" name="q<?php echo $i; ?>" value="c" /> <?php echo htmlspecialchars($q['OptionC']); ?></label>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="actions">
        <button type="button" id="submit-quiz">Submit Quiz</button>
        <button type="button"><a href="quiz.php">Change section / count</a></button>
      </div>
    </form>

    <script>
      // Pass the selected CSV row IDs to JS so it can look up the correct answers.
      window.quizConfig = {
        section: <?php echo json_encode($selectedSection); ?>,
        rowIds: <?php echo json_encode(array_map(fn($q) => $q['rowId'], $quizQuestions)); ?>
      };
    </script>
    <script src="quiz.js"></script>

  <?php endif; ?>
  </div>

</div>
</body>
</html>
