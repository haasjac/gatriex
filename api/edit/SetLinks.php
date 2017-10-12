<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $data = $_REQUEST["data"];
    
    if (!isset($data)) {
        $response = new Response();
        $response->data["Error"] = "Error handling data.";
        $response->valid = false;
        echo json_encode($result);
        return;
    }
    
    $result = $authentication->validateUserFromToken($input->getCookie("Auth_Id"), $input->getCookie("Auth_Token"));
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $user = $result->data["Username"];
    
    try {
        $db->beginTransaction();
        
        $result = $db->prepare("DELETE FROM `Links` WHERE Username = ?");
        $result->execute(array($user));
        
        $sql = "INSERT INTO Links (Text, Link, Header, Username) VALUES ";
        
        $values = "";
        $value_data = array();
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($data[$i]["items"]); $j++) {
                array_push($value_data, $data[$i]["items"][$j]["text"], $data[$i]["items"][$j]["link"], $data[$i]["header"], $user);
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
                
        $stmt = $db->prepare($sql);
        
        $stmt->execute($value_data);
        
        $db->commit();
        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        $response->data["Error"] = $ex->getMessage();
        $response->valid = false;
        echo json_encode($response);
        $db->rollBack();
    }
?>