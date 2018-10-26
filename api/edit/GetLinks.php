<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');

	Input::CheckMethod("GET");

    $response = new Response();
    $response->data["Links"] = array();
    
    $headers = array();
    
    $user = Authentication::GetCurrentUser();
    if ($user === "") {
        $user = "admin";
    }
    
    try {
    
        $result = Database::Get()->prepare("SELECT text, link, header FROM Links WHERE Username = ?");
        $result->execute(array($user));
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if (!isset($headers[$row["header"]])) {
                $headers[$row["header"]] = array();
            }
            $item = array();
            $item["text"] = $row["text"]; 
            $item["link"] = $row["link"];
            array_push($headers[$row["header"]], $item);
        }
        
        foreach ($headers as $key => $value) {
            $category = array();
            $category["header"] = $key;
            $category["items"] = $value;
            array_push($response->data["Links"], $category);
        }
        
        $response->valid = true;
        
        echo json_encode($response);
    
    } catch(PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in GetLinks.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>
