<?php
session_start();
include 'config.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

include 'functions.php';
include 'header/header.php';
?>
<div class="page-header pt-3">
  <h2>Lords</h2>
</div>
<div class="row">
    <div class="col-sm-6 col-md-8">
    <?php generateSeating($link, 1);  ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-8">
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Title(s)</th>
                    <th>Side</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = 'SELECT FirstName, LastName, Party, NobleTitle FROM people WHERE Approved = 1 AND HoL = 1';
                $sqldata = mysqli_query($link,$sql);
                while($lord = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $lord['FirstName'] . ' ' . $lord['LastName'] . '</td>';
                    echo '<td>' . $lord['NobleTitle'] .'</td>';

                    if($lord['Party'] == 0) {
                        $side = "Speaker";
                    } else if($lord['Party'] == 1) {
                        $side = "Government";
                    } else if($lord['Party'] == 2) {
                        $side = "Opposition";
                    } else if($lord['Party'] == 3) {
                        $side = "Crossbench";
                    }
                    echo '<td>' . $side .'</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once "footer.php"; ?>