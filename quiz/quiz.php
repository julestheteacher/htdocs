<?php
declare(strict_types=1);

// Escape helper
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES); }

// Request vars
$course = $_GET["course"] ?? "";
$section = $_GET["section"] ?? "";
$filter = $_GET["filter"] ?? "";
$value = $_GET["value"] ?? "";
$count = isset($_GET["count"]) ? max(1, intval($_GET["count"])) : 0;
$doQuiz = isset($_GET["doQuiz"]);

// CSV list
$courses = [
    "dss"  => "dss.csv",
    "dsd"  => "dsd.csv",
    "mark" => "mark.csv"
];

$csvLoaded = false;
$rows = [];
$sections = [];
$criteriaBySection = [];
$topicBySection = [];

// ----------- LOAD CSV -------------
if ($course !== "" && isset($courses[$course])) {
    $file = __DIR__ . "/" . $courses[$course];

    if (file_exists($file)) {
        $fh = fopen($file, "r");
        $header = fgetcsv($fh);

        if ($header !== false) {
            $map = [];
            foreach ($header as $i => $name) {
                $map[trim($name)] = $i;
            }

            $required = [
                "Section","Criteria","Topic1",
                "QuestionText","OptionA","OptionB",
                "OptionC","CorrectOption"
            ];

            $ok = true;
            foreach ($required as $r) {
                if (!array_key_exists($r, $map)) $ok = false;
            }

            if ($ok) {
                $csvLoaded = true;
                $id = 0;

                while (($data = fgetcsv($fh)) !== false) {
                    $sec = trim($data[$map["Section"]] ?? "");
                    $cri = trim($data[$map["Criteria"]] ?? "");
                    $top = trim($data[$map["Topic1"]] ?? "");
                    $txt = trim($data[$map["QuestionText"]] ?? "");

                    if ($sec === "" || $txt === "") { $id++; continue; }

                    $rows[] = [
                        "id" => $id,
                        "Section" => $sec,
                        "Criteria" => $cri,
                        "Topic" => $top,
                        "QuestionText" => $txt,
                        "A" => $data[$map["OptionA"]] ?? "",
                        "B" => $data[$map["OptionB"]] ?? "",
                        "C" => $data[$map["OptionC"]] ?? "",
                        "D" => isset($map["OptionD"]) ? ($data[$map["OptionD"]] ?? "") : ""
                    ];

                    $sections[$sec] = true;
                    if ($cri !== "") $criteriaBySection[$sec][$cri] = true;
                    if ($top !== "") $topicBySection[$sec][$top] = true;
                    $id++;
                }
                fclose($fh);

                $sections = array_keys($sections);
                sort($sections);

                foreach ($criteriaBySection as $sec => $vals) {
                    $criteriaBySection[$sec] = array_keys($vals);
                    sort($criteriaBySection[$sec]);
                }
                foreach ($topicBySection as $sec => $vals) {
                    $topicBySection[$sec] = array_keys($vals);
                    sort($topicBySection[$sec]);
                }
            }
        }
    }
}

// ----------- BUILD QUIZ -------------
$quiz = [];
$maxCountHint = null;

if ($doQuiz && $csvLoaded && $section !== "" && $filter !== "" && $count > 0) {

    if ($filter === "random") {
        $pool = $rows;
        $maxCountHint = count($pool);

    } elseif ($filter === "criteria" && $value !== "") {
        $pool = array_filter($rows, fn($r) =>
            $r["Section"] === $section && $r["Criteria"] === $value
        );
        $maxCountHint = count($pool);

    } elseif ($filter === "topic" && $value !== "") {
        $pool = array_filter($rows, fn($r) =>
            $r["Section"] === $section && $r["Topic"] === $value
        );
        $maxCountHint = count($pool);

    } else {
        $pool = array_filter($rows, fn($r) =>
            $r["Section"] === $section
        );
        $maxCountHint = count($pool);
    }

    shuffle($pool);
    $quiz = array_slice($pool, 0, $count);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Interactive Quiz</title>
<link rel="stylesheet" href="quiz-layout.css">

</head>
<body></body>

<h1>Interactive Quiz</h1>
<p>Choose your course and quiz settings below.</p>
<p></p>
<div class="quiz-shell">
  <div class="quiz-layout">
    <div class="quiz-left">
    <form method="get" id="settings">

        <label>Course:</label>
        <select name="course" onchange="this.form.submit()">
            <option value="">Select a course</option>
            <?php foreach ($courses as $key => $f): ?>
                <option value="<?=h($key)?>" <?=$course===$key?"selected":""?>><?=strtoupper(h($key))?></option>
            <?php endforeach; ?>
        </select>

            <?php if ($csvLoaded): ?>

            <label>Section:</label>
            <select name="section" onchange="this.form.submit()">
                <option value="">Select a section</option>
                <?php foreach ($sections as $s): ?>
                    <option value="<?=h($s)?>" <?=$section===$s?"selected":""?>><?=h($s)?></option>
                <?php endforeach; ?>
            </select>

          <?php endif; ?>

          <?php if ($csvLoaded && $section !== ""): ?>

          <label>Filter:</label>
          <select name="filter" onchange="this.form.submit()">
              <option value="">Choose filter…</option>
              <option value="random" <?=$filter==="random"?"selected":""?>>Random (all)</option>
              <option value="criteria" <?=$filter==="criteria"?"selected":""?>>Criteria</option>
              <option value="topic" <?=$filter==="topic"?"selected":""?>>Topic</option>
          </select>

          <?php if ($filter === "criteria"): ?>
          <label>Criteria:</label>
          <select name="value" onchange="this.form.submit()">
              <option value="">Pick criteria</option>
              <?php foreach ($criteriaBySection[$section] ?? [] as $c): ?>
                  <option value="<?=h($c)?>" <?=$value===$c?"selected":""?>><?=h($c)?></option>
              <?php endforeach; ?>
          </select>

          <?php elseif ($filter === "topic"): ?>
          <label>Topic:</label>
          <select name="value" onchange="this.form.submit()">
              <option value="">Pick topic</option>
              <?php foreach ($topicBySection[$section] ?? [] as $t): ?>
                  <option value="<?=h($t)?>" <?=$value===$t?"selected":""?>><?=h($t)?></option>
              <?php endforeach; ?>
          </select>

          <?php endif; ?>
          <?php endif; ?>

            <?php if ($csvLoaded && $section !== "" && $filter !== ""): ?>

            <?php
            // Calculate max questions available
            if ($filter === "random") {
                $maxCountHint = count($rows);
            }
            elseif ($filter === "criteria" && $value !== "") {
                $maxCountHint = count(array_filter($rows, fn($r)=>$r["Section"]===$section && $r["Criteria"]===$value));
            }
            elseif ($filter === "topic" && $value !== "") {
                $maxCountHint = count(array_filter($rows, fn($r)=>$r["Section"]===$section && $r["Topic"]===$value));
            }
            else {
                $maxCountHint = count(array_filter($rows, fn($r)=>$r["Section"]===$section));
            }
            ?>

            <label>Number of questions:</label>
            <input type="number"
                min="1"
                max="<?=$maxCountHint?>"
                name="count"
                value="<?=$count>0?$count:""?>"
                required
            >
            <div class="hint">Maximum available: <?=$maxCountHint?></div>

            <br><br>

            <button type="submit" name="doQuiz" value="1">Load Quiz</button>

        <?php endif; ?>

      </form>
      </div>

      <div class="quiz-right">
        <?php if ($doQuiz && !empty($quiz)): ?>
        <hr>
        <h2>Your Quiz</h2>


        <form id="quiz-form" onsubmit="return false;">
        <?php foreach ($quiz as $i => $q): ?>
            <div class="question" data-rowid="<?=$q['id']?>">
                <h3><?=($i+1).". ".h($q["QuestionText"])?></h3>

                <label><input type="radio" name="q<?=$i?>" value="a"> <?=h($q["A"])?></label><br>
                <label><input type="radio" name="q<?=$i?>" value="b"> <?=h($q["B"])?></label><br>
                <label><input type="radio" name="q<?=$i?>" value="c"> <?=h($q["C"])?></label><br>

                <?php if (trim($q["D"]) !== ""): ?>
                <label><input type="radio" name="q<?=$i?>" value="d"> <?=h($q["D"])?></label><br>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button id="submit-quiz" type="button">Submit Quiz</button>
        </form>
        </div>
        <script>
        window.quizConfig = {
            section: <?= json_encode($section) ?>,
            rowIds: <?= json_encode(array_map(fn($r)=>$r["id"], $quiz)) ?>,
            csvFile: <?= json_encode($courses[$course]) ?>,
            passThreshold: 70
        };
        </script>

        <script src="./quiz.js?v=<?=time()?>"></script>

        <?php endif; ?>

        </div>
      </div>
</body>
</html>