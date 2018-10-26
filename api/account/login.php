<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");

	$expected = array(
		"username" =>	NULL,
		"password" =>	NULL,
		"remember" =>	NULL
	);

	$input = Input::GetDataFromBody($expected);
	$username = $input["username"];
	$password = $input["password"];
	$remember = $input["remember"];
    
    $result = Authentication::Login($username, $password, $remember);
    
    echo json_encode($result);
?>