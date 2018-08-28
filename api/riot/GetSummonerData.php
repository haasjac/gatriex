<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $response = new Response();
    $summonerName = Input::GetGet("summonerName");
    $region = Input::GetGet("region");
    
    if ($summonerName === "") {
        $response->data["Error"] = "Summoner name cannot be empty.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
    
    if ($region === "") {
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
    
    $result = api_call("https://" . $region . ".api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/" . $response->data["Summoner"]->id);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    } else {
        $array = $result->data["Response"];
        $sorted = usort($array, "sortMastery");
        if ($sorted) {
            $response->data["Mastery"] = array_slice($array, 0, 3);
        } else {
            $response->data["Mastery"] = array_slice($result->data["Response"], 0, 3);
        }
    }
    
    $response->data["Champions"] = array();
    for ($i = 0; $i < sizeof($response->data["Mastery"]); $i++) {
        $result = getChampion($region, $response->data["Mastery"][$i]->championId);
    
        if (!$result->valid) {
            echo json_encode($result);
            return;
        } else {
            $response->data["Champions"][$i] = $result->data["Key"];
        }
    }    
    
    $result = getVersion($region);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    } else {
        $response->data["Version"] = $result->data["Version"];
    }
    
    $response->valid = true;
    echo json_encode($response);
       
    function getVersion($region): Response {
        $response = new Response();
        $sql = "SELECT * FROM Version WHERE Region = ? AND Time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
        $stmt = Database::Get()->prepare($sql);
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
                Database::Get()->beginTransaction();
                $sql = "UPDATE Version SET Version = ?, Time = CURRENT_TIMESTAMP WHERE Region = ?";
                $stmt = Database::Get()->prepare($sql);
                $stmt->execute(array($response->data["Version"], $region));
                if ($stmt->rowCount() <= 0) {
                    $stmt = Database::Get()->prepare("INSERT INTO Version (Version, Region) VALUES (?,?)");
                    $stmt->execute(array($response->data["Version"], $region));
                }
                Database::Get()->commit();
            } catch (PDOException $ex) {
                Log::Error("Database error in GetSummonerData.php", $ex->getMessage());
                $response->data["Error"] = "Error handling request";
                $response->valid = false;
                Database::Get()->rollBack();
                return $response;
            }
            return $response;
        }
    }
    
    function getChampion($region, $id): Response {
        $response = new Response();
        $sql = "SELECT * FROM Champions WHERE id = ?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($id));
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (sizeof($result) > 0) {
            $response->data["Key"] = $result[0]["ChampKey"];
            $response->valid = true;
            return $response;
        }
        else {
            $result = api_call("https://" . $region . ".api.riotgames.com/lol/static-data/v3/champions/" . $id);
            
            if (!$result->valid) {
                return $result;
            }
            var_dump($result);
            $response->data["Key"] = $result->data["Response"]->key;
            $response->valid = true;
            
            try {
                $stmt = Database::Get()->prepare("INSERT INTO Champions (id, ChampKey) VALUES (?,?)");
                $stmt->execute(array($id, $result->data["Response"]->key));
            } catch (PDOException $ex) {
                Log::Error("Database error in GetSummonerData.php", $ex->getMessage());
                $response->data["Error"] = "Error handling request";
                $response->valid = false;
                return $response;
            }
            return $response;
        }
    }
    
    function sortMastery($a, $b): int {
        try {
            if ($a->championLevel === $b->championLevel) {
                return $b->championPoints - $a->championPoints;
            } else {
                return $b->championLevel - $a->championLevel;
            }
        } catch (Exception $ex) {
            return 0;
        }
    }
?>