<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = Input::GetPost("username");
    $password = Input::GetPost("password");
    $confirmPassword = Input::GetPost("confirmPassword");
    $email = Input::GetPost("email");
    $confirmEmail = Input::GetPost("confirmEmail");
    $summoner = Input::GetPost("summoner");
    $region = Input::GetPost("region");
    
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