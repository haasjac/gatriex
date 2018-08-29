<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = Twig::GetTwig()->load('tabletop/initiativetracker/initiativetracker.twig');
    $twigOptions = Twig::GetTwigOptions();

    Redirect::RequireUser($twigOptions["Username"]);
        
    if (isset($_REQUEST["id"])) {
        try {
            $sql = "SELECT CampaignName FROM Tabletop_Campaigns WHERE Guid = ? AND Username=?";
            $stmt = Database::Get()->prepare($sql);
            $stmt->execute(array($_REQUEST["id"], $twigOptions["Username"]));

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $twigOptions["CampaignGuid"] = $_REQUEST["id"];
            $twigOptions["CampaignName"] = $row["CampaignName"];

        } catch (PDOException $ex) {
            Log::Error("Database error in index.php (initiativetracker)", $ex->getMessage());
        }
    }
    
    echo $template->render($twigOptions);
?>