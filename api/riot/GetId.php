<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    $urlName = $input->getGet("urlName");
    echo api_call("https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $urlName);
?>