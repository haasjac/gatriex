<?php
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/twig.php');
    
    $template = $twig->load('account/register.twig');
    $twig_options = getTwigOptions();
    
    echo $template->render($twig_options);
?>