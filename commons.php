<?php
include 'header/header.php';
$parliament = $_GET['house'];
?>
<div class="page-header pt-3">
  <h2>Commons</h2>
</div>
<p class="page-header">This map shows the parties that occupy the seats of the house of commons</p>
<div class="col-sm-3 col-md-5">
  <?php generateSeating($link, 0, $parliament);  ?>
</div>
<?php include_once "footer.php"; ?>