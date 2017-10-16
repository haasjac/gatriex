<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = $input->getGet("createUsername");
    
    $result = $validation->validateUsername($username);
    
    echo $result->valid ? "true" : "false";
?>

