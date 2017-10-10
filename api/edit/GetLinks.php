<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $response = array();
    $headers = array();
    
    $user = getCurrentUser();
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
            array_push($response, $category);
        }
        
        $out = array_values($response);
        echo json_encode($out, true);
    
    } catch(PDOException $ex) {
        echo $ex->getMessage();
    }
?>
