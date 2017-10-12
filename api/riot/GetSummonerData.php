<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $response = new Response();
    $summonerName = $input->getGet("summonerName");
    
    if ($summonerName === "") {
        $response->data["Error"] = "Summoner name cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
    
    $result = api_call("https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $summonerName);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    } else {
        $response->data["Summoner"] = $result->data["Response"];
    }
    
    $result = api_call("https://na1.api.riotgames.com/lol/league/v3/positions/by-summoner/" . $response->data["Summoner"]->id);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    } else {
        $response->data["League"] = $result->data["Response"];
    }
    
    $result = getVersion();
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    } else {
        $response->data["Version"] = $result->data["Version"];
    }
    
    $response->valid = true;
    echo json_encode($response);
       
    function getVersion() {
        global $db;

        $response = new Response();
        $sql = "SELECT * FROM Version WHERE Time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) > 0) {
            $response->data["Version"] = $result[0]["Version"];
            $response->valid = true;
            return $response;
        }
        else {
            $result = api_call("https://na1.api.riotgames.com/lol/static-data/v3/versions");
            
            if (!$result->valid) {
                return result;
            }
            
            $response->data["Version"] = $result->data["Response"][0];
            $response->valid = true;
            
            try {
                $db->beginTransaction();
                $sql = "DELETE FROM Version WHERE Time < DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
                $result = $db->query($sql);
                $stmt = $db->prepare("INSERT INTO Version (Version) VALUES (?)");
                $stmt->execute(array($response->data["Version"]));
                $db->commit();
            } catch (PDOException $ex) {
                http_response_code(500);
                $response->data["Error"] = $ex->getMessage();
                $response->valid = false;
                $db->rollBack();
                return $response;
            }
            return $response;
        }
    }
?>