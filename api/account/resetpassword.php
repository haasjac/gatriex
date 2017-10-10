<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/validation.php');
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $confirmPassword = $_REQUEST["confirmPassword"];
    $token = $_REQUEST["token"];
    
    $result = confirmPassword($password, $confirmPassword);
    
    if ($result->valid) {
        $result = resetPasswordFromForgetToken($username, $password, $token);
    }
    
    echo json_encode($result);
?>