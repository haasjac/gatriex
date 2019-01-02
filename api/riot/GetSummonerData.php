<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("GET");

	$expected = array(
		"summonerName" => FILTER_SANITIZE_ENCODED,
		"region" => FILTER_SANITIZE_ENCODED
	);

	$input = Input::GetDataFromUrl($expected);

    $response = new Response();
    $summonerName = $input["summonerName"];
    $region = $input["region"];
    
	
    $result = ApiCall("https://" . $region . ".api.riotgames.com/lol/summoner/v4/summoners/by-name/" . $summonerName);
    
    $response->data["Summoner"] = $result->data["Response"];
    
    $result = ApiCall("https://" . $region . ".api.riotgames.com/lol/league/v4/positions/by-summoner/" . $response->data["Summoner"]->id);
    
    $response->data["League"] = $result->data["Response"];
    
    $result = ApiCall("https://" . $region . ".api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-summoner/" . $response->data["Summoner"]->id);
    
    $array = $result->data["Response"];
    $sorted = usort($array, "SortMastery");
    if ($sorted) {
        $response->data["Mastery"] = array_slice($array, 0, 3);
    } else {
        $response->data["Mastery"] = array_slice($result->data["Response"], 0, 3);
    }
		
    $result = GetVersion();

	$response->data["Version"] = $result->data["Version"];
    
    $response->data["Champions"] = array();
    for ($i = 0; $i < sizeof($response->data["Mastery"]); $i++) {
        $result = GetChampion($response->data["Version"], $response->data["Mastery"][$i]->championId);
		$response->data["Champions"][$i] = $result->data["Key"];
    }    
    
    $response->valid = true;
    echo json_encode($response);
       
    function GetVersion(): Response {
		$result = DataDragon("https://ddragon.leagueoflegends.com/api/versions.json");
            
        if (!$result->valid) {
            return $result;
        }
            
		$response = new Response();
        $response->data["Version"] = $result->data["Response"][0];
        $response->valid = true;

		return $response;
    }
    
    function GetChampion($version, $id): Response {
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
			$result = DataDragon("https://ddragon.leagueoflegends.com/cdn/" . $version . "/data/en_US/champion.json");
            
            if (!$result->valid) {
                return $result;
            }
			
			try {
				foreach($result->data["Response"]->data as $name => $data) {
					if (intval($data->key) === $id) {
						$response->data["Key"] = $data->id;
					}
				}
			}
			catch (Exception $ex) {
				$response->data["Key"] = "";
			}

			if (!isset($response->data["Key"])) {
				Log::Error("Cound not find champion in GetSummonerData.php", $id);
				$response->data["Error"] = "Error handling request";
                $response->valid = false;
                return $response;
			}

            $response->valid = true;
            
            try {
                $stmt = Database::Get()->prepare("INSERT INTO Champions (id, ChampKey) VALUES (?,?)");
                $stmt->execute(array($id, $response->data["Key"]));
            } catch (PDOException $ex) {
                Log::Error("Database error in GetSummonerData.php", $ex->getMessage());
                $response->data["Error"] = "Error handling request";
                $response->valid = false;
                return $response;
            }
            return $response;
        }
    }
    
    function SortMastery($a, $b): int {
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