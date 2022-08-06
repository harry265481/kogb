<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include_once "config.php";
include_once "classes/person.php";
$valid;
$player = Person::fromUserID($link, $_SESSION["id"], $valid);
if($valid == false) {
    header('Location: create.php');
    exit;
}
include_once "functions.php";
$data = getTimeStuff($link);
$year = $data[0];
$time = $data[1];
$irlweek = 604800 * 1000;
$irlyear = 31557600 * 1000;
$icyear = $irlweek * $data[2];
$a = $irlyear / $icyear;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>KoGB</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
        <script src="https://kit.fontawesome.com/fb41f04bab.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/style-main.css">

    </head>
    <body class="bg-dark text-light" onload="startTime()">
        <nav class="navbar navbar-expand-sm navbar-light list-group-item-dark">
            <div class="container-fluid">
                <a class="navbar-brand">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/28/Coat_of_Arms_of_Great_Britain_%281714-1801%29.svg" width="30px" class="d-inline-block align-text-top">
                </a>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="https://discord.gg/pymcaUg7MT"><i class="bi bi-discord"></i></a>
                    </li>
                    <li class="nav-item"> 
                        <a id="date" class="nav-link"></a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <ul class="navbar-nav mb-0">
                        <li class="nav-item">
                            <a class="nav-link dopdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button">
                        <?php 
                        if(isset($_SESSION['isDisc']) && $_SESSION['isDisc'] == true) {
                            $discid = $_SESSION['discid'];
                            $discname = $_SESSION['username'];
                            $discavatar = $_SESSION['avatar'];
                            $avatar = "https://cdn.discordapp.com/avatars/{$discid}/{$discavatar}.png";
                            echo "
                            {$discname}
                            <img class=\"rounded-circle\" width=40px src=\"{$avatar}\">
                            ";
                        } else {
                            echo "{$_SESSION['username']}";
                        }
                        ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown-menu profile-dropdown">
                                <a href="account.php" class="dropdown-item"><i class="fa-solid fa-circle-user"></i> My Account</a>
                                <a href="sign-out.php" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Sign out</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="content row flex-nowrap">
                <?php include_once "nav.php" ?>
                <main class="col ps-3 pt-2">
                    <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none list-group-item-dark"><i class="bi bi-list bi-lg py-2 p-1"></i> Menu</a>