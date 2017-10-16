<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $email = $input->getGet("createEmail");
    
    $result = $validation->validateEmail($email);
    
    echo $result->valid ? "true" : "false";
?>