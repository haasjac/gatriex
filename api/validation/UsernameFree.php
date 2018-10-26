<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("GET");

	$expected = array(
		"username" =>	NULL
	);

	$input = Input::GetDataFromURL($expected);
	$username = $input["username"];
    
    $result = Validation::ValidateUsername($username);
    
    echo $result->valid ? "true" : "false";
?>

