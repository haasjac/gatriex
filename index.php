<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    
    $template = $twig->load('home/home.html');
    $twig_options = getTwigOptions();
    
    //var_dump($twig_options);
    
    echo $template->render($twig_options);
?>