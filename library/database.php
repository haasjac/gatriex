<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/credentials/database.php');
    
    $db = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>