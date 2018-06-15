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
    
    $CampaignName = $data["CampaignName"];
	$InitiativeTracker = json_encode($data["InitiativeTracker"]);
    
    try {
        $db->beginTransaction();

		$sql = "UPDATE Tabletop_Campaigns SET InitiativeTracker=? WHERE Username = ? AND CampaignName=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($InitiativeTracker, $user, $CampaignName));
        if ($stmt->rowCount() <= 0) {
            $stmt = $db->prepare("INSERT INTO Tabletop_Campaigns (Username, CampaignName, InitiativeTracker) VALUES (?,?,?)");
            $stmt->execute(array($user, $CampaignName, $InitiativeTracker));
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