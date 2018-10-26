<?php
    require_once('Call.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	Input::CheckMethod("GET");

	$expected = array(
		"region" => FILTER_SANITIZE_URL
	);

	$input = Input::GetDataFromUrl($expected);
	    
    $result = ApiCall("https://" . $input["region"] . ".api.riotgames.com/lol/status/v3/shard-data");
    
    echo json_encode($result);
?>