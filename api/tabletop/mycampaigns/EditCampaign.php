<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("PUT");

	$expected = array(
		"Guid"			=> NULL,
		"CampaignName"	=> NULL
	);

	$input = Input::GetDataFromBody($expected);
    $CampaignName = $input["CampaignName"];
    $Guid = $input["Guid"];
    
    $User = Authentication::GetCurrentUserOrDie();
    
    try {
        $sql = "SELECT Guid FROM Tabletop_Campaigns WHERE Username = ? AND CampaignName=?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($User, $CampaignName));

        $row = $stmt->fetchColumn();

        if ($row && $row != $Guid) {
            $response = new Response();
            $response->data["Error"] = "Campaign \"" . $CampaignName . "\" already exists.";
            $response->valid = false;
            echo json_encode($response);
            return;
        }
        
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in EditCampaign.php", $ex->getMessage());
        echo "Error handling request.";
        return;
    }
    
    try {                
        $stmt = Database::Get()->prepare("UPDATE Tabletop_Campaigns SET CampaignName=? WHERE Guid=? AND Username=?");
        $stmt->execute(array($CampaignName, $Guid, $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in EditCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>