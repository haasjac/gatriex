<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
     
	Input::CheckMethod("POST");

	$expected = array(
		"CampaignGuid"			=> NULL,
		"Name"					=> NULL,
		"Faction"				=> NULL,
		"InitiativeBonus"		=> NULL,
		"InitiativeAdvantage"	=> NULL
	);

	$input = Input::GetDataFromBody($expected); 
    
    $result = Authentication::ValidateUserFromToken();
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }

    $User = $result->data["Username"];
    $Guid = Authentication::GenerateGuid();
    
    try {    
        $stmt = Database::Get()->prepare("INSERT INTO Tabletop_Characters (Guid, Name, Username, CampaignGuid, Faction, InitiativeBonus, InitiativeAdvantage) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute(array($Guid, $input["Name"], $User, $input["CampaignGuid"], $input["Faction"], $input["InitiativeBonus"], $input["InitiativeAdvantage"]));

        $stmt = Database::Get()->prepare("SELECT C.Guid, C.Name, F.Id As FactionId, F.Name As FactionName, F.Icon As FactionIcon, C.InitiativeBonus, C.InitiativeAdvantage FROM Tabletop_Characters C Join Tabletop_Factions F ON C.Faction = F.Id WHERE C.Guid = ?");
        $stmt->execute(array($Guid));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = new Response();
        $response->data["Character"] = $row;
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/AddCharacter.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>