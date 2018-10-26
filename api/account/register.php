<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");

	$expected = array(
		"username" =>	NULL,
		"password" =>	NULL,
		"confirmPassword" =>	NULL,
		"email" =>	NULL,
		"confirmEmail" =>	NULL,
		"summoner" =>	NULL,
		"region" =>	NULL,
	);

	$input = Input::GetDataFromBody($expected);
    $username = $input["username"];
    $password = $input["password"];
    $confirmPassword = $input["confirmPassword"];
    $email = $input["email"];
    $confirmEmail = $input["confirmEmail"];
    $summoner = $input["summoner"];
    $region = $input["region"];
    
    $result = Validation::ValidateUsername($username);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = Validation::ValidatePassword($password);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = Validation::ConfirmPassword($password, $confirmPassword);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = Validation::ValidateEmail($email);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = Validation::ConfirmPassword($email, $confirmEmail, "Emails");
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = Authentication::CreateUser($username, $password, $email, $summoner, $region);
    
    echo json_encode($result);
?>