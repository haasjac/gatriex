<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/validation.php');
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $confirmPassword = $_REQUEST["confirmPassword"];
    $email = $_REQUEST["email"];
    $confirmEmail = $_REQUEST["confirmEmail"];
    $summoner = $_REQUEST["summoner"];
    
    $result = validateUsername($username);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = validatePassword($password);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = confirmPassword($password, $confirmPassword);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = validateEmail($email);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = confirmPassword($email, $confirmEmail, "Emails");
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = createUser($username, $password, $email, $summoner);
    
    echo json_encode($result);
?>