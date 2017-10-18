<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('home/home.twig');
    $twig_options = getTwigOptions();
    
    if ($twig_options["Username"] !== "") {
        try {
            $stmt = $db->prepare("SELECT Summoner_Name, Region FROM User_Info WHERE Username = ?");
            $stmt->execute(array($twig_options["Username"]));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (sizeof($rows) > 0) {
                $twig_options["SummonerName"] = $rows[0]["Summoner_Name"];
                $twig_options["Region"] = $rows[0]["Region"];
            }
        } catch(PDOException $ex) {
            $log->error("Database error in index.php", $ex->getMessage());
        }
    } else {
        $twig_options["Region"] = "North America";
    }
    
    $twig_options["RegionList"] = $riot->getRegionArray();
    
    echo $template->render($twig_options);
?>