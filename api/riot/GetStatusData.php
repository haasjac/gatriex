<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $region = $input->getGet("region");
    
    if ($region=== "") {
        $response->data["Error"] = "Region cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
    
    $result = api_call("https://" . $region . ".api.riotgames.com/lol/status/v3/shard-data");
    
    echo json_encode($result);
?>