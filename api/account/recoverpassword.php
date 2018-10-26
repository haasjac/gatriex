<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");

	$expected = array(
		"username" =>	NULL
	);

	$input = Input::GetDataFromBody($expected);
    $username = $input["username"];
	    
    $result = Authentication::GenerateForgetToken($username);
    
    if ($result->valid) { 
        $result = Mail::SendForgetPasswordEmail($username, $result->data["Token"]);
    }
    
    echo json_encode($result);
?>