<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
	$errorCode = $_SERVER["REDIRECT_STATUS"];
    if ($errorCode === "") {
        $errorCode = 500;
    }
    
    ErrorPage::Render($errorCode);
?>