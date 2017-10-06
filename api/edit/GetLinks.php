<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    
    $response = array();
    $headers = array();
    // Create connection
    $conn = $db;
    
    $sql = "SELECT text, link, header FROM Links";
    $result = $conn->query($sql);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($headers[$row["header"]])) {
            $headers[$row["header"]] = array();
        }
        $item = array();
        $item["text"] = $row["text"]; 
        $item["link"] = $row["link"];
        array_push($headers[$row["header"]], $item);
    }
    
    foreach ($headers as $key => $value) {
        $category = array();
        $category["header"] = $key;
        $category["items"] = $value;
        array_push($response, $category);
    }
    
    $out = array_values($response);
    echo json_encode($out, true);
?>
