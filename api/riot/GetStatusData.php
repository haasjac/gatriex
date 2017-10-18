<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $region = $input->getGet("region");
    
    $result = api_call("https://" . $region . ".api.riotgames.com/lol/status/v3/shard-data");
    
    echo json_encode($result);
?>