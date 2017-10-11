<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = $input->getPost("username");
    $password = $input->getPost("password");
    $remember = $input->getPost("remember");
    
    $result = $authentication->login($username, $password, $remember);
    
    echo json_encode($result);
?>