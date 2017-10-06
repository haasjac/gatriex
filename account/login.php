<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    
    $username = $_REQUEST["username"];
    $password = $_REQUEST["password"];
    
    $result = login($username, $password, true);
    
    echo json_encode($result);
?>