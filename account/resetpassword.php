<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    
    $template = Twig::GetTwig()->load('account/resetpassword.twig');
    $twigOptions = Twig::GetTwigOptions();
        
    $token = Input::GetGet("token");
    
    $twigOptions["token"] = $token;
    
    echo $template->render($twigOptions);
?>