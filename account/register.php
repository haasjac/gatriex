<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('account/register.twig');
    $twig_options = getTwigOptions();
    
    /*if ($twig_options["Username"] !== "") {
        if (headers_sent() === false)
        {
            header('Location: ' . "/");
        }
        exit();
    }*/
    
    $redirect->requireNoUser($twig_options["Username"]);
    
    echo $template->render($twig_options);
?>