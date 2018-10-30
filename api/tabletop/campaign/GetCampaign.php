<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
       
	Input::CheckMethod("GET");

	$expected = array(
		"CampaignGuid" =>	NULL
	);

	$input = Input::GetDataFromURL($expected);
	$Guid = $input["CampaignGuid"];
    
    $User = Authentication::GetCurrentUserOrDie();
    
    try {        
        $sql = "SELECT Id, Name, Icon FROM Tabletop_Factions";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute();

        $Factions = new stdClass();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $factionId = $row["Id"];
            $Factions->$factionId = $row;
        }

        $sql = "SELECT C.Guid, C.Name, F.Id As FactionId, F.Name As FactionName, F.Icon As FactionIcon, C.InitiativeBonus, C.InitiativeAdvantage FROM Tabletop_Characters C Join Tabletop_Factions F ON C.Faction = F.Id WHERE C.CampaignGuid = ?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($Guid));

        $Characters = new stdClass();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rowGuid = $row["Guid"];
            $Characters->$rowGuid = $row;
        }

        $response = new Response();
        $response->valid = true;
        $response->data["Characters"] = $Characters;
        $response->data["Factions"] = $Factions;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/GetCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>