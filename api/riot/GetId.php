<?php
    include('Call.php');
    $urlName = $_REQUEST["urlName"];
    echo api_call("https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $urlName);
?>