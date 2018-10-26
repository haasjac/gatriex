<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("PUT");

	$expected = array(
		"CampaignGuid"		=>  NULL,
		"CharacterInfo"		=>	NULL,
		"CurrentCharacter"	=>	NULL
	);

	$optional = array(
		"CharacterInfo",
		"CurrentCharacter"
	);
	
	$input = Input::GetDataFromBody($expected, $optional);
	$CampaignGuid = $input["CampaignGuid"];
	$CharacterInfo = json_encode($input["CharacterInfo"]);
	$CurrentCharacter = $input["CurrentCharacter"];
	    
    $result = Authentication::ValidateUserFromToken();
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }

    $user = $result->data["Username"];
            
    try {
        Database::Get()->beginTransaction();

        $sql = "UPDATE Tabletop_InitiativeTracker I JOIN Tabletop_Campaigns C ON I.CampaignGuid = C.Guid SET I.CharacterInfo = ?, I.CurrentCharacter = ? WHERE C.Username = ? AND I.CampaignGuid = ?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($CharacterInfo, $CurrentCharacter, $user, $CampaignGuid));
        
        Database::Get()->commit();
        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in SaveCampaign.php", $ex->getMessage());
        echo "Error handling request.";
        Database::Get()->rollBack();
    }
?>