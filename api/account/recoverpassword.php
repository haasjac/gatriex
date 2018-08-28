<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = Input::GetPost("username");
    
    $result = Authentication::GenerateForgetToken($username);
    
    if ($result->valid) { 
        $result = Mail::SendForgetPasswordEmail($username, $result->data["Token"]);
    }
    
    echo json_encode($result);
?>