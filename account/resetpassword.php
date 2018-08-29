<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/input.php');
    
    $template = $twig->load('account/resetpassword.twig');
    $twigOptions = GetTwigOptions();
        
    $token = Input::GetGet("token");
    
    $twigOptions["token"] = $token;
    
    echo $template->render($twigOptions);
?>