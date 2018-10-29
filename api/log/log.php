<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");
    
	$expected = array(
		"message"	=>	NULL,
		"error"		=>	NULL,
	);

	$input = Input::GetDataFromBody($expected);

    http_response_code(200);
    try {
    
        $result = Log::Error($input["message"], $input["error"]);
        
        if (!isset($input["message"]) || !isset($input["error"]) || !$result) {
            throw new Exception("error failed to log.");
        }
    } catch (Exception $ex) {
        http_response_code(500);
        Log::Error("Error writing to log", "Data: " . var_export(Input::GetDataFromBody(), true) . "\r\n" . "Exception" . $ex);
    }
?>

