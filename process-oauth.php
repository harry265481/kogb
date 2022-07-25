<?php
if(!isset($_GET['code'])) {
    echo "Something went wrong. Discord didn't send your details";
    exit();
}

$discord_code = $_GET['code'];

require_once("discordpayload.php");

$payload = CONSTANT("PAYLOAD");

$payload_string = http_build_query($payload);
$discord_token_url = "https://discordapp.com/api/oauth2/token";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $discord_token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);
$result = json_decode($result, true);
$access_token = $result['access_token'];

$discord_users_url = "https://discordapp.com/api/users/@me";
$header = array("Authorization: Bearer $access_token", "Content-Type: application/x-www-form-urlencoded");

$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_URL, $discord_users_url);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

$result = curl_exec($ch);
$result = json_decode($result, true);
include_once "config.php";
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$dUser = $result['id'];

//Check if an account already exists
$res = mysqli_fetch_all(mysqli_query($link, "SELECT id, adminlevel FROM users WHERE discordID = \"{$dUser}\""), MYSQLI_ASSOC);
if($res) {
    //Log them in
    session_start();
    $_SESSION["loggedin"] = true;
    $_SESSION['id'] = $res[0]['id'];
    $_SESSION['isDisc'] = true;
    $_SESSION['username'] = $result['username'];
    $_SESSION['discid'] = $result['id'];
    $_SESSION['avatar'] = $result['avatar'];
    $_SESSION['adminlevel'] = $res[0]['adminlevel'];
    header('Location: home.php');
} else {
    echo "User does not exist";
    $discordUser = $result['username'];
    $discordID = $result['id'];
    $discordAvatar = $result['avatar'];
    $discordDiscriminator = $result['discriminator'];
    $sql = "INSERT INTO users (discordUser, discordID, discordAvatar, discordDiscriminator) VALUES (\"{$discordUser}\", \"{$discordID}\", \"{$discordAvatar}\", \"{$discordDiscriminator}\")";
    mysqli_query($link, $sql);

    //now that they do exist, log them in
    session_start();
    $_SESSION["loggedin"] = true;
    $_SESSION['id'] = $res[0]['id'];
    $_SESSION['isDisc'] = true;
    $_SESSION['username'] = $result['username'];
    $_SESSION["discid"] = $result['id'];
    $_SESSION['avatar'] = $result['avatar'];
    $_SESSION['adminlevel'] = $res[0]['adminlevel'];
    //header('Location: home.php');
}

?>