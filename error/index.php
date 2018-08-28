<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $error_code = Input::GetEnv("REDIRECT_STATUS");
    if ($error_code === "") {
        $error_code = 500;
    }
    
    ErrorPage::Render($error_code);
?>