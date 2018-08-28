<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('account/profile.twig');
    $twig_options = getTwigOptions();
    
    Redirect::RequireUser($twig_options["Username"]);
    
    try {
        $stmt = Database::Get()->prepare("SELECT Email, Summoner_Name, Region FROM User_Info WHERE Username = ?");
        $stmt->execute(array($twig_options["Username"]));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $twig_options["Email"] = $rows[0]["Email"];
        $twig_options["Summoner"] = $rows[0]["Summoner_Name"];
        $twig_options["Region"] = $rows[0]["Region"];
    } catch(PDOException $ex) {
        Log::Error("Database error in profile.php", $ex->getMessage());
    }
    
    $twig_options["RegionList"] = Riot::GetRegions();
    
    echo $template->render($twig_options);
?>