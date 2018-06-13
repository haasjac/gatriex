<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/credentials/database.php');
    
    $db = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname . ';charset=utf8mb4', $username, $password, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>