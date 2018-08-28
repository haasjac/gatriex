<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $email = Input::GetPost("email");
    
    $result = Mail::SendForgetUsernameEmail($email);
    
    echo json_encode($result);
?>