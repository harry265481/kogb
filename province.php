<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
$provinceID = $_GET['id'];
include 'config.php';
include 'functions.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$sqlget = 'SELECT ID, name, Terrain FROM provinces WHERE ID = ' . $provinceID;
$sqldata = mysqli_query($link, $sqlget);
$sqlProvince = mysqli_fetch_assoc($sqldata);

$name = $sqlProvince['name'];
//$workers = ($sqlProvince['laborers'] + $sqlProvince['soldiers'] + $sqlProvince['craftsmen'] + $sqlProvince['artisans'] + $sqlProvince['bureaucrats'] + $sqlProvince['clergymen'] + $sqlProvince['clerks'] + $sqlProvince['officers'] + $sqlProvince['capitalists']);

$workers = mysqli_fetch_all(mysqli_query($link, "SELECT type, size, production, money, lifeneedsmet, everydayneedsmet, luxuryneedsmet FROM pops WHERE province = {$sqlProvince['ID']} ORDER BY type desc"), MYSQLI_ASSOC);
$tworkers = 0;
$bcrats = 0;
foreach($workers as $w) {
    $tworkers += $w['size'];
    if($w['type'] == 5) {
        $bcrats += $w['size'];
    }
}
$population = 4 * $tworkers;
$needs = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM poptypes"));
$maxworker = 10000;
$tn = getTerrainName($link, $sqlProvince['Terrain']);

include 'header/header.php';
?>
    <script>
        window.onload = function() {
            var shape = document.getElementById(<?php echo "'" . $provinceID . "'" ?>);
            var bbox = shape.getBBox();
            var width = bbox.width;
            var height = bbox.height;
            var x = bbox.x;
            var y = bbox.y;
            x -= 150;
            y -= 150;
            width += 300;
            height += 300;
            if(width > height) {
                width = height;
            } else {
                height = width;
            }
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
    <h1 class="mt-3"><?php echo $name ?></h1>
    <div class="row">
        <div class="col-md-5 col-lg-3">
            <?php 
                $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); generateEconMap($link, false);
            ?>
        </div>
    </div>
    <div class="row">
        <h4>About</h4>
        <div class="col-md-10 col-lg-12">
            <i class="fa-solid fa-user"></i> Population: <?php echo number_format($population) ?><br>
            <i class="fa-solid fa-tree"></i> The terrain here is mostly <?php echo strtolower($tn) ?>
        </div>
    </div>
    <div class="row mt-3">
        <h4>Population</h4>
        <div class="col-md-10 col-lg-12">
            <table class="table table-dark">
                <thead>
                    <th>Size</th>
                    <th>Type</th>
                    <th>Production (Daily)</th>
                    <th>Money (£)</th>
                    <th>Life Needs</th>
                    <th>Everyday Needs</th>
                    <th>Luxury Needs</th>
                </thead>
                <tbody>
                    <?php
                        foreach($workers as $w) {
                            $pop = number_format($w['size']);
                            echo "<tr>";
                            echo "<td>{$pop}</td>";
                            echo "<td>" . getPopIcon($w['type']) . " " . getPopName($link, $w['type']) . "</td>";
                            if($w['type'] == 1) {
                                echo "<td>" . round(getProvResourceOutput($link, $provinceID, $w['type'], $w['size'], $bcrats, $sqlProvince['Terrain']),2) . " " . getResourceName($link, $w['production']) . "</td>";
                            } else {
                                echo "<td></td>";
                            }
                            echo "<td>{$w['money']}</td>";

                            //lifeneedsmet, everydayneedsmet, luxuryneedsmet
                            echo "  <td>
                                        <div class='progress'><div class='progress-bar' role='progressbar' style='width: {$w['lifeneedsmet']}%'>{$w['lifeneedsmet']}%</div></div>
                                    </td>";
                            echo "  <td>
                                        <div class='progress'><div class='progress-bar' role='progressbar' style='width: {$w['everydayneedsmet']}%'>{$w['everydayneedsmet']}%</div></div>
                                    </td>";
                            echo "  <td>
                                        <div class='progress'><div class='progress-bar' role='progressbar' style='width: {$w['luxuryneedsmet']}%'>{$w['luxuryneedsmet']}%</div></div>
                                    </td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php 
        include_once "footer.php";
    ?>