<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/libraries.php');
    
    $username = $input->getPost("username");
    $password = $input->getPost("password");
    $confirmPassword = $input->getPost("confirmPassword");
    $token = $input->getPost("token");
    
    $result = $validation->confirmPassword($password, $confirmPassword);
    
    if ($result->valid) {
        $result = $authentication->resetPasswordFromForgetToken($username, $password, $token);
    }
    
    echo json_encode($result);
?>