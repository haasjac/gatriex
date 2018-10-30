<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
       
	Input::CheckMethod("DELETE");

	$expected = array(
		"Guid" => NULL
	);

	$input = Input::GetDataFromBody($expected);

    $User = Authentication::GetCurrentUserOrDie();
    
    try {    
        $stmt = Database::Get()->prepare("DELETE FROM Tabletop_Characters WHERE Guid = ? AND Username = ?");
        $stmt->execute(array($input["Guid"], $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/RemoveCharacter.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>