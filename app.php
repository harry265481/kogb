<?php
session_start();
$ID  = $_GET['id'];
include 'config.php';
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}
if(!isset($_SESSION['adminlevel']) || $_SESSION["adminlevel"] <= 0) {
    header("location: home.php");
    exit;
}
include 'functions.php';
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($_POST['approval'] == '1') {
        $FirstName = mysqli_real_escape_string($link, $_POST['FirstName']);
        $LastName = mysqli_real_escape_string($link, $_POST['LastName']);
        $by = $_POST['birthyear'];
        $noble = mysqli_real_escape_string($link, $_POST['noble']);
        $relation = mysqli_real_escape_string($link, $_POST['relations']);
        $bio = mysqli_real_escape_string($link, $_POST['bio']);
        $sql = "UPDATE `people` SET `FirstName`='{$FirstName}', `LastName`='{$LastName}', `BirthYear`={$by}, `NobleTitle`='{$noble}', `Relations`='{$relation}', `Biography`='{$bio}', `Approved`=1 WHERE `ID`={$ID}";
        mysqli_query($link, $sql);
        if (!mysqli_query($link, $sql)) {
            echo $sql . "<br>";
            echo("Error description: " . mysqli_error($link) . " <br>");
        } else {
            header("location: pending.php");
            exit;
        }
    } else {
        $sql = "UPDATE `people` SET `Denied`=1 WHERE `ID`={$ID}";
        if (!mysqli_query($link, $sql)) {
            echo $sql . "<br>";
            echo("Error description: " . mysqli_error($link) . " <br>");
        } else {
            header("location: pending.php");
            exit;
        }
        header("location: pending.php");
        exit;
    }
}
$sqldata = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM People WHERE id = {$ID}"));
$FirstName = $sqldata['FirstName'];
$LastName = $sqldata['LastName'];
include 'header/header.php';
?>
    <?php echo "<form action=\"app.php?id={$ID}\" method=\"post\">" ?>
        <div class="row">
            <h5 style = "margin-top: 30px">User: <?php echo getUsername($link, $sqldata['User']) ?></h5>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Name</h5>
                <p>Check if another player has the same last name. If they do, make sure the original player is okay with them being related</p>
                <?php
                    $year = $sqldata['BirthYear'];
                    echo "<input type='text' name='FirstName' value={$FirstName}>";
                    echo "<input type='text' name='LastName' value={$LastName}>";
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Birth Year</h5>
                <p>Ensure they're at least 18 and most characters should not be over 80</p>
                <?php 
                    $year = $sqldata['BirthYear'];
                    echo "<input type='number' name='birthyear' value={$year}>";
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Noble Titles</h5>
                <p>Ensure these titles are formatted correctly. i.e. 'duke of norfolk' should be edited to 'Duke of Norfolk'</p>
                <p>If more than one is listed, order them by rank. i.e. Duke of Norfolk, Marquess of Winchester, Earl of Leicester, Viscount Dursley, Baron Seymour of Trowbridge</p>
                <p>Ensure titles are separate by a comma, and then a space. Duke of Norfolk, Earl of Leicester</p>
                <textarea height="600px" class="form-control" name="noble"><?php echo $sqldata['NobleTitle'];?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Relations</h5>
                <p>Having nothing here is fine, just make sure it's grammatically correct</p>
                <textarea height="600px" class="form-control" name="relations"><?php echo $sqldata['Relations'];?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <h5>Biography</h5>
                <p>Make sure it's not too bizarre. A lot of applications will use real history in them</p>
                <textarea height="600px" class="form-control" name="bio"><?php echo $sqldata['Biography'];?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <button class="btn btn-lq btn-primary" name="approval" type="submit" value="1">Approve</button>
                <button class="btn btn-lq btn-danger" name="approval" type="submit" value="0">Deny</button>
            </div>
        </div>
    </form>
    <script>
        const tx = document.getElementsByTagName("textarea");
        for (let i = 0; i < tx.length; i++) {
        tx[i].setAttribute("style", "height:" + (tx[i].scrollHeight) + "px;overflow-y:hidden;");
        tx[i].addEventListener("input", OnInput, false);
        }

        function OnInput() {
        this.style.height = "auto";
        this.style.height = (this.scrollHeight) + "px";
        }
    </script>
    <?php include_once "footer.php"; ?>