<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("DELETE");

	$expected = array(
		"Guid"			=> NULL,
		"CampaignName"	=> NULL
	);

	$input = Input::GetDataFromBody($expected);
    $CampaignName = $input["CampaignName"];    
    $Guid = $input["Guid"];
    
    $result = Authentication::ValidateUserFromToken();
    if (!$result->valid) {
        echo json_encode($result);
        return;
    }

    $User = $result->data["Username"];
    
    

    try {
        $sql = "SELECT COUNT(*) FROM Tabletop_Campaigns WHERE Username=? AND Guid=?";
        $stmt = Database::Get()->prepare($sql);
        $stmt->execute(array($User, $Guid));

        $row = $stmt->fetchColumn();

        if ($row <= 0) {
            $response = new Response();
            $response->data["Error"] = "Campaign \"" . $CampaignName . "\" does not exist.";
            $response->valid = false;
            echo json_encode($response);
            return;
        }
        
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in RemoveCampaign.php", $ex->getMessage());
        echo "Error handling request.";
        return;
    }
    
    try {                
        $stmt = Database::Get()->prepare("DELETE FROM Tabletop_Campaigns WHERE Guid=? AND Username=?");
        $stmt->execute(array($Guid, $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in RemoveCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>