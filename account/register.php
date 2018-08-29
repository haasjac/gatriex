<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('account/register.twig');
    $twigOptions = GetTwigOptions();
    
    Redirect::RequireNoUser($twigOptions["Username"]);
    
    $twigOptions["RegionList"] = Riot::GetRegions();
    
    echo $template->render($twigOptions);
?>