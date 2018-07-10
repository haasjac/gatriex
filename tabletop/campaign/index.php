<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('tabletop/campaign/campaign.twig');
    $twig_options = getTwigOptions();

	$redirect->requireUser($twig_options["Username"]);

	if (!isset($_REQUEST["id"])) {
		$redirect->redirect('/tabletop/mycampaigns');
	}

	try {
		$sql = "SELECT Guid, CampaignName FROM Tabletop_Campaigns WHERE Guid = ? AND Username = ?";
		$stmt = $db->prepare($sql);
		$stmt->execute(array($_REQUEST["id"], $twig_options["Username"]));
		
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			$twig_options["CampaignGuid"] = $_REQUEST["id"];
			$twig_options["CampaignName"] = $row["CampaignName"];
		}
	} catch (PDOException $ex) {
        $log->error("Database error in tabletop/campaign.php", $ex->getMessage());
	}

    echo $template->render($twig_options);
?>