<?php
session_start();
include 'config.php';
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
if($_SESSION["adminlevel"] <= 0){
    header("location: home.php");
    exit;
}

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$sqlget = 'SELECT ID, FirstName, LastName, BirthYear, NobleTitle FROM People WHERE Approved = 0';
$sqldata = mysqli_query($link, $sqlget) or die('Connection could not be established');

include 'functions.php';
include 'header/header.php';
?>
  <div class="page-header pt-3">
    <h2>Pending</h2>
  </div>
  <p class="page-header"></p>
  <div class="table-responsive">
    <table class="table table-dark">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Birth Year</th>
          <th>User</th>
          <th>Noble Title</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
<?php
while ($row = mysqli_fetch_array($sqldata, MYSQLI_ASSOC)) {

    //Print it all as a row
    echo '<tr>';
    echo '<td>' . $row['ID'] .' </td>';
    echo '<td>' . $row['FirstName'] . ' ' . $row['LastName'] . '</td>';
    echo '<td>' . $row['BirthYear'] .' </td>';
    echo '<td>' . $row['NobleTitle'] .' </td>';
    echo '<form action=app.php method=get>';
    echo "<td><input class='btn btn-primary btn-outline' type=submit name=view value=View></td>";
    echo "<td style='display:none;'>".'<input type=hidden name=id value='.$row['ID'].' </td>';
    echo '</form>';
    echo '</tr>';
}

echo '</table></div>';
?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
<?php include_once "footer.php"; ?>