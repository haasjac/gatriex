<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/database.php');
    
    $template = $twig->load('home/home.html');
    $twig_options = getTwigOptions();
    
    if ($twig_options["Username"] !== "") {
        try {
            $stmt = $db->prepare("SELECT Summoner_Name FROM User_Info WHERE Username = ?");
            $stmt->execute(array($twig_options["Username"]));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (sizeof($rows) > 0) {
                $twig_options["SummonerName"] = $rows[0]["Summoner_Name"];
            }
        } catch(PDOException $ex) {
            // log $ex->getMessage();
        }
    }
    
    echo $template->render($twig_options);
?>