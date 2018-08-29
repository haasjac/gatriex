<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('tabletop/mycampaigns/mycampaigns.twig');
    $twigOptions = Twig::GetTwigOptions();

    Redirect::RequireUser($twigOptions["Username"]);
    
    echo $template->render($twigOptions);
?>