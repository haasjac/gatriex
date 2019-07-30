<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('tabletop/initiativetracker/initiativetracker.twig');
    $twigOptions = Twig::GetTwigOptions();
	
	if (isset($_REQUEST["id"])) {
		try {
			$sql = "SELECT CampaignName, Username FROM Tabletop_Campaigns WHERE Guid = ?";
			$stmt = Database::Get()->prepare($sql);
			$stmt->execute(array($_REQUEST["id"]));

			$row = $stmt->fetch(PDO::FETCH_ASSOC);
            
			$twigOptions["CampaignGuid"] = $_REQUEST["id"];
			$twigOptions["CampaignName"] = $row["CampaignName"];

			if (!$twigOptions["Username"] || $twigOptions["Username"] != $row["Username"]) {
				$twigOptions["Readonly"] = true;
			}

		} catch (PDOException $ex) {
			Log::Error("Database error in index.php (initiativetracker)", $ex->getMessage());
		}
	} 
	else {
		Redirect::RequireUser($twigOptions["Username"]);
	}
      
	if ($twigOptions["Username"]) {
		if (isset($_REQUEST["mode"]) && strcasecmp($_REQUEST["mode"], "Readonly") === 0) {
			$twigOptions["Readonly"] = true;
		}
	}
	else {
		$twigOptions["Readonly"] = true;
	}
    
    echo $template->render($twigOptions);
?>