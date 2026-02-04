<?php
include '../index_includes/is_auth.php';
include '../index_includes/check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Digital Software Development/Digital Support Services Pearson</title>
<link rel="stylesheet" href="../global_includes/styles.css">
<script src="../global_includes/script.js"></script>
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <aside class="sidebar">    
    <?php include 'php_includes/data_menu.php'; ?>
  </aside>

  <!-- Main Content -->
  <main class="content">
    <h2>Digital Software Development</h2>
    <h2>Digital Support Services</h2>
    <h2>Data</h2>
    <?php include 'php_includes/data_main.php'; ?>
    
  </main>
</div>
</body>
</html>


