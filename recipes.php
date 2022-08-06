<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include 'config.php';
include 'functions.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$resources = mysqli_fetch_all(mysqli_query($link, "SELECT name, outputAmount, basePrice, recipe FROM resources"), MYSQLI_ASSOC);
include 'header/header.php';
?>
<table class="table table-dark table-bordered align-middle">
    <thead>
        <tr>
            <th>Name</th>
            <th>Input</th>
            <th>Amount</th>
            <th>Base Price</th>
            <th>Total Price of Materials</th>
            <th>Output Amount</th>
            <th>Price</th>
            <th>Sale price</th>
            <th>Profit</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($resources as $rec) {
                if($rec['recipe'] != null) {
                    $recipe = json_decode($rec['recipe']);
                    $num = count($recipe);
                    $tbp = 0;
                    foreach($recipe as $ing) {
                        $tbp += getResourcePrice($link, $ing[0]) * $ing[1];
                    }

                    foreach($recipe as $key => $ing) {
                        echo "<tr>";
                        if($key == 0) {
                            echo "<td rowspan={$num}>Artisan {$rec['name']}</td>";
                        }
                        $a = getResourceName($link, $ing[0]);
                        $c = getResourcePrice($link, $ing[0]);
                        echo "<td>{$a}</td>";
                        echo "<td>{$ing[1]}</td>";
                        echo "<td>{$c}</td>";
                        if($key == 0) {
                            $bp = $rec['basePrice'] * $rec['outputAmount'];
                            $prof = $bp - $tbp;
                            echo "<td rowspan={$num}>{$tbp}</td>";
                            echo "<td rowspan={$num}>{$rec['outputAmount']}</td>";
                            echo "<td rowspan={$num}>{$rec['basePrice']}</td>";
                            echo "<td rowspan={$num}>{$bp}</td>";
                            echo "<td rowspan={$num}>{$prof}</td>";
                        }
                        echo "</tr>";
                    }
                }
            }
        ?>
    </tbody>
</table>
<?php include_once "footer.php" ?>