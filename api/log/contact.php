<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');

	Input::CheckMethod("POST");
    
	$expected = array(
		"name"		=>	FILTER_SANITIZE_STRING,
		"email"		=>	FILTER_VALIDATE_EMAIL,
		"subject"	=>	FILTER_UNSAFE_RAW,
		"message"	=>	FILTER_UNSAFE_RAW
	);

	$input = Input::GetDataFromBody($expected);

    $fromName = $input["name"];
    $fromMail = $input["email"];
    $mailSubject = strip_tags($input["subject"]);
    $mailMessage = strip_tags(nl2br($input["message"]), '<br>');
    
    $result = Mail::SendContactEmail($mailSubject, $mailMessage, $fromName, $fromMail);

	$result->data["message"] = $mailMessage;
    
    echo json_encode($result);
?>

