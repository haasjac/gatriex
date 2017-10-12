<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    $response = new Response();
    $response->data["Links"] = array();
    
    $headers = array();
    
    $user = $authentication->getCurrentUser();
    if ($user === "") {
        $user = "admin";
    }
    
    try {
    
        $result = $db->prepare("SELECT text, link, header FROM Links WHERE Username = ?");
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
        $response->data["Error"] = $ex->getMessage();
        $response->valid = false;
        echo json_encode($response);
    }
?>
