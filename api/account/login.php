<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/libraries.php');
    
    $username = $input->getPost("username");
    $password = $input->getPost("password");
    $remember = $input->getPost("remember");
    
    $result = $authentication->login($username, $password, $remember);
    
    echo json_encode($result);
?>