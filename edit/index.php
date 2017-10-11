<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    
    $template = $twig->load('edit/edit.twig');
    $twig_options = getTwigOptions();
    
    if ($twig_options["Username"] === "") {
        http_response_code(401);
    }
    
    echo $template->render($twig_options);
?>