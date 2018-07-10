<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
       
	$data = $_REQUEST["data"];
    
    if (!isset($data)) {
        $response = new Response();
        $response->data["Error"] = "Error handling data.";
        $response->valid = false;
        echo json_encode($response);
        return;
    }

    $result = $authentication->validateUserFromToken($input->getCookie("Auth_Id"), $input->getCookie("Auth_Token"));
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }

	$User = $result->data["Username"];
	$Guid = $authentication->generate_guid();
    
	try {	
		$stmt = $db->prepare("INSERT INTO Tabletop_Characters (Guid, Name, Username, CampaignGuid, Faction, InitiativeBonus, InitiativeAdvantage) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute(array($Guid, $data["Name"], $User, $data["CampaignGuid"], $data["Faction"], $data["InitiativeBonus"], $data["InitiativeAdvantage"]));

		$stmt = $db->prepare("SELECT C.Guid, C.Name, F.Id As FactionId, F.Name As FactionName, F.Icon As FactionIcon, C.InitiativeBonus, C.InitiativeAdvantage FROM Tabletop_Characters C Join Tabletop_Factions F ON C.Faction = F.Id WHERE C.Guid = ?");
		$stmt->execute(array($Guid));

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $response = new Response();
		$response->data["Character"] = $row;
        $response->valid = true;
        echo json_encode($response);
	} catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in campaign/AddCharacter.php", $ex->getMessage());
        echo "Error handling request.";
	}
?>