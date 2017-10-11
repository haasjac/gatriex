<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/mail.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/validation.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    include_once($_SERVER['DOCUMENT_ROOT'] . '/test.php');
    
    //var_dump($authentication);
    
    //$t = $authentication->validateUserFromToken("", "");
    
    $t = $input->getCookie("Auth_Id");
    
    var_dump($t);
    
    echo "<br>";
    
    $t = $input->getCookie("PHPSESSID");
    
    var_dump($t);
?>