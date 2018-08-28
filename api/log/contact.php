<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $from_name = Input::GetPost("name");
    $from_mail = Input::GetPost("email");
    $mail_subject = Input::GetPost("subject");
    $mail_message = Input::GetPost("message");
    
    $result = Mail::SendContactEmail($mail_subject, $mail_message, $from_name, $from_mail);
    
    echo json_encode($result);
?>

