<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
      
	 Input::CheckMethod("GET");

    $User = Authentication::GetCurrentUserOrDie();
    
    try {                
        $stmt = Database::Get()->prepare("SELECT Guid, CampaignName FROM Tabletop_Campaigns WHERE Username=?");
        $stmt->execute(array($User));

        $Campaigns = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($Campaigns, $row);
        }

        $response = new Response();
        $response->valid = true;
        $response->data["Campaigns"] = $Campaigns;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in AddCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>