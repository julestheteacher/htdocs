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
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>T level Revision Site</title>
<link rel="stylesheet" href="/global_includes/styles.css">
</head>
<body>
<div class="content">
    <h1>Choose Your Course</h1>
<h2>What course do you want to revise?</h2>
<aside class="sidebar">


    <ul>
        <li><a href="index.php?course=dss"><i class="fas fa-cogs"></i>Digital Support Services</a></li> 
        <li><a href="index.php?course=dsd"><i class="fas fa-cogs"></i>Digital SoftwareDevelopment</a></li> 
        <li><a href="index.php?course=marketing"><i class="fas fa-cogs"></i>Marketing</a></li> 
    </ul>
    </aside>
</div>

</body> 
</html>