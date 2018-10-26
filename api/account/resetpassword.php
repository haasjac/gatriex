<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");

	$expected = array(
		"username" =>	NULL,
		"password" =>	NULL,
		"confirmPassword" =>	NULL,
		"token" =>	NULL
	);

	$input = Input::GetDataFromBody($expected);
    $username = $input["username"];
    $password = $input["password"];
    $confirmPassword = $input["confirmPassword"];
    $token = $input["token"];
    
    $result = Validation::ConfirmPassword($password, $confirmPassword);
    
    if ($result->valid) {
        $result = Authentication::ResetPasswordFromForgetToken($username, $password, $token);
    }
    
    echo json_encode($result);
?>