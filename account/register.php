<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('account/register.twig');
    $twigOptions = Twig::GetTwigOptions();
    
    Redirect::RequireNoUser($twigOptions["Username"]);
    
    $twigOptions["RegionList"] = Riot::GetRegions();
    
    echo $template->render($twigOptions);
?>