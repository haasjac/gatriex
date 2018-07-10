<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('tabletop/mycampaigns/mycampaigns.twig');
    $twig_options = getTwigOptions();

	$redirect->requireUser($twig_options["Username"]);
    
    echo $template->render($twig_options);
?>