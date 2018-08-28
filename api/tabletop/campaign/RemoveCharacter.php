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
        $stmt = Database::Get()->prepare("DELETE FROM Tabletop_Characters WHERE Guid = ? AND Username = ?");
        $stmt->execute(array($data["Guid"], $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/RemoveCharacter.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>