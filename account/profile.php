<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('account/profile.twig');
    $twig_options = getTwigOptions();
    
    $redirect->requireUser($twig_options["Username"]);
    
    try {
        $stmt = $db->prepare("SELECT Email, Summoner_Name FROM User_Info WHERE Username = ?");
        $stmt->execute(array($twig_options["Username"]));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $twig_options["Email"] = $rows[0]["Email"];
        $twig_options["Summoner"] = $rows[0]["Summoner_Name"];
    } catch(PDOException $ex) {
        $log->error("Database error in profile.php", $ex->getMessage());
        $response->data["Error"] = "Error handling request.";
        $response->valid = false;
    }
    
    echo $template->render($twig_options);
?>