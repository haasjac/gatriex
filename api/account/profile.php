<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = $input->getPost("username");
    $value = $input->getPost("value");
    $field = $input->getPost("field");
    
    if ($username === "RiotTest") {
        $result = new Response();
        $result->valid = false;
        $result->data["Error"] = "Sample account. Profile cannot be changed.";
        echo json_encode($result); 
        return;
    }
    
    $result = $authentication->validateUserFromToken($input->getCookie("Auth_Id"), $input->getCookie("Auth_Token"));
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    if ($field === "Password") {
        $result = $validation->validatePassword($value);
    
        if (!$result->valid) {
            echo json_encode($result);
            return;
        }

        $confirmPassword = $input->getPost("confirmValue");
        
        $result = $validation->confirmPassword($value, $confirmPassword);

        if (!$result->valid) {
            echo json_encode($result);
            return;
        }
    } else if ($field === "Email") {
        $result = $validation->validateEmail($value);
    
        if (!$result->valid) {
            echo json_encode($result);
            return;
        }
    }
    
    $result = $authentication->updateField($username, $value, $field);
    
    echo json_encode($result);
?>