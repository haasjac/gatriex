<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");
    
	$expected = array(
		"message"		=>	FILTER_SANITIZE_STRING,
		"error"		=>	FILTER_VALIDATE_EMAIL,
		"subject"	=>	FILTER_SANITIZE_STRING,
		"message"	=>	FILTER_UNSAFE_RAW
	);

	$input = Input::GetDataFromBody($expected);

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

