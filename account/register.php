<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('account/register.twig');
    $twig_options = getTwigOptions();
    
    $redirect->requireNoUser($twig_options["Username"]);
    
    $twig_options["RegionList"] = $riot->getRegions();
    
    echo $template->render($twig_options);
?>