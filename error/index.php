<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $error_code = $input->getEnv("REDIRECT_STATUS");
    if ($error_code === "") {
        $error_code = 500;
    }
    
    $errorPage->render($error_code);
?>