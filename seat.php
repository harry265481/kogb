<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
$seatID = $_GET['id'];
include 'config.php';
include 'functions.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$sqlget = 'SELECT * FROM seats WHERE ID = ' . $seatID;
$sqldata = mysqli_query($link, $sqlget) or die('Connection could not be established');
$sqlProvince = mysqli_fetch_assoc($sqldata);

$burgages;

if($sqlProvince['FranchiseType'] == 3) {
    $sqlburgagesget = 'SELECT Count FROM burgages WHERE ID = ' . $seatID;
    $sqlburgages = mysqli_query($link, $sqlburgagesget) or die('Connection could not be established');
    $burgages = mysqli_fetch_assoc($sqlburgages);
    $burgages = $burgages['Count'];
}

$name = $sqlProvince['Name'];

$hasmps = "false";

$sqlget = 'SELECT * FROM EmployedMPs WHERE seatID = ' . $seatID;
$sqlmps = mysqli_query($link, $sqlget);
if(mysqli_num_rows($sqlmps) > 0) {
    $hasmps = true;
}

include 'header/header.php';
?>
    <script>
        window.onload = function() {
            var shape = document.getElementById(<?php echo "'" . $seatID . "'" ?>);
            var bbox = shape.getBBox();
            var width = bbox.width;
            var height = bbox.height;
            var x = bbox.x;
            var y = bbox.y;
            x -= 50;
            y -= 50;
            width += 100;
            height += 100;
            var viewbox = `${x} ${y} ${width} ${height}`;
            console.log(viewbox);
            document.getElementById('map').setAttribute('viewBox', viewbox);

            shape.setAttribute('style', "fill:#CCC");
            var list = document.getElementsByTagName('path');
            for(let item of list) {
                item.setAttribute('stroke-width', 0.2);
            }
        }
    </script>
    <style>
        svg {
            border:solid 1px black;
            background-color:#165ec9;
        }
    </style>
    <h1 style = "margin-top: 70px"><?php echo $name ?></h1>
    <?php 
    if($sqlProvince['Name'] != $sqlProvince['County']) {
        echo "<h4>" . $sqlProvince['County'] . "</h4>";
    }
    echo "<h4>" . $sqlProvince['Country'] . "</h4>";
    ?>
    <div class="row">
        <div class="col-md-8 col-lg-6">
            <?php 
                $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); 
                if($sqlProvince['Parliament'] == 0) {
                    generateMap($link, false);
                } else {
                    generateIrishMap($link, false);
                }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php
            for($i = 1; $i <= $sqlProvince['seats']; $i++) {
                echo "<h5>Seat " . $i . ": <span style=\"color:" . getMPColor($link, $sqlProvince['seat' . $i]) . "\">■</span> " . getMPPartyName($link, $sqlProvince['seat' . $i]) . "</h5>";
            }
            ?>
        </div>
        <div class="col-md-4">
            <h5>Voters</h5>
            <p><?php echo $sqlProvince['voters']; ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <h5>Influences and other Notes</h5>
            <p><?php echo $sqlProvince['chiefinfluencer']; ?></p>
        </div>
        <div class="col-md-4">
            <h5>Approximate price per vote</h5>
            <p>£<?php echo $sqlProvince['Price']; ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h5>Franchise</h5>
            <p><?php echo $sqlProvince['Franchise'] ?></p>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive col-md-8">
            <h4>MPs standing at the next election</h4>
            <table class="table table-dark">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th colspan="2">Employer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($hasmps) {
                        while($mp = mysqli_fetch_array($sqlmps, MYSQLI_ASSOC)) {
                            $employer = getMPEmployerName ($link, $mp['ID']);
                            $color = getMPColor($link, $mp['ID']);
                            echo "<tr><td>{$mp['ID']}</td><td width=\"15px\" style=\"background-color:{$color}\"></td><td>{$employer}</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php 
        if($sqlProvince['FranchiseType'] == 3) {
            echo "<div class=\"row\">";
            echo     "<div class=\"col-md-8\">";
            echo         "<h5>Burgages</h5>";
            echo         "<p>Count: " . $burgages . "</p>";
            echo     "</div>";
            echo "</div>";
        }
        getElectionResultsTable($link, $seatID);
        include_once "footer.php";
    ?>