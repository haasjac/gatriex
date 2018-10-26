<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');

	Input::CheckMethod("POST");
    
	$expected = array(
		"name"		=>	FILTER_SANITIZE_STRING,
		"email"		=>	FILTER_VALIDATE_EMAIL,
		"subject"	=>	FILTER_SANITIZE_STRING,
		"message"	=>	FILTER_UNSAFE_RAW
	);

	$input = Input::GetDataFromBody($expected);

    $fromName = $input["name"];
    $fromMail = $input["email"];
    $mailSubject = $input["subject"];
    $mailMessage = $input["message"];
    
    $result = Mail::SendContactEmail($mailSubject, $mailMessage, $fromName, $fromMail);
    
    echo json_encode($result);
?>

