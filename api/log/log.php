<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    http_response_code(200);
    try {
        $data = $_REQUEST["data"];
    
        $result = Log::Error($data["message"], $data["error"]);
        
        if (!isset($data["message"]) || !isset($data["error"]) || !$result) {
            throw new Exception("error failed to log.");
        }
    } catch (Exception $ex) {
        http_response_code(500);
        Log::Error("Error writing to log", "Data: " . var_export($data, true) . "\r\n" . "Exception" . $ex);
    }
?>

