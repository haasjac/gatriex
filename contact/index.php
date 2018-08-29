<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('contact/contact.twig');
    $twigOptions = GetTwigOptions();
        
    echo $template->render($twigOptions);
?>
