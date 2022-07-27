<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include_once 'config.php';
$character_err = "";

$discid = $_SESSION['discid'];
$discname = $_SESSION['username'];
$discavatar = $_SESSION['avatar'];
$avatar = "https://cdn.discordapp.com/avatars/{$discid}/{$discavatar}.png";

include_once 'functions.php';
include_once 'header/header.php';
?>
<div class="page-header pt-3">
  <h2>My Account</h2>
</div>
<div class="col-12">
    <?php 
        if($_SESSION['isDisc'] == true) {
            echo "<i class=\"fa-brands fa-discord\"></i> Discord connected account<br>";
            echo "<img class=\"rounded-circle mt-3\" width=80px src=\"{$avatar}\">";
            echo "<h3>" . $_SESSION['username'] . "</h3>";
        } else {
            echo 
            "<form action=\"init-oauth-existing.php\" method=\"post\">
                <button class=\"w-100 btn btn-lg btn-primary\">Log in with <i class=\"fa-brands fa-discord\"></i></button>
            </form>";
        }
    ?>
</div>
<?php include_once "footer.php"; ?>