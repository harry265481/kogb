<?php
include_once 'header/header.php';
include_once 'classes/position.php';

?>
<div class="page-header pt-3">
  <h2>Your office</h2>
  <h4>Your positions</h4>
  
    <?php
        foreach($player->positions as $p) {
            echo "<p>{$p['name']}</p><br>";
        }
    ?>
</div>
<div class="row mt-3">
    <div class="col-sm-0 col-xl-3">
    </div>
    <div class="col-sm-12 col-xl-6">  
        <h6>Positions appointed by you</h6>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Incumbent</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($player->positions as $p) {
                    $aPositions = Position::getPositionsAppointedBy($link, $p['ID']);
                    foreach($aPositions as $ap) {
                        echo "<tr>";
                        echo    "<td>{$ap['name']}</td>";
                        echo    "<td>{$ap['description']}</td>";
                        $name = Position::getPositionHolderName($link, $ap['ID']);
                        echo    "<td>{$name}</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="col-sm-0 col-xl-3">
    </div>
</div>
<?php include_once "footer.php"; ?>