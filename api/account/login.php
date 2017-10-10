<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $remember = $_REQUEST["remember"];
    
    $result = login($username, $password, $remember);
    
    echo json_encode($result);
?>