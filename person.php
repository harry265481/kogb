<?php
include_once "header/header.php";
$personID = $_GET['id'];

$valid = false;
$profilePerson = new Person($link, $personID, $valid);

?>

<h2 class="mt-3"><?php echo $profilePerson->fullname; ?></h2>
<h4 class="mt-3"><?php if($profilePerson->title != "" && $profilePerson->title != null)echo $profilePerson->title; ?></h4>
<hr>
<p>Birth Year: <?php echo $profilePerson->birthyear; ?></p>
<p class="mb-0">Positions</p>
<ul>
    <?php
    foreach($profilePerson->positions as $p) {
        echo "<li>{$p['name']}</li>";
    }
    ?>
</ul>