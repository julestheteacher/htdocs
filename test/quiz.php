<?php
declare(strict_types=1);

/**
 * Interactive Quiz — Single CSV (questions.csv)
 * - Filters: Section / Criteria / Topic
 * - Value: specific | Random value | Random questions
 * - Count: how many questions to include
 *
 * Place questions.csv in the same folder as this file (quiz.php).
 */

// -----------------------------
// 0) Config
// -----------------------------
// If you need to point to another folder, set CSV_PATH manually, e.g.:
// define('CSV_PATH', 'C:/xamp/htdocs/test/questions.csv');
if (!defined('CSV_PATH')) {
    define('CSV_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'dss.csv');
}

// Turn on temporarily to see helpful diagnostics (paths, counts, etc.)
const DEBUG = false;

// -----------------------------
// 1) Read GET inputs
// -----------------------------
$selectedDim    = isset($_GET['dim'])   ? strtolower(trim($_GET['dim']))   : ''; // 'section'|'criteria'|'topic'
$selectedValue  = isset($_GET['value']) ? trim($_GET['value'])             : ''; // concrete | 'rand_v' | 'rand_q'
$requestedCount = isset($_GET['count']) ? (int) $_GET['count']             : 0;
if ($requestedCount < 0) $requestedCount = 0;

// -----------------------------
// 2) Load questions.csv (if present)
// -----------------------------
$errorMsg  = null;
$rows      = [];                    // all parsed question rows
$listByDim = [                      // distinct values for each dimension
    'section'  => [],
    'criteria' => [],
    'topic'    => []
];
$headerMap = [];
$csvLoaded = false;

if (!file_exists(CSV_PATH)) {
    // We do NOT abort the page; we show filters and a gentle error.
    $errorMsg = "Could not find questions.csv at: " . htmlspecialchars(CSV_PATH);
} else {
    $fh = @fopen(CSV_PATH, 'r');
    if ($fh === false) {
        $errorMsg = "Unable to open questions.csv";
    } else {
        $header = fgetcsv($fh);
        if ($header === false) {
            $errorMsg = "CSV header row is missing or unreadable.";
        } else {
            foreach ($header as $i => $name) {
                $headerMap[trim((string)$name)] = $i;
            }
            // Required columns
            $required = ['Section','Criteria','Topic','QuestionText','OptionA','OptionB','OptionC','CorrectOption'];
            foreach ($required as $col) {
                if (!array_key_exists($col, $headerMap)) {
                    $errorMsg = "CSV is missing required column: " . htmlspecialchars($col);
                    break;
                }
            }

            if ($errorMsg === null) {
                $rowId = 0;
                while (($data = fgetcsv($fh)) !== false) {
                    $section  = trim((string)($data[$headerMap['Section']]      ?? ''));
                    $criteria = trim((string)($data[$headerMap['Criteria']]     ?? ''));
                    $topic    = trim((string)($data[$headerMap['Topic']]        ?? ''));
                    $qtext    = trim((string)($data[$headerMap['QuestionText']] ?? ''));

                    // Minimum requirement: must have question text and a section
                    if ($qtext === '' || $section === '') { $rowId++; continue; }

                    $row = [
                        'rowId'        => $rowId,
                        'Section'      => $section,
                        'Criteria'     => $criteria,
                        'Topic'        => $topic,
                        'QuestionText' => $qtext,
                        'OptionA'      => (string)($data[$headerMap['OptionA']] ?? ''),
                        'OptionB'      => (string)($data[$headerMap['OptionB']] ?? ''),
                        'OptionC'      => (string)($data[$headerMap['OptionC']] ?? ''),
                        'OptionD'      => array_key_exists('OptionD', $headerMap) ? (string)($data[$headerMap['OptionD']] ?? '') : ''
                    ];
                    $rows[] = $row;

                    if ($section  !== '') $listByDim['section'][$section]   = true;
                    if ($criteria !== '') $listByDim['criteria'][$criteria] = true;
                    if ($topic    !== '') $listByDim['topic'][$topic]       = true;

                    $rowId++;
                }
                fclose($fh);

                // Sort distinct values
                foreach ($listByDim as $k => $set) {
                    $vals = array_keys($set);
                    sort($vals, SORT_NATURAL | SORT_FLAG_CASE);
                    $listByDim[$k] = $vals;
                }

                $csvLoaded = true;
            } else {
                fclose($fh);
            }
        }
    }
}

// -----------------------------
// 3) Build question pool from filters
// -----------------------------
$quizQuestions = [];
$maxCountHint  = null;

$validDims = ['section','criteria','topic'];
$dimToCol  = ['section' => 'Section', 'criteria' => 'Criteria', 'topic' => 'Topic'];

if ($csvLoaded && count($rows) > 0) {
    $pool = [];

    if ($selectedValue === 'rand_q') {
        // Random questions across *all* rows
        $pool = $rows;
        $maxCountHint = count($pool);

    } elseif (in_array($selectedDim, $validDims, true)) {
        $colName = $dimToCol[$selectedDim];
        $effectiveValue = $selectedValue;

        if ($selectedValue === 'rand_v') {
            $vals = $listByDim[$selectedDim] ?? [];
            if (!empty($vals)) {
                $effectiveValue = $vals[random_int(0, count($vals)-1)];
            } else {
                $effectiveValue = '';
            }
        }

        if ($effectiveValue !== '' && $effectiveValue !== 'rand_v') {
            foreach ($rows as $r) {
                if (($r[$colName] ?? '') === $effectiveValue) {
                    $pool[] = $r;
                }
            }
            $maxCountHint = count($pool);
        }
    }

    if (!empty($pool) && $requestedCount > 0) {
        if ($requestedCount > count($pool)) $requestedCount = count($pool);
        shuffle($pool);
        $quizQuestions = array_slice($pool, 0, $requestedCount);
    }
}

// -----------------------------
// 4) (Optional) Diagnostics
// -----------------------------
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$diag = [];
if (DEBUG) {
    $diag[] = 'PHP version: ' . h(PHP_VERSION);
    $diag[] = 'CSV_PATH: ' . h(CSV_PATH);
    $diag[] = 'CSV exists? ' . (file_exists(CSV_PATH) ? 'yes' : 'no');
    $diag[] = 'Rows parsed: ' . count($rows);
    foreach (['section','criteria','topic'] as $d) {
        $diag[] = strtoupper($d) . ' values: ' . count($listByDim[$d] ?? []);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Interactive Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Optional stylesheet -->
    <link rel="stylesheet" href="styles.css">
    <style>
        /* minimal fallback styles if styles.css is missing */
        body { font-family: system-ui, Arial, sans-serif; line-height: 1.45; }
        .container { max-width: 980px; margin: 0 auto; padding: 1rem; }
        .controls label { display:block; font-weight:600; margin:.5rem 0 .25rem; }
        .controls select, .controls input[type=number] { width: 100%; max-width: 460px; padding:.5rem; }
        .controls .hint { color:#555; font-size:.9rem; margin-top:.25rem; }
        .question { margin: 1rem 0; padding: .75rem 1rem; border: 1px solid #e0e0e0; border-radius: .5rem; }
        .option { margin: .25rem 0; }
        .actions { margin-top: 1rem; display:flex; gap:.5rem; }
        .error { color: #a00; font-weight: 600; margin:.5rem 0; }
        .debug { background:#f9f9ff; border:1px dashed #99f; padding:.75rem; margin:1rem 0; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
    </style>
</head>
<body>
<div class="container">
<main class="content">
<div class="wrap">

    <h1>Interactive Quiz</h1>
    <p>Choose a <strong>dimension</strong> (Section / Criteria / Topic), pick a <strong>value</strong> (or random), and set the <strong>number of questions</strong>.</p>

    <?php if (DEBUG && !empty($diag)): ?>
        <div class="debug">
            <strong>Diagnostics</strong><br>
            <?php foreach ($diag as $line): ?>
                <?= $line ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg !== null): ?>
        <div class="error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <!-- SETTINGS FORM -->
    <form method="get" class="controls" aria-label="Quiz settings">

        <!-- DIMENSION -->
        <label for="dim">Filter by</label>
        <select id="dim" name="dim" <?= (!$csvLoaded ? 'disabled' : '') ?> required>
            <option value="" disabled <?= ($selectedDim === '' ? 'selected' : '') ?>>Select a dimension…</option>
            <option value="section"  <?= ($selectedDim === 'section'  ? 'selected' : '') ?>>Section</option>
            <option value="criteria" <?= ($selectedDim === 'criteria' ? 'selected' : '') ?>>Criteria</option>
            <option value="topic"    <?= ($selectedDim === 'topic'    ? 'selected' : '') ?>>Topic</option>
        </select>
        <?php if (!$csvLoaded): ?>
            <div class="hint">Place <code>questions.csv</code> next to <code>quiz.php</code> and reload.</div>
        <?php endif; ?>

        <!-- VALUE -->
        <label for="value">Value</label>
        <select id="value" name="value" <?= (!$csvLoaded || $selectedDim === '' ? 'disabled' : '') ?> required>
            <option value="" disabled <?= ($selectedValue === '' ? 'selected' : '') ?>>Select a value…</option>

            <?php if ($csvLoaded && in_array($selectedDim, $validDims, true)): ?>
                <option value="rand_v" <?= ($selectedValue === 'rand_v' ? 'selected' : '') ?>>
                    Random <?= h(ucfirst($selectedDim)) ?>
                </option>
                <option value="rand_q" <?= ($selectedValue === 'rand_q' ? 'selected' : '') ?>>
                    Random Questions (across all <?= h(ucfirst($selectedDim)) ?> values)
                </option>
                <?php foreach ($listByDim[$selectedDim] as $v): ?>
                    <option value="<?= h($v) ?>" <?= ($selectedValue === $v ? 'selected' : '') ?>>
                        <?= h($v) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <?php if ($csvLoaded): ?>
            <?php
            $hintText = '';
            if ($selectedValue === 'rand_q') {
                $hintText = "Total questions available: " . count($rows);
            } elseif ($selectedValue !== '' && $selectedValue !== 'rand_v' && in_array($selectedDim, array_keys($dimToCol), true)) {
                $colName = $dimToCol[$selectedDim];
                $cnt = 0;
                foreach ($rows as $r) { if (($r[$colName] ?? '') === $selectedValue) $cnt++; }
                $hintText = "Questions available in “".h($selectedValue)."”: " . $cnt;
            } elseif ($selectedValue === 'rand_v' && in_array($selectedDim, array_keys($dimToCol), true)) {
                $hintText = "A random ".ucfirst($selectedDim)." will be selected when you load the quiz.";
            } else {
                $hintText = "Choose a value or one of the random options.";
            }
            ?>
            <div class="hint"><?= $hintText ?></div>
        <?php endif; ?>

        <!-- COUNT -->
        <label for="count">Number of questions</label>
        <input id="count" name="count" type="number" min="1"
               value="<?= ($requestedCount > 0 ? (int)$requestedCount : '') ?>"
               placeholder="e.g. 5" required />

        <br>
        <button type="submit">Load Quiz</button>
    </form>

    <!-- QUIZ RENDER -->
    <?php if ($csvLoaded && count($quizQuestions) > 0): ?>
        <hr>
        <h2>
            <?php
            if ($selectedValue === 'rand_q') {
                echo "Random Questions";
            } elseif ($selectedValue === 'rand_v') {
                echo "Random " . h(ucfirst($selectedDim));
            } elseif ($selectedDim !== '' && $selectedValue !== '') {
                echo h(ucfirst($selectedDim)) . ": " . h($selectedValue);
            } else {
                echo "Quiz";
            }
            ?>
        </h2>
        <p>Answer all questions and then submit.</p>

        <form id="quiz-form" onsubmit="return false;">
            <?php foreach ($quizQuestions as $i => $q): ?>
                <div class="question" data-rowid="<?= (int)$q['rowId'] ?>">
                    <h3><?= ($i + 1) . ". " . h($q['QuestionText']) ?></h3>

                    <div class="option">
                        <label><input type="radio" name="q<?= (int)$i ?>" value="a"> <?= h($q['OptionA']) ?></label>
                    </div>
                    <div class="option">
                        <label><input type="radio" name="q<?= (int)$i ?>" value="b"> <?= h($q['OptionB']) ?></label>
                    </div>
                    <div class="option">
                        <label><input type="radio" name="q<?= (int)$i ?>" value="c"> <?= h($q['OptionC']) ?></label>
                    </div>
                    <?php if (isset($q['OptionD']) && trim((string)$q['OptionD']) !== ''): ?>
                    <div class="option">
                        <label><input type="radio" name="q<?= (int)$i ?>" value="d"> <?= h($q['OptionD']) ?></label>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="actions">
                <button type="button" id="submit-quiz">Submit Quiz</button>
                <button type="button" onclick="window.location.href='quiz.php'">Change settings</button>
            </div>
        </form>

        <script>
        // Provide config to quiz.js
        window.quizConfig = {
            // label/breadcrumb only; grading uses csvFile + rowIds
            section: <?= json_encode($selectedValue) ?>,
            rowIds: <?= json_encode(array_map(fn($q) => $q['rowId'], $quizQuestions)) ?>,
            // For the single-file setup, quiz.js can fetch this directly:
            csvFile: "questions.csv",
            passThreshold: 70
        };
        </script>
        <script src="quiz.js"></script>

    <?php elseif ($csvLoaded && $requestedCount > 0 && ($selectedValue !== '' || $selectedValue === 'rand_q')): ?>
        <p class="error">No questions matched this selection. Try a different value or reduce the count.</p>
    <?php endif; ?>

</div>
</main>
</div>
</body>
</html>