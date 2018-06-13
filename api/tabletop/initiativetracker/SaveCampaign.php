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
    
    $name = $data["name"];
	$campaign = json_encode($data["campaign"]);
    
    try {
        $db->beginTransaction();

		$sql = "UPDATE InitiativeTracker_Campaigns SET Campaign=? WHERE Username = ? AND Name=?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($campaign, $user, $name));
        if ($stmt->rowCount() <= 0) {
            $stmt = $db->prepare("INSERT INTO InitiativeTracker_Campaigns (Username, Name, Campaign) VALUES (?,?,?)");
            $stmt->execute(array($user, $name, $campaign));
        }
        
        $db->commit();
        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
        return;
    } catch (PDOException $ex) {
        http_response_code(500);
        $log->error("Database error in SaveCampaign.php", $ex->getMessage());
        echo "Error handling request." . "a";
        $db->rollBack();
    }
?>