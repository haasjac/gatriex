<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    
    $template = Twig::GetTwig()->load('account/recover.twig');
    $twigOptions = Twig::GetTwigOptions();
    
    echo $template->render($twigOptions);
?>