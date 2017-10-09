<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $token = $_REQUEST["token"];
    
    $result = resetPasswordFromForgetToken($username, $password, $token);
    
    echo json_encode($result);
?>