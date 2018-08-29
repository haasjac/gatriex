<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    
    $template = $twig->load('account/recover.twig');
    $twigOptions = GetTwigOptions();
    
    echo $template->render($twigOptions);
?>