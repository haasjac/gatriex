<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    
    $template = $twig->load('account/resetpassword.twig');
    $twig_options = getTwigOptions();
        
    $token = $input->getGet("token");
    
    $twig_options["token"] = $token;
    
    echo $template->render($twig_options);
?>