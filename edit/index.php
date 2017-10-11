<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/twig.php');
    
    $template = $twig->load('edit/edit.twig');
    $twig_options = getTwigOptions();
    
    echo $template->render($twig_options);
?>