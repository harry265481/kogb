<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include_once 'config.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$sqlget = "SELECT * FROM people WHERE User = " . $_SESSION["id"];
$sqldata = mysqli_query($link, $sqlget);
$sqlrows = mysqli_num_rows($sqldata);
if($sqlrows == 0) {
    header('Location: create.php');
}

$character_err = "";
$firstname = $lastname = $birthyear = $title = "";

$sqlget = "SELECT approved FROM people WHERE User = " . $_SESSION["id"];
$sqldata = mysqli_query($link, $sqlget);
$sqlapproval = mysqli_fetch_assoc($sqldata);
if($sqlapproval['approved'] == 0) {
    $character_err = "Your character has not been approved yet";
} else {
    $sqlget = "SELECT * FROM people WHERE User = " . $_SESSION["id"];
    $sqldata = mysqli_query($link, $sqlget);
    $sqlchar = mysqli_fetch_assoc($sqldata);

    $firstname = $sqlchar["FirstName"];
    $lastname = $sqlchar["LastName"];
    $birthyear = $sqlchar["BirthYear"];
    $title = $sqlchar["NobleTitle"];
    $purse = $sqlchar["purse"];
    $purse = number_format(floatval($purse));
}

include_once 'functions.php';
include_once 'header/header.php';
?>
<div class="page-header pt-3">
  <h2>Home</h2>
  <?php echo $character_err; ?>
</div>
<div class="col-6">
    <?php 
        echo "<h3>Name: " . $firstname . " " . $lastname . "</h3>";
        echo "<h4>DOB: " . $birthyear . "</h4>";
        echo "<h4>Titles: " . $title . "</h4>";
        echo "<h4>Balance: £" . $purse . "</h4>";
    ?>
</div>
<?php include_once "footer.php"; ?>