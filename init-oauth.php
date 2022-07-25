<?php

$discord_url = "https://discord.com/api/oauth2/authorize?client_id=1001049627887415388&redirect_uri=http%3A%2F%2Flocalhost%2Fkogb%2Fprocess-oauth.php&response_type=code&scope=identify%20guilds.join";
header("Location: $discord_url");
exit;
?>