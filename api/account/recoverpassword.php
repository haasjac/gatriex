<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = $input->getPost("username");
    
    $result = $authentication->generateForgetToken($username);
    
    if ($result->valid) { 
        $result = $mail->sendForgetPasswordEmail($username, $result->data["Token"]);
    }
    
    echo json_encode($result);
?>