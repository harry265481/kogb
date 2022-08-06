<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include 'config.php';
include 'functions.php';
include 'header/header.php';
?>
<head>
    <style>
        svg {
            border:solid 1px black;
            background-color:#165ec9;
        }
        path:hover {
            fill:#ccc !important;
        }
    </style>
</head>
<h1 class="mt-3" >Map</h1>
<div class="col-lg-8 col-md-10 col-sm-12">
<?php
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); generateEconMap($link); 
?>
</div>
<?php include_once "footer.php"; ?>