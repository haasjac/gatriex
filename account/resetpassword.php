<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    
    $template = $twig->load('account/resetpassword.html');
    $twig_options = getTwigOptions();
    
    $token = "";
    
    if (isset($_REQUEST["token"])) {
        $token = $_REQUEST["token"];
    }
    
    $twig_options["token"] = $token;
    
    echo $template->render($twig_options);
?>