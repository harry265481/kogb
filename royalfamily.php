<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include 'config.php';
include 'functions.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
include 'header/header.php';
$people = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM familytree"), MYSQLI_ASSOC);
?>
<head>
<script src="js/familytree.js"></script>
</head>
<div style="width:100%; height: 700px" id="tree"></div>
<?php include_once "footer.php";?>