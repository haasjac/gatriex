<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $region = Input::GetGet("region");
    $id = Input::GetGet("id");
    
    /*if ($region === "") {
        $response->data["Error"] = "Region cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }*/
    
    $result = api_call("https://na1.api.riotgames.com/lol/spectator/v3/active-games/by-summoner/29199280");
    
    echo json_encode($result);
?>