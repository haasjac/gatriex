<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    http_response_code(200);
    try {
        $data = $_REQUEST["data"];
    
        $result = $log->error($data["message"], $data["error"]);
        
        if (!isset($data["message"]) || !isset($data["error"]) || !$result) {
            throw new Exception("error failed to log.");
        }
    } catch (Exception $ex) {
        http_response_code(500);
        $log->error("Error writing to log", "Data: " . var_export($data, true) . "\r\n" . "Exception" . $ex);
    }
?>

