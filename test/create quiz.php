<?php


function sanitize($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Normalize header names to a consistent key form.
 */
function normalize_header($header) {
    $header = preg_replace('/^\xEF\xBB\xBF/', '', $header); // remove UTF-8 BOM if present
    return strtolower(trim($header));
}

/**
 * Read CSV file into an array of associative rows keyed by header names.
 */
function read_csv_assoc($path) {
    if (!is_readable($path)) {
        throw new RuntimeException("File '$path' is not readable.");
    }
    $rows = [];
    if (($handle = fopen($path, 'r')) === false) {
        throw new RuntimeException("Failed to open CSV file.");
    }

    $headers = fgetcsv($handle);
    if (!$headers) {
        fclose($handle);
        throw new RuntimeException("CSV appears to have no header row.");
    }
    // Normalize headers
    $normHeaders = array_map('normalize_header', $headers);

    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) === 1 && trim($data[0]) === '') {
            // skip empty lines
            continue;
        }
        $row = [];
        foreach ($normHeaders as $i => $key) {
            $row[$key] = $data[$i] ?? '';
        }
        $rows[] = $row;
    }
    fclose($handle);
    return $rows;
}

/**
 * Discover option columns (OptionA..OptionZ) present in header.
 */
function get_option_keys($headersAssoc) {
    $optionKeys = [];
    foreach ($headersAssoc as $key => $_) {
        // accept "optiona", "option_b", "option a", etc.
        if (preg_match('/^option\s*([a-z])$/i', $key, $m)) {
            $optionKeys[] = $key;
        }
    }
    // Sort by letter (a, b, c, ...)
    usort($optionKeys, function($a, $b) {
        preg_match('/^option\s*([a-z])$/i', $a, $ma);
        preg_match('/^option\s*([a-z])$/i', $b, $mb);
        return strcmp($ma[1], $mb[1]);
    });
    return $optionKeys;
}

/**
 * Generate the quiz HTML form in the requested format.
 * - Adds section comments like <!-- 8.1.1 Questions -->
 * - Uses <div class="quiz-question"> for each item
 * - Radio names are q1, q2, ..., values a/b/c/...
 */
function generate_quiz_html(array $rows, string $formId = 'quizForm', string $onclickHandler = 'gradeLongQuiz8_1'): string {
    if (empty($rows)) {
        return "<p>No rows found in CSV.</p>";
    }

    // Build a header map from the first row keys
    $headerKeys = array_keys($rows[0]);
    $headerAssoc = array_fill_keys($headerKeys, true);
    $optionKeys = get_option_keys($headerAssoc);

    // Validate minimal requirements
    if (!isset($headerAssoc['questiontext'])) {
        throw new RuntimeException("CSV must include a 'QuestionText' header.");
    }
    if (empty($optionKeys)) {
        throw new RuntimeException("CSV must include at least 'OptionA', 'OptionB', 'OptionC' headers.");
    }

    $html = [];
    $html[] = '<form id="'.sanitize($formId).'">';

    $currentSection = null;
    $questionCounter = 0;

    foreach ($rows as $row) {
        $section = trim($row['section'] ?? '');
        $qText   = trim($row['questiontext'] ?? '');
        if ($qText === '') {
            // skip rows with no question text
            continue;
        }

        // Numbering: use provided QuestionNumber or auto-increment
        $providedNum = trim($row['questionnumber'] ?? '');
        if ($providedNum !== '' && is_numeric($providedNum)) {
            $qNum = (int)$providedNum;
        } else {
            $questionCounter++;
            $qNum = $questionCounter;
        }

        // Section comment when section changes
        if ($section !== '' && $section !== $currentSection) {
            $currentSection = $section;
            $html[] = "\n" . '<!-- ' . sanitize($currentSection) . ' Questions -->';
        }

        // Start question block
        $html[] = '<div class="quiz-question">';
        $html[] = '  <p>' . sanitize($qNum) . '. ' . sanitize($qText) . '</p>';

        // Render options a/b/c/... based on available columns
        $optionValueLetter = 'a';
        foreach ($optionKeys as $optKey) {
            $optText = trim($row[$optKey] ?? '');
            if ($optText === '') {
                $optionValueLetter++;
                continue;
            }
            $nameAttr = 'q' . $qNum;
            $valueAttr = $optionValueLetter; // a, b, c, ...
            $html[] = '  <label><input type="radio" name="' . sanitize($nameAttr) . '" value="' . sanitize($valueAttr) . '"> ' . sanitize($optText) . '</label><br>';
            $optionValueLetter++;
        }

        // Close question block
        // If a CorrectOption is provided, we can add a hidden marker for client-side grading
        if (!empty($row['correctoption'])) {
            $correct = strtolower(trim($row['correctoption']));
            $html[] = '  <input type="hidden" name="correct_' . sanitize($qNum) . '" value="' . sanitize($correct) . '">';
        }
        $html[] = '</div>';
    }

    // Submit button + score area
    $html[] = '<button type="button" onclick="'.sanitize($onclickHandler).'()">Submit Quiz</button>';
    $html[] = '<p id="quiz-score"></p>';
    $html[] = '<p></p>';
    $html[] = '</form>';

    return implode("\n", $html);
}

/**
 * Optional: a basic JS grader (if you don't already have gradeLongQuiz8_1()).
 * This uses the hidden correct_* inputs created above.
 */
function get_basic_grader_js(string $formId = 'quizForm', string $onclickHandler = 'gradeLongQuiz8_1'): string {
    $js = <<<JS
<script>
function $onclickHandler() {
  const form = document.getElementById('$formId');
  if (!form) return;
  let total = 0;
  let correct = 0;
  // Find all hidden correct markers
  const hiddenCorrects = [...form.querySelectorAll('input[type="hidden"][name^="correct_"]')];
  hiddenCorrects.forEach(h => {
    const qNum = h.name.replace('correct_', '');
    const expected = h.value.trim().toLowerCase();
    const chosen = (form.querySelector('input[name="q' + qNum + '"]:checked') || { value: '' }).value.trim().toLowerCase();
    total++;
    if (expected && expected === chosen) {
      correct++;
    }
  });
  const scoreEl = document.getElementById('quiz-score');
  if (scoreEl) {
    scoreEl.textContent = 'Score: ' + correct + ' / ' + total;
  } else {
    alert('Score: ' + correct + ' / ' + total);
  }
}
</script>
JS;
    return $js;
}

// Handle upload & render
$generatedHtml = '';
$savedPath = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formId = $_POST['form_id'] ?? 'quizForm';
    $onclickHandler = $_POST['onclick_handler'] ?? 'gradeLongQuiz8_1';
    $includeJs = isset($_POST['include_js']) && $_POST['include_js'] === '1';
    $saveHtml = isset($_POST['save_html']) && $_POST['save_html'] === '1';

    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['csv_file']['tmp_name'];
        try {
            $rows = read_csv_assoc($tmpPath);
            $generatedHtml = generate_quiz_html($rows, $formId, $onclickHandler);
            if ($includeJs) {
                $generatedHtml .= "\n" . get_basic_grader_js($formId, $onclickHandler);
            }
            if ($saveHtml) {
                $savedPath = __DIR__ . DIRECTORY_SEPARATOR . 'output_quiz.html';
                file_put_contents($savedPath, "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"utf-8\">\n<title>Generated Quiz</title>\n</head>\n<body>\n" . $generatedHtml . "\n</body>\n</html>");
            }
        } catch (Exception $e) {
            $generatedHtml = '<p style="color:red;">Error: ' . sanitize($e->getMessage()) . '</p>';
        }
    } else {
        $generatedHtml = '<p style="color:red;">Please upload a valid CSV file.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CSV → Quiz HTML Generator</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; line-height: 1.5; margin: 2rem; }
    .panel { border: 1px solid #ddd; padding: 1rem; border-radius: 8px; max-width: 900px; }
    .quiz-question { margin: 1rem 0; }
    label { cursor: pointer; }
    .note { color: #555; font-size: 0.95rem; }
    .success { color: green; }
    .error { color: red; }
    pre.code { background: #f7f7f7; padding: 1rem; overflow: auto; }
  </style>
</head>
<body>
  <h1>CSV → Quiz HTML Generator</h1>
  <div class="panel">
    <form method="post" enctype="multipart/form-data">
      <div>
        <label>Upload CSV: <input type="file" name="csv_file" accept=".csv" required></label>
      </div>
      <div>
        <label>Form ID: <input type="text" name="form_id" value="quizForm"></label>
      </div>
      <div>
        <label>Submit Onclick Handler: <input type="text" name="onclick_handler" value="gradeLongQuiz8_1"></label>
        <span class="note">E.g., <code>gradeLongQuiz8_1</code> to match your existing JS.</span>
      </div>
      <div>
        <label><input type="checkbox" name="include_js" value="1"> Include a basic grading JS function</label>
      </div>
      <div>
        <label><input type="checkbox" name="save_html" value="1"> Save HTML to <code>output_quiz.html</code></label>
      </div>
      <div style="margin-top: 0.5rem;">
        <button type="submit">Generate</button>
      </div>
    </form>
  </div>

  <?php if ($generatedHtml): ?>
    <h2>Generated Quiz HTML</h2>
    <p class="note">This is the live-rendered output. Below is the raw HTML for copy/paste.</p>
    <div class="panel">
      <!-- Rendered quiz -->
      <?php echo $generatedHtml; ?>
    </div>

    <h3>Raw HTML</h3>
    <pre class="code"><?php echo sanitize($generatedHtml); ?></pre>

    <?php if ($savedPath): ?>
      <p class="success">Saved to: <code><?php echo sanitize($savedPath); ?></code></p>
    <?php endif; ?>
  <?php endif; ?>

  <hr>
  <h2>CSV Headers Recap</h2>
  <ul>
    <li><strong>Section</strong> (optional): e.g., <code>8.1.1</code> → prints <code>&lt;!-- 8.1.1 Questions --&gt;</code></li>
    <li><strong>QuestionNumber</strong> (optional): numeric → controls numbering; auto-increments if omitted</li>
    <li><strong>QuestionText</strong> (required)</li>
    <li><strong>OptionA</strong>, <strong>OptionB</strong>, <strong>OptionC</strong>, … (required: at least A–C)</li>
    <li><strong>CorrectOption</strong> (optional): <code>a</code>, <code>b</code>, <code>c</code>, used by the basic grader</li>
  </ul>
</body>
</html>