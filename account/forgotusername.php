<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/library/mail.php');
    
    $email = $_REQUEST["email"];
    
    $result = sendForgetUsernameEmail($email);
    
    echo json_encode($result);
?>