<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("PUT");

	$expected = array(
		"CampaignGuid"		=> NULL,
		"CurrentCharacter"	=> NULL
	);

	$optional = array(
		"CurrentCharacter"
	);

	$input = Input::GetDataFromBody($expected, $optional);
	$CampaignGuid = $input["CampaignGuid"];
	$CurrentCharacter = $input["CurrentCharacter"];
    
    $user = Authentication::GetCurrentUserOrDie();
        
    try {
        Database::Get()->beginTransaction();

        $sql = "UPDATE Tabletop_InitiativeTracker I JOIN Tabletop_Campaigns C ON I.CampaignGuid = C.Guid SET I.CurrentCharacter = ? WHERE C.Username = ? AND I.CampaignGuid = ?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($CurrentCharacter, $user, $CampaignGuid));
        
        Database::Get()->commit();
        $response = new Response();
        $response->valid = true;
		$response->data["cc"] = $CurrentCharacter;
		$response->data["i"] = $input;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in SaveCurrentCharacter.php", $ex->getMessage());
        echo "Error handling request.";
        Database::Get()->rollBack();
    }
?>