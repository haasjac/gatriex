<?php
    require_once('Call.php');
    $summonerID = $_REQUEST["summonerID"];
    echo api_call("https://na1.api.riotgames.com/lol/league/v3/positions/by-summoner/" . $summonerID);
?>