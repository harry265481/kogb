<?php
include_once 'header/header.php';

//$sqlget = "SELECT ID, purse FROM people WHERE User = " . $_SESSION["id"];
$ID = $player->ID;
$balance = $player->purseString;

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $money = intval($_POST["money"]);
  $sql = 'INSERT INTO EmployedMPs (employerID, seatID, purse) VALUES
                                  (' . $ID . ', ' . $_POST["seat"] .  ', ' . $money . ')';
  mysqli_query($link, $sql);
  $sql = 'UPDATE people SET purse = purse - ' . $money . ' WHERE ID = ' . $ID;
  mysqli_query($link, $sql);
}

include_once "classes/mp.php";
$mps = MP::getAllMPsEmployedBy($link, $ID);

include_once "classes/constituency.php";
$constituencies = Constituency::getAllIDName($link);


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
<div class="col-md-10 col-sm-12">
  <h3>MPs</h3>
  <p>This is a list of persons you have hired to stand at elections</p>
  <p>People hired to stand at elections are given money to spend on things such as bribes, burgages, and to 'treat' the electors of the given seat. Also, sending more people than there are seats in a given constituency will have an adverse effect in that you will be splitting your own vote</p>
  <p>Balance: £<?php echo $balance; ?></p>
  <button onclick="unhideMPHiring()" type="button" class="btn btn-primary mb-3">Hire an MP</button>
  <div id="hiring-div" style="display:none">
    <form action="pol-office.php" method="post">
      <div class="input-group mb-3">
      <span class="input-group-text">Purse</span><input type="number" name="money" class="form-control">
      </div>
        <select class="form-select mb-3" name="seat">
          <?php
            foreach($constituencies as $c) {
              echo "<option value=\"{$c[0]}\">{$c[1]}</option>";
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
          <th>Name</th>
          <th>Purse</th>
          <th>Seat</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if($mps != false) {
          foreach($mps as $mp) {
            $seatname = Constituency::getConstituencyName($link, $mp[3]);
            $mpBal = number_format($mp[4]);
            echo "<tr><td>{$mp[0]}</td><td>{$mp[1]}</td><td>£{$mpBal}</td><td><a class=\"link-info\" href=\"seat.php?id={$mp[3]}\">{$seatname}</a></td></tr>";
          }
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once "footer.php"; ?>