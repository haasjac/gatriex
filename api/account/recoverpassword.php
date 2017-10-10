<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/mail.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $username = $_REQUEST["username"];
    
    $result = generateForgetToken($username);
    
    if ($result->valid) { 
        $result = sendForgetPasswordEmail($username, $result->data["Token"]);
    }
    
    echo json_encode($result);
?>