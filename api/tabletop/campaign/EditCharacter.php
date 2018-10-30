<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
       
	Input::CheckMethod("PUT");

	$expected = array(
		"Guid"					=> NULL,
		"Name"					=> NULL,
		"Faction"				=> NULL,
		"InitiativeBonus"		=> NULL,
		"InitiativeAdvantage"	=> NULL
	);

	$input = Input::GetDataFromBody($expected);
    
    $User = Authentication::GetCurrentUserOrDie();
    
    try {    
        $stmt = Database::Get()->prepare("UPDATE Tabletop_Characters SET Name = ?, Faction = ?, InitiativeBonus = ?, InitiativeAdvantage = ? WHERE Guid = ? AND Username = ?");
        $stmt->execute(array($input["Name"], $input["Faction"], $input["InitiativeBonus"], $input["InitiativeAdvantage"], $input["Guid"], $User));

        $response = new Response();
        $response->valid = true;
        echo json_encode($response);
    } catch (PDOException $ex) {
        http_response_code(500);
        Log::Error("Database error in campaign/AddCharacter.php", $ex->getMessage());
        echo "Error handling request.";
    }
?>