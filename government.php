<?php
include_once 'header/header.php';
include_once 'classes/government.php';
?>
<h1 class="h1 mt-3">Cabinet</h1>
<?php 
Government::printGovernment($link);
?>
<?php include_once "footer.php"; ?>