<?php
include 'header/header.php';
include_once 'classes/party.php';

$name_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["name"]))) {
        $name_err = "Enter a faction name";
    }
    if($name_err == "") {
        $namecheck = mysqli_query($link, "SELECT Name FROM parties WHERE Name = '{$_POST['name']}'");
        if(mysqli_num_rows($namecheck) > 0) {
            $name_err = "This faction name is taken";
        } else {
            $name  = mysqli_real_escape_string($link, trim($_POST['name']));
            $color = $_POST['color'];
            $position  = $_POST['position'];
            $monarchy = $_POST['monarchy'];
            $religion = $_POST['religion'];
            $trade = $_POST['trade'];
            $tariffs  = $_POST['tariffs'];
            $army  = $_POST['army'];
            $navy  = $_POST['navy'];
            $colonies  = $_POST['colonies'];
            $foreign  = $_POST['foreign'];
            $scotland  = $_POST['scotland'];
            $ireland  = $_POST['ireland'];
            $desc  = mysqli_real_escape_string($link, trim($_POST['desc']));
            
            $sql = "INSERT INTO `parties` (`Name`, `Color`, `Parliament`, `Position`, `monarchyStance`, `tradeStance`, `tariffStance`, `religiousStance`, `navyStance`, `armyStance`, `colonialStance`, `foreignStance`, `scotlandStances`, `irelandStances`, `Leader`, `description`) 
                                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            if($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssiiiiiiiiiiiiis", $param_name, $param_color, $param_parliament, $param_position, $param_monarchy, $param_trade, $param_tariff, $param_religion, $param_navy, $param_army, $param_colonial, $param_foreign, $param_scotland, $param_ireland, $param_leader, $param_description);
                $param_name = $name;
                $param_color = $color;
                $param_parliament = 1;
                $param_position = $position;
                $param_monarchy = $monarchy;
                $param_trade = $trade;
                $param_tariff = $tariffs;
                $param_religion = $religion;
                $param_navy = $navy;
                $param_army = $army;
                $param_colonial = $colonies;
                $param_foreign = $foreign;
                $param_scotland = $scotland;
                $param_ireland = $ireland;
                $param_leader = $player->ID;
                $param_description = $desc;
                if(mysqli_stmt_execute($stmt)) {
                    $id = mysqli_stmt_insert_id($stmt);
                    echo "<script>parent.self.location=\"party.php?id={$id}\"</script>";
                }
            }
        }
    }
}

?>
<h3 class="mt-3">Create Faction</h3>
<form action="createfaction.php" method="post">
    <div class="col-md-12 col-xl-10">
        <div class="row mt-3">
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Name" class="form-label">Faction Name</label>
                <input type="text" id="Name" class="form-control" name="name">
                <?php if($name_err != "") {echo "<p class=\"text-light\">{$name_err}</p>"; } ?>
                <div id="NameHelp" class="form-text">Innappropriate names or names the team feel are not appropriate for the era will be edited without warning</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Color" class="form-label">Color</label><br>
                <input type="color" id="Color" class="form-control" name="color">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Position" class="form-label">Position</label><br>
                <select id="Position" class="form-select form-select-sm mb-3" name="position">
                    <option value="1">Government</option>
                    <option value="2">Opposition</option>
                    <option value="3">Crossbench</option>
                </select>
                <div class="form-text">Which side of the Houses of Parliament will your faction be sitting?</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Monarchy" class="form-label">Monarchy</label><br>
                <select id="Monarchy" class="form-select form-select-sm mb-3" name="monarchy">
                    <?php foreach(Party::$monarchyStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on the Monarchy</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Religion" class="form-label">Religion</label><br>
                <select id="Religion" class="form-select form-select-sm mb-3" name="religion">
                    <?php foreach(Party::$religiousStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on State Religion</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Trade" class="form-label">Trade</label><br>
                <select id="Trade" class="form-select form-select-sm mb-3" name="trade">
                    <?php foreach(Party::$tradeStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on Trade Protections for British Merchants</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Tariffs" class="form-label">Tariffs</label><br>
                <select id="Tariffs" class="form-select form-select-sm mb-3" name="tariffs">
                    <?php foreach(Party::$tariffStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on Import Tariffs on goods outside the British Isles</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Navy" class="form-label">Navy</label><br>
                <select id="Navy" class="form-select form-select-sm mb-3" name="navy">
                    <?php foreach(Party::$navyStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on the Navy</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Army" class="form-label">Army</label><br>
                <select id="Army" class="form-select form-select-sm mb-3" name="army">
                    <?php foreach(Party::$armyStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on the Army</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Colonies" class="form-label">Colonies</label><br>
                <select id="Colonies" class="form-select form-select-sm mb-3" name="colonies">
                    <?php foreach(Party::$colonialStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on Colonial Expansion</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="ForeignRelations" class="form-label">Foreign Relations</label><br>
                <select id="ForeignRelations" class="form-select form-select-sm mb-3" name="foreign">
                    <?php foreach(Party::$foreignStance as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on Foreign Relations</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Scotland" class="form-label">Scotland</label><br>
                <select id="Scotland" class="form-select form-select-sm mb-3" name="scotland">
                    <?php foreach(Party::$scotlandStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on Scotland</div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 col-xxl-3">
                <label for="Ireland" class="form-label">Ireland</label><br>
                <select id="Ireland" class="form-select form-select-sm mb-3" name="ireland">
                    <?php foreach(Party::$irelandStances as $k => $o) { echo "<option value={$k}>{$o}</option>";} ?>
                </select>
                <div class="form-text">Your factions view on Ireland</div>
            </div>
            <div class="col-sm-12 col-lg-8 col-xl-8 col-xxl-6">
                <label for="Description" class="form-label">Description</label>
                <textarea type="text" id="Description" class="form-control" name="desc"></textarea>
            </div>
        </div>
        <div class="mt-3 col-sm-12 col-md-6 col-lg-4 col-xxl-3">
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </div>
</form>
<?php include_once "footer.php"; ?>