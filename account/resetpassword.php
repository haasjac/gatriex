<?php
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/twig.php');
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/input.php');
    
    $template = $twig->load('account/resetpassword.twig');
    $twig_options = getTwigOptions();
        
    $token = $input->getGet("token");
    
    $twig_options["token"] = $token;
    
    echo $template->render($twig_options);
?>