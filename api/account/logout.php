<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/libraries.php');
    
    $authentication->logout();
?>