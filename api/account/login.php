<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = Input::GetPost("username");
    $password = Input::GetPost("password");
    $remember = Input::GetPost("remember");
    
    $result = Authentication::Login($username, $password, $remember);
    
    echo json_encode($result);
?>