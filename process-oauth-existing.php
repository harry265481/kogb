<?php
start_session();
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

$result = curl_exec($ch);
$result = json_decode($result, true);
include_once "config.php";
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
//insert their discord info
$discuser = $result['username'];
$discid = $result['id'];
$discavatar = $result['avatar'];
$discordDiscriminator = $result['discriminator'];
$id = $_SESSION['id'];
$sql = "UPDATE `users` SET `discordUser`=\"{$discuser}\", `discordID`=\"{$discid}\", `discordAvatar`=\"{$discavatar}\", `discordDiscriminator`=\"{$discordDiscriminator}\" WHERE id = {$id}";
if(mysqli_query($link, $sql)) {
    header('Location: home.php');
    exit;
} else {
    echo "There was an error. Notify Earl of Berkeley on Discord";
}
?>