<?php
session_start();
include 'config.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

include 'functions.php';
include 'header/header.php';
?>
<div class="page-header pt-3">
  <h2>Election Test</h2>
</div>
<div class="col-sm-12 col-md-12">
  <?php processElection($link, 1750);  ?>
</div>
<?php include_once "footer.php"; ?>