<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/libraries.php');
    
    $template = $twig->load('edit/edit.twig');
    $twig_options = getTwigOptions();
        
    Redirect::RequireUser($twig_options["Username"]);
    
    echo $template->render($twig_options);
?>