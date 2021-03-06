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

	$input = Input::GetDataFromURL($expected); 

	$file = Input::GetFile('file');

	if (isset($file))
	{
		$fileName = $file['name'];
	}
	else {
		$fileName = NULL;
	}
    
    $User = Authentication::GetCurrentUserOrDie();
    $Guid = Authentication::GenerateGuid();
    
    try {    
		Database::Get()->beginTransaction();

        $stmt = Database::Get()->prepare("INSERT INTO Tabletop_Characters (Guid, Name, Username, CampaignGuid, Faction, InitiativeBonus, InitiativeAdvantage, Portrait) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute(array($Guid, $input["Name"], $User, $input["CampaignGuid"], $input["Faction"], $input["InitiativeBonus"], $input["InitiativeAdvantage"], $fileName));

        $stmt = Database::Get()->prepare("SELECT C.Guid, C.Name, F.Id As FactionId, F.Name As FactionName, F.Icon As FactionIcon, C.InitiativeBonus, C.InitiativeAdvantage FROM Tabletop_Characters C Join Tabletop_Factions F ON C.Faction = F.Id WHERE C.Guid = ?");
        $stmt->execute(array($Guid));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($fileName)) {

			if (Input::UploadFile("tabletop/characters/" . $row["Guid"], $file) === false) {
				$response = new Response();
				$response->data["Error"] = "Failed to upload Portrait";
				$response->valid = false;
				echo json_encode($response);
				Database::Get()->rollBack();
				return;
			}
		}

		Database::Get()->commit();

        $response = new Response();
        $response->data["Character"] = $row;
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/AddCharacter.php", $ex->getMessage());
        echo "Error handling request.";
		Database::Get()->rollBack();
    }
?>