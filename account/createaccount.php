<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    $email = $_REQUEST["email"];
    $summoner = $_REQUEST["summoner"];
    
    $result = createUser($username, $password, $email, $summoner);
    
    echo json_encode($result);
?>