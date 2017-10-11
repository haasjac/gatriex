<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/libraries.php');
    
    $email = $input->getPost("email");
    
    $result = $mail->sendForgetUsernameEmail($email);
    
    echo json_encode($result);
?>