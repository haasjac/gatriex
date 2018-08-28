<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $email = Input::GetPost("email");
    
    $result = Validation::ValidateEmail($email);
    
    echo $result->valid ? "true" : "false";
?>