<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('tabletop/initiativetracker/initiativetracker.twig');
    $twig_options = getTwigOptions();

    Redirect::RequireUser($twig_options["Username"]);
        
    if (isset($_REQUEST["id"])) {
        try {
            $sql = "SELECT CampaignName FROM Tabletop_Campaigns WHERE Guid = ? AND Username=?";
            $stmt = Database::Get()->prepare($sql);
            $stmt->execute(array($_REQUEST["id"], $twig_options["Username"]));

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $twig_options["CampaignGuid"] = $_REQUEST["id"];
            $twig_options["CampaignName"] = $row["CampaignName"];

        } catch (PDOException $ex) {
            Log::Error("Database error in index.php (initiativetracker)", $ex->getMessage());
        }
    }
    
    echo $template->render($twig_options);
?>