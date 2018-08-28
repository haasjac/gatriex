<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $username = Input::GetPost("username");
    $value = Input::GetPost("value");
    $field = Input::GetPost("field");
    
    if ($username === "RiotTest") {
        $result = new Response();
        $result->valid = false;
        $result->data["Error"] = "Sample account. Profile cannot be changed.";
        echo json_encode($result); 
        return;
    }
    
    $result = Authentication::ValidateUserFromToken();
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }
    
    if ($field === "Password") {
        $result = Validation::ValidatePassword($value);
    
        if (!$result->valid) {
            echo json_encode($result);
            return;
        }

        $confirmPassword = Input::GetPost("confirmValue");
        
        $result = Validation::ConfirmPassword($value, $confirmPassword);

        if (!$result->valid) {
            echo json_encode($result);
            return;
        }
    } else if ($field === "Email") {
        $result = Validation::ValidateEmail($value);
    
        if (!$result->valid) {
            echo json_encode($result);
            return;
        }
    }
    
    $result = Authentication::UpdateField($username, $value, $field);
    
    echo json_encode($result);
?>