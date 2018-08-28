<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = Input::GetPost("username");
    $password = Input::GetPost("password");
    $confirmPassword = Input::GetPost("confirmPassword");
    $token = Input::GetPost("token");
    
    $result = Validation::ConfirmPassword($password, $confirmPassword);
    
    if ($result->valid) {
        $result = Authentication::ResetPasswordFromForgetToken($username, $password, $token);
    }
    
    echo json_encode($result);
?>