<?php
include_once 'header/header.php';
include_once 'classes/party.php';
include_once 'classes/person.php';
$partyID = $_GET['id'];
$partyDetails = Party::getPartyDetails($link, $partyID);
$position = $partyDetails[4];
$side = Party::$sides[$position];
?>
<div class="page-header pt-3">
  <h2><?php echo $partyDetails[1];?></h2>
  <?php echo "<strong>Leader:</strong> " . Person::getDisplayName($link, $partyDetails['Leader']) . "<br>";?>
  <?php echo "<strong>Position:</strong>{$side}<br>";?>
</div>
<div class="row">
    <div class="col-sm-12 col-md-10 col-lg-8" <?php echo "style=\"border-color:{$partyDetails[2]}; border-style: solid\""?> >
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Issue</th>
                    <th>Stance</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Monarchy</td>
                    <td><?php echo Party::$monarchyStances[$partyDetails[6]] ?></td>
                </tr>
                <tr>
                    <td>Trade</td>
                    <td><?php echo Party::$tradeStances[$partyDetails[7]] ?></td>
                </tr>
                <tr>
                    <td>Tariffs</td>
                    <td><?php echo Party::$tariffStances[$partyDetails[8]] ?></td>
                </tr>
                <tr>
                    <td>Religion</td>
                    <td><?php echo Party::$religiousStances[$partyDetails[9]] ?></td>
                </tr>
                <tr>
                    <td>Navy</td>
                    <td><?php echo Party::$navyStances[$partyDetails[10]] ?></td>
                </tr>
                <tr>
                    <td>Army</td>
                    <td><?php echo Party::$armyStances[$partyDetails[11]] ?></td>
                </tr>
                <tr>
                    <td>Colonies</td>
                    <td><?php echo Party::$colonialStances[$partyDetails[12]] ?></td>
                </tr>
                <tr>
                    <td>Foreign Relations</td>
                    <td><?php echo Party::$foreignStance[$partyDetails[13]] ?></td>
                </tr>
                <tr>
                    <td>Scotland</td>
                    <td><?php echo Party::$scotlandStances[$partyDetails[14]] ?></td>
                </tr>
                <tr>
                    <td>Ireland</td>
                    <td><?php echo Party::$irelandStances[$partyDetails[15]] ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="col-md-8">
</div>
<?php include_once "footer.php"; ?>