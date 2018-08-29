<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('home/home.twig');
    $twigOptions = Twig::GetTwigOptions();
    
    if ($twigOptions["Username"] !== "") {
        try {
            $stmt = Database::Get()->prepare("SELECT Summoner_Name, Region FROM User_Info WHERE Username = ?");
            $stmt->execute(array($twigOptions["Username"]));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (sizeof($rows) > 0) {
                $twigOptions["SummonerName"] = $rows[0]["Summoner_Name"];
                $twigOptions["Region"] = $rows[0]["Region"];
            }
        } catch(PDOException $ex) {
            Log::Error("Database error in index.php", $ex->getMessage());
        }
    } else {
        $twigOptions["Region"] = "North America";
    }
    
    $twigOptions["RegionList"] = Riot::GetRegionArray();
    
    echo $template->render($twigOptions);
?>