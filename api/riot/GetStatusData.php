<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $region = Input::GetGet("region");
    
    if ($region === "") {
        $response->data["Error"] = "Region cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
    
    $result = ApiCall("https://" . $region . ".api.riotgames.com/lol/status/v3/shard-data");
    
    echo json_encode($result);
?>