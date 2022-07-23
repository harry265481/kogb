<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
include_once 'config.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$sqlget = "SELECT ID, purse FROM people WHERE User = " . $_SESSION["id"];
$ID = mysqli_fetch_array(mysqli_query($link, $sqlget), MYSQLI_ASSOC);
$money = $ID['purse'];
$ID = $ID['ID'];
$money = number_format(floatval($money));

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $money = intval($_POST["money"]);
  $sql = 'INSERT INTO EmployedMPs (employerID, seatID, purse) VALUES
                                  (' . $ID . ', ' . $_POST["seat"] .  ', ' . $money . ')';
  mysqli_query($link, $sql);
  $sql = 'UPDATE people SET purse = purse - ' . $money . ' WHERE ID = ' . $ID;
  mysqli_query($link, $sql);
}

include_once 'functions.php';
include_once 'header/header.php';

$sqlget = "SELECT * FROM EmployedMPs WHERE employerID = " . $ID;
$sqlmps = mysqli_query($link, $sqlget);

$sqlget = "SELECT ID, Name FROM `seats` ORDER BY `seats`.`Name` ASC";
$sqlseats = mysqli_query($link, $sqlget);

$sqlget = "SELECT Name FROM `seats` ORDER BY ID ASC";
$sqlseatnames = mysqli_query($link, $sqlget);
?>
<script>
  function unhideMPHiring() {
    var x = document.getElementById("hiring-div");
    if(x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }
</script>
<div class="page-header pt-3">
  <h2>Political HQ</h2>
</div>
<div class="col-6">
  <h3>MPs</h3>
  <p>This is a list of persons you have hired to stand at elections</p>
  <p>People hired to stand at elections are given money to spend on things such as bribes, burgages, and to 'treat' the electors of the given seat. Also, sending more people than there are seats in a given constituency will have an adverse effect in that you will be splitting your own vote</p>
  <p>Balance: £<?php echo $money; ?></p>
  <button onclick="unhideMPHiring()" type="button" class="btn btn-primary">Hire an MP</button>
  <div id="hiring-div" style="display:none">
    <form action="pol-office.php" method="post">
      <input type="number" name="money">
      <select name="seat">
        <?php
          while($seat = mysqli_fetch_array($sqlseats, MYSQLI_ASSOC)) {
            echo '<option value="' . $seat["ID"] . '">' . $seat["Name"] . '</option>';
          }
        ?>
      </select>
      <button type="submit" class="btn btn-primary">Hire</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-dark">
      <thead>
        <tr>
          <th>ID</th>
          <th>Purse</th>
          <th>Seat</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $mps = mysqli_fetch_all($sqlmps, MYSQLI_ASSOC);
          foreach($mps as $mp) {
            $seatname = mysql_result($sqlseatnames, $mp["seatID"] - 1);
            echo "<tr><td>" . $mp["ID"] . "</td><td>" . $mp["purse"] . "</td><td>" . $seatname . "</td></tr>";
          }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once "footer.php"; ?>