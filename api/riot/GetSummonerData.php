<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    $summonerID = $input->getGet("summonerID");
    echo api_call("https://na1.api.riotgames.com/lol/league/v3/positions/by-summoner/" . $summonerID);
?>