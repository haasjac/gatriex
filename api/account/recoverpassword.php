<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/libraries.php');
    
    $username = $input->getPost("username");
    
    $result = $authentication->generateForgetToken($username);
    
    if ($result->valid) { 
        $result = $mail->sendForgetPasswordEmail($username, $result->data["Token"]);
    }
    
    echo json_encode($result);
?>