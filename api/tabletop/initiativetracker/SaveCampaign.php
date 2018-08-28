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
    
    $result = Authentication::ValidateUserFromToken();
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }

    $user = $result->data["Username"];
        
    $CampaignGuid = $data["CampaignGuid"];
    if (isset($data["CharacterInfo"])) {
        $CharacterInfo = json_encode($data["CharacterInfo"]);
    } else {
        $CharacterInfo = NULL;
    }

    if (isset($data["CurrentCharacter"])) {
        $CurrentCharacter = $data["CurrentCharacter"];
    } else {
        $CurrentCharacter = NULL;
    }
    
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