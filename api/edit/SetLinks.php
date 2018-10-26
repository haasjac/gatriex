<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("PUT");

	$expected = array(
		"data" => NULL
	);

	$input = Input::GetDataFromBody($expected);
	$data = $input["data"];
    
    $result = Authentication::ValidateUserFromToken();
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $user = $result->data["Username"];
    
    try {
        Database::Get()->beginTransaction();
        
        $result = Database::Get()->prepare("DELETE FROM `Links` WHERE Username = ?");
        $result->execute(array($user));
        
        $sql = "INSERT INTO Links (Text, Link, Header, Username) VALUES ";
        
        $values = "";
        $valueData = array();
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($data[$i]["items"]); $j++) {
                array_push($valueData, $data[$i]["items"][$j]["text"], $data[$i]["items"][$j]["link"], $data[$i]["header"], $user);
                $values .= "(?, ?, ?, ?)";
                if ($j != count($data[$i]["items"]) - 1) {
                    $values .= ", ";
                }
            }
            if ($i != count($data) - 1) {
                $values .= ", ";
            } else {
                $values .= ";";
            }
        }
        
        $sql .= $values;
                
        $stmt = Database::Get()->prepare($sql);
        
        $stmt->execute($valueData);
        
        Database::Get()->commit();
        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in SetLinks.php", $ex->getMessage());
        echo "Error handling request.";
        Database::Get()->rollBack();
    }
?>