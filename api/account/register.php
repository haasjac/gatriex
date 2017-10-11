<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/libraries.php');
    
    $username = $input->getPost("username");
    $password = $input->getPost("password");
    $confirmPassword = $input->getPost("confirmPassword");
    $email = $input->getPost("email");
    $confirmEmail = $input->getPost("confirmEmail");
    $summoner = $input->getPost("summoner");
    
    $result = $validation->validateUsername($username);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = $validation->validatePassword($password);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = $validation->confirmPassword($password, $confirmPassword);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = $validation->validateEmail($email);
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = $validation->confirmPassword($email, $confirmEmail, "Emails");
    
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    $result = $authentication->createUser($username, $password, $email, $summoner);
    
    echo json_encode($result);
?>