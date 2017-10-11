<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $email = $input->getPost("email");
    
    $result = $mail->sendForgetUsernameEmail($email);
    
    echo json_encode($result);
?>