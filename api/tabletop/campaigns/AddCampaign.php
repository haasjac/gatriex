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

	$User = $result->data["Username"];
    
    $CampaignName = $data["CampaignName"];

	$Guid = $authentication->generate_guid();

	try {
		$sql = "SELECT COUNT(*) FROM Tabletop_Campaigns WHERE Username = ? AND CampaignName=?";
        $stmt = $db->prepare($sql);
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
        $log->error("Database error in AddCampaign.php", $ex->getMessage());
        echo "Error handling request.";
		return;
    }
    
    try {				
        $stmt = $db->prepare("INSERT INTO Tabletop_Campaigns (Guid, CampaignName, Username) VALUES (?,?,?)");
        $stmt->execute(array($Guid, $CampaignName, $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in AddCampaign.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>