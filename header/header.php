<?php
include_once "config.php";
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
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
    <link rel="icon" href="../../favicon.ico">

    <title>KoGB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/fb41f04bab.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">

</head>
<body class="bg-dark text-light" onload="startTime()">
    <nav class="navbar navbar-expand navbar-light list-group-item-dark">
        <div class="container-fluid">
            <a class="navbar-brand"><img src="https://upload.wikimedia.org/wikipedia/commons/2/28/Coat_of_Arms_of_Great_Britain_%281714-1801%29.svg" height="40px"></a>
            <a class="navbar-brand">KoGB</a>
            <ul class="navbar-nav me-auto mb-0">
                <li class="nav-item">
                    <a class="nav-link" href="https://discord.gg/pymcaUg7MT"><i class="bi bi-discord"></i></a>
                </li>
                <li class="nav-item"> 
                    <a id="date" class="nav-link"></a>
                </li>
                <script>
                    function startTime() {
                        var startDate = new Date('<?php echo $time ?> GMT-0400');
                        var year = startDate.getFullYear();
                        var now = new Date();

                        //RL ms since time start x a for dilation
                        var ct = (now - startDate) * <?php echo $a ?>;
                        //ct = Math.floor(ct);
                        var timestamp = new Date(ct);
                        timestamp.setFullYear(<?php echo $year ?>);
                        //Time started in 1750 so add 1750
                        document.getElementById('date').innerHTML = ordinal_suffix_of(timestamp.getUTCDate()) + ", " + month(timestamp.getUTCMonth()) + ", " + timestamp.getUTCFullYear();
                        setTimeout(startTime, 1000);
                    }

                    function ordinal_suffix_of(i) {
                        var j = i % 10,
                            k = i % 100;
                        if (j == 1 && k != 11) {
                            return i + "st";
                        }
                        if (j == 2 && k != 12) {
                            return i + "nd";
                        }
                        if (j == 3 && k != 13) {
                            return i + "rd";
                        }
                        return i + "th";
                    }

                    function month(i) {
                        let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        return months[i];
                    }
                </script>
            </ul>
            <ul class="navbar-nav mb-0">
                <li class="nav-item">
                    <a class="nav-link" href="sign-out.php">Sign Out</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <?php include_once "nav.php" ?>
            <main class="col ps-3 pt-2">
                <a href="#" data-bs-target="#sidebar" data-bs-toggle="collapse" class="border rounded-3 p-1 text-decoration-none list-group-item-dark"><i class="bi bi-list bi-lg py-2 p-1"></i> Menu</a>