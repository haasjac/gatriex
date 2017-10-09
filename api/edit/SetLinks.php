<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    $data = json_decode(file_get_contents("php://input"))->data;
    $userPassword = json_decode(file_get_contents("php://input"))->password;
    
    if (!isset($data)) {
        http_response_code(500);
        echo ("No data");
        return;
    }
    if ($userPassword != $editPassword) {
        http_response_code(401);
        echo ("Bad Password");
        return;
    }
    
    // Create connection
    $conn = $db;
    
    try {
        $conn->beginTransaction();
        
        $sql = "DELETE FROM `Links`";
        $result = $conn->exec($sql);
        if (!$result) {
            $error = 'Error in delete.';
            throw new Exception($error);
        }
        
        $sql = "INSERT INTO Links (Text, Link, Header) VALUES ";
        
        $values = "";
        $value_data = array();
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($data[$i]->items); $j++) {
                array_push($value_data, $data[$i]->items[$j]->text, $data[$i]->items[$j]->link, $data[$i]->header);
                $values .= "(?, ?, ?)";
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
        
        $stmt = $conn->prepare($sql);
        
        $result = $stmt->execute($value_data);
        if (!$result) {
            $error = 'Error in insert.';
            throw new Exception($error);
        }
        
        $conn->commit();
    } catch (PDOException $ex) {
        http_response_code(500);
        echo $ex;
        $conn->rollBack();
    }
?>