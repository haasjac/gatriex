<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('test/test.twig');
    $twig_options = getTwigOptions();
        
    //echo $template->render($twig_options);
    
    // The message
    $message = "Line 1\r\nLine 2\r\nLine 3";
    
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message = wordwrap($message, 70, "\r\n");
    
    // Send
    $result = mail('haasjac@umich.edu', 'My Subject', $message);
    
    var_dump($result);
?>