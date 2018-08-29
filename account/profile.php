<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('account/profile.twig');
    $twigOptions = Twig::GetTwigOptions();
    
    Redirect::RequireUser($twigOptions["Username"]);
    
    try {
        $stmt = Database::Get()->prepare("SELECT Email, Summoner_Name, Region FROM User_Info WHERE Username = ?");
        $stmt->execute(array($twigOptions["Username"]));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $twigOptions["Email"] = $rows[0]["Email"];
        $twigOptions["Summoner"] = $rows[0]["Summoner_Name"];
        $twigOptions["Region"] = $rows[0]["Region"];
    } catch(PDOException $ex) {
        Log::Error("Database error in profile.php", $ex->getMessage());
    }
    
    $twigOptions["RegionList"] = Riot::GetRegions();
    
    echo $template->render($twigOptions);
?>