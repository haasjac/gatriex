<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/credentials/database.php');
    
    $db = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>