<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('tabletop/mycampaigns/mycampaigns.twig');
    $twigOptions = GetTwigOptions();

    Redirect::RequireUser($twigOptions["Username"]);
    
    echo $template->render($twigOptions);
?>