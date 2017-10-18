<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $response = new Response();
    $summonerName = $input->getGet("summonerName");
    $region = $input->getGet("region");
    
    if ($summonerName === "") {
        $response->data["Error"] = "Summoner name cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
    
    if ($region=== "") {
        $response->data["Error"] = "Region cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
    
    $result = api_call("https://" . $region . ".api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $summonerName);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    } else {
        $response->data["Summoner"] = $result->data["Response"];
    }
    
    $result = api_call("https://" . $region . ".api.riotgames.com/lol/league/v3/positions/by-summoner/" . $response->data["Summoner"]->id);
    
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
        global $db, $log, $region;

        $response = new Response();
        $sql = "SELECT * FROM Version WHERE Region = ? AND Time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($region));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) > 0) {
            $response->data["Version"] = $result[0]["Version"];
            $response->valid = true;
            return $response;
        }
        else {
            $result = api_call("https://" . $region . ".api.riotgames.com/lol/static-data/v3/versions");
            
            if (!$result->valid) {
                return $result;
            }
            
            $response->data["Version"] = $result->data["Response"][0];
            $response->valid = true;
            
            try {
                $db->beginTransaction();
                $sql = "UPDATE Version SET Version = ?, Time = CURRENT_TIMESTAMP WHERE Region = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($response->data["Version"], $region));
                if ($stmt->rowCount() <= 0) {
                    $stmt = $db->prepare("INSERT INTO Version (Version, Region) VALUES (?,?)");
                    $stmt->execute(array($response->data["Version"], $region));
                }
                $db->commit();
            } catch (PDOException $ex) {
                $log->error("Database error in GetSummonerData.php", $ex->getMessage());
                $response->data["Error"] = "Error handling request";
                $response->valid = false;
                $db->rollBack();
                return $response;
            }
            return $response;
        }
    }
?>