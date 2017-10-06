<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/credentials/riot.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    $response = "";
    
    // Create connection
    $conn = $db;
    
    $sql = "SELECT * FROM Version WHERE Time >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
    $stmt = $conn->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (sizeof($result) > 0) {
        //$row = $result->fetch(PDO::FETCH_ASSOC);
		$response = '["' . $result[0]["Version"] . '"]';
    }
    else {
        $url = "https://na1.api.riotgames.com/lol/static-data/v3/versions?api_key=" . $api_token;
        $curl = curl_init();
        curl_setopt_array($curl, array(
        	CURLOPT_URL => $url,
        	CURLOPT_SSL_VERIFYPEER => FALSE,
        	CURLOPT_RETURNTRANSFER => TRUE,
        	CURLOPT_HTTPHEADER => array('X-Riot-Token: ' . $api_token)
        ));
        
        $response = curl_exec($curl);
        $key = curl_error($curl);
        $keyx = curl_errno($curl);
        
        curl_close($curl);
        
        $json_response = json_decode($response, true);
        
        if (array_key_exists('status', $json_response)) {
            echo $response;
            return;
        }
        
        $version = $json_response[0];
        
        try {
            $sql = "DELETE FROM Version WHERE Time < DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
            $result = $conn->query($sql);
            $stmt = $conn->prepare("INSERT INTO Version (Version) VALUES (?)");
            $stmt->execute(array($version));
        } catch (PDOException $ex) {
            echo $e;
        }
    }
    
    echo $response;
?>