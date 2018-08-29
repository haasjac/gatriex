<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $fromName = Input::GetPost("name");
    $fromMail = Input::GetPost("email");
    $mailSubject = Input::GetPost("subject");
    $mailMessage = Input::GetPost("message");
    
    $result = Mail::SendContactEmail($mailSubject, $mailMessage, $fromName, $fromMail);
    
    echo json_encode($result);
?>

