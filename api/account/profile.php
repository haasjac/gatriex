<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("POST");

	$expected = array(
		"username"		=> NULL,
		"value"			=> NULL,
		"field"			=> NULL,
		"confirmValue"	=> NULL
	);

	$optional = array(
		"confirmValue"
	);

	$input = Input::GetDataFromBody($expected, $optional);
    $username = $input["username"];
    $value = $input["value"];
    $field = $input["field"];
	$confirmValue = $input["confirmValue"];
    
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
		        
        $result = Validation::ConfirmPassword($value, $confirmValue);

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