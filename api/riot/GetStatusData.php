<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $result = api_call("https://na1.api.riotgames.com/lol/status/v3/shard-data");
    
    echo json_encode($result);
?>