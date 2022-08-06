<?php
include 'header/header.php';
include_once 'classes/house.php';
include_once 'classes/party.php';
$parliamentID = $_GET['id'];
$house = new House($link, $parliamentID);
?>
<h3 class="mt-3">Factions of the <?php echo $house->getParliamentName($link); ?></h3>
<a class="btn btn-primary" href="createfaction.php">Create Faction</a>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <table class="table table-dark">
            <thead>
                <tr>
                    <th></th>
                    <th>Leader</th>
                    <th>Position</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $parties = Party::getAllParties($link, $parliamentID);
                foreach($parties as $p) {
                    echo '<tr>';
                    echo "<td width='15px' style=\"background-color: {$p['Color']}\"></td>";
                    echo "<td>{$p['Name']}</td>";
                    $position = $p['Position'];
                    $side;
                    if($position == 0) {
                        $side = "Speaker";
                    } else if($position == 1) {
                        $side = "Government";
                    } else if($position == 2) {
                        $side = "Opposition";
                    } else if($position == 3) {
                        $side = "Crossbench";
                    }
                    echo "<td>{$side}</td>";
                    echo "<td><a class=\"btn btn-primary\" href=\"party.php?id={$p['ID']}\">View</a></td>";
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once "footer.php"; ?>