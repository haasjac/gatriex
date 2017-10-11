<?php
    require_once (filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/twig.php');
    
    $template = $twig->load('account/recover.twig');
    $twig_options = getTwigOptions();
    
    echo $template->render($twig_options);
?>