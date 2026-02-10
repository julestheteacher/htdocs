<?php
include($_SERVER['DOCUMENT_ROOT'].'/index_includes/is_auth.php');
include($_SERVER['DOCUMENT_ROOT'].'/index_includes/check.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>Choose Your Course</title>
<link rel="stylesheet" href="/quiz/quiz-layout.css">
</head>
<body>

<div class="quiz-shell">
    <div class="quiz-layout">
        <div class="quiz-right">
           <h1>What course do you want to revise?</h1>

            <div class="result-buttons" >

                <a href="<?='/dss_index.php' ?>">DSS</a>
                <a href="<?= '/dsd_index.php' ?>">DSD</a>
                <a href="<?= '/marketing_index.php' ?>">Marketing</a>


            </div>

        </div>
    </div>
</div>

</body>
</html>