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

    $User = $result->data["Username"];
    
    $CampaignName = $data["CampaignName"];

    $Guid = Authentication::GenerateGuid();

    try {
        $sql = "SELECT COUNT(*) FROM Tabletop_Campaigns WHERE Username = ? AND CampaignName=?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($User, $CampaignName));

        $row = $stmt->fetchColumn();

        if ($row > 0) {
            $response = new Response();
            $response->data["Error"] = "Campaign \"" . $CampaignName . "\" already exists.";
            $response->valid = false;
            echo json_encode($response);
            return;
        }
        
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in AddCampaign.php", $ex->getMessage());
        echo "Error handling request.";
        return;
    }
    
    try {                
        $stmt = Database::Get()->prepare("INSERT INTO Tabletop_Campaigns (Guid, CampaignName, Username) VALUES (?,?,?)");
        $stmt->execute(array($Guid, $CampaignName, $User));

        $stmt = Database::Get()->prepare("INSERT INTO Tabletop_InitiativeTracker (CampaignGuid) VALUES (?)");
        $stmt->execute(array($Guid));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in AddCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>