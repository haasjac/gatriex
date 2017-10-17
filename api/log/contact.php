<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $from_name = $input->getPost("name");
    $from_mail = $input->getPost("email");
    $mail_subject = $input->getPost("subject");
    $mail_message = $input->getPost("message");
    
    $result = $mail->sendContactEmail($mail_subject, $mail_message, $from_name, $from_mail);
    
    echo json_encode($result);
?>

