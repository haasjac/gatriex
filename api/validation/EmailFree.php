<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("GET");

	$expected = array(
		"email" =>	NULL
	);

	$input = Input::GetDataFromURL($expected);
	$email = $input["email"];
    
    $result = Validation::ValidateEmail($email);
    
    echo $result->valid ? "true" : "false";
?>