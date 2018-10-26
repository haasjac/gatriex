<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");

	$expected = array(
		"email" =>	FILTER_VALIDATE_EMAIL
	);

	$input = Input::GetDataFromBody($expected);
    $email = $input["email"];
    
    $result = Mail::SendForgetUsernameEmail($email);
    
    echo json_encode($result);
?>