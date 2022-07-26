<?php
session_start();
include 'config.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

include 'functions.php';
include 'header/header.php';
$positions = mysqli_fetch_all(mysqli_query($link, "SELECT * FROM positions"), MYSQLI_ASSOC);
?>
<div class="table-responsive">
    <h1 class="mt-3">Cabinet</h1>
    <table class="table table-dark">
      <thead>
        <tr>
          <th>Name</th>
          <th>Incumbent</th>
        </tr>
      </thead>
      <tbody>
<?php
foreach($positions as $pos) {
    if($pos['cabinet'] == 1) {
    //Print it all as a row
    echo '<tr>';
    echo '<td>' . $pos['name'] .' </td>';
    if($pos['holderID'] != null) {
        echo '<td>' . getName($link, $pos['holderID']) .' </td>';
    } else {
        echo '<td><i>Vacant</i></td>';
    }
    echo '</tr>';
    }
}

echo '</table></div>';
?>

              </tbody>
            </table>
            <h1 class="mt-3">Other positions</h1>
            <table class="table table-dark">
            <thead>
                <tr>
                <th>Name</th>
                <th>Incumbent</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($positions as $pos) {
                    if($pos['cabinet'] == 0) {
                    //Print it all as a row
                    echo '<tr>';
                    echo '<td>' . $pos['name'] .' </td>';
                    if($pos['holderID'] != null) {
                        echo '<td>' . getName($link, $pos['holderID']) .' </td>';
                    } else {
                        echo '<td><i>Vacant</i></td>';
                    }
                    echo '</tr>';
                    }
                }

                echo '</table></div>';
                ?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
<?php include_once "footer.php"; ?>