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
        $CharacterInfo = array();
        $CurrentCharacter = NULL;
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
            
        $CharacterInfo = json_decode($row["CharacterInfo"]);
        $CurrentCharacter = $row["CurrentCharacter"];

        $sql = "SELECT C.Guid, C.Name, F.Id As FactionId, F.Name As FactionName, F.Icon As FactionIcon, F.Precedence As FactionPrecedence, C.InitiativeBonus, C.InitiativeAdvantage FROM Tabletop_Characters C Join Tabletop_Factions F ON C.Faction = F.Id WHERE C.CampaignGuid = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($Guid));

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rowGuid = $row["Guid"];
            $Characters->$rowGuid = $row;
        }

        $sql = "SELECT Id, Name, Precedence FROM Tabletop_Factions";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($Guid));

        $Factions = new stdClass();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $factionId = $row["Id"];
            $Factions->$factionId = $row;
        }
                        
        $response = new Response();
        $response->data["CharacterInfo"] = $CharacterInfo;
        $response->data["CurrentCharacter"] = $CurrentCharacter;
        $response->data["Characters"] = $Characters;
        $response->data["Factions"] = $Factions;
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in GetCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>