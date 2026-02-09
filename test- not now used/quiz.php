<?php
declare(strict_types=1);

/**
 * Interactive Quiz (Windows XAMPP)
 * - Course: DSS / DSD / MARK (auto-detects which ones are present)
 * - Dimension: Section / Criteria / Topic
 * - Value: specific value, Random value, or Random questions
 * - Count: how many questions
 *
 * Files are expected in C:\xamp\htdocs\test\ (adjust if yours is C:\xampp\...)
 * and can be named either with or without .csv, e.g. "dss.csv" or "dss".
 */

/* -----------------------------
   0) CONFIG: WINDOWS PATHS
   ----------------------------- */

// Filesystem path where the CSVs live (Windows; forward slashes work in PHP)
define('BASE_PATH_FS', 'C:/xamp/htdocs/test/');  // <-- change to C:/xampp/htdocs/test/ if needed

// Public web path to the same folder (what the browser can fetch via URL)
define('BASE_PATH_WEB', '/test/');               // http://localhost/test/<file>

/* Optional tiny diagnostics (set true temporarily to see paths and detected files) */
const DEBUG = false;

/* -----------------------------
   1) Read input
   ----------------------------- */
$selectedCourse  = isset($_GET['course']) ? strtolower(trim($_GET['course'])) : '';
$selectedDim     = isset($_GET['dim'])    ? strtolower(trim($_GET['dim']))    : ''; // section|criteria|topic
$selectedValue   = isset($_GET['value'])  ? trim($_GET['value'])              : ''; // concrete | rand_v | rand_q
$requestedCount  = isset($_GET['count'])  ? (int) $_GET['count']              : 0;
if ($requestedCount < 0) $requestedCount = 0;

/* -----------------------------
   2) Known courses & filenames
   ----------------------------- */
// We’ll accept either "dss" or "dss.csv" (same for dsd/mark).
$knownMapBase = [
    'dss'  => 'dss',
    'dsd'  => 'dsd',
    'mark' => 'mark',
];
$courseLabels = ['dss'=>'DSS','dsd'=>'DSD','mark'=>'Marketing'];

/* -----------------------------
   3) Detect available course files
   ----------------------------- */
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$availableCourses = [];     // courseKey => actual filename (with or without .csv)
$diagnostics = [];

foreach ($knownMapBase as $key => $base) {
    $candidates = [$base, $base . '.csv'];
    $found = null;
    foreach ($candidates as $cand) {
        if (file_exists(BASE_PATH_FS . $cand)) {
            $found = $cand;
            break;
        }
    }
    if ($found !== null) {
        $availableCourses[$key] = $found;
        if (DEBUG) $diagnostics[] = "Found for $key: " . BASE_PATH_FS . $found;
    } else {
        if (DEBUG) $diagnostics[] = "Missing for $key: tried " . BASE_PATH_FS . $base . " and " . BASE_PATH_FS . $base . ".csv";
    }
}

/* -----------------------------
   4) Load CSV for selected course
   ----------------------------- */
$csvLoaded  = false;
$errorMsg   = null;
$rows       = []; // parsed question rows
$listByDim  = ['section'=>[], 'criteria'=>[], 'topic'=>[]];
$headerMap  = [];

if ($selectedCourse !== '') {
    if (!isset($availableCourses[$selectedCourse])) {
        $errorMsg = "The CSV for course “" . h($selectedCourse) . "” was not found under " . h(BASE_PATH_FS) . ".";
    } else {
        $csvPath = BASE_PATH_FS . $availableCourses[$selectedCourse];
        $fh = @fopen($csvPath, 'r');
        if ($fh === false) {
            $errorMsg = "Unable to open the CSV file for this course (" . h($csvPath) . ").";
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
                        $errorMsg = "CSV is missing required column: " . h($col);
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
                        if ($qtext === '' || $section === '') { $rowId++; continue; }

                        $rows[] = [
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
}

/* -----------------------------
   5) Build question pool
   ----------------------------- */
$quizQuestions = [];
$maxCountHint  = null;

$validDims = ['section','criteria','topic'];
$dimToCol  = ['section'=>'Section','criteria'=>'Criteria','topic'=>'Topic'];

if ($csvLoaded && count($rows) > 0) {
    $pool = [];

    if ($selectedValue === 'rand_q') {
        // Random questions across the selected course
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

/* -----------------------------
   6) (Optional) Diagnostics
   ----------------------------- */
if (DEBUG) {
    $diagnostics[] = "BASE_PATH_FS = " . BASE_PATH_FS;
    $diagnostics[] = "BASE_PATH_WEB = " . BASE_PATH_WEB;
    $scan = @scandir(BASE_PATH_FS);
    if ($scan !== false) {
        foreach ($scan as $f) {
            if (preg_match('/\.csv$/i', $f) || in_array($f, array_values($knownMapBase), true)) {
                $diagnostics[] = "Folder contains: $f";
            }
        }
    } else {
        $diagnostics[] = "Could not scandir " . BASE_PATH_FS . " (check permissions).";
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
        /* minimal fallback styles */
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
    <p>Select a <strong>Course</strong>, then filter by <strong>Section / Criteria / Topic</strong>, choose a <strong>Value</strong> (or Random), and set the <strong>Number of questions</strong>.</p>

    <?php if (DEBUG && !empty($diagnostics)): ?>
        <div class="debug">
            <strong>Diagnostics</strong><br>
            <?php foreach ($diagnostics as $line): ?>
                <?= h($line) ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($availableCourses)): ?>
        <p class="error">
            No course files were found under <code><?= h(BASE_PATH_FS) ?></code>.<br>
            Add one or more of: <code>dss</code>/<code>dss.csv</code>, <code>dsd</code>/<code>dsd.csv</code>, <code>mark</code>/<code>mark.csv</code>.
        </p>
    <?php endif; ?>

    <!-- SETTINGS FORM -->
    <form method="get" class="controls" aria-label="Quiz settings">

        <!-- COURSE -->
        <label for="course">Course</label>
        <select id="course" name="course" required>
            <option value="" disabled <?= ($selectedCourse === '' ? 'selected' : '') ?>>Select a course…</option>
            <?php foreach ($availableCourses as $key => $file): ?>
                <option value="<?= h($key) ?>" <?= ($selectedCourse === $key ? 'selected' : '') ?>>
                    <?= h($courseLabels[$key] ?? strtoupper($key)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($selectedCourse === '' && !empty($availableCourses)): ?>
            <div class="hint">Pick a course to load its CSV and values.</div>
        <?php endif; ?>

        <?php if ($selectedCourse !== '' && !$csvLoaded && $errorMsg !== null): ?>
            <div class="error"><?= $errorMsg ?></div>
        <?php endif; ?>

        <!-- DIMENSION -->
        <label for="dim">Filter by</label>
        <select id="dim" name="dim" <?= (!$csvLoaded ? 'disabled' : '') ?> required>
            <option value="" disabled <?= ($selectedDim === '' ? 'selected' : '') ?>>Select a dimension…</option>
            <option value="section"  <?= ($selectedDim === 'section'  ? 'selected' : '') ?>>Section</option>
            <option value="criteria" <?= ($selectedDim === 'criteria' ? 'selected' : '') ?>>Criteria</option>
            <option value="topic"    <?= ($selectedDim === 'topic'    ? 'selected' : '') ?>>Topic</option>
        </select>

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
                    <h3><?= ($i+1) . ". " . h($q['QuestionText']) ?></h3>

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
                <a class="button linklike" href="quiz.php">Change settings</a>
            </div>
        </form>

        <script>
        // IMPORTANT: quiz.js should do: fetch(window.quizConfig.csvFile, { cache: 'no-store' })
        window.quizConfig = {
            // label only; grading uses csvFile + rowIds
            section: <?= json_encode($selectedValue) ?>,
            rowIds: <?= json_encode(array_map(fn($q) => $q['rowId'], $quizQuestions)) ?>,
            // Browser must fetch via web path (served by Apache)
            csvFile: "<?= h(BASE_PATH_WEB . ($availableCourses[$selectedCourse] ?? '')) ?>",
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
