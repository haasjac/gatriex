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
    
    try {    
        $stmt = Database::Get()->prepare("UPDATE Tabletop_Characters SET Name = ?, Faction = ?, InitiativeBonus = ?, InitiativeAdvantage = ? WHERE Guid = ? AND Username = ?");
        $stmt->execute(array($data["Name"], $data["Faction"], $data["InitiativeBonus"], $data["InitiativeAdvantage"], $data["Guid"], $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/AddCharacter.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>