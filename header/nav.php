<?php 
$isadmin = false;
if($_SESSION["adminlevel"] > 0){
    $isadmin = true;
}
?>
<div class="col-auto px-0">
    <div id="sidebar" class="show collapse collapse-horizontal">
        <div id="sidebar-nav" class="list-group border-0 rounded-0 text-sm-start min-vh-100">
            <a href="home.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar">
                <i class="fa-solid fa-house"></i> <span>Home</span>
            </a>
            
            <a href="pol-office.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar">
                <i class="fa-solid fa-building-flag"></i> <span>Political Office</span>
            </a>
            <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar" data-bs-target="#country-dropdown" data-bs-toggle="collapse"><?php echo "<img height=\"16px\" src=\"assets/icons/flags/{$player->nation->abbrev}.svg\"/>" ?> <span><?php echo $player->nation->name ?></span></a>
            <div id="country-dropdown" class="collapse">
                <div id="country-nav" class="list-group border-0 rounded-0 text-sm-smart">
                    <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#country-dropdown" data-bs-target="#government-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-landmark-dome"></i> <span>Government</span></a>
                    <div id="government-dropdown" class="collapse">
                        <div id="government-nav" class="list-group border-0 rounded-0 text-sm-smart">
                            <a href="government.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#government-dropdown"><i class="fa-solid fa-users"></i> <span>Current Government</span></a>
                            <a href="parliamenthouse.php?id=1" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#government-dropdown"><i class="fa-solid fa-landmark"></i> <span>House of Lords</span></a>
                            <a href="parliamenthouse.php?id=2"" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#government-dropdown"><i class="fa-solid fa-archway"></i><span> House of Commons</span></a>
                        </div>
                    </div>
                    <a href="parties.php?id=1" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#country-dropdown"><i class="fa-solid fa-user-group"></i> <span>Factions</span></a>
                </div>  
            </div>
            <!--
            <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar" data-bs-target="#econ-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-sterling-sign"></i> <span>Economy</span></a>
            <div id="econ-dropdown" class="collapse">
                <div id="econ-nav" class="list-group border-0 rounded-0 text-sm-smart">
                    <a href="econmap.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#econ-dropdown"><i class="fa-solid fa-map"></i> <span>Map</span></a>
                    <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#econ-dropdown" data-bs-target="#prod-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-industry"></i> <span>Production</span></a>
                    <div id="prod-dropdown" class="collapse">
                        <div id="prod-nav" class="list-group border-0 rounded-0 text-sm-smart">
                            <a href="econcountry.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#prod-dropdown"><i class="fa-solid fa-chart-pie"></i> <span>Stats</span></a>
                            <a href="recipes.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#prod-dropdown"> <span>Recipes</span></a>
                        </div>
                    </div>
                </div>
            </div>
            -->
            <?php 
            if($isadmin) {
                echo'
                <a href="pending.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate"><i class="fa-solid fa-clipboard"></i> Pending Characters</a>';
            }
            ?>
        </div>
    </div>
</div>