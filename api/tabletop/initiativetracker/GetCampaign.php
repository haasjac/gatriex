<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $Guid = $_REQUEST["CampaignGuid"];
    
    if (!isset($Guid)) {
        $response = new Response();
        $response->data["Error"] = "Campaign not found.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }
        
    try {
		$Campaign = new stdClass();
		$Characters = new stdClass();

		$sql = "SELECT CharacterInfo, CurrentCharacter FROM Tabletop_InitiativeTracker WHERE CampaignGuid = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($Guid));

		if ($stmt->rowCount() <= 0) {
			$response = new Response();
			$response->data["Error"] = "Campaign not found.";
			$response->valid = false;
			echo json_encode($response);
			return;
		}

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
			
		$Campaign->CharacterInfo = json_decode($row["CharacterInfo"]);
		$Campaign->CurrentCharacter = $row["CurrentCharacter"];

		$sql = "SELECT Guid, Name, Faction, InitiativeBonus, InitiativeAdvantage FROM Tabletop_Characters WHERE CampaignGuid = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($Guid));

		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$rowGuid = $row["Guid"];
			$Characters->$rowGuid = $row;
        }
				        
        $response = new Response();
		$response->data["Campaign"] = $Campaign;
		$response->data["Characters"] = $Characters;
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in GetCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>