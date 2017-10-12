<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    $urlName = $input->getGet("urlName");
    
    $response = api_call("https://na1.api.riogames.com/lol/summoner/v3/summoners/by-name/" . $urlName);
    
    echo json_encode($response);
?>