<?php
    require_once('Call.php');
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/input.php');
    $summonerID = $input->getGet("summonerID");
    echo api_call("https://na1.api.riotgames.com/lol/league/v3/positions/by-summoner/" . $summonerID);
?>