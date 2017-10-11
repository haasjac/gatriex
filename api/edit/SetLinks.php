<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    $data = $_REQUEST["data"];
    $userPassword = $_REQUEST["password"];
    
    if (!isset($data)) {
        http_response_code(500);
        echo ("No data");
        return;
    }
    /*if ($userPassword != $editPassword) {
        http_response_code(401);
        echo ("Bad Password");
        return;
    }*/
    
    if (isset($_COOKIE["Auth_Id"]) && isset($_COOKIE["Auth_Token"])) {
        $result = validateUserFromToken($_COOKIE["Auth_Id"], $_COOKIE["Auth_Token"]);
        if (!$result->valid) {
            http_response_code(401);
            echo json_encode($result);
            return;
        }
    }
    
    $user = $result->data["Username"];
    
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
    
    try {
        $conn->beginTransaction();
        
        $result = $db->prepare("DELETE FROM `Links` WHERE Username = ?");
        $result->execute(array($user));
        
        $sql = "INSERT INTO Links (Text, Link, Header, Username) VALUES ";
        
        $values = "";
        $value_data = array();
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($data[$i]->items); $j++) {
                array_push($value_data, $data[$i]->items[$j]->text, $data[$i]->items[$j]->link, $data[$i]->header, $user);
                $values .= "(?, ?, ?, ?)";
                if ($j != count($data[$i]->items) - 1) {
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
                
        $stmt = $db->prepare($sql);
        
        $stmt->execute($value_data);
        
        $conn->commit();
    } catch (PDOException $ex) {
        http_response_code(500);
        echo $ex;
        $conn->rollBack();
    }
?>