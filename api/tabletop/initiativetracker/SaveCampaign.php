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

	$user = $result->data["Username"];
	    
    $CampaignGuid = $data["CampaignGuid"];
	$CharacterInfo = json_encode($data["CharacterInfo"]);
	$CurrentCharacter = $data["CurrentCharacter"];

	try {
		$sql = "SELECT COUNT(*) FROM Tabletop_Campaigns WHERE Username = ? AND Guid=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($user, $CampaignGuid));

        $row = $stmt->fetchColumn();

		if ($row <= 0) {
			$response = new Response();
			$response->data["Error"] = "Permission denied.";
			$response->valid = false;
			echo json_encode($response);
			return;
		}
        
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in AddCampaign.php", $ex->getMessage());
        echo "Error handling request.";
		return;
    }
    
    try {
        $db->beginTransaction();

		$sql = "UPDATE Tabletop_InitiativeTracker SET CharacterInfo=? WHERE CampaignGuid=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($CharacterInfo, $CampaignGuid));
        if ($stmt->rowCount() <= 0) {
            $stmt = $db->prepare("INSERT INTO Tabletop_InitiativeTracker (CampaignGuid, CharacterInfo) VALUES (?,?)");
            $stmt->execute(array($CampaignGuid, $CharacterInfo));
        }
        
        $db->commit();
        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in SaveCampaign.php", $ex->getMessage());
        echo "Error handling request.";
        $db->rollBack();
    }
?>