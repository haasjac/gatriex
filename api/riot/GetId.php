<?php
    require_once('Call.php');
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/input.php');
    $urlName = $input->getGet("urlName");
    echo api_call("https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $urlName);
?>