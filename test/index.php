<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('test/test.twig');
    $twig_options = getTwigOptions();
        
    //echo $template->render($twig_options);
    
    // The message
    $message = "Why am I able to do this? wtf.";
    
    // Send
    //$result = sendEmail('cischken@umich.edu', 'Jake sent this', $message, "Cooper Riehl", "cariehl@umich.edu");
    
    var_dump($result);
    
    function sendEmail($mail_to, $mail_subject, $mail_message, $from_name, $from_mail) {
        //$from_name = "Gatriex";
        //$from_mail = "DoNotReply@Gatriex.com";

        $encoding = "utf-8";

        // Mail header
        $header = "Content-type: text/html; charset=".$encoding." \r\n";
        $header .= "From: ".$from_name." <".$from_mail."> \r\n";
        $header .= "MIME-Version: 1.0 \r\n";
        $header .= "Content-Transfer-Encoding: 8bit \r\n";
        $header .= "Date: ".date("r (T)")." \r\n";

        // Send mail
        return mail($mail_to, $mail_subject, $mail_message, $header);
    }
?>