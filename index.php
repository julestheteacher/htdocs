<?php
include($_SERVER['DOCUMENT_ROOT'].'/index_includes/is_auth.php');
include($_SERVER['DOCUMENT_ROOT'].'/index_includes/check.php');
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars(SITE_NAME) ?> — Protected</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="index-css.css">

</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <?php include($_SERVER['DOCUMENT_ROOT'].'/index_includes/index_menu.php'); ?>
  </aside>

  <?php include($_SERVER['DOCUMENT_ROOT']. '/index_includes/index_main.php'); ?>

 </div>
</body>
</html>

