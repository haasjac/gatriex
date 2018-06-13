<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('dnd/dnd.twig');
    $twig_options = getTwigOptions();
    	
	if (isset($_REQUEST["CampaignName"])) {
		$twig_options["CampaignName"] = $_REQUEST["CampaignName"];
	}
	else {
		$twig_options["CampaignName"] = "";
	}
    
    echo $template->render($twig_options);
?>