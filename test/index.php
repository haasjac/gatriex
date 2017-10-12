<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('test/test.twig');
    $twig_options = getTwigOptions();
    
    if ($twig_options["Username"] === "") {
        $errorPage->render(403);
        return;
    }
    
    echo $template->render($twig_options);
?>