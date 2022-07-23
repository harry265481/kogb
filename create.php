<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}

require_once "config.php";

$firstname = $lastname = $birthyear = "";
$firstname_err = $lastname_err = $birthyear_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["firstname"]))) {
        $firstname_err = "Enter a first name";
    }
    if(empty(trim($_POST["lastname"]))) {
        $firstname_err = "Enter a first name";
    }
    if(!$firstname_err == "" && !$lastname_err == "") {
        $sql = "SELECT FirstName, LastName FROM People WHERE FirstName = ? AND LastName = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $param_firstname, $param_lastname);
            $param_firstname = mysql_real_escape_string(trim($_POST["firstname"]));
            $param_lastname = mysql_real_escape_string(trim($_POST["lastname"]));
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_store_rows($stmt) == 1) {
                    $lastname_err = "This name combination is taken";
                } else {
                    $firstname = mysql_real_escape_string($link, trim($_POST["firstname"]));
                    $lastname = mysql_real_escape_string($link, trim($_POST["lastname"]));
                }
            } else {
                echo "There was some kind of error, contact Earl of Berkeley on Discord";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if(empty(trim($_POST["birthyear"]))) {
        $birthyear_err = "Please enter a birthyear";
    } else {
        $birthyear = trim($_POST["birthyear"]);
    }

    if(empty($firstname_err) && empty($lastname_err) && empty($birthyear_err)) {
        $sql = "INSERT INTO People (FirstName, LastName, BirthYear, User, NobleTitle, Relations, Biography) VALUES (?,?,?,?,?,?,?)";
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssiisss", $param_firstname, $param_lastname, $param_birthyear, $param_user, $param_noble, $param_relation, $param_biography);
            $param_firstname = mysqli_real_scape_string($link, $firstname);
            $param_lastname = mysqli_real_scape_string($link, $lastname);
            $param_birthyear = $birthyear;
            $param_user = $_SESSION["id"];
            $param_noble = mysql_real_escape_string($link, $_POST["noble"]);
            $param_relation = mysql_real_escape_string($link, $_POST["relations"]);
            $param_biography = mysql_real_escape_string($link, $_POST["bio"]);
            if(mysqli_stmt_execute($stmt)) {
                header("location: home.php");
            } else {
                echo "Something went wrong, contact Earl of Berkeley on Discord";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>KoGB | Create a Character</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="charactercreate.css">
    </head>
    <body class="text-center">
       <main class="form-signin">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <img class="mb-4" src="https://upload.wikimedia.org/wikipedia/commons/2/28/Coat_of_Arms_of_Great_Britain_%281714-1801%29.svg" width="200px">
                <h1 class="h3 mb-3 fw-normal" style="color: white;">Create a character</h1>
                <div class="form-floating">
                    <input type="text" class="form-control" name="firstname">
                    <label for="floatingInput">First Name</label>
                    <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" name="lastname">
                    <label for="floatingInput">Last Name</label>
                    <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
                </div>
                <div class="form-floating">
                    <input type="number" min="1670" max="1732" class="form-control" name="birthyear">
                    <label for="floatingInput">Birthyear</label>
                    <span class="invalid-feedback"><?php echo $birthyear_err; ?></span>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" name="noble">
                    <label for="floatingInput">Noble Title <small>(if any)</small></label>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" name="relations">
                    <label for="floatingInput">Important family relations <small>(if any)</small></label>
                </div><div class="form-floating">
                    <textarea class="form-control" name="bio"></textarea>
                    <label for="floatingTextarea">Character Biography</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Create</button>
            </form>
        </main>
    </body>
</html>