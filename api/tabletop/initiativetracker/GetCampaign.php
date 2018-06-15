<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $name = $_REQUEST["CampaignName"];
    
    if (!isset($name)) {
        $response = new Response();
        $response->data["Error"] = "Campaign name is required.";
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
    
    try {
		$sql = "SELECT InitiativeTracker FROM Tabletop_Campaigns WHERE Username = ? AND CampaignName=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($user, $name));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response = new Response();
		$response->data["InitiativeTracker"] = json_decode($row["InitiativeTracker"]);
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in GetCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>