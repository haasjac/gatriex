<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('about/about.twig');
    $twigOptions = Twig::GetTwigOptions();
        
    echo $template->render($twigOptions);
?>
