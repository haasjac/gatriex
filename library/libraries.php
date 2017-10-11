<?php
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/database.php');
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/session.php');
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/response.php');
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/authentication.php');
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/input.php');
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/mail.php');
    require_once(filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_STRING) . '/library/validation.php');
?>

