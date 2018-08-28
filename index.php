<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('home/home.twig');
    $twig_options = getTwigOptions();
    
    if ($twig_options["Username"] !== "") {
        try {
            $stmt = Database::Get()->prepare("SELECT Summoner_Name, Region FROM User_Info WHERE Username = ?");
            $stmt->execute(array($twig_options["Username"]));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (sizeof($rows) > 0) {
                $twig_options["SummonerName"] = $rows[0]["Summoner_Name"];
                $twig_options["Region"] = $rows[0]["Region"];
            }
        } catch(PDOException $ex) {
            Log::Error("Database error in index.php", $ex->getMessage());
        }
    } else {
        $twig_options["Region"] = "North America";
    }
    
    $twig_options["RegionList"] = Riot::GetRegionArray();
    
    echo $template->render($twig_options);
?>