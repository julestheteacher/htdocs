<?php
include($_SERVER['DOCUMENT_ROOT'].'/index_includes/is_auth.php');
include($_SERVER['DOCUMENT_ROOT'].'/index_includes/check.php');


// If user picks a course:
if (isset($_GET['course'])) {
    // store for 24 hours, available site-wide
    setcookie("course", strtolower($_GET['course']), time() + 86400, "/");

    // redirect to the correct course homepage
    $map = [
        "dss" => "dss_index.php",
        "dsd" => "dsd_index.php",
        "marketing" => "marketing_index.php"
    ];

    if (isset($map[$_GET['course']])) {
        header("Location: " . $map[$_GET['course']]);
        exit;
    }
}
?>

<h1>Choose Your Course</h1>
    <h2>What course do you want to revise?</h2>

<a href="index.php?course=dss">DSS</a><br>
<a href="index.php?course=dsd">DSD</a><br>
<a href="index.php?course=marketing">Marketing</a>