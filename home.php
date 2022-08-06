<?php
include_once 'header/header.php';
$character_err = "";
$firstname = $lastname = $birthyear = $title = $purse = "";

if($player->approved == false) {
    $character_err = "Your character has not been approved yet";
} else {
    $firstname = $player->firstname;
    $lastname = $player->lastname;
    $birthyear = $player->birthyear;
    $title = $player->title;
    $purseString = $player->purseString;
}
?>
<div class="page-header pt-3">
  <h2>Home</h2>
  <?php echo $character_err; ?>
</div>
<div class="col-sm-12 col-xl-6">
    <?php 
        echo "<h3>Name: " . $firstname . " " . $lastname . "</h3>";
        echo "<h4>DOB: " . $birthyear . "</h4>";
        echo "<h4>Titles: " . $title . "</h4>";
        echo "<h4>Balance: £" . $purseString . "</h4>";
    ?>
</div>
<?php include_once "footer.php"; ?>