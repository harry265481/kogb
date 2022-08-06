<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include 'config.php';
include 'functions.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$pops = mysqli_fetch_all(mysqli_query($link, "SELECT name, lifeneeds, everydayneeds, luxuryneeds FROM poptypes"), MYSQLI_ASSOC);
include 'header/header.php';
?>
<table class="table table-dark table-bordered align-middle">
    <thead>
        <tr>
            <th colspan=2>Name</th>
            <th colspan=2>Life Needs</th>
            <th colspan=2>Everyday Needs</th>
            <th colspan=2>Luxury Needs</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($pops as $pop) {
                $life = json_decode($pop['lifeneeds']);
                $everyday = json_decode($pop['everydayneeds']);
                $luxury = json_decode($pop['luxuryneeds']);
                $num = max(count($life), count($everyday), count($luxury));

                for($i = 0; $i < $num; $i++) {
                    echo "<tr>";
                    if($i == 0) {
                        echo "<td rowspan={$num}>{$pop['name']}</td>";
                    }

                    if(array_key_exists($i, $life)) {
                        $a = getResourceName($link, $life[$i][0]);
                        echo "<td>{$a}</td>";
                        echo "<td>{$life[$i][1]}</td>";
                    } else {
                        echo "<td></td>";
                        echo "<td></td>";
                    }

                    if(array_key_exists($i, $everyday)) {
                        $a = getResourceName($link, $everyday[$i][0]);
                        echo "<td>{$a}</td>";
                        echo "<td>{$everyday[$i][1]}</td>";
                    } else {
                        echo "<td></td>";
                        echo "<td></td>";
                    }

                    if(array_key_exists($i, $luxury)) {
                        $a = getResourceName($link, $luxury[$i][0]);
                        echo "<td>{$a}</td>";
                        echo "<td>{$everyday[$i][1]}</td>";
                    } else {
                        echo "<td></td>";
                        echo "<td></td>";
                    }
                    echo "</tr>";
                }
            }
        ?>
    </tbody>
</table>
<?php include_once "footer.php" ?>