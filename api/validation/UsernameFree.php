<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = Input::GetPost("username");
    
    $result = Validation::ValidateUsername($username);
    
    echo $result->valid ? "true" : "false";
?>

