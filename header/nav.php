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
            <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar">
                <i class="fa-solid fa-award"></i> <span>Privy Council</span>
            </a>
            <a href="government.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar">
                <i class="fa-solid fa-users"></i> <span>Government</span>
            </a>
            <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar" data-bs-target="#lords-dropdown" data-bs-toggle="collapse">
            <i class="fa-solid fa-landmark"></i> <span>House of Lords</span>
            </a>
            <div id="lords-dropdown" class="collapse">
                <div id="lords-nav" class="list-group border-0 rounded-0 text-sm-start">
                    <a href="lords.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#lords-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Members</span>
                    </a>
                </div>
            </div>
            <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar" data-bs-target="#commons-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-archway"></i><span> House of Commons</span></a>
            <div id="commons-dropdown" class="collapse">
                <div id="commons-nav" class="list-group border-0 rounded-0 text-sm-start">
                    <a href="commons.php?house=0" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#commons-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Composition</span>
                    </a>
                    <a href="seats.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#commons-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Seats</span>
                    </a>
                    <a href="map.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#commons-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Map</span>
                    </a>
                </div>
            </div>
            <a href="#" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar" data-bs-target="#icommons-dropdown" data-bs-toggle="collapse"><i class="fa-solid fa-archway"></i><span> Irish House of Commons</span></a>
            <div id="icommons-dropdown" class="collapse">
                <div id="icommons-nav" class="list-group border-0 rounded-0 text-sm-start">
                    <a href="commons.php?house=1" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#icommons-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Composition</span>
                    </a>
                    <a href="irishseats.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#icommons-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Seats</span>
                    </a>
                    <a href="irishmap.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#icommons-dropdown">
                        <i class="bi bi-arrow-return-right"></i> <span>Map</span>
                    </a>
                </div>
            </div>
            <a href="cabinet.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate" data-bs-parent="#sidebar"> 
                <i class="fa-solid fa-box-archive"></i> <span>Previous Elections</span>
            </a>
            <?php 
            if($isadmin) {
                echo'
                <a href="pending.php" class="list-group-item list-group-item-dark border-end-0 d-inline-block text-truncate"><i class="fa-solid fa-clipboard"></i> Pending Characters</a>';
            }
            ?>
        </div>
    </div>
</div>