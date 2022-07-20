<?php
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

$sqlgetPartyNames = mysqli_query($link, 'SELECT Name, Color FROM parties');

$name = $sqlProvince['Name'];

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
            <div class="col-md-12">
                <div class="profile-img">
                    <?php $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); generateMap($link, false);?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?php
                for($i = 1; $i <= $sqlProvince['seats']; $i++) {
                    echo "<h5>Seat " . $i . ": <span style=\"color:" . mysql_result($sqlgetPartyNames, $sqlProvince['seat' . $i], 'Color') . "\">■</span> " . mysql_result($sqlgetPartyNames, $sqlProvince['seat' . $i], 'Name') . "</h5>";
                }
                ?>
            </div>
            <div class="col-md-6">
                <h5>Voters</h5>
                <p><?php echo $sqlProvince['voters']; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h5>Influences and other Notes</h5>
                <p><?php echo $sqlProvince['chiefinfluencer']; ?></p>
            </div>
            <div class="col-md-6">
                <h5>Approximate price per vote</h5>
                <p>£<?php echo $sqlProvince['Price']; ?></p>
            </div>
        </div>
        <?php 
        if($sqlProvince['FranchiseType'] == 3) {
            echo "<div class=\"row\">";
            echo     "<div class=\"col-md-6\">";
            echo         "<h5>Burgages</h5>";
            echo         "<p>Count: " . $burgages . "</p>";
            echo     "</div>";
            echo "</div>";
        }
include_once "footer.php";
?>